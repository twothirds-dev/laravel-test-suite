<?php

namespace TwoThirds\TestSuite\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class RunTestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test
        {--s|simple : Don\'t calculate code coverage}
        {--a|all : Run all tests ( don\'t stop on errors )}
        {--o|open : Open html code coverage folder in browser on success}
        {--d|debug : Run phpunit in debug mode}
        {--f|filter= : Isolate an individual test with phpunit}
        {args? : Additional arguments that will be appended to each command}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the test suite for the project';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $command = './vendor/bin/phpunit';

        if (! $this->option('simple')) {
            $command = 'phpdbg -qrr ' . $command;
        }

        if (! $this->option('all')) {
            $command .= ' --stop-on-error --stop-on-warning --stop-on-risky --stop-on-skipped';
        }

        if ($this->option('debug')) {
            $command .= ' --debug';
        }

        if ($filter = $this->option('filter')) {
            $command .= " --filter $filter";
        }

        if ($args = $this->argument('args')) {
            $command .= ' ' . $args;
        }

        $rval = app()->makeWith(Process::class, [
            'commandline' => $command,
            'cwd'         => base_path(),
        ])
            ->setEnv($this->getCleanEnv())
            ->setTty(true)
            ->run();

        if (! $rval && file_exists('./coverage/index.html') && $this->option('open')) {
            exec('open coverage/index.html');
        }

        return $rval;
    }

    /**
     * Gets all of the environment variables and sets them to false
     *
     * @return array
     */
    protected function getCleanEnv()
    {
        return collect(getenv())
            ->transform(function () {
                return false;
            })->toArray();
    }
}
