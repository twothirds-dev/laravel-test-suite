<?php

namespace TwoThirds\TestSuite\Console;

use Illuminate\Console\Command;

class RunDuskCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:dusk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run dusk for the project';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->announceTest();

        $command = array_merge(
            $this->getBaseDuskCommand(),
            $this->parseConfigOptions(),
            $this->unhandledOptions()
        );

        return $this->runCommand($command);
    }

    /**
     * Gets the base command for executing dusk from the command line
     *
     * @return array
     */
    protected function getBaseDuskCommand()
    {
        return [
            trim(exec('which php')),
            'artisan',
            'dusk',
        ];
    }
}
