<?php

namespace TwoThirds\TestSuite\Console;

use Exception;
use Illuminate\Console\Command;

class RunPhpmdCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:phpmd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run phpmd for the project.';

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
            throw new Exception('You must define a folders array in the config with at least one element');
        }

        return $this->foreachFolder($command, function ($command, $folder) {
            $this->line("Running for $folder");

            return $this->runCommand($command);
        });
    }
}
