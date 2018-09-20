<?php

namespace Nitso\SqlConsole\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @method \Nitso\SqlConsole\Application getApplication()
 */
class Connect extends Command
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('connect')
            ->setDescription('Connect an SQL server instance')
            ->addArgument(
                'dsn',
                InputArgument::OPTIONAL,
                'DSN (server connect string)'
            )
            ->addOption('type', null, InputOption::VALUE_OPTIONAL,
                'Server type: ' . join(', ', $this->getServerTypesMap()))
            ->addOption('user', null, InputOption::VALUE_OPTIONAL, 'User')
            ->addOption('password', null, InputOption::VALUE_OPTIONAL, 'Password')
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'Host or host:port pair')
            ->addOption('database', null, InputOption::VALUE_OPTIONAL, 'Database name')
        ;
    }

    /**
     * @return array
     */
    protected function getServerTypesMap()
    {
        return array(
            'db2',
            'mssql',
            'mysql',
            'pgsql',
            'sqlite',
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $dsn = $input->getArgument('dsn');
        $type = $input->getOption('type');
        $user = $input->getOption('user');
        $password = $input->getOption('password');
        $host = $input->getOption('host');
        $database = $input->getOption('database');

        if ($dsn && ($type || $user || $password || $host || $database)) {
            $io->error('Use either dsn or define connection parameters');
            return 0;
        }

        if (!$dsn && !($type && $host)) {
            $io->error('Uou have to specify either dsn or type, host and other parameters optionally');
            return 0;
        }

        if ($dsn) {
            $this->getApplication()->setDsn($dsn);
        }
        else {
            list($hostname, $port) = explode(':', $host);
            $this->getApplication()->setDsnParams(array(
                'scheme' => $type,
                'user' => $user,
                'pass' => $password,
                'host' => $hostname,
                'port' => $port,
                'path' => $database,
            ));
        }

        $this->getApplication()->connect();

        return 0;
    }

}

