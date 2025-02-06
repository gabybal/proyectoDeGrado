<?php

namespace App\Controller;

use App\Entity\Prestamo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/api/stats', name: 'app_stats')]
    public function getStats(EntityManagerInterface $entityManager): JsonResponse
    {
        // Obtener los géneros más leídos
        $genres = $entityManager->getRepository(Prestamo::class)
            ->createQueryBuilder('p')
            ->select('b.genre as genero, COUNT(p.id) as total')
            ->join('p.book', 'b')
            ->groupBy('b.genre')
            ->orderBy('total', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Obtener los libros más leídos
        $books = $entityManager->getRepository(Prestamo::class)
            ->createQueryBuilder('p')
            ->select('b.title as titulo, COUNT(p.id) as total')
            ->join('p.book', 'b')
            ->groupBy('b.title')
            ->orderBy('total', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Obtener los autores más leídos
        $authors = $entityManager->getRepository(Prestamo::class)
            ->createQueryBuilder('p')
            ->select('b.autor as autor, COUNT(p.id) as total')
            ->join('p.book', 'b')
            ->groupBy('b.autor')
            ->orderBy('total', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Obtener los estudiantes que más leen
        $students = $entityManager->getRepository(Prestamo::class)
            ->createQueryBuilder('p')
            ->select('s.nombre as nombre, COUNT(p.id) as total')
            ->join('p.student', 's')
            ->groupBy('s.nombre')
            ->orderBy('total', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        return $this->json([
            'genres' => $genres,
            'books' => $books,
            'authors' => $authors,
            'students' => $students
        ]);
    }
}