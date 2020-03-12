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
     * Instantiate a new console command
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->addOption('show', 's', null, 'Show the Chrome browser during tests ( disable headless mode )');
    }

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

        if ($this->option('show')) {
            $envvars = $this->input->getOption('envvar');
            $envvars[] = 'DUSK_DISABLE_HEADLESS=true';
            $this->input->setOption('envvar', $envvars);
        }

        return $this->runTestCommand($command);
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
