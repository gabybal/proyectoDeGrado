<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Student;
use App\Form\StudentFormType;

class StudentController extends AbstractController
{
    // Ruta para listar estudiantes
    #[Route('/students', name: 'app_students_list')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Obtener el término de búsqueda desde la URL (si existe)
        $search = $request->query->get('search');

        // Construir la consulta para buscar estudiantes por nombre o cédula
        if ($search) {
            $students = $entityManager->getRepository(Student::class)
                ->createQueryBuilder('s')
                ->where('s.nombre LIKE :search OR s.cedula LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->getQuery()
                ->getResult();
        } else {
            // Si no hay búsqueda, obtener todos los estudiantes
            $students = $entityManager->getRepository(Student::class)->findAll();
        }

        // Renderizar la vista con la lista de estudiantes
        return $this->render('student/studentlist.html.twig', [
            'students' => $students,
        ]);
    }

    // Ruta para agregar estudiantes
    #[Route('/student', name: 'app_student')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $student = new Student();
        $form = $this->createForm(StudentFormType::class, $student);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($student);
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Estudiante registrado correctamente.');
                return $this->redirectToRoute('app_students_list');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error al guardar el estudiante: ' . $e->getMessage());
            }
        }

        return $this->render('student/student.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Ruta para eliminar un estudiante
    #[Route('/student/delete/{id}', name: 'app_student_delete')]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $student = $entityManager->getRepository(Student::class)->find($id);

        if (!$student) {
            $this->addFlash('error', 'El estudiante no fue encontrado.');
        } else {
            try {
                $entityManager->remove($student);
                $entityManager->flush();
                $this->addFlash('success', 'Estudiante eliminado correctamente.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error al eliminar el estudiante: ' . $e->getMessage());
            }
        }

        return $this->redirectToRoute('app_students_list');
    }
}

