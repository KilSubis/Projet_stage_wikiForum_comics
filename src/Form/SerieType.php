<?php

namespace App\Form;

use App\Entity\Comics;
use App\Entity\Series;
use App\Repository\ComicsRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('Nom', TextType::class, [
            'attr' => [
                'class' => 'form-control',
                'minlength' => '2',
                'maxlength' => '50',

            ],
            'label' => 'Nom',
            'label_attr' => [
                'class' => 'form-label mt-4'
            ],
            'constraints' => [
                new Assert\Length(['min' =>2, 'max' => 50]),
                new Assert\NotBlank()
            ]
        ])
            ->add('Annee', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1903,
                    'max' => 2023
                ],
                'label' => 'Année',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\Positive(),
                    new Assert\NotNull(),
                    new Assert\Range(
                        min: 1903 ,
                        max: 2023,
                        notInRangeMessage: 'You must be between {{ min }}cm and {{ max }}cm tall to enter',
                    )

                ]
            ])
            ->add('NbComics', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'max' => 1000
                ],
                'label' => 'NbComics',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\PositiveOrZero(),
                    new Assert\LessThan(1000),
                ]
            ])
            ->add('Description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '1',
                    'maxlength' => '200',
    
                ],
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('isFavorite', CheckboxType::class, [
                'attr' => [
                    'class' => 'form-check',
                ],
                'required' => true,
                'label' => 'Favori ?',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotNull()
                ]
            ])
            ->add('Comics', EntityType::class, [
                'class' => Comics::class,
                'label' => 'Comics : ',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'query_builder' => function (ComicsRepository $r) {
                    return $r->createQueryBuilder('i')
                        ->orderBy('i.Nom', 'ASC');
                },
                'choice_label' => 'Nom',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary mt-4',
                ],
                'label' => 'Ajouter la serie'
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Series::class,
        ]);
    }
}
