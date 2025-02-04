<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookFormType;
use Symfony\Component\HttpFoundation\JsonResponse; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class BookController extends AbstractController
{
    // Ruta para listar libros
    #[Route('/books', name: 'app_books_list')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        // Obtener el término de búsqueda
        $search = $request->query->get('search', '');

        // Construir la consulta de búsqueda
        $queryBuilder = $entityManager->getRepository(Book::class)->createQueryBuilder('b');

        if ($search) {
            $queryBuilder->andWhere('b.title LIKE :search OR b.autor LIKE :search OR b.genre LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $books = $queryBuilder->getQuery()->getResult();

        return $this->render('book/booklist.html.twig', [
            'books' => $books,
            'search' => $search, // Pasar el término de búsqueda a la vista
        ]);
    }

    // Ruta para agregar libros
    #[Route('/book', name: 'app_book')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = new Book();
        $form = $this->createForm(BookFormType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($book);
                $entityManager->flush();
                $this->addFlash('success', 'Libro registrado correctamente.');
                return $this->redirectToRoute('app_books_list');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Hubo un error al registrar el libro: ' . $e->getMessage());
            }
        }

        return $this->render('book/book.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Ruta para eliminar un libro
    #[Route('/book/delete/{id}', name: 'app_book_delete')]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            $this->addFlash('error', 'El libro no fue encontrado.');
        } else {
            try {
                $entityManager->remove($book);
                $entityManager->flush();
                $this->addFlash('success', 'Libro eliminado correctamente.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error al eliminar el libro: ' . $e->getMessage());
            }
        }

        return $this->redirectToRoute('app_books_list');
    }
    // Ruta para editar un libro
    #[Route('/book/edit/{id}', name: 'app_book_edit')]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
    $book = $entityManager->getRepository(Book::class)->find($id);

    if (!$book) {
        $this->addFlash('error', 'El libro no fue encontrado.');
        return $this->redirectToRoute('app_books_list');
    }

    $form = $this->createForm(BookFormType::class, $book);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        try {
            $entityManager->flush();
            $this->addFlash('success', 'Libro actualizado correctamente.');
            return $this->redirectToRoute('app_books_list');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error al actualizar el libro: ' . $e->getMessage());
        }
    }

    return $this->render('book/book.html.twig', [
        'form' => $form->createView(),
        'editMode' => true, // Variable para indicar que se está editando
    ]);
    }

    //Agregar las categorias
    #[Route('/api/genres', name: 'app_genres_list')]
    public function getGenres(EntityManagerInterface $entityManager): JsonResponse
    {
    $genres = $entityManager->getRepository(Book::class)
        ->createQueryBuilder('b')
        ->select('DISTINCT b.genre')
        ->getQuery()
        ->getResult();

    return $this->json($genres);
    }

    #[Route('/books/genre/{genre}', name: 'app_books_by_genre')]
    public function booksByGenre(string $genre, EntityManagerInterface $entityManager): Response
    {
       $books = $entityManager->getRepository(Book::class)
        ->findBy(['genre' => $genre]);

       return $this->render('book/booklist.html.twig', [
        'books' => $books,
        'selectedGenre' => $genre,
        'search' => '' // Agregar 'search' vacío para evitar el error
       ]);
    }



}


