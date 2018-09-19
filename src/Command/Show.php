<?php

namespace Nitso\SqlConsole\Command;

use Doctrine\DBAL\DBALException;
use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Show extends AbstractDatabaseCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('show')
            ->setDescription('Show database info')
            ->addArgument('subject', InputArgument::REQUIRED)
            ->addUsage('show tables')
            ->addUsage('show databases')
        ;

        $this->getHelp();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $subject = $input->getArgument('subject');
        switch ($subject) {
            case 'tables':
                $this->processTablesRequest($output);
                break;
            case 'databases':
                $this->processDatabasesRequest($output);
                break;
            case 'views':
                $this->processViewsRequest($output);
                break;
            default:
                $helper = new DescriptorHelper();
                $helper->describe($output, $this);
                return;
        }
    }

    /**
     * @param OutputInterface $output
     */
    private function processTablesRequest(OutputInterface $output)
    {
        $connection = $this->getApplication()->getConnection();
        $tables = $connection->getSchemaManager()->listTables();

        if ($tables) {
            /** @var Table $table */
            $tableHelper = new Table($output);
            foreach ($tables as $table) {
                $tableHelper->addRow(array($table->getName()));
            }

            $tableHelper->render();
        }
        else {
            $output->writeln('<comment>Empty result</comment>');
        }
    }

    /**
     * @param OutputInterface $output
     */
    private function processDatabasesRequest(OutputInterface $output)
    {
        $connection = $this->getApplication()->getConnection();

        try {
            $databases = $connection->getSchemaManager()->listDatabases();
        }
        catch (DBALException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return;
        }

        if ($databases) {
            array_walk($databases, function(&$row) {
                $row = (array) $row;
            });
            /** @var Table $database */
            $table = new Table($output);
            $table->addRows((array) $databases);
            $table->render();
        }
        else {
            $output->writeln('<comment>Empty result</comment>');
        }
    }

    /**
     * @param OutputInterface $output
     */
    private function processViewsRequest(OutputInterface$output)
    {
        $connection = $this->getApplication()->getConnection();

        try {
            $views = $connection->getSchemaManager()->listViews();
        }
        catch (DBALException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return;
        }

        if ($views) {
            /** @var Table $database */
            $table = new Table($output);

            foreach ($views as $view) {
                $table->addRow(array($view->getName()));
            }

            $table->render();
        }
        else {
            $output->writeln('<comment>Empty result</comment>');
        }
    }
}