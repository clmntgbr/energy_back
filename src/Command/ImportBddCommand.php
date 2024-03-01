<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'bdd:import',
    description: 'Add a short description for your command',
)]
class ImportBddCommand extends Command
{
    public function __construct(
        private readonly Connection $connection
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sqlDumpFile = 'public/data-dump.sql';

        if (!file_exists($sqlDumpFile)) {
            return Command::FAILURE;
        }

        $sql = file_get_contents($sqlDumpFile);

        try {
            $this->connection->executeStatement($sql);
            $output->writeln('<info>SQL dump imported successfully!</info>');
        } catch (\Exception $e) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
