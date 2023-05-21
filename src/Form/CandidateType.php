<?php

namespace App\Form;

use App\Entity\Candidate;
use App\Entity\Degree;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\File;


class CandidateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Lastname is required.',
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Lastname must not exceed {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('firstName' , TextType::class, [
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Firstname is required.',
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Firstname must not exceed {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('email', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Lastname is required.',
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Lastname must not exceed {{ limit }} characters.',
                    ]),
                    new Email([
                        'message' => 'Please enter a valid email address.',
                    ]),
                ],
            ])
            ->add('mobile', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'max' => 10,
                        'minMessage' => 'Mobile must not be less than {{ limit }} characters.',
                        'maxMessage' => 'Mobile must not exceed {{ limit }} characters.',
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]+$/',
                        'message' => 'Please check the inserted phone.',
                    ]),
                ],
            ])
            ->add('resume', FileType::class, ['label' => 'Resume', 
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'data_class' => null,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                                // Add more allowed MIME types if necessary
                        ],
                        'mimeTypesMessage' => 'Resume must be in word or pdf format.',
                    ])
                ]
            ])
            // ->add('applicationDate', DateType::class , [
            //     'widget' => 'single_text',
            //     'html5' => false,
            //     'data' => new \DateTime(),
            //     'attr' => [
            //         'readonly' => true
            //     ],
            //     'format' => 'dd/MM/yyyy',
            //     'label' => 'Application Date :',
                
            // ]) 
            //->add('degree')
            ->add('degree', EntityType::class, [
                'attr' => ['class' => 'form-control'],
                'class' => Degree::class,
                'required' => false,
                'placeholder' => '(Not Selected)',
                'choice_label' => function( Degree $degree ){
                    return $degree->getDegreeTitle();
                },
                // Other options as needed
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidate::class,
        ]);
    }
}
