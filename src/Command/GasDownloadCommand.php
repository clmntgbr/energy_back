<?php

namespace App\Command;

use App\Service\FileSystemService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:gas:download',
)]
class GasDownloadCommand extends Command
{
    public function __construct(
        private string $gasUrl,
        private string $gasPath,
        private string $gasName,
        private string $gasJsonName,
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

        $io->title('Step 1 : deleting old files');
        FileSystemService::delete(FileSystemService::find($this->gasPath, "%\.(xml)$%i"));
        FileSystemService::delete($this->gasPath, $this->gasName);
        FileSystemService::delete($this->gasPath, $this->gasJsonName);

        $io->title('Step 2 : downloading gas zip');
        FileSystemService::download($this->gasUrl, $this->gasName, $this->gasPath);

        if (false === FileSystemService::exist($this->gasPath, $this->gasName)) {
            throw new \Exception('Zip file cant be found.');
        }

        $io->title('Step 3 : unziping gas zip');
        if (false === FileSystemService::unzip(sprintf('%s%s', $this->gasPath, $this->gasName), $this->gasPath)) {
            throw new \Exception('Zip file cant be unzip.');
        }

        FileSystemService::delete($this->gasPath, $this->gasName);

        if (null === $xmlPath = FileSystemService::find($this->gasPath, "%\.(xml)$%i")) {
            throw new \Exception('Xml file cant be found.');
        }

        $io->title('Step 4 : create new gas json file');
        $elements = simplexml_load_file($xmlPath);
        $json = json_encode($elements);
        $data = json_decode($json, true);

        $file = fopen("$this->gasPath/$this->gasJsonName", 'w') or exit('Cant open the file.');
        fwrite($file, json_encode($data['pdv'] ?? []));
        fclose($file);

        $io->title('Step 5 : deleting xml gas file');
        FileSystemService::delete(FileSystemService::find($this->gasPath, "%\.(xml)$%i"));

        return Command::SUCCESS;
    }
}
