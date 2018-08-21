<?php

namespace TwoThirds\TestSuite\Console;

use Illuminate\Console\Command;

class RunTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test
        {--a|all : Run all test suites}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all of the enabled test suites for the project';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach ($this->enabledSuites() as $suite) {
            $rval = $this->getApplication()->call('test:' . $suite, [], $this->output);

            if ($rval !== 0) {
                $this->error("Test Suite: $suite exited with non-zero return value [$rval]");

                break;
            }
        }
    }

    /**
     * Gets all of the enabled test suites
     *
     * @return array
     */
    protected function enabledSuites()
    {
        return array_keys(array_filter(config('test-suite.enabled')));
    }
}
