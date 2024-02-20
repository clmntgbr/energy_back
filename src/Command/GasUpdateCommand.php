<?php

namespace App\Command;

use App\Service\GasUpdateCommandService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:gas:update',
)]
class GasUpdateCommand extends Command
{
    public function __construct(
        private GasUpdateCommandService $gasUpdateCommandService,
    )
    {
        parent::__construct(self::getDefaultName());
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->gasUpdateCommandService->invoke();
        return Command::SUCCESS;
    }
}