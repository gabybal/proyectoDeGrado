<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Prestamo;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class PrestamoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
                'choice_label' => 'cedula', // Mostrar la cédula en lugar del nombre
                'label' => false, // No mostrar etiqueta
                'required' => true,
                'attr' => [
                    'class' => 'student-search',
                    'data-placeholder' => 'Escribe la C.I. del estudiante...',
                ],
            ])
            ->add('book', EntityType::class, [
                'class' => Book::class,
                'choice_label' => 'title', // Mostrar el título del libro
                'label' => false, // No mostrar etiqueta
                'required' => true,
                'attr' => [
                    'class' => 'book-search',
                    'data-placeholder' => 'Escribe el título del libro...',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Prestamo::class,
        ]);
    }
}

