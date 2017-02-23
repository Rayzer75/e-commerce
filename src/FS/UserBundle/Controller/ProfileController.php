<?php

namespace FS\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\ProfileController as BaseController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FS\UserBundle\Entity\User;
use FS\UserBundle\Form\ProfileType;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller managing the profile.
 */
class ProfileController extends BaseController {

	/**
	 * Show all the orders made by the customer.
	 * 
	 * @Security("has_role('ROLE_CUSTOMER')")
	 * @param Request $request
	 * @return Response
	 */
	public function viewOrdersAction(Request $request) {

		$listOrderProducts = array();
		$listOrders = array();
		$em = $this->getDoctrine()->getManager();
		$orders = $em
				->getRepository('FSPlatformBundle:Orders')
				->findBy(array('user' => $this->getUser()))
		;
		
		foreach ($orders as $order) {
			$orderProduct = $em->getRepository('FSPlatformBundle:OrderProduct')
					->findBy(array('order' => $order));
			$listOrderProducts[$order->getId()] = $orderProduct;
			$listOrders[$order->getId()] = $order;
		}
		return $this->render("FSUserBundle:Profile:view_orders.html.twig", array(
					'listOrders' => $listOrders,
					'listOrderProducts' => $listOrderProducts
		));
	}
	
	/**
	 * Show all the reviews written by the customer.
	 * 
	 * @Security("has_role('ROLE_CUSTOMER')")
	 * @param Resquest $request
	 * @return Response
	 */
	public function reviewAction(Request $request) {
		
		$em = $this->getDoctrine()->getManager();
		$listReviews = $em->getRepository('FSPlatformBundle:Review')
							->findBy(array('author' => $this->getUser()));
		
		return $this->render("FSUserBundle:Profile:reviews.html.twig", array(
			'listReviews' => $listReviews
		));
	}
	
	/**
	 * Show all the messages received.
	 * 
	 * @Security("has_role('ROLE_ADMIN')")
	 * @return Response
	 */
	public function viewMessagesAction() {
		$em = $this->getDoctrine()->getManager();
		$listMessages = $em->getRepository('FSCoreBundle:Message')->findAll();
		return $this->render('FSUserBundle:Profile:view_messages.html.twig', array(
			'listMessages' => $listMessages
		));
	}
	
	/**
     * Edit the user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory FactoryInterface */
        $formFactory = $this->get('form.factory');

        $form = $formFactory->create(ProfileType::class, $user, array('edit' => true));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var $userManager UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('@FOSUser/Profile/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}
