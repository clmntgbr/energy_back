<?php

namespace App\Command;

use App\Service\EnergyStationClosedCommandService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:station:close',
)]
class EnergyStationClosedCommand extends Command
{
    public function __construct(
        private EnergyStationClosedCommandService $energyStationClosedCommandService,
    )
    {
        parent::__construct(self::getDefaultName());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->energyStationClosedCommandService->invoke();

        return Command::SUCCESS;
    }
}
