<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;

class TmdbService
{
    public function __construct(
        private readonly string $apikey,
        private Client $client = new Client(['base_uri' => 'https://api.themoviedb.org/3/'])
    )
    {
    }

    public  function searchMovie(string $query)
    {
        $response = $this->client->request('GET','search/movie', [
            'query' => [
                'api_key' => $this->apikey,
                'language' => 'fr-FR',
                'query' => $query,
            ],
        ]);
        return json_decode($response->getBody(), true);
    }

    public  function getMovieDetailsWithCredits(string $idMovie)
    {
        $response = $this->client->request('GET','movie/'.$idMovie, [
            'query' => [
                'api_key' => $this->apikey,
                'language' => 'fr-FR',
                'append_to_response' => 'credits',
            ],
        ]);
        return json_decode($response->getBody(), true);
    }
}