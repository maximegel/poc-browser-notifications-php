<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // Set to false in production.
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header.

        // Renderer settings.
        'renderer' => [
            'views_path' => __DIR__ . '/Views/',
        ],

        // Monolog settings.
        'logger' => [
            'name' => 'browser-notifications-demo',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // Data settings.
        'data' => [
            'dir' => __DIR__ . '/../data/'
        ],

        // Push messaging settings.
        'push_messaging' => [
            'auth' => [
                'VAPID' => [
                    'subject' => 'mailto:maxime.gelinas@idmobiles.com',
                    'publicKey' => 'BETMjzbMvx0HPbS2ch2KlJmmZfbX0cxWQq7wR2Anuzd8MDQOiG7g05192GPDX6vMIWRm92YCfJeAdXxj1RNxhzw',
                    'privateKey' => 'R3JCylZAHUVNc0Tlj229qaoS-2AGJrvqEwtMQE3aaeE',
                ]
            ]
        ]
    ],
];
