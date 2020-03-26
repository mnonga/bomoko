<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array('label' => 'Nom complet', 'required' => true))
            ->add('email', EmailType::class, array('label' => 'Email', 'required' => true))
            ->add('datenaissance', BirthdayType::class, array('label' => 'Date de naissance', 'required' => true))
            ->add('adresse', TextType::class, array('label' => 'Adresse', 'required' => true))
            ->add('sexe', ChoiceType::class, array('label' => 'Sexe', 'required' => true, 'choices' => array('Masculin' => 'M', 'Feminin' => 'F'), 'expanded' => true, 'multiple' => false))
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Veuillez bien confirmer votre mot de passe !',
                'required' => true,
                'first_options' => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Confirmation mot de passe'),
            ))
            ->add('accept_licence', CheckboxType::class, array(
                'label' => 'J\'ai lu, je comprends et j\'accepte les conditions d\'utilisation',
                'required' => false, 'mapped' => false,
                'constraints' => array(new \Symfony\Component\Validator\Constraints\IsTrue(['message' => 'Vous devez accepter les conditions d\'utilisation !']))
            ));
        /*->add('photo',FileType::class, 
            array('label' => 'Choisir votre photo de profile', 'required'=> true, 'mapped'=> true, 'multiple'=> false,
            'attr'=> array('class'=> 'form-control') ));*/
        //      'choices' => array('Admistrateur' => ['ROLE_ADMIN'], 'Utilisateur normal' => ['ROLE_USER'], 'Bussiness Man' => ['ROLE_BUSINESSMAN'])));

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'userbundle_user';
    }


}
