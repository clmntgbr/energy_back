<?php

namespace App\Command;

use App\Service\FileSystemService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:ev:download',
)]
class EvDownloadCommand extends Command
{
    public function __construct(
        private string $evUrl,
        private string $evPath,
        private string $evJsonName,
    )
    {
        parent::__construct(self::getDefaultName());
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Step 1 : deleting old file');
        FileSystemService::delete(FileSystemService::find($this->evPath, "%\.(json)$%i"));

        $io->title('Step 2 : downloading ev json');
        FileSystemService::download($this->evUrl, $this->evJsonName, $this->evPath);

        if (false === FileSystemService::exist($this->evPath, $this->evJsonName)) {
            throw new \Exception('json file cant be found.');
        }

        return Command::SUCCESS;
    }
}
