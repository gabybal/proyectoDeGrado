<?php

namespace App\Controller;

use App\Entity\Prestamo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Student;
use App\Form\StudentFormType;
use Symfony\Component\HttpFoundation\JsonResponse;

class StudentController extends AbstractController
{
    //Ruta para agregar estudiantes retornando un json
    #[Route('/students/add', name: 'app_student_json')]
    public function addStudentJson(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $student = new Student();
        $data = $request->request->all();
        $student->setNombre($data['nombre']);
        $student->setCedula($data['cedula']);
        
        try {
            $entityManager->persist($student);
            $entityManager->flush();

            $response = [
            'status' => 'success',
            'message' => 'Estudiante agregado correctamente.'
            ];
        } catch (\Exception $e) {
            $response = [
            'status' => 'error',
            'message' => 'Error al agregar el estudiante: ' . $e->getMessage()
            ];
        }

        return new JsonResponse($response);


    }

    //ruta para editar estudiantes por post retornando un json
    #[Route('/students/edit', name: 'app_student_edit_json')]
    public function editStudentJson(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $request->request->all();
        //return new JsonResponse($data);
        $student = $entityManager->getRepository(Student::class)->find($data['id']);
        if (!$student) {
            $response = [
                'status' => 'error',
                'message' => 'El estudiante no fue encontrado.'
            ];
        } else {
            $student->setNombre($data['nombre']);
            $student->setCedula($data['cedula']);

            try {
                $entityManager->persist($student);
                $entityManager->flush();
                $response = [
                    'status' => 'success',
                    'message' => 'Estudiante actualizado correctamente.'
                ];
            } catch (\Exception $e) {
                $response = [
                    'status' => 'error',
                    'message' => 'Error al actualizar el estudiante: ' . $e->getMessage()
                ];
            }
        }

        return new JsonResponse($response);
    }
    //ruta para eliminar estudiantes por post retornando un json 
    #[Route('/students/delete', name: 'app_student_delete_json')]
    public function deleteStudentJson(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $request->request->all();
        $student = $entityManager->getRepository(Student::class)->find($data['id']);

        if (!$student) {
            $response = [
                'status' => 'error',
                'message' => 'El estudiante no fue encontrado.'
            ];
        } else {
            try {
                //valida que no exista en prestamos
                $prestamos = $entityManager->getRepository(Prestamo::class)->findBy(['student' => $student->getId()]);
                if ($prestamos) {
                    $response = [
                        'status' => 'error',
                        'message' => 'El estudiante tiene préstamos asociados.'
                    ];
                    return new JsonResponse($response);
                }
                $entityManager->remove($student);
                $entityManager->flush();
                $response = [
                    'status' => 'success',
                    'message' => 'Estudiante eliminado correctamente.'
                ];
            } catch (\Exception $e) {
                $response = [
                    'status' => 'error',
                    'message' => 'Error al eliminar el estudiante: ' . $e->getMessage()
                ];
            }
        }

        return new JsonResponse($response);
    }

    // Ruta para listar estudiantes retornando un json
    #[Route('/students/list', name: 'app_students_list_json')]
    public function listJson(EntityManagerInterface $entityManager): JsonResponse
    {
        // Obtener todos los estudiantes
        $students = $entityManager->getRepository(Student::class)->findBy([],['id'=>'ASC']);

        // Convertir los datos de los estudiantes a un array
        $data = [];
        if (!$students) {
            return new JsonResponse($data, Response::HTTP_OK);
        }

        foreach ($students as $student) {
            $data[] = [
                'id' => $student->getId(),
                'nombre' => $student->getNombre(),
                'cedula' => $student->getCedula()
            ];
        }

        // Crear una respuesta JSON
        return $this->json($data);
    }

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
            $students = $entityManager->getRepository(Student::class)->findBy([],['id'=>'ASC']);
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

    // Ruta para editar un estudiante
    #[Route('/student/{id}/edit', name: 'app_student_edit')]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
   {
    $student = $entityManager->getRepository(Student::class)->find($id);

    if (!$student) {
        $this->addFlash('error', 'El estudiante no fue encontrado.');
        return $this->redirectToRoute('app_students_list');
    }

    $form = $this->createForm(StudentFormType::class, $student);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        try {
            $entityManager->flush();
            $this->addFlash('success', 'Estudiante actualizado correctamente.');
            return $this->redirectToRoute('app_students_list');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error al actualizar el estudiante: ' . $e->getMessage());
        }
    }

    return $this->render('student/edit.html.twig', [
        'form' => $form->createView(),
        'student' => $student
    ]);
   }

   

}

