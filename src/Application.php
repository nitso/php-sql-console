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
     * @var string[]
     */
    private $dsnParams;

    /**
     * @var string
     */
    private $dsn;

    /**
     * @return array
     */
    public function getDefaultCommands()
    {
        return array(
            new ListCommand(),
            new HelpCommand(),
            new Command\Connect(),
            new Command\Disconnect(),
            new Command\Status(),
            new Command\E(),
            new Command\Show(),
            new Command\UseDB(),
            new AuxCommand\ExitCommand(),
            new AuxCommand\EmptyCommand(),
        );
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function connect()
    {
        $this->configuration = new Configuration();
        $this->connection = DriverManager::getConnection(array('url' => $this->dsn), $this->configuration);
        $this->connection->connect();

        $this->setConnectionPrompt();
    }

    /**
     * @return void
     */
    public function setConnectionPrompt()
    {
        if (!$this->connection || !$this->connection->isConnected()) {
            $this->setPrompt($this->getName());
        }


        $this->setPrompt(sprintf(
            'Connected (%s/%s)',
            $this->connection->getHost(),
            $this->connection->getDatabase()
        ));
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

    /**
     * @return string
     */
    public function getDatabase()
    {
        return empty($this->dsnParams['path']) ? '' : ltrim('/', $this->dsnParams['path']);
    }

    /**
     * @param string $database
     * @return $this
     */
    public function setDatabase($database)
    {
        $params = $this->getDsnParams();
        $params['path'] = '/' . $database;
        $this->setDsnParams($params);

        return $this;
    }

    /**
     * @return string[]
     */
    protected function getDsnParams()
    {
        return $this->dsnParams;
    }

    /**
     * @param string $dsnParams
     * @return $this
     */
    public function setDsnParams($dsnParams)
    {
        $this->dsnParams = $dsnParams;
        $this->dsn = $this->buildUrl($this->dsnParams);
        return $this;
    }

    /**
     * @return string
     */
    protected function getDsn()
    {
        return $this->dsn;
    }

    /**
     * @param string $dsn
     * @return $this
     */
    public function setDsn($dsn)
    {
        $this->dsn = $dsn;
        $this->dsnParams = parse_url($dsn);
        return $this;
    }

    /**
     * SO-driven development https://stackoverflow.com/a/35207936/824926
     * @param array $parts
     * @return string
     */
    private function buildUrl(array $parts) {
        return
            (isset($parts['scheme']) ? "{$parts['scheme']}:" : '') .
            ((isset($parts['user']) || isset($parts['host'])) ? '//' : '') .
            (isset($parts['user']) ? "{$parts['user']}" : '') .
            (isset($parts['pass']) ? ":{$parts['pass']}" : '') .
            (isset($parts['user']) ? '@' : '') .
            (isset($parts['host']) ? "{$parts['host']}" : '') .
            (isset($parts['port']) ? ":{$parts['port']}" : '') .
            (isset($parts['path']) ? "{$parts['path']}" : '') .
            (isset($parts['query']) ? "?{$parts['query']}" : '') .
            (isset($parts['fragment']) ? "#{$parts['fragment']}" : '');
    }
}