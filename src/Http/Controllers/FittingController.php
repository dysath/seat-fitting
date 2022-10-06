<?php

namespace Denngarr\Seat\Fitting\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Seat\Web\Http\Controllers\Controller;
use Seat\Web\Models\Acl\Role;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Seat\Eveapi\Models\Alliances\Alliance;
use Seat\Eveapi\Models\Alliances\AllianceMember;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\Character\CharacterAffiliation;
use Seat\Eveapi\Models\Character\CharacterSkill;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Eveapi\Models\Sde\InvType;
use Seat\Eveapi\Models\Sde\DgmTypeAttribute;
use Denngarr\Seat\Fitting\Helpers\CalculateConstants;
use Denngarr\Seat\Fitting\Helpers\CalculateEft;
use Denngarr\Seat\Fitting\Models\Fitting;
use Denngarr\Seat\Fitting\Models\Doctrine;
use Denngarr\Seat\Fitting\Validation\FittingValidation;
use Denngarr\Seat\Fitting\Validation\DoctrineValidation;

class FittingController extends Controller implements CalculateConstants
{
    use CalculateEft;

    private $requiredSkills = [];

    public function getDoctrineEdit($doctrine_id)
    {
        $selected = [];
        $unselected = [];
        $doctrine_fits = [];

        $fittings = Fitting::all();
        $doctrine_fittings = Doctrine::find($doctrine_id)->fittings()->get();

        foreach ($doctrine_fittings as $doctrine_fitting) {
            array_push($doctrine_fits, $doctrine_fitting->id);
        }

        foreach ($fittings as $fitting) {
            $ship = InvType::where('typeName', $fitting->shiptype)->first();

            $entry = [
                'id' => $fitting->id,
                'shiptype' => $fitting->shiptype,
                'fitname' => $fitting->fitname,
                'typeID' => $ship->typeID,
            ];

            if (array_search($fitting->id, $doctrine_fits) !== false) {
                array_push($selected, $entry);
            } else {
                array_push($unselected, $entry);
            }
        }

        return [
            $selected,
            $unselected,
            $doctrine_id,
            Doctrine::find($doctrine_id)->name,
        ];
    }

    public function getDoctrineList()
    {
        $doctrine_names = [];

        $doctrines = Doctrine::all();

        if (count($doctrines) > 0) {

            foreach ($doctrines as $doctrine) {
                array_push($doctrine_names, [
                    'id' => $doctrine->id,
                    'name' => $doctrine->name,
                ]);
            }
        }

        return $doctrine_names;
    }

    public function getDoctrineById($id)
    {
        $fitting_list = [];

        $doctrine = Doctrine::find($id);
        $fittings = $doctrine->fittings()->get();

        foreach ($fittings as $fitting) {
            $ship = InvType::where('typeName', $fitting->shiptype)->first();

            array_push($fitting_list, [
                'id' => $fitting->id,
                'name' => $fitting->fitname,
                'shipType' => $fitting->shiptype,
                'shipImg' => $ship->typeID,
            ]);
        }

        return $fitting_list;
    }

    public function delDoctrineById($id)
    {
        Doctrine::destroy($id);

        return "Success";
    }

    public function deleteFittingById($id)
    {
        Fitting::destroy($id);

        return "Success";
    }

    public function getSkillsByFitId($id)
    {
        $characters = [];
        $skillsToons = [];

        $fitting = Fitting::find($id);
        $skillsToons['skills'] = $this->calculate($fitting->eftfitting);
        $skilledCharacters = CharacterInfo::with('skills')->whereIn('character_id', auth()->user()->associatedCharacterIds())->get();

        foreach ($skilledCharacters as $character) {

            $index = $character->character_id;

            $skillsToons['characters'][$index]['id']   = $character->character_id;
            $skillsToons['characters'][$index]['name'] = $character->name;

            foreach ($character->skills as $skill) {

                $rank = DgmTypeAttribute::where('typeID', $skill->skill_id)->where('attributeID', '275')->first();

                $skillsToons['characters'][$index]['skill'][$skill->skill_id]['level'] = $skill->trained_skill_level;
                $skillsToons['characters'][$index]['skill'][$skill->skill_id]['rank']  = $rank->valueFloat;
            }

            // Fill in missing skills so Javascript doesn't barf and you have the correct rank
            foreach ($skillsToons['skills'] as $skill) {

                if (isset($skillsToons['characters'][$index]['skill'][$skill['typeId']])) {
                    continue;
                }

                $rank = DgmTypeAttribute::where('typeID', $skill['typeId'])->where('attributeID', '275')->first();

                $skillsToons['characters'][$index]['skill'][$skill['typeId']]['level'] = 0;
                $skillsToons['characters'][$index]['skill'][$skill['typeId']]['rank'] = $rank->valueFloat;
            }
        }

        return json_encode($skillsToons);
    }

