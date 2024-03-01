<?php

namespace App\Pipe;

/**
 * @psalm-type ResultType=list{string,string}
 */
class NameDetector
{
    public const NAME_PATTERN = '/Họ và tên: ([^\n]+)/u';

    /**
     * @return ResultType
     *
     * @throws \LogicException
     */
    public function __invoke(string $content): array
    {
        if (!preg_match(self::NAME_PATTERN, $content, $matches)) {
            throw new \LogicException('Name not found.');
        }

        // @phpstan-ignore-next-line
        return [array_pop($matches), $content];
    }
}
