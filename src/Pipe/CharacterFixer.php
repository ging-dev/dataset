<?php

namespace App\Pipe;

class CharacterFixer
{
    public function __invoke(string $content): string
    {
        return str_replace(['Ƣ', 'ƣ'], ['Ư', 'ư'], $content);
    }
}
