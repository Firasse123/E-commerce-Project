<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CheckoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('deliveryAddress', TextareaType::class, [
                'label' => 'Adresse de livraison',
                'attr' => ['class' => 'form-control', 'rows' => 4],
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse de livraison est obligatoire'])
                ]
            ])
            ->add('deliveryFirstName', TextType::class, [
                'label' => 'Prénom de livraison',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                new NotBlank(['message' => 'Le prénom est obligatoire'])
            ]
            ])

            ->add('deliveryLastName', TextType::class, [
            'label' => 'Nom de livraison',
            'attr' => ['class' => 'form-control'],
            'constraints' => [
                new NotBlank(['message' => 'Le nom est obligatoire'])
            ]
            ])

            ->add('deliveryAddress', TextareaType::class, [
            'label' => 'Adresse de livraison',
            'attr' => ['class' => 'form-control', 'rows' => 4],
            'constraints' => [
                new NotBlank(['message' => 'L\'adresse de livraison est obligatoire'])
            ]
            ])

            ->add('deliveryCity', TextType::class, [
            'label' => 'Ville de livraison',
            'attr' => ['class' => 'form-control'],
            'constraints' => [
                new NotBlank(['message' => 'La ville de livraison est obligatoire'])
            ]
            ])

            ->add('deliveryPhone', TextType::class, [
                'label' => 'Téléphone',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro de téléphone est obligatoire'])
                ]
            ])
            ->add('deliveryZipCode', TextType::class, [
            'label' => 'Code postal',
            'attr' => ['class' => 'form-control'],
            'constraints' => [
                new NotBlank(['message' => 'Le code postal est obligatoire'])
            ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}