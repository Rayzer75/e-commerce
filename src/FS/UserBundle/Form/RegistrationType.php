<?php

namespace FS\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
				->add('lastname', TextType::class, ['label_format' => 'name.last'])
				->add('firstname', TextType::class, ['label_format' => 'name.first'])
				->add('address', TextType::class, ['label_format' => 'address'])
		;
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'FS\UserBundle\Entity\User',
			'translation_domain' => 'FSUserBundle'
		));
	}

	public function getParent() {
		return 'FOS\UserBundle\Form\Type\RegistrationFormType';
	}

	public function getBlockPrefix()
    {
        return 'fs_user_registration';
    }

}
