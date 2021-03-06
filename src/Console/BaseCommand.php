<?php

namespace TwoThirds\TestSuite\Console;

use Closure;
use Exception;
use ReflectionClass;
use ReflectionObject;
use ReflectionException;
use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
abstract class BaseCommand extends Command
{
    /**
     * Instantiate a new console command
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        if (count($this->getDefinition()->getArguments())) {
            throw new Exception('You can\'t define additional arguments, only options.');
        }

        $this->ignoreValidationErrors();

        $this->addOption('without-tty', null, null, 'Disable output to TTY');
        $this->addOption('no-clean-env', null, null, 'Do not sanitize the environment first');
        $this->addOption('envvar', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Environment variables to use pass through');

        $this->addAliases();
    }

    /**
     * Parse all of the command line options and remove any known options that symphony already handles
     *
     * @return array
     */
    protected function unhandledOptions()
    {
        $rawInput = $this->getRawInput();

        $options = $this->expandShortOptions(array_slice($rawInput, 1));

        return $this->removeKnownOptions($options);
    }

    /**
     * Gets the raw input from Input, supporting either ArgvInput or ArrayInput
     *
     * @return array
     */
    protected function getRawInput()
    {
        switch (get_class($this->input)) {
            case ArrayInput::class:
                return $this->normalizeArrayInputParameters(
                    $this->getInputProperty('parameters')
                );

            case ArgvInput::class:
            case StringInput::class:
                return $this->getInputProperty('tokens');

            default:
                throw new Exception('Unable to handle input type of ' . get_class($this->input));
        }
    }

    /**
     * Get a protected property off the current input
     *
     * @param string $name
     *
     * @return array
     */
    protected function getInputProperty(string $name)
    {
        $reflection = new ReflectionObject($this->input);

        try {
            $property = $reflection->getProperty($name);
        } catch (ReflectionException $exception) {
            if (! $parent = $reflection->getParentClass()) {
                return [];
            }
            $property = $parent->getProperty($name);
        }

        $property->setAccessible(true);

        return $property->getValue($this->input);
    }

    /**
     * Normalized ArrayInput parameters
     *
     * @param array $options
     *
     * @return array
     */
    protected function normalizeArrayInputParameters(array $options)
    {
        foreach ($options as $key => $value) {
            if (is_integer($key)) {
                continue;
            }

            unset($options[$key]);

            if ($value === true) {
                $options[] = $key;

                continue;
            }

            $options[] = "$key=$value";
        }

        return $options;
    }

    /**
     * Parses and applies options from the config file
     *
     * @return array
     */
    protected function parseConfigOptions()
    {
        $options = $this->config('options', []);

        foreach ($this->config('aliases', []) as $alias => $config) {
            $options = array_merge(
                $options,
                $this->option($alias) ?
                    ($config['options-enabled'] ?? []) :
                    ($config['options-disabled'] ?? [])
            );
        }

        return array_unique($options);
    }

    /**
     * Remove all of the known options from the list so they can be passed along to the command
     *
     * @param array $options
     *
     * @return array
     */
    protected function removeKnownOptions(array $options)
    {
        foreach ($this->options() as $name => $value) {
            for ($index = 0; $index < count($options); $index++) {
                if (is_array($value)) {
                    foreach ($value as $subvalue) {
                        $this->removeFromOptionsArray($options, $index, $name, $subvalue);
                    }

                    continue 2;
                }

                $this->removeFromOptionsArray($options, $index, $name, $value);
            }
        }

        return $options;
    }

    /**
     * Remove the provided name and value from the options array
     *
     * @param array &$options
     * @param int $index
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    protected function removeFromOptionsArray(&$options, int $index, string $name, string $value = null)
    {
        if ($options[$index] === "--$name=$value") {
            array_splice($options, $index, 1);

            return;
        }

        if ($options[$index] === "--$name") {
            if (isset($options[$index + 1]) && $options[$index + 1] === $value) {
                array_splice($options, $index, 2);

                return;
            }

            array_splice($options, $index, 1);

            return;
        }
    }

    /**
     * Takes any known short option, expands it and throws it at the beginning.
     *     Any unhandled short options are appended to the list.
     *
     * @param array $options
     *
     * @return array
     */
    protected function expandShortOptions(array $options)
    {
        $unhandledShorts = [];

        for ($index = 0; $index < count($options); $index++) {
            if (preg_match('/^-([a-z]+)$/i', $options[$index], $matches)) {
                array_splice($options, $index, 1);
                foreach (str_split($matches[1]) as $short) {
                    if ($this->getDefinition()->hasShortcut($short)) {
                        $option = $this->getDefinition()->getOptionForShortcut($short);
                        array_unshift($options, '--' . $option->getName());

                        continue;
                    }

                    $unhandledShorts[] = $short;
                }
            }
        }

        foreach ($unhandledShorts as $short) {
            $options[] = "-$short";
        }

        return $options;
    }

