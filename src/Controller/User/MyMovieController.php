<?php

namespace App\Controller\User;

use App\Entity\Movie;
use App\Entity\User;
use App\Form\EditMovieType;
use App\Form\MovieType;
use App\Manager\MovieManager;
use App\Repository\MovieRepository;
use App\Service\TmdbService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Environment;

#[Route("/movies")]
#[IsGranted("ROLE_USER")]
class MyMovieController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/myMovie', name: 'app_my_movie')]
    public function index(
        Request         $request,
        MovieManager    $movieManager,
        MovieRepository $movieRepository
    ): Response
    {
        $user = $this->getUser();

        // Création film
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $movieManager->new($form, $user, $movie, $this->getParameter('movie'));

            return $this->redirectToRoute('app_my_movie');
        }

        // Récupération des films
        $movies = $movieRepository->findBy(['user' => $user]);

        return $this->render('my_movie/myMovie.html.twig', [
            'form' => $form->createView(),
            'movies' => $movies

        ]);
    }

    #[Route('/details/{id}', name: 'app_movie_details')]
    public function movieDetails(
        Movie        $movie,
        Request      $request,
        MovieManager $movieManager,
    ): Response
    {
        // Modification film
        $form = $this->createForm(EditMovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $movieManager->edit($form, $movie);
            return $this->redirectToRoute('app_movie_details', ['id' => $movie->getId()]);
        }

        return $this->render('my_movie/details.html.twig', [
            'movie' => $movie,
            'form' => $form->createView()
        ]);
    }

    #[Route('/changeSee/{id}', name: 'app_movie_change_see')]
    public function changeSee(
        Movie        $movie,
        MovieManager $movieManager,
    ): RedirectResponse|Response
    {
        $movieManager->changeSee($movie);
        return $this->redirectToRoute('app_movie_details', ['id' => $movie->getId()]);
    }

    #[Route('/removeMovie/{id}', name: 'app_remove_movie')]
    public function removeMovie(
        Movie        $movie,
        MovieManager $movieManager,
    ): Response
    {
        $movieManager->remove($movie);
        return $this->redirectToRoute('app_my_movie');
    }

    #[Route('/seeMovie', name: 'app_see_movie')]
    public function seeMovie(
        MovieRepository $movieRepository
    ): Response
    {
        $user = $this->getUser();

        // Récupération des films vue
        $movies = $movieRepository->findBy([
            'user' => $user,
            'see'  => true
        ]);

        return $this->render('my_movie/seeMovie.html.twig', [
            'movies' => $movies
        ]);
    }

    #[Route('/toSeeMovie', name: 'app_to_see_movie')]
    public function toSeeMovie(
        MovieRepository $movieRepository,
    ): Response
    {
        $user = $this->getUser();

        // Récupération des films vue
        $movies = $movieRepository->findBy([
            'user' => $user,
            'see'  => false
        ]);

        return $this->render('my_movie/toSeeMovie.html.twig', [
            'movies' => $movies
        ]);
    }

    #[Route('/searchMovie', name: 'app_search_movie')]
    public function searchMovie(
        Request $request,
        TmdbService $tmdbService,
        Environment $environment
    ): JsonResponse
    {
        $query = $request->request->get('searchValue');
        // Récupération des films chercher
        $movies = $tmdbService->searchMovie($query);

        return  $this->json([
            'html' => $environment->render('my_movie/_movies_list.html.twig', [
                'movies' => $movies
            ])
        ]);
    }

    #[Route('/saveMovie/{id}/{see}', name: 'app_save_movie')]
    public function saveMovie(
        $id,
        $see,
        TmdbService $tmdbService,
        MovieManager $movieManager,
    ): Response
    {
        $user = $this->getUser();

        $movieId = $tmdbService->getMovieDetailsWithCredits($id);
        $newMovie = $movieManager->newMovieApi($movieId, $see, $user);



        return $this->redirectToRoute('app_movie_details', [
            'id' => $newMovie->getId()
        ]);
    }


}
