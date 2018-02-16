<?php

namespace Nitso\SqlConsole\Command;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\PDOException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \Nitso\SqlConsole\Application getApplication()
 */
class E extends Command
{
    /**
     * @var int
     */
    protected $verbosity;

    /**
     * @var bool
     */
    protected $interactive;

    protected function configure()
    {
        $this
            ->setName('e')
            ->setDescription('Execute raw query')
            ->addArgument(
                'query',
                InputArgument::IS_ARRAY,
                'Query to execute'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getApplication()->getConnection();

        if (!$connection) {
            $output->writeln('<error>Disconnected</error>');
            return 0;
        }

        $queryArguments = $input->getArgument('query');
        if (count($queryArguments) < 1) {
            $output->writeln('<comment>Empty query</comment>');
            return 0;
        }

        $isSelectQuery = strcasecmp($queryArguments[0], 'select');
        $result = null;

        try {
            $result = $connection->query(join(' ', $queryArguments))->fetchAll(\PDO::FETCH_ASSOC);
        } catch (DBALException $e) {
            if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                throw $e;
            }
            else {
                if ($e->getPrevious() instanceof PDOException) {
                    $exception = $e->getPrevious();
                }
                else {
                    $exception = $e;
                }

                $output->writeln('<error>' . $exception->getMessage() . '</error>');
            }
        }

        if ($result) {
            $table = new Table($output);
            $table
                ->setHeaders(array_keys($result[0]))
                ->setRows($result);
            $table->render();
        }
        elseif ($isSelectQuery) {
            $output->writeln('<comment>Empty result</comment>');
        }

        return 0;
    }
}