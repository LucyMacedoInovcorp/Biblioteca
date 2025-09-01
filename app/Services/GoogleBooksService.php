<?php

namespace App\Services;

use GuzzleHttp\Client;

class GoogleBooksService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://www.googleapis.com/books/v1/',
        ]);
        $this->apiKey = config('services.google_books.key');
    }

    public function searchBooks(string $query)
    {
        try {
            $response = $this->client->get('volumes', [
                'query' => [
                    'q' => $query,
                    'key' => $this->apiKey,
                    // Você pode adicionar outros parâmetros como `maxResults` ou `startIndex`
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            // Lidar com erros, como requisições falhadas
            return null;
        }
    }
}