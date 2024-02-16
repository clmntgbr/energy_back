<?php

namespace App\Command;

use App\Service\EvUpdateCommandService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:ev:update',
)]
class EvUpdateCommand extends Command
{
    public function __construct(
        private EvUpdateCommandService $evUpdateCommandService,
    )
    {
        parent::__construct(self::getDefaultName());
    }

    protected function configure(): void
    {
    }

    /** @throws \Exception */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->evUpdateCommandService->invoke();
        return Command::SUCCESS;
    }
}
