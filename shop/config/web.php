<?php
$config = [
    'id' => 'shop',
    'controllerNamespace' => 'shop\controllers',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@shop' => dirname(__DIR__)
    ],
    'modules' => [
        'user' => [
            'class' => 'shop\modules\user\Module'
        ]
    ],
    'components' => [
        'redis' => [
            'class' => 'lingyin\cache\redis\Cache',
            'keyPrefix' => 'mifan:',
            'redis' => [
                'hostname' => '115.29.49.123',
                'port' => 6379,
                'database' => 3,
                'password' => 'localpass',
            ]
        ],
        'view' => [
            'defaultExtension' => 'html',
        ]
    ]
];

return $config;
