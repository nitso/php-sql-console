<?php

namespace Nitso\SqlConsole\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \Nitso\SqlConsole\Application getApplication()
 */
class Status extends Command
{
    protected function configure()
    {
        $this
            ->setName('status')
            ->setDescription('Report status of selected server')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getApplication()->getConnection();

        if ($connection) {
            $output->writeln('Connection established');
        }
        else {
            $output->writeln('Not connected');
        }

        return 0;
    }

}