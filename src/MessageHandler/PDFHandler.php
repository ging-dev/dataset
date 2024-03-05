<?php

namespace App\MessageHandler;

use App\Entity\Teacher;
use App\Message\PDF;
use App\Pipe\NameDetector;
use App\Pipe\PDFPipeline;
use App\Repository\TeacherRepository;
use App\Service\ZZZCodeAI;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @psalm-import-type ResultType from NameDetector
 */
#[AsMessageHandler]
class PDFHandler
{
    public const PROMPT = <<<PROMPT
    Give a short description to introduce lecturer of Thai Nguyen University.

    Required:
    - Repeat the lecturer's name several times
    - Hometown information, only mention provinces and cities
    - Summary of achievements
    PROMPT;

    public function __construct(
        private ZZZCodeAI $ai,
        private TeacherRepository $teacherRepository,
        private PDFPipeline $pipeline,
    ) {
    }

    public function __invoke(PDF $message): void
    {
        $result = $this->pipeline->process((string) $message);

        [$name, $context] = $result;

        $desc = $this->ai->ask($context, self::PROMPT);

        $teacher = (new Teacher())
            ->setName($name)
            ->setDescription($desc);

        $this->teacherRepository->save($teacher, true);
    }
}
