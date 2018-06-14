<?php

namespace TwoThirds\Testing;

use Orchestra\Testbench\TestCase as BaseTestCase;
use TwoThirds\TestSuite\TestSuiteServiceProvider;

class TestCase extends BaseTestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            TestSuiteServiceProvider::class,
        ];
    }
}
