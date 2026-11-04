<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Movie;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Response;

final class MoviesController extends AbstractController
{
    #[Route('/movies', name: 'app_movies')]
    public function index(
        Request $request,
        MovieRepository $movieRepository,
        CategorieRepository $categorieRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        $createdMovie = new Movie();
        $createdMovie->setTitle($data['title']);
        $createdMovie->setDescription($data['description']);
        $createdMovie->setYear($data['year']);

        foreach ($data['categories'] ?? [] as $categorieId) {
            $categorie = $categorieRepository->find($categorieId);
            if (!$categorie) {
                return $this->json(['error' => "Catégorie $categorieId introuvable"], 404);
            }
            $createdMovie->addCategory($categorie);
        }

        $entityManager->persist($createdMovie);
        $entityManager->flush();

        return $this->json($createdMovie, Response::HTTP_OK, [], ['groups' => 'movie:read']);
    }

    #[Route('/movies/{id}', name: 'app_movies_update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        MovieRepository $movieRepository,
        CategorieRepository $categorieRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $movie = $movieRepository->find($id);

        if (!$movie) {
            return $this->json(['error' => 'Film introuvable'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['title'])) {
            $movie->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $movie->setDescription($data['description']);
        }
        if (isset($data['year'])) {
            $movie->setYear($data['year']);
        }

        if (isset($data['categories'])) {
            // Clear existing categories
            foreach ($movie->getCategories() as $existingCategory) {
                $movie->removeCategory($existingCategory);
            }

            // Add new categories
            foreach ($data['categories'] as $categorieId) {
                $categorie = $categorieRepository->find($categorieId);
                if (!$categorie) {
                    return $this->json(['error' => "Catégorie $categorieId introuvable"], 404);
                }
                $movie->addCategory($categorie);
            }
        }

        $entityManager->flush();

        return $this->json($movie, Response::HTTP_OK, [], ['groups' => 'movie:read']);
    }
}