<?php

namespace App\Command;

use App\Service\EnergyStationStatusUpdateCommandService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:status:update',
)]
class EnergyStationStatusUpdateCommand extends Command
{
    public function __construct(
        private EnergyStationStatusUpdateCommandService $energyStationStatusUpdateCommandService,
    )
    {
        parent::__construct(self::getDefaultName());
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'energyStationIds',
                InputArgument::IS_ARRAY,
                'Some EnergyStationIds ?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $energyStationIds = $input->getArgument('energyStationIds');

        $this->energyStationStatusUpdateCommandService->invoke($energyStationIds);

        return Command::SUCCESS;
    }
}
