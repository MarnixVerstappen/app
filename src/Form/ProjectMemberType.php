<?php

namespace App\Form;

use App\Entity\ProjectMember;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectMemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstname', // Dit laat de voornaam zien in de lijst
                'label' => 'Selecteer Medewerker',
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Kies iemand...',
            ])
            ->add('vurisRole', ChoiceType::class, [
                'label' => 'Rol in Project (VURIS)',
                'choices'  => [
                    'Verantwoordelijk (V)' => 'V',
                    'Uitvoerend (U)'       => 'U',
                    'Raadplegend (R)'      => 'R',
                    'Informerend (I)'      => 'I',
                    'Support (S)'          => 'S',
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Toevoegen aan Team',
                'attr' => ['class' => 'btn btn-success w-100 mt-3'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectMember::class,
        ]);
    }
}