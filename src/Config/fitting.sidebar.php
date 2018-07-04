<?php 

return [
    'doctrine' => [
        'name' => 'Doctrines & Fittings',
        'permission' => 'fitting.doctrineview',
        'route_segment' => 'fitting',
        'icon' => 'fa-rocket',
        'entries'       => [
            'fitting' => [
                'name' => 'Fittings',
                'icon' => 'fa-rocket',
                'route_segment' => 'fitting',
                'route' => 'fitting.view',
                'permission' => 'fitting.view'
            ],
            'doctrine' => [
                'name' => 'Doctrine',
                'icon' => 'fa-rocket',
                'route_segment' => 'fitting',
                'route' => 'fitting.doctrineview',
                'permission' => 'fitting.view'
            ],
            'doctrinereport' => [
                'name' => 'Doctrine Report',
                'icon' => 'fa-notepad',
                'route_segment' => 'fitting',
                'route' => 'fitting.doctrinereport',
                'permission' => 'fitting.view'
            ],
        ]
    ]
];
