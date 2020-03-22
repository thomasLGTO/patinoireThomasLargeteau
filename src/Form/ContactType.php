<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,[
                'label' => 'Votre nom',
                'required'=>true,
            ])
            ->add('email', EmailType::class,[
                'label' => 'Votre email',
                'required'=>true,
            ])
            ->add('message', TextareaType::class,[
                'label' => 'Votre message',
                'required'=>true,
                'constraints' => [
                    new Length([
                        'min' => 20,
                        'minMessage' => 'Votre message est trop court',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
            ]])
            ->add('envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}