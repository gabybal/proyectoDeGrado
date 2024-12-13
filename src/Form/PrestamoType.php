<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Prestamo;
use App\Entity\student;
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
        $builder
            ->add('fechaPrestamo', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Fecha del Préstamo',
                'required' => true,
            ])
            ->add('fechaDevolucion', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Fecha de Devolución',
                'required' => false, // Opcional
            ])
            ->add('student', TextType::class, [
                'label' => 'Número de Cédula',
                'required' => true,
                'attr' => ['class' => 'student-search'],
            ])
            ->add('book', EntityType::class, [
                'class' => Book::class,
                'choice_label' => 'title', // Título del libro
                'label' => 'Libro',
                'placeholder' => 'Seleccione un libro',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Prestamo::class,
        ]);
    }
}