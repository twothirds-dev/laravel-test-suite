<?php

namespace TwoThirds\Testing;

use Mockery;
use phpmock\Mock;
use phpmock\phpunit\PHPMock;
use Symfony\Component\Process\Process;
use Orchestra\Testbench\TestCase as BaseTestCase;
use TwoThirds\TestSuite\TestSuiteServiceProvider;

class TestCase extends BaseTestCase
{
    use PHPMock;

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getPackageProviders($app)
    {
        return [
            TestSuiteServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application   $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app->setBasePath(__DIR__ . '/../..');

        $app->bind(Process::class, function () {
            return $this->createProcessMock();
        });

        config(['test-suite.enabled' => [
            'phpunit'      => true,
            'php-cs-fixer' => true,
            'phpmd'        => true,
            'dusk'         => true,
        ]]);
    }

    /**
     * Creates a process mock
     *
     * @return \Mockery\MockInterface
     */
    protected function createProcessMock()
    {
        $this->mock = Mockery::mock(Process::class);

        $this->mock->shouldReceive('setEnv', 'setTty')
            ->andReturnSelf();

        $this->mock->shouldReceive('run')
            ->andReturn(0);

        return $this->mock;
    }

    /**
     * Asserts that the command line equals the provided command when executed
     *
     * @param array $command
     *
     * @return void
     */
    protected function commandLineWillEqual(array $command)
    {
        $this->app->bind(Process::class, function ($unused, $params) use ($command) {
            $this->assertEquals($command, $params['command']);

            return $this->createProcessMock();
        });
    }

    /**
     * Mocks one or many calls to the provided function
     *
     * @param string $function
     * @param array $calls
     *
     * @return void
     */
    protected function mocksFunctions(string $function, array $calls)
    {
        $mock = $this->getFunctionMock('TwoThirds\TestSuite\Console', $function);

        foreach ($calls as $index => list($arguments, $return)) {
            $arguments = (array) $arguments;

            $mock->expects($this->at($index))
                ->with(...$arguments)
                ->willReturn($return);
        }
    }
}
