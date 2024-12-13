<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Student;
use App\Form\StudentFormType;


class StudentController extends AbstractController
{
    #[Route('/student', name: 'app_Student')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $student = new Student();
        $form = $this->createForm(StudentFormType::class, $student);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          $entityManager->persist($student);
          $entityManager->flush();

          $this->addFlash('success', 'Estudiante registrado correctamente.');
          return $this->redirectToRoute('app_Student');
        }
        return $this->render('Student/student.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
