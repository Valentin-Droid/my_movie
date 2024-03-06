<?php

namespace App\Manager;

use App\Entity\Movie;
use App\Entity\User;
use App\Repository\MovieRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Response;
use Symfony\Component\String\Slugger\SluggerInterface;


class MovieManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MovieRepository $movieRepository,
        private SluggerInterface $slugger
    )
    {
    }

    public function new($form, User $user, Movie $movie, $parameter): void
    {
        $file = $form->get('file')->getData();

        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
            $file->move($parameter, $newFilename);
            $movie->setFile($newFilename);

        }

        $movie->setUser($user);
        $this->entityManager->persist($movie);
        $this->entityManager->flush();
    }

    public function edit($form, Movie $movie): void
    {
        $this->entityManager->flush();
    }

    public function changeSee(Movie $movie): void
    {
        $see = $movie->isSee();
        $movie->setSee(!$see);

        $this->entityManager->flush();
    }

    public function remove(Movie $movie): void
    {
        $this->entityManager->remove($movie);
        $this->entityManager->flush();
    }

    public function newMovieApi($data, string $see, User $user): Movie
    {
        $existingMovie = $this->movieRepository->findOneBy([
            'idMovieApi' => $data['id'],
        ]);

        if ($existingMovie) {
            return $existingMovie;
        }

        // Ajouter film
        $movie = new Movie();
        $movie->setIdMovieApi($data['id']);
        $movie->setUser($user);
        $movie->setName($data['title']);

        $date_string = $data['release_date'];
        $date = new \DateTime($date_string);
        $movie->setDateRelease($date);

        $genres = '';
        foreach ($data['genres'] as $subArray) {
            $genreName = $subArray['name'];
            $genres .= $genreName . ' ';
        }
        $movie->setType($genres);
        $movie->setSynopsis($data['overview']);

        $writer = '';
        foreach ($data['credits']['crew'] as $crewMember) {
            if ($crewMember['job'] === 'Director') {
                $writer = $crewMember['name'];
                break;
            }
        }
        $movie->setRealisator($writer);
        $movie->setPosterPath($data['poster_path']);

        if ($see == 'true'){
            $seeBool = true;
        } else {
            $seeBool = false;
        }
        $movie->setSee($seeBool);

        $this->entityManager->persist($movie);
        $this->entityManager->flush();

        return $movie;

    }
}