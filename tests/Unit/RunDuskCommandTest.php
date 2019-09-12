<?php

namespace TwoThirds\Testing\Unit;

use TwoThirds\Testing\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RunDuskCommandTest extends TestCase
{
    /**
     * @test
     */
    public function duskRunsWithEmptyConfig()
    {
        config(['test-suite.dusk' => []]);

        $this->commandLineWillEqual([
            trim(`which php`),
            'artisan',
            'dusk',
        ]);

        $this->artisan('test:dusk');
    }

    /**
     * @test
     */
    public function duskPassesThroughSimpleOptions()
    {
        config(['test-suite.dusk' => []]);

        $this->commandLineWillEqual([
            trim(`which php`),
            'artisan',
            'dusk',
            '--foobar',
        ]);

        $this->artisan('test:dusk', ['--foobar' => true]);
    }

    /**
     * @test
     */
    public function duskPassesThroughSpecifiedOptions()
    {
        config(['test-suite.dusk' => []]);

        $this->commandLineWillEqual([
            trim(`which php`),
            'artisan',
            'dusk',
            '--foobar=barbaz',
        ]);

        $this->artisan('test:dusk', ['--foobar=barbaz']);
    }
}
