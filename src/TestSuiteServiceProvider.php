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
     * Provides the artisan commands
     *
     * @var array
     */
    protected $commands = [
        \TwoThirds\TestSuite\Console\RunTestsCommand::class,
    ];

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
}
