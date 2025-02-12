<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class BookController extends AbstractController
{
    // Ruta para listar libros
    #[Route('/books', name: 'app_books_list')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Obtener el término de búsqueda desde la URL (si existe)
        $search = $request->query->get('search');

        // Construir la consulta para buscar libros por título
        if ($search) {
            $books = $entityManager->getRepository(Book::class)
                ->createQueryBuilder('b')
                ->where('b.title LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->getQuery()
                ->getResult();
        } else {
            // Si no hay búsqueda, obtener todos los libros
            $books = $entityManager->getRepository(Book::class)->findBy([], ['id' => 'ASC']);
        }

        // Renderizar la vista con la lista de libros
        return $this->render('book/booklist.html.twig', [
            'books' => $books,
            'search' => $search, // Pasar el término de búsqueda a la vista
            'selectedGenre' => null // Asegurarse de que 'selectedGenre' esté definido
        ]);
    }

    // Ruta para obtener géneros
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

    // Ruta para listar libros por género en formato JSON
    #[Route('/api/books/genre/{genre}', name: 'app_books_by_genre_json')]
    public function booksByGenreJson(string $genre, EntityManagerInterface $entityManager): JsonResponse
    {
        $books = $entityManager->getRepository(Book::class)
            ->findBy(['genre' => $genre]);

        $data = [];
        foreach ($books as $book) {
            $data[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'autor' => $book->getAutor(),
                'genre' => $book->getGenre()
            ];
        }

        return new JsonResponse($data);
    }

    // Ruta para agregar libros retornando un json
    #[Route('/books/add', name: 'app_book_add_json')]
    public function addBookJson(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $book = new Book();
        $data = $request->request->all();
        $book->setTitle($data['title']);
        $book->setAutor($data['autor']);
        $book->setGenre($data['genre']);
        
        try {
            $entityManager->persist($book);
            $entityManager->flush();

            $response = [
                'status' => 'success',
                'message' => 'Libro agregado correctamente.'
            ];
        } catch (\Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'Error al agregar el libro: ' . $e->getMessage()
            ];
        }

        return new JsonResponse($response);
    }

    // Ruta para editar libros por post retornando un json
    #[Route('/books/edit', name: 'app_book_edit_json')]
    public function editBookJson(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $request->request->all();
        $book = $entityManager->getRepository(Book::class)->find($data['id']);
        if (!$book) {
            $response = [
                'status' => 'error',
                'message' => 'El libro no fue encontrado.'
            ];
        } else {
            $book->setTitle($data['title']);
            $book->setAutor($data['autor']);
            $book->setGenre($data['genre']);

            try {
                $entityManager->persist($book);
                $entityManager->flush();
                $response = [
                    'status' => 'success',
                    'message' => 'Libro actualizado correctamente.'
                ];
            } catch (\Exception $e) {
                $response = [
                    'status' => 'error',
                    'message' => 'Error al actualizar el libro: ' . $e->getMessage()
                ];
            }
        }

        return new JsonResponse($response);
    }

    // Ruta para eliminar libros por post retornando un json 
    #[Route('/books/delete', name: 'app_book_delete_json')]
    public function deleteBookJson(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $request->request->all();
        $book = $entityManager->getRepository(Book::class)->find($data['id']);

        if (!$book) {
            $response = [
                'status' => 'error',
                'message' => 'El libro no fue encontrado.'
            ];
        } else {
            try {
                $entityManager->remove($book);
                $entityManager->flush();
                $response = [
                    'status' => 'success',
                    'message' => 'Libro eliminado correctamente.'
                ];
            } catch (\Exception $e) {
                $response = [
                    'status' => 'error',
                    'message' => 'Error al eliminar el libro: ' . $e->getMessage()
                ];
            }
        }

        return new JsonResponse($response);
    }

    // Ruta para listar libros retornando un json
    #[Route('/books/list', name: 'app_books_list_json')]
    public function listJson(EntityManagerInterface $entityManager): JsonResponse
    {
        // Obtener todos los libros
        $books = $entityManager->getRepository(Book::class)->findBy([], ['id' => 'ASC']);

        // Convertir los datos de los libros a un array
        $data = [];
        if (!$books) {
            return new JsonResponse($data, Response::HTTP_OK);
        }

        foreach ($books as $book) {
            $data[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'autor' => $book->getAutor(),
                'genre' => $book->getGenre()
            ];
        }

        // Crear una respuesta JSON
        return $this->json($data);
    }
}


