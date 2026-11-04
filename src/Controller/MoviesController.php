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
use App\Model\PaginationDTO;
use App\Model\Paginator;

final class MoviesController extends AbstractController
{
    
    #[Route('/movies', name: 'app_movies_paginate', methods: ['GET'])]
    public function paginate(
        Request $request,
        MovieRepository $movieRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 10);

        $paginationDTO = new PaginationDTO($page, $limit);
        $paginator = new Paginator();

        $query = $movieRepository->createQueryBuilder('m')
            ->andWhere('m.title LIKE :title')
            ->setParameter('title', '%' . $request->query->get('title', '') . '%')
            ->andWhere('m.description LIKE :description')
            ->setParameter('description', '%' . $request->query->get('description', '') . '%')
            ->andWhere('m.year LIKE :year')
            ->setParameter('year', '%' . $request->query->get('year', '') . '%')
            ->join('m.categories', 'c')
            ->andWhere('c.name LIKE :category')
            ->setParameter('category', '%' . $request->query->get('category', '') . '%');
        return $this->json($paginator->paginate($query, $paginationDTO), Response::HTTP_OK, [], ['groups' => ['movie:read', 'paginate']]);
    }

    #[Route('/movies/{id}', name: 'app_one_movie', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getOneMovie(
        int $id,
        MovieRepository $movieRepository
    ): JsonResponse {
        $movie = $movieRepository->find($id);

        if (!$movie) {
            return $this->json(['error' => 'Film introuvable'], 404);
        }

        return $this->json($movie, Response::HTTP_OK, [], ['groups' => 'movie:read']);
    }

    #[Route('/movies', name: 'app_new_movies', methods: ['POST'])]
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
        EntityManagerInterface $entityManager,
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
        
            foreach ($movie->getCategories() as $existingCategory) {
                $movie->removeCategory($existingCategory);
            }

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

    #[Route('/movies/{id}', name: 'app_movies_delete', methods: ['DELETE'])]
    public function delete(
        int $id,
        MovieRepository $movieRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $movie = $movieRepository->find($id);

        if (!$movie) {
            return $this->json(['error' => 'Film introuvable'], 404);
        }

        $entityManager->remove($movie);
        $entityManager->flush();

        return $this->json(['message' => 'Film supprimé avec succès'], Response::HTTP_OK);
    }

}