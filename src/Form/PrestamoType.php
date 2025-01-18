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
                'attr' => ['class' => 'student-search', 'data-url' => '/api/student'], // Añadimos data-url para AJAX
            ])
            ->add('book', TextType::class, [
                'label' => 'Título del Libro',
                'required' => true,
                'attr' => ['class' => 'book-search', 'data-url' => '/api/book'], // Añadimos data-url para AJAX
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Prestamo::class,
        ]);
    }
}
