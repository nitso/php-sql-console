<?php

namespace Nitso\SqlConsole;

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * A modified version of Symfony\Component\Console\Shell v2.8
 * https://github.com/symfony/symfony/blob/2.8/src/Symfony/Component/Console/Shell.php
 */
class Shell
{
    /**
     * @var Application
     */
    protected $application;

    /**
     * @var ConsoleOutput
     */
    protected $output;

    /**
     * @var string
     */
    private $history;

    /**
     * @var bool
     */
    private $hasReadline;

    /**
     * Shell constructor.
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
        $this->output = new ConsoleOutput();
        $this->hasReadline = function_exists('readline');
        $this->history = getenv('HOME') . '/.history_' . $application->getName();
    }

    /**
     * Runs the shell.
     */
    public function run()
    {
        $this->application->setAutoExit(false);
        $this->application->setCatchExceptions(true);

        if ($this->hasReadline) {
            readline_read_history($this->history);
            readline_completion_function(array($this, 'autocompleter'));
        }
        $this->output->writeln($this->getHeader());
        $php = null;

        while (true) {
            $command = $this->readline();

            if (false === $command) {
                $this->output->writeln("\n");

                break;
            }

            if ($this->hasReadline) {
                readline_add_history($command);
                readline_write_history($this->history);
            }

            $ret = $this->application->run(new StringInput($command), $this->output);

            if (0 !== $ret) {
                $this->output->writeln(sprintf('<error>The command terminated with an error status (%s)</error>', $ret));
            }
        }
    }


    /**
     * Reads a single line from standard input.
     *
     * @return string The single line from standard input
     */
    private function readline()
    {
        if ($this->hasReadline) {
            $line = readline($this->getPrompt());
        } else {
            $this->output->write($this->getPrompt());
            $line = fgets(STDIN, 1024);
            $line = (false === $line || '' === $line) ? false : rtrim($line);
        }
        return $line;
    }

    /**
     * Renders a prompt.
     *
     * @return string
     */
    protected function getPrompt()
    {
        // using the formatter here is required when using readline
        return $this->output->getFormatter()->format($this->application->getPrompt().' > ');
    }

    /**
     * @return ConsoleOutput
     */
    protected function getOutput()
    {
        return $this->output;
    }

    /**
     * @return Application
     */
    protected function getApplication()
    {
        return $this->application;
    }

    /**
     * Shell header.
     * @return string
     */
    protected function getHeader()
    {
        return <<<EOF
Welcome to the <info>{$this->application->getName()}</info> shell.
Type <comment>help</comment> for some help, <comment>list</comment> to get a list of available commands, <comment>exit</comment> to exit the shell.

EOF;
    }

    /**
     * Tries to return autocompletion for the current entered text.
     *
     * @param string $text The last segment of the entered text
     *
     * @return bool|array A list of guessed strings or true
     */
    private function autocompleter($text)
    {
        $info = readline_info();

        // try to overcome some buggy windows libraries
        if (!isset($info['end']) && PHP_OS == 'WINNT') {
            $info['end'] = $info['point'];
        }

        $text = substr($info['line_buffer'], 0, $info['end']);
        if ($info['point'] !== $info['end']) {
            return true;
        }
        // task name?
        if (false === strpos($text, ' ') || !$text) {
            return array_keys($this->application->all());
        }
        // options and arguments?
        try {
            $command = $this->application->find(substr($text, 0, strpos($text, ' ')));
        } catch (\Exception $e) {
            return true;
        }
        $list = array('--help');
        foreach ($command->getDefinition()->getOptions() as $option) {
            $list[] = '--' . $option->getName();
        }
        return $list;
    }
}