    protected function getFittings()
    {
        return Fitting::all();
    }

    public function getFittingList()
    {
        $fitnames = [];
        $alliance_corps = [];

        $fittings = $this->getFittings();

        if (count($fittings) <= 0)
            return $fitnames;

        foreach ($fittings as $fit) {
            $ship = InvType::where('typeName', $fit->shiptype)->first();

            array_push($fitnames, [
                'id' => $fit->id,
                'shiptype' => $fit->shiptype,
                'fitname' => $fit->fitname,
                'typeID' => $ship->typeID
            ]);
        }

        return $fitnames;
    }

    public function getEftFittingById($id)
    {
        $fitting = Fitting::find($id);

        return $fitting->eftfitting;
    }

    public function getFittingCostById($id)
    {
        $fit = Fitting::find($id);

        // $eft = implode("\n", $fit->eftfitting);
        
        $response = (new Client())
            ->request('POST', 'http://evepraisal.com/appraisal.json?market=jita&persist=no', [
                'multipart' => [
                    [
                        'name' => 'uploadappraisal',
                        'contents' => $fit->eftfitting,
                        'filename' => 'notme',
                        'headers' => [
                            'Content-Type' => 'text/plain',
                            'User-Agent' => 'seat-srp'
                        ]
                    ],
                ]
            ]);

        return response()->json(json_decode($response->getBody()->getContents()));
    }

    public function getFittingById($id)
    {
        $fitting = Fitting::find($id);

        return response()->json($this->fittingParser($fitting->eftfitting));
    }

    public function getFittingView()
    {
        $corps = [];
        $fitlist = $this->getFittingList();

        if (Gate::allows('global.superuser')) {
            $corpnames = CorporationInfo::all();
        } else {
            $corpids = CharacterAffiliation::whereIn('character_id', auth()->user()->associatedCharacterIds())->select('corporation_id')->get()->toArray();
            $corpnames = CorporationInfo::whereIn('corporation_id', $corpids)->get();
        }

        foreach ($corpnames as $corp) {
            $corps[$corp->corporation_id] = $corp->name;
        }

        return view('fitting::fitting', compact('fitlist', 'corps'));
    }

    public function getDoctrineView()
    {
        $doctrine_list = $this->getDoctrineList();

        return view('fitting::doctrine', compact('doctrine_list'));
    }

    public function getAboutView()
    {
        return view('fitting::about');
    }

    public function saveFitting(FittingValidation $request)
    {
        $fitting = new Fitting();

        if ($request->fitSelection > 0) {
            $fitting = Fitting::find($request->fitSelection);
        }

        $eft = explode("\n", $request->eftfitting);
        list($fitting->shiptype, $fitting->fitname) = explode(", ", substr($eft[0], 1, -2));
        $fitting->eftfitting = $request->eftfitting;
        $fitting->save();

        $fitlist = $this->getFittingList();

        return view('fitting::fitting', compact('fitlist'));
    }

    public function postFitting(FittingValidation $request)
    {
        $eft = $request->input('eftfitting');

        return response()->json($this->fittingParser($eft));
    }


