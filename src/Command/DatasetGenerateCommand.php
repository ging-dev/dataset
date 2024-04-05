<?php

namespace App\Command;

use App\Repository\TeacherRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

#[AsCommand(
    name: 'dataset:generate',
    description: 'Add a short description for your command',
)]
class DatasetGenerateCommand extends Command
{
    public function __construct(private TeacherRepository $teacherRepository)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fileSystem = new Filesystem();

        $data = [];
        foreach ($this->teacherRepository->findAll() as $teacher) {
            $data[] = (string) json_encode([
                'user' => sprintf('Thông tin giảng viên %s', $teacher->getName()),
                'assistant' => $teacher->getDescription(),
            ], JSON_UNESCAPED_UNICODE);
        }

        $fileSystem->appendToFile(Path::join(getcwd(), 'data.jsonl'), join("\n", $data));

        return Command::SUCCESS;
    }
}
