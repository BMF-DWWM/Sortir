<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,[])
            ->add('dateHeureDebut', DateTimeType::class,[
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('duree')
//            ->add('dateLimiteInscription')
            ->add('nbInscriptionsMax',null,[
                'required' => false
            ])
            ->add('infosSortie',null, [
                'required' => false
            ])
            ->add('etat', EntityType::class, [
                'class'=> Etat::class,
                'choice_label'=> 'libelle',
            ])
//            ->add('Lieu')
//            ->add('campus')
//            ->add('membreInscrit')
//            ->add('organisateur')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
