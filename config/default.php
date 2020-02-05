<?php

// This file contains the base default configuration that is used when the
// config file ( test-suite.php ) is not published, or when any required
// config value is not provided by the end user

return [
    'enabled' => [
        'phpunit'      => file_exists(base_path('vendor/bin/phpunit')),
        'dusk'         => class_exists('Laravel\Dusk\Dusk'),
        'php-cs-fixer' => file_exists(base_path('vendor/bin/php-cs-fixer')),
        'phpmd'        => file_exists(base_path('vendor/bin/phpmd')),
        'phpcs'        => file_exists(base_path('vendor/bin/phpcs')),
        'phpcbf'       => file_exists(base_path('vendor/bin/phpcbf')),
    ],

    'php-binary' => exec('which php'),

    'phpunit' => [
        'binary'        => './vendor/bin/phpunit',
        'code-coverage' => [
            'enabled'       => true,
            'driver'        => 'phpdbg',
            'phpdbg-config' => [
                'binary'  => exec('which phpdbg'),
                'options' => [],
            ],
            'xdebug-config' => [
                'options' => [],
            ],
        ],

        'options' => [
        ],

        'aliases' => [
            'all' => [
                'description'      => 'Run all tests ( don\'t stop on errors or warnings )',
                'short'            => 'a',
                'options-disabled' => [
                    '--cache-result',
                    '--order-by=defects',
                    '--stop-on-defect',
                ],
            ],
        ],
    ],

    'dusk' => [
        'options' => [],
    ],

    'php-cs-fixer' => [
        'binary' => [
            exec('which php'),
            './vendor/bin/php-cs-fixer',
        ],
        'folders' => [
            './app',
            './tests',
        ],

        'options' => [
            'fix',
            '{folder}',
            '--using-cache=false',
            '--verbose',
            '--diff',
            '--diff-format=udiff',
        ],

        'aliases' => [
            'fix' => [
                'description'      => 'Apply the fixes automatically',
                'short'            => 'f',
                'options-disabled' => [
                    '--dry-run',
                ],
            ],
        ],
    ],

    'phpmd' => [
        'binary' => [
            exec('which php'),
            './vendor/bin/phpmd',
        ],
        'folders' => [
            './app',
            './tests',
        ],

        'options' => [
            '{folder}',
            'text',
            './vendor/two-thirds/laravel-test-suite/config/phpmd.xml',
        ],
    ],

    'phpcs' => [
        'binary' => [
            exec('which php'),
            './vendor/bin/phpcs',
        ],
    ],

    'phpcbf' => [
        'binary' => [
            exec('which php'),
            './vendor/bin/phpcbf',
        ],
    ],
];
