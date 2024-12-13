<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book_new')]
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
                return $this->redirectToRoute('app_book_new');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Hubo un error al registrar el libro: ' . $e->getMessage());
            }
        }

        return $this->render('Book/book.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
