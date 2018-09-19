<?php

namespace Nitso\SqlConsole;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Nitso\SqlConsole\Command;
use Nitso\SqlConsole\Command\Auxiliary as AuxCommand;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends ConsoleApplication
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var string
     */
    private $prompt;

    /**
     * @return array
     */
    public function getDefaultCommands()
    {
        return array(
            new ListCommand(),
            new HelpCommand(),
            new Command\Connect(),
            new Command\Status(),
            new Command\E(),
            new Command\Show(),
            new AuxCommand\ExitCommand(),
            new AuxCommand\EmptyCommand(),
        );
    }

    /**
     * @param string $dsn
     * @throws \Doctrine\DBAL\DBALException
     */
    public function connect($dsn)
    {
        $this->configuration = new Configuration();
        $this->connection = DriverManager::getConnection(array('url' => $dsn), $this->configuration);
        $this->connection->connect();
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return string
     */
    public function getPrompt()
    {
        return $this->prompt ?: $this->getName();
    }

    /**
     * @param string $prompt
     * @return $this
     */
    public function setPrompt($prompt)
    {
        $this->prompt = $prompt;
        return $this;
    }

    /**
     * Stupid hack just to reuse IO configuration code. Sorry for that.
     *
     * @see Shell::run
     * @inheritdoc
     */
    public function preConfigureIO(InputInterface $input, OutputInterface $output)
    {
        parent::configureIO($input, $output);
    }
}