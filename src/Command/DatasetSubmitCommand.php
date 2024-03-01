<?php

namespace App\Command;

use App\Message\PDF;
use Laravel\Prompts\Progress;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Messenger\MessageBusInterface;

use function Laravel\Prompts\progress;

/**
 * @author gingdev <anonymous>
 */
#[AsCommand(
    name: 'dataset:submit',
    description: 'Add a short description for your command',
)]
class DatasetSubmitCommand extends Command
{
    public function __construct(private MessageBusInterface $bus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('dir', InputArgument::REQUIRED, 'Argument description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $finder = new Finder();

        $finder
            ->files()
            ->in((string) $input->getArgument('dir'))
            ->name('*.pdf');

        if (!$finder->hasResults()) {
            $io->error('No files found.');

            return Command::FAILURE;
        }

        progress(
            'Processing',
            $finder->getIterator(),
            /** @param Progress<void> $progress */
            function (SplFileInfo $file, Progress $progress) {
                $this->bus->dispatch(new PDF($file->getPathname()));
            },
            'Send pdf content to queue'
        );

        return Command::SUCCESS;
    }
}
