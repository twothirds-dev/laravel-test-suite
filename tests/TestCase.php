<?php

namespace TwoThirds\Testing;

use Orchestra\Testbench\TestCase as BaseTestCase;
use TwoThirds\TestSuite\TestSuiteServiceProvider;

class TestCase extends BaseTestCase
{
    /**
     * Get package providers.
     *
     * @return array
     */
    protected function getPackageProviders()
    {
        return [
            TestSuiteServiceProvider::class,
        ];
    }
}
