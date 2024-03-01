<?php

namespace App\Message;

final readonly class PDF implements \Stringable
{
    public function __construct(public string $filename)
    {
    }

    public function __toString(): string
    {
        return $this->filename;
    }
}
