<?php

namespace FS\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderProductType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {

		$choices = array();
		for ($i = 1; $i <= 10; $i++) {
			$choices[strval($i)] = $i;
		}
		$builder
				->add('quantity', ChoiceType::class, array(
					'choices' => $choices))
				->add('save', SubmitType::class, array('label' => false))
		;
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'FS\PlatformBundle\Entity\OrderProduct'
		));
	}

}