    private function fittingParser($eft)
    {
        $jsfit = [];
        $data = preg_split("/\r?\n\r?\n/", $eft);
        $jsfit['eft'] = $eft;

        $header = preg_split("/\r?\n/", $data[0]);

        list($jsfit['shipname'], $jsfit['fitname']) = explode(",", substr($header[0], 1, -1));
        array_shift($header);
        $data[0] = implode("\r\n", $header);

        // Deal with a blank line between the name and the first low slot    
        $lowslot = array_filter(preg_split("/\r?\n/", $data[0]));
        if (empty($lowslot)) {
            $data = array_splice($data, 1, count($data));
        }

        $lowslot = array_filter(preg_split("/\r?\n/", $data[0]));
        $midslot = array_filter(preg_split("/\r?\n/", $data[1]));
        $highslot = array_filter(preg_split("/\r?\n/", $data[2]));
        $rigs = array_filter(preg_split("/\r?\n/", $data[3]));

        // init drones array
        if (count($data) > 4) {
            //Deal with extra blank line between rigs and drones
            $drones = array_filter(preg_split("/\r?\n/", $data[4]));
            if (empty($drones)) {
                $data = array_splice($data, 1, count($data));
                $drones = array_filter(preg_split("/\r?\n/", $data[4]));
            }
        }

        // special case for tech 3 cruiser which may have sub-modules
        if (in_array($jsfit['shipname'], ['Tengu', 'Loki', 'Legion', 'Proteus'])) {

            $subslot = array_filter(preg_split("/\r?\n/", $data[4]));

            // bump drones to index 5
            $drones = [];
            if (count($data) > 5) {
                $drones = array_filter(preg_split("/\r?\n/", $data[5]));
            }
        }

        $this->loadSlot($jsfit, "LoSlot", $lowslot);
        $this->loadSlot($jsfit, "MedSlot", $midslot);
        $this->loadSlot($jsfit, "HiSlot", $highslot);

        if (isset($subslot)) {
            $this->loadSlot($jsfit, "SubSlot", $subslot);
        }

        $this->loadSlot($jsfit, "RigSlot", $rigs);

        if (isset($drones)) {
            foreach ($drones as $slot) {
                list($drone, $qty) = explode(" x", $slot);
                $item = InvType::where('typeName', $drone)->first();

                $jsfit['dronebay'][$item->typeID] = [
                    'name' => $drone,
                    'qty'  => $qty,
                ];
            }
        }
        return $jsfit;
    }

    private function loadSlot(&$jsfit, $slotname, $slots)
    {
        $index = 0;

        foreach ($slots as $slot) {
            $module = explode(",", $slot);

            if (!preg_match("/\[Empty .+ slot\]/", $module[0])) {
                $item = InvType::where('typeName', $module[0])->first();

                if (empty($item)) {
                    continue;
                }

                $jsfit[$slotname . $index] = [
                    'id'   => $item->typeID,
                    'name' => $module[0],
                ];

                $index++;
            }
        }
        return;
    }


    public function postSkills(FittingValidation $request)
    {
        $skillsToons = [];
        $fitting = $request->input('eftfitting');
        $skillsToons['skills'] = $this->calculate($fitting);

        $characters = $this->getUserCharacters(auth()->user()->id);

        foreach ($characters as $character) {
            $index = $character->characterID;

            $skillsToons['characters'][$index] = [
                'id'   => $character->characterID,
                'name' => $character->characterName,
            ];

            //            $characterSkills = $this->getCharacterSkillsInformation($character->characterID);
            $characterSkills = CharacterInfo::with('skills')->where('character_id', $character->characterID)->get();

            foreach ($characterSkills as $skill) {
                $rank = DgmTypeAttributes::where('typeID', $skill->typeID)->where('attributeID', '275')->first();

                $skillsToons['characters'][$index]['skill'][$skill->typeID] = [
                    'level' => $skill->level,
                    'rank'  => $rank->valueFloat,
                ];
            }

            // Fill in missing skills so Javascript doesn't barf and you have the correct rank
            foreach ($skillsToons['skills'] as $skill) {

                if (isset($skillsToons['characters'][$index]['skill'][$skill['typeId']])) {
                    continue;
                }

                $rank = DgmTypeAttributes::where('typeID', $skill['typeId'])->where('attributeID', '275')->first();

                $skillsToons['characters'][$index]['skill'][$skill['typeId']] = [
                    'level' => 0,
                    'rank'  => $rank->valueFloat,
                ];
            }
        }

        return response()->json($skillsToons);
    }

    private function getSkillNames($types)
    {
        $skills = [];

        foreach ($types as $skill_id => $level) {
            $res = InvType::where('typeID', $skill_id)->first();

            $skills[] = [
                'typeId' => $skill_id,
                'typeName' => $res->typeName,
                'level' => $level,
            ];
        }

        ksort($skills);

        return $skills;
    }

    public function getRoleList()
    {
        return Role::all();
    }

    public function saveDoctrine(DoctrineValidation $request)
    {
        $doctrine = new Doctrine();

        if ($request->doctrineid > 0) {
            $doctrine = Doctrine::find($request->doctrineid);
        }

        $doctrine->name = $request->doctrinename;
        $doctrine->save();

        foreach ($request->selectedFits as $fitId) {
            $doctrine->fittings()->sync($request->selectedFits);
        }

        return redirect()->route('fitting.doctrineview');
    }

