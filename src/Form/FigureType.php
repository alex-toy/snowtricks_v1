<?php

namespace App\Form;

use App\Entity\Figure;
use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;




class FigureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',     TextType::class)
    		->add('description',   TextareaType::class)
    		->add('difficulty', ChoiceType::class, array(
    			'choices'  => array(
        		'1' => 1,
        		'2' => 2,
        		'3' => 3,
        		'4' => 4,
        		'5' => 5,
        		'6' => 6,
        		'7' => 7,
        		'8' => 8,
        		'9' => 9,
        		'10' => 10,
    		),
			))
			
			//->add('images', FileType::class)
			
			
			//->add('images', FileType::class, array('multiple' => true,'data_class' => null))
			
			
			// ->add('images', EntityType::class, array(
//                             'class' => 'App\Entity\Image',
//                             'choice_label' => 'images',
//                             'multiple' => true,
//                     ))


			->add('uploadedFiles', FileType::class, array(
            	'multiple' => true, 
            	'data_class' => null,
        	))
			
			
			->add('save', SubmitType::class, array('label' => 'Create figure'));
    }

    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Figure::class,
        ));
    }
    
}


?>













