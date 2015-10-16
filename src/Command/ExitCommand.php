<?php

namespace Nitso\SqlConsole\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExitCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('exit')
            ->setAliases(array('quit'))
            ->setDescription('Exit application')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Bye-bye!');
        exit(0);
    }
}