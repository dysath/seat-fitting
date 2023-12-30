<?PHP

namespace Denngarr\Seat\Fitting\Helpers;

interface CalculateConstants
{

    // don't touch this, or you will lose your hands
    // see dgmAttributeTypes to know what they are
    public const REQ_SKILLS_ATTRIBUTES = [
        182, 183, 184, 1285, 1289, 1290,
    ];

    public const REQ_SKILLS_LEVELS     = [
        277, 278, 279, 1286, 1287, 1288,
    ];

    public const REQ_SKILLS_ATTR_LEVELS = [
        182 => 277,
        183 => 278,
        184 => 279,
        1285 => 1286,
        1289 => 1287,
        1290 => 1288,
    ];

    public const DG_PGOUTPUT  = 11;
    public const DG_PGLOAD    = 15;
    public const DG_CPUOUTPUT = 48;
    public const DG_CPULOAD   = 49;

    public const RAISE_ALREADY_FULLFILLED = 0;
    public const RAISE_SKILL_RAISED       = 1;
    public const RAISE_CANNOT_RAISE       = 2;

    public const CPU_SKILL_ORDER = [
        // CPU Management
        // Weapon Upgrades
        [3426 => 1],
        [3318 => 1],
        [3426 => 2],
        [3318 => 2],
        [3426 => 3],
        [3318 => 3],
        [3426 => 4],
        [3426 => 5],
        [3318 => 4],
        [3318 => 5],
    ];
   public const PG_SKILL_ORDER = [
        // Power Grid Management
        // Shield Upgrades
        // Advanced Weapon Upgrades
        [3413  => 1],
        [3413  => 2],
        [3413  => 3],
        [3413  => 4],
        [3413  => 5],
        [3425  => 1],
        [11207 => 1],
        [3425  => 2],
        [11207 => 2],
        [3425  => 3],
        [11207 => 3],
        [3425  => 4],
        [11207 => 4],
        [3425  => 5],
        [11207 => 5],
    ];

}

