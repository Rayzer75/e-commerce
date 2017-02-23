<?php

namespace FS\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FS\PlatformBundle\Entity\Product;
use FS\PlatformBundle\Entity\Review;
use FS\PlatformBundle\Form\ProductType;
use FS\PlatformBundle\Form\ReviewType;
use Symfony\Component\HttpFoundation\Response;

//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Controller managing the product.
 */
class ProductController extends Controller {

	/**
	 * Returns the page with all the available products in category $pages.
	 * 
	 * @param integer $page	Number of the page (which is the category's id)
	 * @return Response		Rendered page
	 * @throws NotFoundHttpException	
	 */
	public function indexAction($page) {

		$translator = $this->get('translator');
		// A category can't be negative or null
		if ($page < 1) {
			throw $this->createNotFoundException($translator->trans('page.not_found', array('%page%' => $page), 'FSPlatformBundle'));
		}

		$em = $this->getDoctrine()->getManager();

		$listProducts = $em
				->getRepository('FSPlatformBundle:Product')
				->findBy(array('category' => $page, 'available' => true))
		;

		return $this->render('FSPlatformBundle:Product:index.html.twig', array(
					'page' => $page,
					'listProducts' => $listProducts
		));
	}

	/**
	 * Returns the page with all the details on the product which id is $id.
	 * 
	 * @param Request $request
	 * @param integer $id	Product id
	 * @return Response		Rendered page
	 * @throws NotFoundHttpException
	 */
	public function viewAction(Request $request, $id) {

		$repository = $this->getDoctrine()
				->getManager()
				->getRepository('FSPlatformBundle:Product')
		;
		$product = $repository->find($id);

		$translator = $this->get('translator');
		// If the product doesn't exist
		if ($product === null) {
			throw new NotFoundHttpException($translator->trans('product.not_found', array('%id%' => $id), 'FSPlatformBundle'));
		}

		$em = $this->getDoctrine()->getManager();
		$listReviews = $em
				->getRepository('FSPlatformBundle:Review')
				->findBy(array('product' => $product))
		;

		return $this->render('FSPlatformBundle:Product:view.html.twig', array(
					'product' => $product,
					'listReviews' => $listReviews
		));
	}

	/**
	 * Add a product.
	 * 
	 * @Security("has_role('ROLE_ADMIN')")
	 * @param Request $request
	 * @return Response		Rendered page
	 */
	public function addAction(Request $request) {

		$product = new Product();

		$form = $this->get('form.factory')->create(ProductType::class, $product);

		if ($request->isMethod('POST')) {

			$form->handleRequest($request);

			if ($form->isValid()) {

				$em = $this->getDoctrine()->getManager();
				$em->persist($product);
				$em->flush();
				
				$translator = $this->get('translator');
				$request->getSession()->getFlashBag()->add('success', $translator->trans('product.added', array(), 'FSPlatformBundle'));

				return $this->redirectToRoute('fs_platform_view', array('id' => $product->getId()));
			}
		}
		
		/*
		 * At this point, the form can't be validated :
		 * - Either the request's type is GET so the user have to see the form
		 * - Either the request's type is POST and the form contains invalid fields
		 */
		return $this->render('FSPlatformBundle:Product:add.html.twig', array(
					'form' => $form->createView(),
		));
	}

	/**
	 * Edit a product
	 * 
	 * @Security("has_role('ROLE_ADMIN')")
	 * @param integer $id		Product id
	 * @param Request $request
	 * @return Response			Rendered page
	 * @throws NotFoundHttpException
	 */
	public function editAction($id, Request $request) {

		$em = $this->getDoctrine()->getManager();

		$product = $em->getRepository('FSPlatformBundle:Product')->find($id);
		
		$translator = $this->get('translator');
		if ($product === null) {
			throw new NotFoundHttpException($translator->trans('product.not_found', array('%id%' => $id), 'FSPlatformBundle'));
		}

		$form = $this->get('form.factory')->create(ProductType::class, $product, array('edit' => true));

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			// Doctrine knows the product : no need to persist
			$em->flush();

			$request->getSession()->getFlashBag()->add('success', $translator->trans('product.updated', array(), 'FSPlatformBundle'));

			return $this->redirectToRoute('fs_platform_view', array('id' => $product->getId()));
		}

		return $this->render('FSPlatformBundle:Product:edit.html.twig', array(
					'product' => $product,
					'form' => $form->createView(),
		));
	}

	/**
	 * Delete the product which id is $id
	 * 
	 * @Security("has_role('ROLE_ADMIN')")
	 * @param Request $request
	 * @param integer $id		Product id
	 * @return Response			Rendered page
	 * @throws NotFoundHttpException
	 */
	public function deleteAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();

		$product = $em->getRepository('FSPlatformBundle:Product')->find($id);
		$translator = $this->get('translator');
		if ($product === null) {
			throw new NotFoundHttpException($translator->trans('product.not_found', array('%id%' => $id), 'FSPlatformBundle'));
		}

		$form = $this->get('form.factory')->create();

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$product->setAvailable(false);
			$em->flush();

			$request->getSession()->getFlashBag()->add('success', $translator->trans('product.deleted', array(), 'FSPlatformBundle'));

			return $this->redirectToRoute('fs_platform_home');
		}

		return $this->render('FSPlatformBundle:Product:delete.html.twig', array(
					'product' => $product,
					'form' => $form->createView(),
		));
	}
	
	/**
	 * Renders the menu according to the current category
	 * 
	 * @param integer $currentCategory
	 * @return Response
	 */
	public function menuAction($currentCategory) {

		$em = $this->getDoctrine()->getManager();

		$listCategories = $em->getRepository('FSPlatformBundle:Category')->findAll();

		return $this->render('FSPlatformBundle:Product:menu.html.twig', array(
					'listCategories' => $listCategories,
					'currentCategory' => $currentCategory
		));
	}

}
