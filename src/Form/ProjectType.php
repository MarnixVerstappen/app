<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType; // Handmatig toevoegen
use Symfony\Component\Form\Extension\Core\Type\SubmitType; // Handmatig toevoegen
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File; // Handmatig toevoegen

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Projectnaam',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Startdatum',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            // DIT GEDEELTE MOET JE ALTIJD ZELF TOEVOEGEN (want mapped => false)
            ->add('excelFile', FileType::class, [
                'label' => 'Upload Excel (Vuris format)',
                'mapped' => false,
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel.sheet.macroEnabled.12',
                        ],
                    ])
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Project Starten',
                'attr' => ['class' => 'btn btn-primary w-100 mt-3'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}