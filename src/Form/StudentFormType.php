<?php

namespace App\Form;

use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;


class StudentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('cedula', TextType::class, [
            'label' => 'Número de Cédula',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 10]),
                new Assert\Regex([
                    'pattern' => '/^\d{10}$/',
                    'message' => 'El número de cédula debe tener el formato XXXXXXXXXX.',
                ]),
            ],
        ])
        ->add('nombre', TextType::class, [
            'label' => 'Nombre del Estudiante',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(['max' => 255]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
