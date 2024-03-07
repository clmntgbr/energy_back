<?php

namespace App\Command;

use App\Repository\EnergyStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:change',
    description: 'Add a short description for your command',
)]
class ChangesCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EnergyStationRepository $energyStationRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $backupFile = '/sauvegarde.sql';

        // Commande pour exécuter mysqldump
        $command = "mysqldump --host=energy_database --user=random --password=random energy > {$backupFile}";

        // Exécution de la commande
        exec($command, $output, $returnValue);

        return Command::SUCCESS;
    }
}
