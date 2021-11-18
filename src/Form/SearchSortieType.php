<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class,[
                    'class'=> Campus::class,
                    'choice_label'=> 'nom',
                ]
            )
            ->add('mots', SearchType::class,[
                'label'=> false,
                'attr'=> [
                    'class' => 'form-control',
                    'placeholder' => 'search'
                ],
                'required'=>false
            ])
            ->add('date1', DateTimeType::class,[
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'Entre',
                'required'=>false

            ])
            ->add('date2', DateTimeType::class,[
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'Entre',
                'required'=>false

            ])
            ->add('Rerchercher', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
