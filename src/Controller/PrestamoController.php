<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\StudentRepository;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class PrestamoController extends AbstractController
{
    /**
     * @Route("/buscar-estudiante/{cedula}", name="buscar_estudiante", methods={"GET"})
     */
    public function buscarEstudiante($cedula, StudentRepository $studentRepository): JsonResponse
    {
        // Buscar al estudiante por cédula
        $student = $studentRepository->findOneBy(['cedula' => $cedula]);

        if ($student) {
            // Retornar el nombre completo del estudiante
            return new JsonResponse(['student' => ['fullName' => $student->getFullName()]]);
        } else {
            // Retornar respuesta si no se encuentra el estudiante
            return new JsonResponse(['student' => null]);
        }
    }

    /**
     * @Route("/buscar-libro/{titulo}", name="buscar_libro", methods={"GET"})
     */
    public function buscarLibro($titulo, BookRepository $bookRepository): JsonResponse
    {
        // Buscar el libro por título
        $book = $bookRepository->findOneBy(['title' => $titulo]);

        if ($book) {
            // Retornar el título del libro
            return new JsonResponse(['book' => ['title' => $book->getTitle()]]);
        } else {
            // Retornar respuesta si no se encuentra el libro
            return new JsonResponse(['book' => null]);
        }
    }
}
