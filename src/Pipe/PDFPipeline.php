<?php

namespace App\Pipe;

use League\Pipeline\Pipeline;

/**
 * @psalm-import-type ResultType from NameDetector
 *
 * @method ResultType process(string $payload)
 */
class PDFPipeline extends Pipeline
{
    public static function create(): PDFPipeline
    {
        $self = (new self())
            ->pipe(new PDFReader())
            ->pipe(new CharacterFixer())
            ->pipe(new NameDetector());

        return $self;
    }
}
