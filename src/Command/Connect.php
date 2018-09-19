<?php

namespace Nitso\SqlConsole\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \Nitso\SqlConsole\Application getApplication()
 */
class Connect extends Command
{
    protected function configure()
    {
        $this
            ->setName('connect')
            ->setDescription('Connect an SQL server instance')
            ->addArgument(
                'dsn',
                InputArgument::REQUIRED,
                'DSN (server connect string)'
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
        $dsn = $input->getArgument('dsn');

        $this->getApplication()->connect($dsn);

        $host = $this->getApplication()->getConnection()->getHost();
        $database = $this->getApplication()->getConnection()->getDatabase();

        $this->getApplication()->setPrompt(sprintf('Connected (%s/%s)', $host, $database));

        return 0;
    }

}

