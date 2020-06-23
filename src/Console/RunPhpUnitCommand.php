<?php

namespace TwoThirds\TestSuite\Console;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class RunPhpUnitCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:phpunit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run phpunit for the project.';

    /**
     * Instantiate a new console command
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        if ($this->config('code-coverage.enabled', false)) {
            $this->addOption('simple', 's', null, 'Don\'t calculate code coverage');
            $this->addOption('open', 'o', null, 'Open html code coverage folder in browser on success');
        }
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
            $this->wrapCodeCoverage('./vendor/bin/phpunit'),
            $this->parseConfigOptions(),
            $this->unhandledOptions()
        );

        if (! ($rval = $this->runTestCommand($command, [], $this->output)) &&
            $this->calculateCodeCoverage() &&
            $this->option('open')
        ) {
            exec('open coverage/index.html');
        }

        return $rval;
    }

    /**
     * Wraps the command accordingly based on the code coverage driver selected
     *
     * @param string $command
     *
     * @return array
     */
    protected function wrapCodeCoverage(string $command)
    {
        if (! $this->calculateCodeCoverage()) {
            return [$command];
        }

        $driver = $this->config('code-coverage.driver');

        if (method_exists($this, $method = 'configure' . ucfirst($driver) . 'Coverage')) {
            return $this->$method($command);
        }

        throw new Exception("Unknown code-coverage driver: $driver.");
    }

    /**
     * Configures code coverage for phpdbg
     *
     * @param string $command
     *
     * @return array
     */
    protected function configurePhpdbgCoverage(string $command)
    {
        if (! $binary = $this->config('code-coverage.phpdbg-config.binary')) {
            throw new Exception('Unable to find phpdbg. ' .
                'Install, set phpdbg-config.binary or use a different code coverage driver');
        }

        return array_merge(
            [$binary, '-qrr'],
            $this->config('code-coverage.phpdbg-config.options', []),
            [$command]
        );
    }

    /**
     * Configures code coverage for xdebug
     *
     * @param string $command
     *
     * @return array
     */
    protected function configureXdebugCoverage(string $command)
    {
        $binary = $this->baseConfig('php-binary');

        if ($extension = $this->config('code-coverage.xdebug-config.extension')) {
            $binary .= ' -dzend_extension=' . $extension;
        }

        exec("$binary -v", $info);

        if (! Str::contains(implode(' ', $info), 'with Xdebug')) {
            throw new Exception('Php does not seem to have proper xdebug support. ' .
                'Try setting xdebug-extension or use a different code coverage driver');
        }

        return  array_merge(
            explode(' ', $binary),
            $this->config('code-coverage.xdebug-config.options', []),
            [$command]
        );
    }

    /**
     * Do we need to calcaulte code coverage?
     *
     * @return bool
     */
    protected function calculateCodeCoverage()
    {
        return $this->config('code-coverage.enabled', false) && ! $this->option('simple');
    }
}
