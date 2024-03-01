<?php

namespace App\Pipe;

use Smalot\PdfParser\Parser;

use function Symfony\Component\String\u;

class PDFReader
{
    public function __construct(private Parser $parser = new Parser())
    {
    }

    public function __invoke(string $filename): string
    {
        $content = $this->parser->parseFile($filename)->getText();

        return u($content)->truncate(5000);
    }
}
