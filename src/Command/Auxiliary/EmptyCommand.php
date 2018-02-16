<?php

namespace Nitso\SqlConsole\Command\Auxiliary;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EmptyCommand extends Command
{
    protected function configure()
    {
        $this->setName('empty');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}