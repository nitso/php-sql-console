<?php

namespace Nitso\SqlConsole\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \Nitso\SqlConsole\Application getApplication()
 */
abstract class AbstractDatabaseCommand extends Command
{
    public function run(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getApplication()->getConnection();

        if (!$connection) {
            $output->writeln('<error>Disconnected</error>');
            return 0;
        }

        return parent::run($input, $output);
    }
}