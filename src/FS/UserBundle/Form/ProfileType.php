<?php

namespace FS\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\UserBundle\Util\LegacyFormHelper;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ProfileType extends AbstractType {
	
	// An user can't edit his username, last/first name
	public function buildForm(FormBuilderInterface $builder, array $options) {

		$constraintsOptions = array(
			'message' => 'fos_user.current_password.invalid',
		);

		if (!empty($options['validation_groups'])) {
			$constraintsOptions['groups'] = array(reset($options['validation_groups']));
		}

		$builder->add('username', TextType::class, array(
					'label' => 'form.username',
					'translation_domain' => 'FOSUserBundle',
					'disabled' => $options['edit']
				))
				->add('email', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\EmailType'), array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
				->add('current_password', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\PasswordType'), array(
					'label' => 'form.current_password',
					'translation_domain' => 'FOSUserBundle',
					'mapped' => false,
					'constraints' => new UserPassword($constraintsOptions),
				))
				->add('lastname', TextType::class, array('label_format' => 'name.last', 'disabled' => $options['edit']))
				->add('firstname', TextType::class, array('label_format' => 'name.first', 'disabled' => $options['edit']))
				->add('address', TextType::class, ['label_format' => 'address'])
		;
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'FS\UserBundle\Entity\User',
			'translation_domain' => 'FSUserBundle',
			'edit' => false
		));
	}

	public function getBlockPrefix() {
		return 'fs_user_profile';
	}

}
