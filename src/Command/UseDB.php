<?php

namespace Nitso\SqlConsole\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \Nitso\SqlConsole\Application getApplication()
 */
class UseDB extends Command
{
    protected function configure()
    {
        $this
            ->setName('use')
            ->setDescription('Select active database')
            ->setHelp('Closes active connection, changes database name in dsn and reconnects')
            ->addArgument(
                'database',
                InputArgument::REQUIRED,
                'Database name'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $database = $input->getArgument('database');

        if (!$this->getApplication()->getConnection() || !$this->getApplication()->getConnection()->isConnected()) {
            $output->writeln('<comment>Can not change database while not connected. Use `connect` instead</comment>');
            return 0;
        }

        $this->getApplication()->getConnection()->close();
        $this->getApplication()->setDatabase($database);
        $this->getApplication()->connect();
        return 0;
    }
}

