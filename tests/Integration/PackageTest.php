<?php

namespace TwoThirds\Testing\Integration;

use TwoThirds\Testing\TestCase;
use Illuminate\Contracts\Console\Kernel;
use TwoThirds\TestSuite\Console\RunDuskCommand;
use TwoThirds\TestSuite\Console\RunTestCommand;
use TwoThirds\TestSuite\Console\RunPhpmdCommand;
use TwoThirds\TestSuite\Console\RunPhpUnitCommand;
use TwoThirds\TestSuite\Console\RunPhpCsFixerCommand;

class PackageTest extends TestCase
{
    /**
     * @test
     */
    public function serviceProviderLoadsCommands()
    {
        $commands = $this->app[Kernel::class]->all();

        $registered = [
            'test'              => RunTestCommand::class,
            'test:phpunit'      => RunPhpUnitCommand::class,
            'test:php-cs-fixer' => RunPhpCsFixerCommand::class,
            'test:phpmd'        => RunPhpmdCommand::class,
            'test:dusk'         => RunDuskCommand::class,
        ];

        foreach ($registered as $name => $class) {
            $this->assertContains($name, array_keys($commands));
            $this->assertInstanceOf($class, $commands[$name]);
        }
    }
}
