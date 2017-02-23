<?php

namespace FS\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FS\PlatformBundle\Entity\Review;
use FS\PlatformBundle\Form\ReviewType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller managing the reviews.
 */
class ReviewController extends Controller {
	
	/**
	 * Add a review for the product.
	 * 
	 * @Security("has_role('ROLE_CUSTOMER')")
	 * @param Request $request
	 * @param integer $id		Product id
	 * @return Response			Rendered form
	 * @throws NotFoundHttpException
	 */
	public function addAction(Request $request, $id) {

		$em = $this->getDoctrine()->getManager();
		$review = new Review();
		$repository = $this->getDoctrine()
				->getManager()
				->getRepository('FSPlatformBundle:Product')
		;
		$product = $repository->find($id);
		
		$translator = $this->get('translator');
		if ($product === null) {
			throw new NotFoundHttpException($translator->trans('product.not_found', array('%id' => $id), 'FSPlatformBundle'));
		}

		$form = $this->get('form.factory')->create(ReviewType::class, $review, array(
			'action' => $this->generateUrl('fs_platform_review_add', array('id' => $id)),
			'method' => 'POST'
		));

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

			$user = $this->getUser();
			$review->setAuthor($user);
			$review->setDate(new \Datetime());
			$review->setProduct($product);

			$em->persist($review);
			$em->flush();

			$request->getSession()->getFlashBag()->add('success', $translator->trans('review.added', array(), 'FSPlatformBundle'));

			return $this->redirectToRoute('fs_platform_view', array('id' => $product->getId()));
		}

		return $this->render('FSPlatformBundle:Review:review_form.html.twig', array(
					'form' => $form->createView()
		));
	}

	/**
	 * Edit a review.
	 * 
	 * @Security("has_role('ROLE_CUSTOMER')")
	 * @param integer $id	Review id
	 * @param Request $request
	 * @return Response		Rendered page
	 * @throws NotFoundHttpException
	 * @throws AccessDeniedException
	 */
	public function editAction($id, Request $request) {

		$em = $this->getDoctrine()->getManager();
		$review = $em->getRepository('FSPlatformBundle:Review')->find($id);
		
		$translator = $this->get('translator');
		if ($review === null) {
			throw new NotFoundHttpException($translator->trans('review.not_found', array('%id%' => $id), 'FSPlatformBundle'));
		}
		
		// if the current user is not the review's author
		if ($review->getAuthor() != $this->getUser()) {
			throw $this->createAccessDeniedException($translator->trans('review.denied_access', array('%id%' => $id), 'FSPlatformBundle'));
		}
		
		$form = $this->get('form.factory')->create(ReviewType::class, $review);

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$em->flush();
			$request->getSession()->getFlashBag()->add('success', $translator->trans('review.updated', array(), 'FSPlatformBundle'));

			return $this->redirectToRoute('fs_platform_view', array('id' => $review->getProduct()->getId()));
		}

		return $this->render('FSPlatformBundle:Review:edit.html.twig', array(
					'review' => $review,
					'form' => $form->createView(),
		));
	}
	
	/**
	 * Delete a review
	 * 
	 * @Security("has_role('ROLE_CUSTOMER')")
	 * @param integer $id	Review id
	 * @param Request
	 * @throws NotFoundHttpException
	 * @throws AccessDeniedException
	 */
	public function deleteAction($id, Request $request) {
		$em = $this->getDoctrine()->getManager();
		$review = $em->getRepository('FSPlatformBundle:Review')->find($id);
		$translator = $this->get('translator');
		if ($review === null) {
			throw new NotFoundHttpException($translator->trans('review.not_found', array('%id%' => $id), 'FSPlatformBundle'));
		}
		if ($review->getAuthor() != $this->getUser()) {
			throw $this->createAccessDeniedException($translator->trans('product.access_denied', array('%id%' => $id), 'FSPlatformBundle'));
		}

		$form = $this->get('form.factory')->create();

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$em->remove($review);
			$em->flush();

			$request->getSession()->getFlashBag()->add('success', $translator->trans('review.deleted', array(), 'FSPlatformBundle'));

			return $this->redirectToRoute('fs_platform_home');
		}

		return $this->render('FSPlatformBundle:Review:delete.html.twig', array(
					'review' => $review,
					'form' => $form->createView(),
		));
	}

}
