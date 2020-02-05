<?php

namespace TwoThirds\Testing\Unit;

use TwoThirds\Testing\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RunPhpCbfCommandTest extends TestCase
{
    /**
     * @test
     */
    public function phpcbfRunsWithDefaultConfig()
    {
        config(['test-suite.phpcbf' => []]);

        $this->commandLineWillEqual([
            trim(`which php`),
            './vendor/bin/phpcbf',
        ]);

        $this->artisan('test:phpcbf');
    }
}