    public function viewDoctrineReport()
    {
        $doctrines = Doctrine::all();
        $corps = CorporationInfo::all();
        $chars = CharacterInfo::select('character_infos.*');
        $alliances = array();

        $allids = array();

        foreach ($corps as $corp) {
            if (!is_null($corp->alliance_id)) {
                array_push($allids, $corp->alliance_id);
            }
        }

        $alliances = Alliance::whereIn('alliance_id', $allids)->get();

        return view('fitting::doctrinereport', compact('doctrines', 'corps', 'alliances', 'chars'));
    }

    public function runReport($alliance_id, $corp_id, $char_id, $doctrine_id)
    {
        $characters = collect();

        if ($alliance_id !== '0') {

            $chars = CharacterInfo::with('skills')->whereHas('affiliation', function ($affiliation) use ($alliance_id) {
                $affiliation->where('alliance_id', $alliance_id);
            })->get();
            $characters = $characters->concat($chars);
        } else if ($char_id !== '0') {
            $characters = CharacterInfo::with('skills')->where('character_id', $char_id)->get();
        } else {
            $characters = CharacterInfo::with('skills')->whereHas('affiliation', function ($affiliation) use ($corp_id) {
                $affiliation->where('corporation_id', $corp_id);
            })->get();
        }


        $doctrine = Doctrine::where('id', $doctrine_id)->first();
        $fittings = $doctrine->fittings;
        $charData = [];
        $fitData = [];
        $data = [];
        $data['fittings'] = [];
        $data['totals'] = [];

        foreach ($characters as $character) {
            $charData[$character->character_id]['name'] = $character->name;
            $charData[$character->character_id]['skills'] = [];

            foreach ($character->skills as $skill) {
                $charData[$character->character_id]['skills'][$skill->skill_id] = $skill->trained_skill_level;
            }
        }

        foreach ($fittings as $fitting) {
            $fit = Fitting::find($fitting->id);

            array_push($data['fittings'], $fit->fitname);

            $this->requiredSkills = [];
            $shipSkills = $this->calculate("[" . $fit->shiptype . ", a]");

            foreach ($shipSkills as $shipSkill) {
                $fitData[$fitting->id]['shipskills'][$shipSkill['typeId']] = $shipSkill['level'];
            }

            $this->requiredSkills = [];
            $fitSkills = $this->calculate($fit->eftfitting);
            $fitData[$fitting->id]['name'] = $fit->fitname;

            foreach ($fitSkills as $fitSkill) {
                $fitData[$fitting->id]['skills'][$fitSkill['typeId']] = $fitSkill['level'];
            }
        }

        foreach ($charData as $char) {

            foreach ($fitData as $fit) {
                $canflyfit = true;
                $canflyship = true;

                foreach ($fit['skills'] as $skill_id => $level) {
                    if (isset($char['skills'][$skill_id])) {
                        if ($char['skills'][$skill_id] < $level) {
                            $canflyfit = false;
                        }
                    } else {
                        $canflyfit = false;
                    }
                }

                foreach ($fit['shipskills'] as $skill_id => $level) {
                    if (isset($char['skills'][$skill_id])) {
                        if ($char['skills'][$skill_id] < $level) {
                            $canflyship = false;
                        }
                    } else {
                        $canflyship = false;
                    }
                }

                if (!isset($data['totals'][$fit['name']]['ship'])) {
                    $data['totals'][$fit['name']]['ship'] = 0;
                }
                if (!isset($data['totals'][$fit['name']]['fit'])) {
                    $data['totals'][$fit['name']]['fit'] = 0;
                }

                $data['chars'][$char['name']][$fit['name']]['ship'] = false;
                if ($canflyship) {
                    $data['chars'][$char['name']][$fit['name']]['ship'] = true;
                    $data['totals'][$fit['name']]['ship']++;
                }

                $data['chars'][$char['name']][$fit['name']]['fit'] = false;
                if ($canflyfit) {
                    $data['chars'][$char['name']][$fit['name']]['fit'] = true;
                    $data['totals'][$fit['name']]['fit']++;
                }
            }
        }

        $data['totals']['chars'] = count($charData);

        return response()->json($data);
    }
}
