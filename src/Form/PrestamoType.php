<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Prestamo;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType; 


class PrestamoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $studentUrl = $options['student_autocomplete_url'] ?? null;  // Obtener URL pasada como opción
        $bookUrl = $options['book_autocomplete_url'] ?? null;  // Obtener URL pasada como opción
    
        $builder
            ->add('fechaPrestamo', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Fecha del Préstamo',
                'required' => true,
            ])
            ->add('fechaDevolucion', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Fecha de Devolución',
                'required' => false,
            ])
            ->add('student', EntityType::class, [
                'class' => Student::class,
                'choice_label' => 'nombre',
                'label' => 'Estudiante',
                'placeholder' => 'Buscar por C.I...',
                'required' => true,
                'attr' => [
                    'class' => 'student-search',
                    'data-autocomplete-url' => $studentUrl // Pasas la URL aquí
                ],
            ])
            ->add('book', EntityType::class, [
                'class' => Book::class,
                'choice_label' => 'title',
                'label' => 'Libro',
                'placeholder' => 'Buscar por titulo...',
                'required' => true,
                'attr' => [
                    'class' => 'book-search',
                    'data-autocomplete-url' => $bookUrl // Pasas la URL aquí
                ],
            ]);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Prestamo::class,
            'student_autocomplete_url' => null, // Agregar una opción para las URLs
            'book_autocomplete_url' => null,    // Agregar una opción para las URLs
        ]);
    }
}    