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
            ->add('nombre', TextType::class, [
                'label' => 'Nombre del Estudiante',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'El nombre no puede estar vacío.']),
                    new Assert\Length(['max' => 255, 'maxMessage' => 'El nombre no puede tener más de 255 caracteres.']),
                ],
            ])
            ->add('cedula', TextType::class, [
                'label' => 'Cédula del Estudiante',
                'constraints' => [
                    // Validaciones genéricas para longitud y campos vacíos
                    new Assert\NotBlank(['message' => 'La cédula no puede estar vacía.']),
                    new Assert\Length([
                        'min' => 10,
                        'max' => 10,
                        'exactMessage' => 'La cédula debe tener exactamente 10 dígitos.',
                    ]),
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
