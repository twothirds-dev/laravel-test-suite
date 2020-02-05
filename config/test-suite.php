<?php

return [
    'enabled' => [
        'phpunit'      => true,
        'dusk'         => class_exists('Laravel\Dusk\Dusk'),
        'php-cs-fixer' => true,
        'phpmd'        => true,
        'phpcs'        => false,
    ],

    'phpunit' => [
        'timeout'       => 120,
        'code-coverage' => [
            'enabled'       => true,
            'driver'        => 'phpdbg',
            'phpdbg-config' => [
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
        'options' => [],
    ],

    'phpcbf' => [
        'options' => [],
    ],
];
