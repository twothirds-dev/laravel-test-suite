<?php

namespace TwoThirds\Testing\Unit;

use TwoThirds\Testing\TestCase;

class RunPhpCsCommandTest extends TestCase
{
    /**
     * @test
     */
    public function phpcsfRunsWithDefaultConfig()
    {
        config(['test-suite.phpcs' => [
            'folders' => ['./app'],
        ]]);

        $this->commandLineWillEqual([
            exec('which php'),
            './vendor/bin/phpcs',
        ]);

        $this->artisan('test:phpcs');
    }
}
