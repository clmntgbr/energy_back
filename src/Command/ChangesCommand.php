<?php

namespace App\Command;

use App\Entity\EvInformation;
use App\Repository\EnergyStationRepository;
use Doctrine\DBAL\Connection;
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
        $stations = $this->energyStationRepository->findBy(['type' => 'GAS']);

        foreach ($stations as $station) {
            if ($station->getEvInformation() === null) {
                $station->setEvInformation(new EvInformation());
                $this->em->persist($station);
                $this->em->flush();
            }
        }

        return Command::SUCCESS;
    }
}
