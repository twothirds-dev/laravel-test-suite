<?php

namespace TwoThirds\Testing\Unit;

use TwoThirds\Testing\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RunPhpCsFixerCommandTest extends TestCase
{
    /**
     * @test
     */
    public function phpcsfixerRunsWithDefaultConfig()
    {
        config(['test-suite.php-cs-fixer' => [
            'folders' => ['./app'],
        ]]);

        $this->commandLineWillEqual([
            exec('which php'),
            './vendor/bin/php-cs-fixer',
            'fix',
            './app',
            '--using-cache=false',
            '--verbose',
            '--diff',
            '--diff-format=udiff',
            '--dry-run',
        ]);

        $this->artisan('test:php-cs-fixer');
    }

    /**
     * @test
     */
    public function phpcsfixerRunsWithFolders()
    {
        config(['test-suite.php-cs-fixer' => [
            'folders' => ['./foobar'],
            'options' => [
                'fix',
                '{folder}',
            ],
        ]]);

        $this->commandLineWillEqual([
            exec('which php'),
            './vendor/bin/php-cs-fixer',
            'fix',
            './foobar',
            '--dry-run',
        ]);

        $this->artisan('test:php-cs-fixer');
    }

    /**
     * @test
     */
    public function phpcsfixerRunsWithoutFolders()
    {
        config(['test-suite.php-cs-fixer' => [
            'folders' => false,
            'options' => [
                'fix',
                './app',
            ],
        ]]);

        $this->commandLineWillEqual([
            exec('which php'),
            './vendor/bin/php-cs-fixer',
            'fix',
            './app',
            '--dry-run',
        ]);

        $this->artisan('test:php-cs-fixer');
    }
}
