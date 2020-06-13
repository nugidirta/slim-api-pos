<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        'upload_directory' => __DIR__ . '/../public/uploads', // upload directory => dibuat 21.07.18

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // Database Settings => dibuat 21.07.18
        'db' => [
            'port' => 5444,
            'host' => '127.0.0.1',
            'user' => 'sysi5adm',
            'pass' => '123456',
            'dbname' => 'i5_test',
            'driver' => 'pgsql'
        ]
    ],
];
