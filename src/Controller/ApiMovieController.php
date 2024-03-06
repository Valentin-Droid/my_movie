<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiMovieController extends AbstractController
{
    #[Route('/apiMovie', name: 'app_api_movie', methods: ['GET'])]
    public function apiMovieList(
        MovieRepository $movieRepository,
        Request $request
    )
    {
        $apiKey = $request->headers->get('X-API-KEY');


        if ($apiKey === $_ENV['APIKEY']) {

            // Récupération des films en bdd
            $movies = $movieRepository->findAll();

            // Transformation de la liste des films en tableau associatif
            $moviesArray = [];
            foreach ($movies as $movie) {
                $moviesArray[] = [
                    'id' => $movie->getId(),
                    'title' => $movie->getName(),
                    'release_date' => $movie->getDateRelease(),
                    'director' => $movie->getRealisator(),
                ];
            }

            // Retour de la liste des films au format JSON
            return $this->json($moviesArray);
        }
        return $this->json('401');
    }

    #[Route('/movieWatched/{id}', name: 'app_movie_watched', methods: ['GET'])]
    public function apiMovieWatched(
        User $user,
        MovieRepository $movieRepository
    )
    {
        // Récupération des films en bdd
        $movies = $movieRepository->findBy([
            'user' => $user,
            'see' => true
        ]);

        // Transformation de la liste des films en tableau associatif
        $moviesArray = [];
        foreach ($movies as $movie) {
            $moviesArray[] = [
                'id' => $movie->getId(),
                'title' => $movie->getName(),
                'release_date' => $movie->getDateRelease(),
                'director' => $movie->getRealisator(),
                'user' => $user->getFirstName()
            ];
        }

        // Retour de la liste des films au format JSON
        return $this->json($moviesArray);
    }


    #[Route('/movieNotWatched/{id}', name: 'app_not_movie_watched', methods: ['GET'])]
    public function apiMovieNotWatched(
        User $user,
        MovieRepository $movieRepository
    )
    {
        // Récupération des films en bdd
        $movies = $movieRepository->findBy([
            'user' => $user,
            'see'  => false
        ]);

        // Transformation de la liste des films en tableau associatif
        $moviesArray = [];
        foreach ($movies as $movie) {
            $moviesArray[] = [
                'id' => $movie->getId(),
                'title' => $movie->getName(),
                'release_date' => $movie->getDateRelease()->format('d/m/Y'),
                'director' => $movie->getRealisator(),
                'user' => $user->getFirstName()

            ];
        }

        // Retour de la liste des films au format JSON
        return $this->json($moviesArray);
    }

    #[Route('/movieGenres/{genres}', name: 'app_movie_genres', methods: ['GET'])]
    public function apiMovieGenres(
        $genres,
        MovieRepository $movieRepository
    )
    {
        // Récupération des films en bdd
        $movies = $movieRepository->findBy([
            'type'  => $genres
        ]);

        // Transformation de la liste des films en tableau associatif
        $moviesArray = [];
        foreach ($movies as $movie) {
            $moviesArray[] = [
                'id' => $movie->getId(),
                'title' => $movie->getName(),
                'release_date' => $movie->getDateRelease(),
                'director' => $movie->getRealisator(),
            ];
        }

        // Retour de la liste des films au format JSON
        return $this->json($moviesArray);
    }

    #[Route('/movieDirector/{director}', name: 'app_movie_director', methods: ['GET'])]
    public function apiMovieDirector(
        $director,
        MovieRepository $movieRepository
    )
    {
        // Récupération des films en bdd
        $movies = $movieRepository->findBy([
            'realisator'  => str_replace('-', ' ', $director)
        ]);

        // Transformation de la liste des films en tableau associatif
        $moviesArray = [];
        foreach ($movies as $movie) {
            $moviesArray[] = [
                'id' => $movie->getId(),
                'title' => $movie->getName(),
                'release_date' => $movie->getDateRelease(),
                'director' => $movie->getRealisator(),
            ];
        }

        // Retour de la liste des films au format JSON
        return $this->json($moviesArray);
    }
}
