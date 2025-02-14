<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends AbstractController
{
    // Ruta para buscar estudiante por cédula
    #[Route('/api/student/cedula/{cedula}', name: 'api_student_by_cedula')]
    public function getStudentByCedula($cedula, EntityManagerInterface $entityManager): JsonResponse
    {
        $student = $entityManager->getRepository(Student::class)->findOneBy(['cedula' => $cedula]);

        if (!$student) {
            return new JsonResponse(['error' => 'Estudiante no encontrado'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $student->getId(),
            'nombre' => $student->getNombre(),
            'cedula' => $student->getCedula()
        ]);
    }

    // Ruta para buscar libro por título
    #[Route('/api/book/title/{title}', name: 'api_book_by_title')]
    public function getBookByTitle($title, EntityManagerInterface $entityManager): JsonResponse
    {
        $book = $entityManager->getRepository(Book::class)->findOneBy(['title' => $title]);

        if (!$book) {
            return new JsonResponse(['error' => 'Libro no encontrado'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'autor' => $book->getAutor()
        ]);
    }

    #[Route('/api/book/suggestions/{query}', name: 'app_book_suggestions', methods: ['GET'])]
    public function getBookSuggestions(string $query, EntityManagerInterface $entityManager): JsonResponse
    {
        $books = $entityManager->getRepository(Book::class)->createQueryBuilder('b')
            ->where('b.title LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();

        $suggestions = [];
        foreach ($books as $book) {
            $suggestions[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
            ];
        }

        return new JsonResponse($suggestions);
    }
}
