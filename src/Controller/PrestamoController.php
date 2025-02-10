<?php

namespace App\Controller;

use App\Entity\Prestamo;
use App\Entity\Book;
use App\Entity\Student;
use App\Form\PrestamoType;
use App\Repository\StudentRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrestamoController extends AbstractController
{
    // Ruta para buscar estudiantes por cédula
    #[Route('/search/students', name: 'search_students', methods: ['GET'])]
    public function searchStudents(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $searchTerm = $request->query->get('q'); // Obtener el término de búsqueda
        $students = $entityManager->getRepository(Student::class)
            ->createQueryBuilder('s')
            ->where('s.cedula LIKE :searchTerm OR s.nombre LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();

        $results = [];
        foreach ($students as $student) {
            $results[] = [
                'id' => $student->getId(),
                'label' => $student->getNombre() . ' (' . $student->getCedula() . ')',
            ];
        }

        return new JsonResponse($results);
    }

    // Ruta para obtener los detalles del estudiante
    #[Route('/student-details/{id}', name: 'student_details')]
    public function studentDetails($id, StudentRepository $studentRepository): JsonResponse
    {
        $student = $studentRepository->find($id);

        if (!$student) {
            return new JsonResponse(['error' => 'Estudiante no encontrado'], 404);
        }

        return new JsonResponse([
            'nombre' => $student->getNombre(),
        ]);
    }

    // Ruta para buscar libros por título
    #[Route('/search/books', name: 'search_books', methods: ['GET'])]
    public function searchBooks(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $searchTerm = $request->query->get('q'); // Obtener el término de búsqueda
        $books = $entityManager->getRepository(Book::class)
            ->createQueryBuilder('b')
            ->where('b.title LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();

        $results = [];
        foreach ($books as $book) {
            $results[] = [
                'id' => $book->getId(),
                'label' => $book->getTitle(),
            ];
        }

        return new JsonResponse($results);
    }

    // Ruta para obtener los detalles del libro
    #[Route('/book-details/{id}', name: 'book_details')]
    public function bookDetails($id, BookRepository $bookRepository): JsonResponse
    {
        $book = $bookRepository->find($id);

        if (!$book) {
            return new JsonResponse(['error' => 'Libro no encontrado'], 404);
        }

        return new JsonResponse([
            'genre' => $book->getGenre(),
            'autor' => $book->getAutor(),
        ]);
    }

    // Ruta para crear un préstamo
    #[Route('/prestamo', name: 'app_prestamo')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $prestamo = new Prestamo();
        $form = $this->createForm(PrestamoType::class, $prestamo);  // Crea el formulario

        $form->handleRequest($request);  // Maneja la solicitud

        if ($form->isSubmitted() && $form->isValid()) {
            // Verificar si ya existe un préstamo para ese libro sin devolver
            $existingPrestamo = $entityManager->getRepository(Prestamo::class)
                ->findOneBy([
                    'book' => $prestamo->getBook(),
                    'fechaDevolucion' => null,
                ]);

            if ($existingPrestamo) {
                $student = $existingPrestamo->getStudent();
                $this->addFlash('error', 'Este libro ya ha sido prestado a ' . $student->getNombre() . ' y no ha sido devuelto.');
                return $this->redirectToRoute('app_prestamo');
            }

            // Guardar el préstamo
            $entityManager->persist($prestamo);
            $entityManager->flush();

            $this->addFlash('success', 'Préstamo realizado correctamente');
            return $this->redirectToRoute('app_prestamos_activos');
        }

        return $this->render('prestamo/prestamo.html.twig', [
            'form' => $form->createView(),  // Pasa el formulario a la vista
        ]);
    }

    // Ruta para ver préstamos activos
    #[Route('/prestamos/activos', name: 'app_prestamos_activos')]
    public function prestamosActivos(EntityManagerInterface $entityManager): Response
    {
        // Pagina los resultados si es necesario
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

        // Verificar si el libro ya ha sido devuelto
        if ($prestamo->getFechaDevolucion() !== null) {
            $this->addFlash('error', 'Este libro ya ha sido devuelto.');
            return $this->redirectToRoute('app_prestamos_activos');
        }

        // Asignar la fecha de devolución a la fecha actual
        $fechaDevolucion = new \DateTime();
        $prestamo->setFechaDevolucion($fechaDevolucion);

        $entityManager->flush();

        $this->addFlash('success', 'Libro devuelto correctamente.');
        return $this->redirectToRoute('app_prestamos_activos');
    }

    // Ruta para ver historial de devoluciones
    #[Route('/prestamos/devoluciones', name: 'app_historial_devoluciones')]
    public function historialDevoluciones(EntityManagerInterface $entityManager): Response
    {
        // Obtener los préstamos con fecha de devolución no nula usando DQL
        $query = $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Prestamo p
            WHERE p.fechaDevolucion IS NOT NULL'
        );

        $prestamos = $query->getResult();

        return $this->render('prestamo/historial_devoluciones.html.twig', [
            'prestamos' => $prestamos, // Pasando los préstamos a la plantilla
        ]);
    }

    // Ruta API para obtener el estudiante por cédula
    #[Route('/api/student/{cedula}', name: 'api_student')]
    public function getStudentByCedula($cedula, StudentRepository $studentRepository): JsonResponse
    {
        $student = $studentRepository->findOneBy(['cedula' => $cedula]);

        if ($student) {
            return new JsonResponse([
                'id' => $student->getId(),  // Devuelve el ID del estudiante
                'nombre' => $student->getNombre(),
            ]);
        }

        return new JsonResponse(['error' => 'Estudiante no encontrado'], Response::HTTP_NOT_FOUND);
    }

    // Ruta API para obtener el libro por título
    #[Route('/api/book/{titulo}', name: 'api_book')]
    public function getBookByTitle($titulo, BookRepository $bookRepository): JsonResponse
    {
        // Hacer búsqueda insensible a mayúsculas/minúsculas
        $book = $bookRepository->findOneBy(['title' => strtolower($titulo)]);

        if ($book) {
            return new JsonResponse([
                'id' => $book->getId(),     // Devuelve el ID del libro
                'autor' => $book->getAutor(),
                'genre' => $book->getGenre(),
            ]);
        }

        return new JsonResponse(['error' => 'Libro no encontrado'], Response::HTTP_NOT_FOUND);
    }
}
