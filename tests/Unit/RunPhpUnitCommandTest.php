<?php

namespace TwoThirds\Testing\Unit;

use Exception;
use phpmock\Mock;
use TwoThirds\Testing\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RunPhpUnitCommandTest extends TestCase
{
    /**
     * @test
     */
    public function phpunitRunsWithDefaultConfig()
    {
        config(['test-suite.phpunit' => []]);

        $this->commandLineWillEqual([
            trim(`which phpdbg`),
            '-qrr',
            './vendor/bin/phpunit',
            '--cache-result',
            '--order-by=defects',
            '--stop-on-defect',
        ]);

        $this->artisan('test:phpunit');
    }

    /**
     * @test
     */
    public function phpunitRunsWithBasicConfig()
    {
        config(['test-suite.phpunit' => [
            'code-coverage' => ['enabled' => false],
            'aliases'       => [],
        ]]);

        $this->commandLineWillEqual([
            './vendor/bin/phpunit',
        ]);

        $this->artisan('test:phpunit');
    }

    /**
     * @test
     */
    public function artisanPassesThroughSimpleOptions()
    {
        config(['test-suite.phpunit' => [
            'code-coverage' => ['enabled' => false],
            'aliases'       => [],
        ]]);

        $this->commandLineWillEqual([
            './vendor/bin/phpunit',
            '--foobar',
        ]);

        $this->artisan('test:phpunit', ['--foobar' => true]);
    }

    /**
     * @test
     */
    public function artisanPassesThroughArgumentOptions()
    {
        config(['test-suite.phpunit' => [
            'code-coverage' => ['enabled' => false],
            'aliases'       => [],
        ]]);

        $this->commandLineWillEqual([
            './vendor/bin/phpunit',
            '--filter=foobar',
        ]);

        $this->artisan('test:phpunit', ['--filter' => 'foobar']);
    }

    /**
     * @test
     */
    public function phpunitRunsWithPhpdbg()
    {
        config(['test-suite.phpunit' => [
            'code-coverage' => [
                'enabled' => true,
                'driver'  => 'phpdbg',
            ],
            'aliases' => [],
        ]]);

        $this->commandLineWillEqual([
            trim(`which phpdbg`),
            '-qrr',
            './vendor/bin/phpunit',
        ]);

        $this->artisan('test:phpunit');
    }

    /**
     * @test
     */
    public function phpunitAddsPhpdbgOptions()
    {
        config(['test-suite.phpunit' => [
            'code-coverage' => [
                'enabled'       => true,
                'driver'        => 'phpdbg',
                'phpdbg-config' => [
                    'options' => [
                        '--foobar',
                        '--barbaz',
                    ],
                ],
            ],
            'aliases' => [],
        ]]);

        $this->commandLineWillEqual([
            trim(`which phpdbg`),
            '-qrr',
            '--foobar',
            '--barbaz',
            './vendor/bin/phpunit',
        ]);

        $this->artisan('test:phpunit');
    }

    /**
     * @test
     */
    public function phpunitFailsWhenPhpdbgMissing()
    {
        config(['test-suite.phpunit' => [
            'code-coverage' => [
                'enabled'       => true,
                'driver'        => 'phpdbg',
                'phpdbg-config' => ['binary' => ''],
            ],
        ]]);

        try {
            $this->artisan('test:phpunit');
        } catch (Exception $exception) {
            $this->assertEquals(
                'Unable to find phpdbg. Install, set phpdbg-config.binary or use a different code coverage driver',
                $exception->getMessage()
            );

            return;
        }

        $this->fail('Failed to catch exception with phpdbg not installed');
    }

    /**
     * @test
     */
    public function phpunitRunsWithBuiltInXdebug()
    {
        config(['test-suite.phpunit' => [
            'code-coverage' => [
                'enabled' => true,
                'driver'  => 'xdebug',
            ],
            'aliases' => [],
        ]]);

        $mock = new Mock('TwoThirds\TestSuite\Console', 'exec', function ($command, &$output) {
            $command = trim(`which php`) . ' -v';
            $output = ['PHP 7.2.4', 'with Xdebug ...', 'with Some other module ...'];
        });
        $mock->enable();

        $this->commandLineWillEqual([
            trim(`which php`),
            './vendor/bin/phpunit',
        ]);

        $this->artisan('test:phpunit');

        $mock->disable();
    }

    /**
     * @test
     */
    public function phpunitRunsWithXdebugExtension()
    {
        config(['test-suite.phpunit' => [
            'code-coverage' => [
                'enabled'       => true,
                'driver'        => 'xdebug',
                'xdebug-config' => [
                    'extension' => '/usr/local/lib/php/xdebug.so',
                ],
            ],
            'aliases' => [],
        ]]);

        $mock = new Mock('TwoThirds\TestSuite\Console', 'exec', function ($command, &$output) {
            $command = trim(`which php`) . ' -dzend_extension=/usr/local/lib/php/xdebug.so -v';
            $output = ['PHP 7.2.4', 'with Xdebug ...', 'with Some other module ...'];
        });
        $mock->enable();

        $this->commandLineWillEqual([
            trim(`which php`),
            '-dzend_extension=/usr/local/lib/php/xdebug.so',
            './vendor/bin/phpunit',
        ]);

        $this->artisan('test:phpunit');

        $mock->disable();
    }

    /**
     * @test
     */
    public function phpunitAddsXdebugOptions()
    {
        config(['test-suite.phpunit' => [
            'code-coverage' => [
                'enabled'       => true,
                'driver'        => 'xdebug',
                'xdebug-config' => [
                    'options' => [
                        '--foobar',
                        '--barbaz',
                    ],
                ],
            ],
            'aliases' => [],
        ]]);

        $mock = new Mock('TwoThirds\TestSuite\Console', 'exec', function ($command, &$output) {
            $command = trim(`which php`) . ' -v';
            $output = ['PHP 7.2.4', 'with Xdebug ...', 'with Some other module ...'];
        });
        $mock->enable();

        $this->commandLineWillEqual([
            trim(`which php`),
            '--foobar',
            '--barbaz',
            './vendor/bin/phpunit',
        ]);

        $this->artisan('test:phpunit');

        $mock->disable();
    }

    /**
     * @test
     */
    public function phpunitFailsWhenXdebugMissing()
    {
        config(['test-suite.phpunit' => [
            'code-coverage' => [
                'enabled' => true,
                'driver'  => 'xdebug',
            ],
        ]]);

        $mock = new Mock('TwoThirds\TestSuite\Console', 'exec', function ($command, &$output) {
            $command = trim(`which php`) . ' -v';
            $output = ['PHP 7.2.4', 'with Some other module ...'];
        });
        $mock->enable();

        try {
            $this->artisan('test:phpunit');
        } catch (Exception $exception) {
            $this->assertEquals(
                'Php does not seem to have proper xdebug support. ' .
                    'Try setting xdebug-extension or use a different code coverage driver',
                $exception->getMessage()
            );

            return;
        } finally {
            $mock->disable();
        }

        $this->fail('Failed to catch exception with missing xdebug support');
    }

    /**
     * @test
     */
    public function phpunitFailsWithUnknownDriver()
    {
        config(['test-suite.phpunit' => [
            'code-coverage' => [
                'enabled' => true,
                'driver'  => 'unknown',
            ],
        ]]);

        try {
            $this->artisan('test:phpunit');
        } catch (Exception $exception) {
            $this->assertEquals('Unknown code-coverage driver: unknown.', $exception->getMessage());

            return;
        }

        $this->fail('Failed to catch exception from unknown driver');
    }

    /**
     * @test
     */
    public function phpunitOpensCoverage()
    {
        config(['test-suite.phpunit' => [
            'code-coverage' => [
                'enabled' => true,
                'driver'  => 'phpdbg',
            ],
        ]]);

        $this->mocksFunctions('exec', [
            ['open coverage/index.html', true],
        ]);

        $this->artisan('test:phpunit', ['--open' => true]);
    }

    /**
     * @test
     */
    public function phpunitDisablesCodeCoverageInSimpleMode()
    {
        config(['test-suite.phpunit' => [
            'code-coverage' => [
                'enabled' => true,
                'driver'  => 'phpdbg',
            ],
        ]]);

        // we're not really doing any assertions intentionally. If this didn't work, exec would be bitching

        $this->artisan('test:phpunit', ['--simple' => true]);
    }
}
