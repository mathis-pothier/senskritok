<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Common\Collections\EntityManagerInterface;

final class MoviesController extends AbstractController
{

    #[Route('/movies', name: 'app_movies')]
    public function index(Request $request): JsonResponse
    {
        dd(
            $request,
            $request->query->all(),
            json_decode($request->getContent(), true)
        );
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MoviesController.php',
        ]);
        
    }
}
