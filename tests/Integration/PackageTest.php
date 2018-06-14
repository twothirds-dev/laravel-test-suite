<?php

namespace TwoThirds\Testing\Integration;

use TwoThirds\Testing\TestCase;
use Illuminate\Contracts\Console\Kernel;
use TwoThirds\TestSuite\Console\RunTestsCommand;

class PackageTest extends TestCase
{
    /**
     * @test
     */
    public function serviceProviderLoadsRunTestsCommand()
    {
        $commands = $this->app[Kernel::class]->all();

        $this->assertContains('test', array_keys($commands));
        $this->assertInstanceOf(RunTestsCommand::class, $commands['test']);
    }
}
