<?PHP

namespace Denngarr\Seat\Fitting\Helpers;

use Seat\Eveapi\Models\Sde\InvType;
use Seat\Eveapi\Models\Sde\DgmTypeAttribute;

trait CalculateEft 
{
    private $ctx;

    private $cpu_raise_index = 0;

    private $pg_raise_index = 0;

    public function calculate($fitting)
    {

        $items = $this->parseEftFitting($fitting);
        $item_ids = $this->getUniqueTypeIDs($items['all_item_types']);
        $this->getReqSkillsByTypeIDs($item_ids);
        $this->modifyRequiredSkills($items['fit_items']);

        return $this->getSkillNames($this->requiredSkills);
    }

    private function modifyRequiredSkills($fitting): void
    {
        // skip this, if dogma extension isn't loaded
        if (!extension_loaded('dogma')) {
            return;
        }

        dogma_init_context($this->ctx);
        dogma_set_default_skill_level($this->ctx, 0);

        $fitting = $this->convertToTypeIDs($fitting);

        // add ship
        dogma_set_ship($this->ctx, array_shift($fitting));

        // add skills
        foreach ($this->requiredSkills as $skill => $level) {
            dogma_set_skill_level($this->ctx, $skill, $level);
        }

        // add modules
        foreach ($fitting as $item) {
            dogma_add_module_s($this->ctx, $item, $key, \DOGMA_STATE_ACTIVE);
        }

        // raise CPU skills
        $raise = null;
        while ($this->getAttribValue(self::DG_CPUOUTPUT) < $this->getAttribValue(self::DG_CPULOAD) && $raise !== self::RAISE_CANNOT_RAISE) {
            $raise = $this->raiseSkill('cpu');
        }

        // raise Powergrid skills
        $raise = null;
        while ($this->getAttribValue(self::DG_PGOUTPUT) < $this->getAttribValue(self::DG_PGLOAD) && $raise !== self::RAISE_CANNOT_RAISE) {
            $raise = $this->raiseSkill('powergrid');
        }
    }

    private function getAttribValue($attrib)
    {
        dogma_get_ship_attribute($this->ctx, $attrib, $ret);

        return $ret;
    }

    private function raiseSkill($type)
    {
        switch ($type) {
            case 'cpu':
                $index =& $this->cpu_raise_index;
                $skillsOrder = self::CPU_SKILL_ORDER;
                break;
            case 'powergrid':
                $index =& $this->pg_raise_index;
                $skillsOrder = self::PG_SKILL_ORDER;
                break;
        }

        if (!isset($skillsOrder[$index])) {
            return self::RAISE_CANNOT_RAISE;
        }

        $skill = $skillsOrder[$index];
        $skillId = key($skill);
        $level = $skill[$skillId];

        $index++;

        if (! isset($this->requiredSkills[$skillId]) || $this->requiredSkills[$skillId] < $level) {
            dogma_set_skill_level($this->ctx, $skillId, $level);
            $this->requiredSkills[$skillId] = $level;

            return self::RAISE_SKILL_RAISED;
        }

        return self::RAISE_ALREADY_FULLFILLED;
    }

    private function getReqSkillsByTypeIDs($typeIDs): void
    {
        $attributeids = [...array_keys(self::REQ_SKILLS_ATTR_LEVELS), ...array_values(self::REQ_SKILLS_ATTR_LEVELS)];

        foreach ($typeIDs as $type) {
            $res = DgmTypeAttribute::where('typeid', $type['typeID'])->wherein('attributeID', $attributeids)->get();

            if (count($res) == 0) {
                continue;
            }

            $skillsToAdd = $this->prepareRequiredSkills($res);
            $this->buildMinRequiredSkills($skillsToAdd);
            $this->findSubRequiredSkills($skillsToAdd);
        }
    }

    private function findSubRequiredSkills($skills): void
    {
        $toFind = [];

        foreach ($skills as $skill => $level) {
            $toFind[] = ['typeID' => $skill];
        }

        $this->getReqSkillsByTypeIDs($toFind);
    }

    private function buildMinRequiredSkills($toAdd): void
    {
        foreach ($toAdd as $skill => $level) {
            if (! isset($this->requiredSkills[$skill]) || $this->requiredSkills[$skill] < $level) {
                $this->requiredSkills[$skill] = $level;
            }
        }
    }

    /**
     * @return mixed[]
     */
    private function prepareRequiredSkills($attributes): array
    {
        $skills = [];
        $keys = [];

        // build an array of attributes
        foreach ($attributes as $attribute) {
            $attribValue = $attribute['valueInt'] ?? $attribute['valueFloat'];
            $keys[$attribute['attributeID']] = $attribValue;
        }

        // iterate over all attributes and build the skill array
        foreach (self::REQ_SKILLS_ATTR_LEVELS as $k => $v) {
            if (isset($keys[$k])) {
                $skills[$keys[$k]] = $keys[$v];
            }
        }

        return $skills;
    }

    private function parseEftFitting($fitting): array
    {
        $fitting = $this->sanatizeFittingBlock($fitting);
        $fitsplit = explode("\n", (string) $fitting);

        // get shipname of first line by removing brackets
        [$shipname, $fitname] = explode(", ", substr(array_shift($fitsplit), 1, -1));

        $fit_all_items = [];
        $fit_calc_items = [];

        // first element is always the ship type
        $fit_all_items[] = $fit_calc_items[] = $shipname;

        foreach ($fitsplit as $key => $line) {
            // split line to get charge
            $linesplit = explode(",", $line);

            if (isset($linesplit[1])) {
                $fit_all_items[] = $linesplit[1];
            }

            // don't add drones to fitting
            if (preg_match("/ x\d+/", $linesplit[0])) {
                $fit_all_items[] = $this->sanatizeTypeName($linesplit[0]);
            } else {
                $fit_all_items[] = $fit_calc_items[] = $this->sanatizeTypeName($linesplit[0]);
            }
        }

        return [
            'all_item_types' => array_unique($fit_all_items),
            'fit_items' => $fit_calc_items,
        ];
    }

    private function getUniqueTypeIDs($items)
    {
        return InvType::wherein('typeName', $items)->get();
    }

    private function convertToTypeIDs(array $items): array
    {
        foreach ($items as $key => $item) {
            $items[$key] = InvType::where('typeName', $item)->first()->id;
        }

        return $items;
    }

    private function sanatizeFittingBlock($fitting): string
    {
        // remove useless empty lines and whatnot
        $fitting = preg_replace("/\[Empty .+ slot\]/", "", (string) $fitting);

        return ltrim(rtrim(preg_replace("/^[ \t]*[\r\n]+/m", "", $fitting)));
    }

    private function sanatizeTypeName($item): string
    {
        // remove amount for charges
        // sample: Scourge Rage Heavy Assault Missile x66
        return ltrim(rtrim(preg_replace("/ x\d+/", "", (string) $item)));
    }

};
