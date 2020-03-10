<?php

namespace App\Form;

use App\Entity\Tips;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class TipsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titleTips',TextType::class, [
                'label' => 'Le titre du Tips *',
                'required'=>true,
                'attr' => [
                    'placeholder' => 'Saisir le titre ici!'
            ]])
            ->add('keywords',TextType::class, [
                'label' => 'Mots clés *',
                'required'=>true,
                'attr' => [
                    'placeholder' => 'ex : cuisine, casserole, nettoyer'
            ]])
            ->add('contentTips', TextareaType::class,[
                'label' => 'Le tips *',
                'required'=>true,
            ])
            ->add('imageFile', VichImageType::class, [
                'label'=> 'joindre des photos',
                'required' => false,
                'allow_delete' => false,
                'download_label' => '...',
                'download_uri' => false,
                'image_uri' => false,
                'asset_helper' => true,
                
            ])
            ->add('category', EntityType::class, [  
                'label' => 'Catégories *',   
                'class' => Category::class,
                'choice_label' => 'nameCategory',  
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tips::class,
        ]);
    }
}
