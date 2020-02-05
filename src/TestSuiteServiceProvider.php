<?php

namespace TwoThirds\TestSuite;

use Illuminate\Support\ServiceProvider;

class TestSuiteServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The test suite commands that are available
     *
     * @var array
     */
    protected $suite = [
        'phpunit'      => Console\RunPhpUnitCommand::class,
        'dusk'         => Console\RunDuskCommand::class,
        'php-cs-fixer' => Console\RunPhpCsFixerCommand::class,
        'phpmd'        => Console\RunPhpmdCommand::class,
        'phpcs'        => Console\RunPhpCsCommand::class,
        'phpcbf'       => Console\RunPhpCbfCommand::class,
    ];

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $configPath        = __DIR__ . '/../config/test-suite.php';
        $defaultConfigPath = __DIR__ . '/../config/default.php';

        if (is_null(config('test-suite'))) {
            config(['test-suite' => require $defaultConfigPath]);
        }

        config(['test-suite-defaults' => require $defaultConfigPath]);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                $configPath => config_path('test-suite.php'),
            ], 'laravel-test-suite');

            $this->commands(Console\RunTestCommand::class);

            $enabled = config('test-suite.enabled', config('test-suite-defaults.enabled'));

            foreach (array_keys(array_filter($enabled)) as $command) {
                $this->commands($this->suite[$command]);
            }
        }
    }
}
