<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Categorie;
use Symfony\Component\HttpFoundation\Response;

final class CategoriesController extends AbstractController
{

    #[Route('/categories', name: 'app_categories')]
    public function index(
        Request $request,
        CategorieRepository $categorieRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        $createdCategory = new Categorie();
        $createdCategory->setName($data['name']);

        $entityManager->persist($createdCategory);
        $entityManager->flush();


        return $this->json($createdCategory, Response::HTTP_OK, [], ['groups' => 'categorie:read']); 
    }
}