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
                'icon' => 'fa-list',
                'route_segment' => 'fitting',
                'route' => 'fitting.doctrineview',
                'permission' => 'fitting.doctrineview'
            ],
            'doctrinereport' => [
                'name' => 'Doctrine Report',
                'icon' => 'fa-pie-chart',
                'route_segment' => 'fitting',
                'route' => 'fitting.doctrinereport',
                'permission' => 'fitting.reportview'
            ],
        ]
    ]
];
