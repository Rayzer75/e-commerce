<?php

namespace FS\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$choices = array();
		for ($i = 0; $i <= 5; $i++) {
			$choices[strval($i)] = $i;
		}
		$builder
				->add('mark', ChoiceType::class, array('choices' => $choices, 'label_format' => 'mark'))
				->add('content', TextareaType::class, array('label_format' => 'review.review'))
		;
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'FS\PlatformBundle\Entity\Review',
			'translation_domain' => 'FSPlatformBundle'
		));
	}

}
