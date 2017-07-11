<?php
$config = [
    'id' => 'App',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@shop' => dirname(__DIR__)
    ],
    'components' => [
        'db' => [

        ],
        'redis' => [
            'class' => 'lingyin\cache\redis\Cache',
            'keyPrefix' => 'mifan:',
            'redis' => [
                'hostname' => '115.29.49.123',
                'port' => 6379,
                'database' => 3,
                'password' => 'localpass',
            ]
        ]
    ]
];

return $config;
