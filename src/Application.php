<?php

namespace Nitso\SqlConsole;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Nitso\SqlConsole\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\ListCommand;

class Application extends \Symfony\Component\Console\Application
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
}