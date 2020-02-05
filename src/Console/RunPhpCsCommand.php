<?php

namespace TwoThirds\TestSuite\Console;

use Illuminate\Console\Command;

class RunPhpCsCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:phpcs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run PHP Codesniffer for the project.';

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
