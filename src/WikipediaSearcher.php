<?php

namespace DivineOmega\WikipediaSearch;

use DivineOmega\WikipediaSearch\Interfaces\SearcherInterface;

class WikipediaSearcher implements SearcherInterface
{
    const URL = 'https://[LANGUAGE].wikipedia.org/w/api.php?action=query&list=search&utf8=&format=json&srlimit=500&srsearch=[QUERY]';

    private $language;

    public function __construct(string $language)
    {
        $this->language = $language;
    }

    public function search(string $query): array
    {
        $url = $this->buildUrl($query);

        $response = file_get_contents($url);
        $decodedResponse = json_decode($response, true);

        $results = [];

        $score = count($decodedResponse['query']['search']);

        foreach ($decodedResponse['query']['search'] as $item) {
            $results[] = new WikipediaSearchResult($item, $this->language, $score);
            $score--;
        }

        var_dump($results[1]);

        return $results;
    }

    private function buildUrl(string $query): string
    {
        return str_replace(
            ['[QUERY]', '[LANGUAGE]'],
            [urlencode($query), urlencode($this->language)],
            self::URL
        );
    }
}