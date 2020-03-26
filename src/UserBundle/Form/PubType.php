<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PubType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('titre', TextType::class, array('label' => 'Titre de la Publication', 'required'=> true ))
        ->add('contenu', TextareaType::class, array('label' => 'Contenu de la Publication', 'required'=> true, 'attr'=>['rows'=>10] ))
        ->add('categorie', EntityType::class, array('class'=>'UserBundle\Entity\Categorie', 'choice_label' => 'nom',  'expanded' => true, 'multiple' => false, 'label' => 'Categorie de la Publication', 'required'=> true ))
        ->add('uploadedFiles', FileType::class, array('label' => 'Ajouter des images à joindre à votre Publication',
            'required'=> false, 'multiple'=> true, 'attr'=> array('class'=> 'form-control', 'accept'=>'image/*'),
            'data_class'=>null ));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\Pub',
            'allow_extra_fields'=>true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'userbundle_pub';
    }


}
