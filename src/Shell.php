<?php

namespace Nitso\SqlConsole;

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;

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

    public function __construct(Application $application)
    {
        $this->application = $application;
        $this->output = new ConsoleOutput();
    }

    /**
     * Starts shell
     */
    public function run()
    {
        $this->application->setAutoExit(false);
        $this->application->setCatchExceptions(true);

        $this->output->writeln($this->getHeader());
        $php = null;

        while (true) {
            $command = $this->readline();

            if (false === $command) {
                $this->output->writeln("\n");

                break;
            }

            $ret = $this->application->run(new StringInput($command), $this->output);

            if (0 !== $ret) {
                $this->output->writeln(sprintf('<error>The command terminated with an error status (%s)</error>', $ret));
            }
        }
    }

    /**
     * @return string
     */
    protected function readline() {
        $this->output->write($this->getPrompt());
        $line = fgets(STDIN, 1024);
        $line = (false === $line || '' === $line) ? false : rtrim($line);

        return $line;
    }

    /**
     * @return string
     */
    protected function getPrompt()
    {
        // using the formatter here is required when using readline
        return $this->output->getFormatter()->format($this->application->getPrompt().' > ');
    }

    /**
     * Shell header.
     * @return string
     */
    protected function getHeader()
    {
        return <<<EOF
Welcome to the <info>{$this->application->getName()}</info> shell.
Type <comment>help</comment> for some help, or <comment>list</comment> to get a list of available commands.
To exit the shell, hit <comment>^C</comment>.
EOF;
    }
}