<?php
// src/Controller/BookController.php
namespace App\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class BookController extends AbstractController
{
    #[Route('/books/add', name: 'app_book_add', methods: ['POST'])]
    public function addBook(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $request->request->all();
        $book = new Book();
        $book->setTitle($data['title']);
        $book->setAutor($data['autor']);
        $book->setGenre($data['genre']);

        try {
            $entityManager->persist($book);
            $entityManager->flush();

            return new JsonResponse(['status' => 'success', 'message' => 'Book added successfully.']);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => 'Error adding book: ' . $e->getMessage()]);
        }
    }

    #[Route('/books/edit', name: 'app_book_edit', methods: ['POST'])]
    public function editBook(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $request->request->all();
        $book = $entityManager->getRepository(Book::class)->find($data['id']);

        if (!$book) {
            return new JsonResponse(['status' => 'error', 'message' => 'Book not found.']);
        }

        $book->setTitle($data['title']);
        $book->setAutor($data['autor']);
        $book->setGenre($data['genre']);

        try {
            $entityManager->persist($book);
            $entityManager->flush();

            return new JsonResponse(['status' => 'success', 'message' => 'Book updated successfully.']);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => 'Error updating book: ' . $e->getMessage()]);
        }
    }

    #[Route('/books/delete', name: 'app_book_delete', methods: ['POST'])]
    public function deleteBook(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $request->request->all();
        $book = $entityManager->getRepository(Book::class)->find($data['id']);

        if (!$book) {
            return new JsonResponse(['status' => 'error', 'message' => 'Book not found.']);
        }

        try {
            $entityManager->remove($book);
            $entityManager->flush();

            return new JsonResponse(['status' => 'success', 'message' => 'Book deleted successfully.']);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => 'Error deleting book: ' . $e->getMessage()]);
        }
    }

    #[Route('/books/list', name: 'app_books_list', methods: ['GET'])]
    public function listBooks(EntityManagerInterface $entityManager): JsonResponse
    {
        $books = $entityManager->getRepository(Book::class)->findAll();
        $data = [];

        foreach ($books as $book) {
            $data[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'autor' => $book->getAutor(),
                'genre' => $book->getGenre(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/books/genres', name: 'app_books_genres', methods: ['GET'])]
    public function listGenres(EntityManagerInterface $entityManager): JsonResponse
    {
        $books = $entityManager->getRepository(Book::class)->findAll();
        $genres = [];

        foreach ($books as $book) {
            $genres[] = $book->getGenre();
        }

        $uniqueGenres = array_unique($genres);

        return new JsonResponse($uniqueGenres);
    }

    #[Route('/books/view', name: 'app_books_view', methods: ['GET'])]
    public function viewBooks(): Response
    {
        return $this->render('book/booklist.html.twig');
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
    
    #[Route('/books/search', name: 'app_book_search')]
    public function searchBook(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $titulo = $request->query->get('title');
        $books = $entityManager->getRepository(Book::class)->createQueryBuilder('b')
            ->where('b.title LIKE :titulo')
            ->setParameter('titulo', '%' . $titulo . '%')
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($books as $book) {
            $data[] = [
                'id' => $book->getId(),
                'titulo' => $book->getTitle(),
            ];
        }

        return new JsonResponse($data);
    }
}