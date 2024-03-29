<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ZZZCodeAI
{
    public const BASE_URL = 'https://zzzcode.ai';

    public const QA_ENDPOINT = '/api/tools/answer-question';

    /**
     * @var list<string>
     */
    public const FIELDS = ['option1', 'option2', 'option3', 'p1', 'p2'];

    /**
     * @var list<string>
     */
    public const OPTIONS = ['Brief answer', 'Professional', 'Vietnamese'];

    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client->withOptions(
            (new HttpOptions())
                ->setBaseUri(static::BASE_URL)
                ->toArray()
        );
    }

    public function ask(string $context, string $question): string
    {
        $values = array_merge(self::OPTIONS, [$context, $question]);

        $content = $this->client->request('POST', self::QA_ENDPOINT, [
            'json' => array_combine(self::FIELDS, $values),
        ])->getContent();

        if (
            !preg_match(
                '/zzzredirectmessageidzzz:\s([a-z0-9-]+)/',
                $content,
                $matches
            )
        ) {
            throw new \RuntimeException('Unexpected error.');
        }

        $id = array_pop($matches);

        $response = $this->client->request('POST', self::QA_ENDPOINT, [
            'json' => ['id' => $id],
        ]);

        $buffer = '';
        foreach ($this->client->stream($response) as $chunk) {
            $buffer .= $chunk->getContent();
        }

        if (!preg_match_all('/data:\s(.+?)$/m', $buffer, $matches)) {
            throw new \RuntimeException('An unknown error has occurred.');
        }

        $result = array_pop($matches);

        // Release memory
        unset($buffer, $matches);

        array_shift($result);
        array_pop($result);

        $answer = join('', array_map(json_decode(...), $result));

        return trim(explode('## Answer', $answer)[1] ?? $answer);
    }
}
