<?php

namespace Nitso\SqlConsole\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \Nitso\SqlConsole\Application getApplication()
 */
class E extends Command
{
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

        $query = join(' ', $input->getArgument('query'));

        $result = $connection->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        if ($result) {
            $table = $this->getHelper('table');
            $table
                ->setHeaders(array_keys($result[0]))
                ->setRows($result);
            $table->render($output);
        }

        return 0;
    }
}