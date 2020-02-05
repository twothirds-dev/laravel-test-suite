<?php

namespace TwoThirds\TestSuite\Console;

use Illuminate\Console\Command;

class RunPhpCbfCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:phpcbf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run PHP Code Beautifier and Fixer for the project.';

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

        return $this->runTestCommand($command);
    }
}
