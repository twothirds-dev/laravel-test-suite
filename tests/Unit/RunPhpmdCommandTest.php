<?php

namespace TwoThirds\Testing\Unit;

use Exception;
use TwoThirds\Testing\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RunPhpmdCommandTest extends TestCase
{
    /**
     * @test
     */
    public function phpmdRunsWithDefaultConfig()
    {
        config(['test-suite.phpmd' => [
            'folders' => ['./app'],
        ]]);

        $this->commandLineWillEqual([
            exec('which php'),
            './vendor/bin/phpmd',
            './app',
            'text',
            './vendor/two-thirds/laravel-test-suite/config/phpmd.xml',
        ]);

        $this->artisan('test:phpmd');
    }

    /**
     * @test
     */
    public function phpmdRunsWithFolders()
    {
        config(['test-suite.phpmd' => [
            'folders' => ['./foobar'],
            'options' => [
                '{folder}',
                'text',
                './vendor/two-thirds/laravel-test-suite/config/phpmd.xml',
            ],
        ]]);

        $this->commandLineWillEqual([
            exec('which php'),
            './vendor/bin/phpmd',
            './foobar',
            'text',
            './vendor/two-thirds/laravel-test-suite/config/phpmd.xml',
        ]);

        $this->artisan('test:phpmd');
    }

    /**
     * @test
     */
    public function phpmdThrowsExceptionWithoutFolders()
    {
        config(['test-suite.phpmd' => [
            'folders' => false,
        ]]);

        try {
            $this->artisan('test:phpmd');
        } catch (Exception $exception) {
            $this->assertEquals(
                'You must define a folders array in the config with at least one element',
                $exception->getMessage()
            );

            return;
        }

        $this->fail('Failed to catch exception when phpmd doesn\'t have folder defined.');
    }
}
