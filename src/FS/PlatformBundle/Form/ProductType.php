<?php

namespace FS\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use FS\PlatformBundle\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		
		// the admin can't edit neither the name nor the category
		if ($options['edit']) {
			$builder->add('name', TextType::class, array(
						'disabled' => true,
						'label_format' => 'product.name'
					))
					->add('category', EntityType::class, array(
						'class' => 'FSPlatformBundle:Category',
						'choice_label' => 'name',
						'multiple' => false,
						'label_format' => 'product.category',
						'disabled' => true
					))
					;
		} else {
			$builder->add('name', TextType::class, array(
						'label_format' => 'product.name'
					))
					->add('category', EntityType::class, array(
						'class' => 'FSPlatformBundle:Category',
						'choice_label' => 'name',
						'multiple' => false,
						'label_format' => 'product.category'
			));
		}
		$builder
				->add('price', NumberType::class, array('label_format' => 'product.price'))
				->add('description', TextareaType::class, array('label_format' => 'product.description'))
				->add('image', ImageType::class)
				->add('save', SubmitType::class, array('label_format' => 'product.submit'));
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'FS\PlatformBundle\Entity\Product',
			'edit' => false,
			'translation_domain' => 'FSPlatformBundle'
		));
	}

}
