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
            ->add('DeliveryAddress', TextareaType::class, [
                'label' => 'Adresse de livraison',
                'attr' => ['class' => 'form-control', 'rows' => 4],
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse de livraison est obligatoire'])
                ]
            ])
            ->add('billingAddress', TextareaType::class, [
                'label' => 'Adresse de facturation',
                'attr' => ['class' => 'form-control', 'rows' => 4],
                'required' => false,
                'help' => 'Laissez vide pour utiliser l\'adresse de livraison'
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro de téléphone est obligatoire'])
                ]
            ])
            ->add('deliveryFirstName', TextType::class, [
            'label' => 'Prénom',
            'required' => true,
            'attr' => ['class' => 'form-control'],
            'constraints' => [
                new NotBlank(['message' => 'Le prénom de livraison est obligatoire']),
            ],
        ])
    // ... add other delivery fields here (last name, address, city, etc.)
    ->add('deliveryLastName', TextType::class, [
        'label' => 'Nom',
        'required' => true,
        'attr' => ['class' => 'form-control'],
        'constraints' => [
            new NotBlank(['message' => 'Le nom de livraison est obligatoire']),
        ],
    ])
    ->add('deliveryAddress', TextareaType::class, [
        'label' => 'Adresse',
        'required' => true,
        'attr' => ['class' => 'form-control', 'rows' => 3],
        'constraints' => [
            new NotBlank(['message' => 'L\'adresse de livraison est obligatoire']),
        ],
    ])
    ->add('deliveryCity', TextType::class, [
        'label' => 'Ville',
        'required' => true,
        'attr' => ['class' => 'form-control'],
        'constraints' => [
            new NotBlank(['message' => 'La ville de livraison est obligatoire']),
        ],
    ])
    ->add('deliveryPostalCode', TextType::class, [
        'label' => 'Code Postal',
        'required' => true,
        'attr' => ['class' => 'form-control'],
        'constraints' => [
            new NotBlank(['message' => 'Le code postal de livraison est obligatoire']),
        ],
    ])
    ->add('notes', TextareaType::class, [
        'label' => 'Notes de commande',
        'required' => false,
        'attr' => ['class' => 'form-control', 'rows' => 3]
    ]);
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}