<?php

namespace App\Controller;

use App\Entity\Prestamo;
use App\Entity\Book;
use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrestamoController extends AbstractController
{
    // Ruta para agregar préstamos retornando un json
    #[Route('/prestamos/add', name: 'app_prestamo_add_json')]
    public function addPrestamoJson(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $request->request->all();
        $prestamo = new Prestamo();
        $prestamo->setStudent($entityManager->getRepository(Student::class)->find($data['studentId']));
        $prestamo->setBook($entityManager->getRepository(Book::class)->find($data['bookId']));
        $prestamo->setFechaPrestamo(new \DateTime($data['fechaPrestamo']));

        try {
            $entityManager->persist($prestamo);
            $entityManager->flush();

            $response = [
                'status' => 'success',
                'message' => 'Préstamo agregado correctamente.'
            ];
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'Error al agregar el préstamo: ' . $e->getMessage()
            ];
        }

        return new JsonResponse($response);
    }

    // Ruta para devolver préstamos retornando un json
    #[Route('/prestamos/devolver', name: 'app_prestamo_devolver_json')]
    public function devolverPrestamoJson(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $request->request->all();
        $prestamo = $entityManager->getRepository(Prestamo::class)->find($data['id']);
        if (!$prestamo) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Préstamo no encontrado.'
            ]);
        }

        $prestamo->setFechaDevolucion(new \DateTime());
        $prestamo->setComentario($data['comentario']);

        try {
            $entityManager->persist($prestamo);
            $entityManager->flush();

            $response = [
                'status' => 'success',
                'message' => 'Préstamo devuelto correctamente.'
            ];
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'Error al devolver el préstamo: ' . $e->getMessage()
            ];
        }

        return new JsonResponse($response);
    }

    // Ruta para agregar fecha de devolución a un préstamo
    #[Route('/prestamos/agregarFechaDevolucion', name: 'app_prestamo_agregar_fecha_devolucion_json')]
    public function agregarFechaDevolucionJson(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $request->request->all();
        $prestamo = $entityManager->getRepository(Prestamo::class)->find($data['id']);
        if (!$prestamo) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Préstamo no encontrado.'
            ]);
        }

        $prestamo->setFechaDevolucion(new \DateTime($data['fechaDevolucion']));

        try {
            $entityManager->persist($prestamo);
            $entityManager->flush();

            $response = [
                'status' => 'success',
                'message' => 'Fecha de devolución agregada correctamente.'
            ];
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'Error al agregar la fecha de devolución: ' . $e->getMessage()
            ];
        }

        return new JsonResponse($response);
    }

    // Ruta para listar préstamos retornando un json
    #[Route('/prestamos/list', name: 'app_prestamos_list_json')]
    public function listJson(EntityManagerInterface $entityManager): JsonResponse
    {
        $prestamos = $entityManager->getRepository(Prestamo::class)->findBy(['fechaDevolucion' => null]);

        $data = [];
        foreach ($prestamos as $prestamo) {
            $data[] = [
                'id' => $prestamo->getId(),
                'student' => $prestamo->getStudent()->getNombre(),
                'book' => $prestamo->getBook()->getTitle(),
                'fechaPrestamo' => $prestamo->getFechaPrestamo()->format('Y-m-d'),
                'fechaDevolucion' => $prestamo->getFechaDevolucion() ? $prestamo->getFechaDevolucion()->format('Y-m-d') : null,
            ];
        }

        return new JsonResponse($data);
    }

    // Ruta para la vista de préstamos
    #[Route('/prestamos', name: 'app_prestamos')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        return $this->render('prestamo/prestamo.html.twig');
    }

    
    // Ruta para listar devoluciones retornando un json
    #[Route('/devoluciones/list', name: 'app_devoluciones_list_json')]
    public function listDevolucionesJson(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $devoluciones = $entityManager->getRepository(Prestamo::class)->createQueryBuilder('p')
                ->where('p.fechaDevolucion IS NOT NULL')
                ->getQuery()
                ->getResult();

            $data = [];
            foreach ($devoluciones as $devolucion) {
                $data[] = [
                    'id' => $devolucion->getId(),
                    'student' => $devolucion->getStudent()->getNombre(),
                    'book' => $devolucion->getBook()->getTitle(),
                    'fechaPrestamo' => $devolucion->getFechaPrestamo()->format('Y-m-d'),
                    'fechaDevolucion' => $devolucion->getFechaDevolucion()->format('Y-m-d'),
                    'comentario' => $devolucion->getComentario(),
                ];
            }

            return new JsonResponse($data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Ruta para la vista de devoluciones
    #[Route('/devoluciones', name: 'app_devoluciones')]
    public function devoluciones(): Response
    {
        return $this->render('prestamo/devoluciones.html.twig');
    }
}