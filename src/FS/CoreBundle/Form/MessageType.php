<?php

namespace FS\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
class MessageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['user'] === null) {
			$builder->add('name', TextType::class, array('label_format' => 'message.name'))
					->add('email', TextType::class, array('label_format' => 'message.email'))
			;
		} else {
			$builder->add('name', TextType::class, array('attr' => array('readonly' => true), 'label_format' => 'message.name'))
					->add('email', TextType::class, array('attr' => array('readonly' => true), 'label_format' => 'message.email'))
			;
		}
		$builder->add('subject', TextType::class, array('label_format' => 'message.subject'))
				->add('content', TextareaType::class, array('label_format' => 'message.content'))
				->add('save', SubmitType::class, array('label_format' => 'message.submit'))
		;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FS\CoreBundle\Entity\Message',
			'user' => null,
			'translation_domain' => 'FSCoreBundle'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'fs_corebundle_message';
    }


}
