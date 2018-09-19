<?php

namespace Nitso\SqlConsole\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \Nitso\SqlConsole\Application getApplication()
 */
class Disconnect extends Command
{
    protected function configure()
    {
        $this
            ->setName('disconnect')
            ->setDescription('Disconnect active connection')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->getApplication()->getConnection()->isConnected()) {
            $this->getApplication()->getConnection()->close();
        }

        $this->getApplication()->setPrompt('');
        return 0;
    }

}

