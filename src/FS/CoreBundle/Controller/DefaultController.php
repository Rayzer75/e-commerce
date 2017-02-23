<?php

namespace FS\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FS\CoreBundle\Entity\Message;
use FS\CoreBundle\Form\MessageType;
use FS\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller managing the navbar.
 */
class DefaultController extends Controller
{
	/**
	 * Show the 'About us' page.
	 * 
	 * @return Response
	 */
    public function aboutAction()
    {
        return $this->render('FSCoreBundle:Default:about.html.twig');
    }
	
	/**
	 * Contact the webmaster.
	 * 
	 * @param Request $request
	 * @return Response
	 */
	public function contactAction(Request $request) {
		
		$translator = $this->get('translator');
		$em = $this->getDoctrine()->getManager();
		$message = new Message();
		$user = $this->getUser();
		if ($user !== null) {
			$message->setUser($user);
			$message->setName($user->getLastName().' '.$user->getFirstName());
			$message->setEmail($user->getEmail());
		}

		$form = $this->get('form.factory')->create(MessageType::class, $message, array('user' => $user));

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$message->setDate(new \Datetime());

			$em->persist($message);
			$em->flush();

			$request->getSession()->getFlashBag()->add('success', $translator->trans('message.sent', array(), 'FSUserBundle'));

			return $this->redirectToRoute('fs_core_contact');
		}
		
		return $this->render('FSCoreBundle:Default:contact_form.html.twig', array(
			'form' => $form->createView()
		));
	}
	
	/**
	 * Change the locale.
	 * 
	 * @param Request $request
	 */
	public function setLocaleAction(Request $request) {
		$language = $request->get('language');
		$previousLanguage = $request->getLocale();
		$request->setLocale($language);
		$url = $request->headers->get('referer');
		// replaces '/prevLocale/' with '/newLocale/' => very dirty
		return new RedirectResponse(str_replace('/'.$previousLanguage.'/', '/'.$language.'/', $url));
	}

}
