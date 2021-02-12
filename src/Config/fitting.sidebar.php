<?php 

return [
    'doctrine' => [
        'name' => 'Doctrines & Fittings',
        'permission' => 'fitting.doctrineview',
        'route_segment' => 'fitting',
        'icon' => 'fas fa-rocket',
        'entries'       => [
            'fitting' => [
                'name' => 'Fittings',
                'icon' => 'fas fa-rocket',
                'route_segment' => 'fitting',
                'route' => 'fitting.view',
                'permission' => 'fitting.view'
            ],
            'doctrine' => [
                'name' => 'Doctrine',
                'icon' => 'fas fa-list',
                'route_segment' => 'fitting',
                'route' => 'fitting.doctrineview',
                'permission' => 'fitting.doctrineview'
            ],
            'doctrinereport' => [
                'name' => 'Doctrine Report',
                'icon' => 'fas fa-chart-pie',
                'route_segment' => 'fitting',
                'route' => 'fitting.doctrinereport',
                'permission' => 'fitting.reportview'
            ],
            'about' => [
                'name' => 'About',
                'icon' => 'fas fa-info',
                'route_segment' => 'fitting',
                'route' => 'fitting.about',
                'permission' => 'fitting.view'
            ],
        ]
    ]
];
