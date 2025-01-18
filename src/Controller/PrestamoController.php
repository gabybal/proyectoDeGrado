<?php

namespace App\Controller;

use App\Entity\Prestamo;
use App\Entity\Book;
use App\Entity\Student;
use App\Form\PrestamoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrestamoController extends AbstractController
{
    // Ruta para realizar un préstamo
    #[Route('/prestamo', name: 'app_prestamo')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $prestamo = new Prestamo();
        $form = $this->createForm(PrestamoType::class, $prestamo);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Obtener el número de cédula del formulario
                $cedula = $prestamo->getStudent();

                // Buscar al estudiante por la cédula
                $student = $entityManager->getRepository(Student::class)->findOneBy(['cedula' => $cedula]);

                if ($student) {
                    // Asignar el estudiante al préstamo
                    $prestamo->setStudent($student);
                } else {
                    $this->addFlash('error', 'Estudiante no encontrado.');
                    return $this->redirectToRoute('app_prestamo');
                }

                // Obtener la fecha de préstamo desde el formulario y asegurarse de que sea un objeto DateTime
                $fechaPrestamo = $prestamo->getFechaPrestamo();
                if (!$fechaPrestamo instanceof \DateTimeInterface) {
                    $fechaPrestamo = new \DateTime(); // Usar la fecha actual si no se ha proporcionado
                }
                $prestamo->setFechaPrestamo($fechaPrestamo);

                // Si la fecha de devolución está establecida, se maneja como DateTime también
                $fechaDevolucion = $prestamo->getFechaDevolucion();
                if ($fechaDevolucion instanceof \DateTimeInterface) {
                    $prestamo->setFechaDevolucion($fechaDevolucion);
                }

                // Persistir el préstamo en la base de datos
                $entityManager->persist($prestamo);
                $entityManager->flush();

                $this->addFlash('success', 'Préstamo realizado correctamente.');
                return $this->redirectToRoute('app_prestamos_activos'); // Redirigir a préstamos activos
            } catch (\Exception $e) {
                $this->addFlash('error', 'Hubo un error al realizar el préstamo: ' . $e->getMessage());
            }
        }

        return $this->render('prestamo/prestamo.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Ruta para ver préstamos activos
    #[Route('/prestamos/activos', name: 'app_prestamos_activos')]
    public function prestamosActivos(EntityManagerInterface $entityManager): Response
    {
        $prestamos = $entityManager->getRepository(Prestamo::class)->findBy(['fechaDevolucion' => null]);

        return $this->render('prestamo/prestamos_activos.html.twig', [
            'prestamos' => $prestamos,
        ]);
    }

    // Ruta para devolver un libro
    #[Route('/prestamo/devolver/{id}', name: 'app_prestamo_devolver')]
    public function devolver(int $id, EntityManagerInterface $entityManager): Response
    {
        $prestamo = $entityManager->getRepository(Prestamo::class)->find($id);

        if (!$prestamo) {
            $this->addFlash('error', 'Préstamo no encontrado.');
            return $this->redirectToRoute('app_prestamos_activos');
        }

        // Asignar la fecha de devolución a la fecha actual
        $fechaDevolucion = new \DateTime();
        $prestamo->setFechaDevolucion($fechaDevolucion);

        $entityManager->flush();

        $this->addFlash('success', 'Libro devuelto correctamente.');
        return $this->redirectToRoute('app_historial_devoluciones');
    }

    // Ruta para ver historial de devoluciones
    #[Route('/prestamos/devoluciones', name: 'app_historial_devoluciones')]
    public function historialDevoluciones(EntityManagerInterface $entityManager): Response
    {
        $prestamos = $entityManager->getRepository(Prestamo::class)->findBy(['fechaDevolucion' => ['neq' => null]]);

        return $this->render('prestamo/historial_devoluciones.html.twig', [
            'prestamos' => $prestamos,
        ]);
    }

    // Ruta para obtener el nombre del estudiante por cédula
    #[Route('/api/student/{cedula}', name: 'api_get_student')]
    public function getStudent($cedula, EntityManagerInterface $entityManager): Response
    {
        $student = $entityManager->getRepository(Student::class)->findOneBy(['cedula' => $cedula]);

        if ($student) {
            return $this->json(['nombre' => $student->getNombre()]);
        }

        return $this->json(['nombre' => 'Estudiante no encontrado'], 404);
    }

    // Ruta para obtener los detalles del libro (autor y género)
    #[Route('/api/book/{titulo}', name: 'api_get_book')]
    public function getBook($titulo, EntityManagerInterface $entityManager): Response
    {
        $book = $entityManager->getRepository(Book::class)->findOneBy(['title' => $titulo]);

        if ($book) {
            return $this->json([
                'autor' => $book->getAutor(),
                'genre' => $book->getGenre()
            ]);
        }

        return $this->json(['message' => 'Libro no encontrado'], 404);
    }
}
