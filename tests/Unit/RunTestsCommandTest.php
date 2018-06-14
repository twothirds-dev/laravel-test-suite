<?php

namespace TwoThirds\Testing\Unit;

use Mockery;
use phpmock\phpunit\PHPMock;
use TwoThirds\Testing\TestCase;
use Symfony\Component\Process\Process;

class RunTestsCommandTest extends TestCase
{
    use PHPMock;

    /**
     * @test
     */
    public function artisanAddsArguments()
    {
        $this->app->bind(Process::class, function ($unused, $params) {
            $this->assertStringEndsWith(
                ' foobar',
                $params['commandline']
            );

            return $this->mock;
        });

        $this->artisan('test', ['args' => 'foobar']);
    }

    /**
     * @test
     */
    public function artisanAddsDebug()
    {
        $this->app->bind(Process::class, function ($unused, $params) {
            $this->assertContains(
                ' --debug',
                $params['commandline']
            );

            return $this->mock;
        });

        $this->artisan('test', ['--debug' => true]);
    }

    /**
     * @test
     */
    public function artisanAddsFilter()
    {
        $this->app->bind(Process::class, function ($unused, $params) {
            $this->assertContains(
                ' --filter foobar',
                $params['commandline']
            );

            return $this->mock;
        });

        $this->artisan('test', ['--filter' => 'foobar']);
    }

    /**
     * @test
     */
    public function artisanOpensCoverage()
    {
        $this->getFunctionMock('TwoThirds\TestSuite\Console', 'exec')
            ->expects($this->once())
            ->with('open coverage/index.html')
            ->willReturn(true);

        $this->app->bind(Process::class, function () {
            return $this->mock;
        });

        $this->artisan('test', ['--open' => true]);
    }

    /**
     * @test
     */
    public function artisanRunsFullCommand()
    {
        $this->app->bind(Process::class, function ($unused, $params) {
            $this->assertEquals(
                'phpdbg -qrr ./vendor/bin/phpunit --stop-on-error --stop-on-warning --stop-on-risky --stop-on-skipped',
                $params['commandline']
            );

            return $this->mock;
        });

        $this->artisan('test');
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

        $this->mock = Mockery::mock(Process::class);

        $this->mock->shouldReceive('setEnv', 'setTty')
            ->andReturnSelf();

        $this->mock->shouldReceive('run')
            ->andReturn(0);
    }
}
