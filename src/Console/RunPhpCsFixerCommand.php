<?php

namespace TwoThirds\TestSuite\Console;

use Illuminate\Console\Command;

class RunPhpCsFixerCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:php-cs-fixer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run php-cs-fixer for the project.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->announceTest();

        $command = array_merge(
            (array) $this->config('binary'),
            $this->parseConfigOptions(),
            $this->unhandledOptions()
        );

        if (! $this->config('folders')) {
            return $this->runTestCommand($command);
        }

        return $this->foreachFolder($command, function ($command, $folder) {
            $this->line("Running for $folder");

            return $this->runTestCommand($command);
        });
    }
}