    /**
     * Gets the requested base config
     *
     * @param string $config
     * @param mixed|null $default
     *
     * @return mixed
     */
    protected function baseConfig(string $config, $default = null)
    {
        return config('test-suite.' . $config) ?? config('test-suite-defaults.' . $config, $default);
    }

    /**
     * Gets the requested config
     *
     * @param string $config
     * @param mixed|null $default
     *
     * @return mixed
     */
    protected function config(string $config, $default = null)
    {
        return $this->baseConfig($this->name() . '.' . $config, $default);
    }

    /**
     * Get the current name name
     *
     * @return string
     */
    protected function name()
    {
        $command = Arr::first(preg_split('/[\n\r\s]+/', $this->signature, 2));

        return str_replace('test:', '', $command);
    }

    /**
     * Announce the current test name
     *
     * @return $this
     */
    protected function announceTest()
    {
        return $this->infoBlock('Running ' . $this->name());
    }

    /**
     * Print out a block containing a string
     *
     * @param string $string
     *
     * @return $this
     */
    protected function infoBlock(string $string)
    {
        $this->line('');
        $this->info(str_repeat('-', strlen($string) + 4));
        $this->info('| ' . $string . ' |');
        $this->info(str_repeat('-', strlen($string) + 4));

        return $this;
    }

    /**
     * Gets all of the environment variables and sets them to false
     *
     * @param array $vars
     *
     * @return array
     */
    protected function getCleanEnv(array $vars)
    {
        $vars = collect($vars)->mapWithKeys(function ($var) {
            $elements = explode('=', $var, 2);

            return [$elements[0] => $elements[1]];
        });

        return collect(getenv())
            ->transform(function ($value, $name) {
                return $name === 'PATH' ? $value : false;
            })
            ->merge($vars)
            ->toArray();
    }

    /**
     * Print a message out when in verbose mode
     *
     * @param string $string
     *
     * @return void
     */
    protected function verbose(string $string)
    {
        $this->info($string, OutputInterface::VERBOSITY_VERBOSE);
    }

    /**
     * Create a new process and execute the provided commands
     *
     * @param string|array $commands
     *
     * @return int
     */
    protected function runTestCommand($commands)
    {
        $commands = (array) $commands;

        $this->verbose(implode(' ', $commands));

        $processParams = (new ReflectionClass(Process::class))
            ->getConstructor()
            ->getParameters();

        $process = app()->makeWith(Process::class, [
            $processParams[0]->name => $commands,
            'cwd'                   => base_path(),
            'timeout'               => $this->config('timeout', 60),
        ]);

        if (! $this->option('no-clean-env')) {
            $process->setEnv($this->getCleanEnv($this->option('envvar')));
        }

        if (! $this->option('without-tty')) {
            return $process
                ->setTty(true)
                ->run();
        }

        return $process->run(function ($type, $buffer) {
            if ($type === Process::ERR) {
                return $this->output->write("<error>$buffer</error>");
            }

            return $this->output->write($buffer);
        });
    }

    /**
     * Execute an artisan command directly
     *
     * @param mixed $command
     * @param array $parameters
     *
     * @return int
     */
    protected function runArtisan($command, array $parameters = [])
    {
        $this->verbose(PHP_BINARY . " artisan $command " . implode(' ', $parameters));

        return $this->getApplication()->call($command, $parameters, $this->output);
    }

    /**
     * Parses the aliass from the config
     *
     * @return  void
     */
    protected function addAliases()
    {
        foreach ($this->config('aliases', []) as $alias => $config) {
            $this->addOption(
                $alias,
                $config['short'] ?? null,
                $config['mode'] ?? null,
                $config['description'] ?? ''
            );
        }
    }

    /**
     * Finds any instance of $find in an array and replaces it with $replace
     *
     * @param array $array
     * @param string $find
     * @param string $replace
     *
     * @return array
     */
    protected function arrayFindReplace(array $array, string $find, string $replace)
    {
        foreach ($array as $index => $value) {
            if ($value === $find) {
                $array[$index] = $replace;
            }
        }

        return $array;
    }

    /**
     * Iterate over each folder, calling the closure
     *
     * @param array $command
     * @param Closure $closure
     *
     * @return int
     */
    protected function foreachFolder(array $command, Closure $closure)
    {
        foreach ($this->config('folders', ['']) as $folder) {
            $rval = $closure(
                $this->arrayFindReplace($command, '{folder}', $folder),
                $folder
            );

            if ($rval !== 0) {
                return $rval;
            }
        }

        return $rval;
    }
}
