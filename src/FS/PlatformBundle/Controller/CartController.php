<?php

namespace FS\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FS\PlatformBundle\Entity\Product;
use FS\PlatformBundle\Form\OrderProductType;
use FS\PlatformBundle\Form\OrderType;
use FS\PlatformBundle\Entity\Orders;
use FS\PlatformBundle\Entity\OrderProduct;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller managing the shopping cart.
 */
class CartController extends Controller {
	
	/**
	 * Show the cart.
	 * 
	 * @param Request $request
	 * @return type
	 */
	public function viewAction(Request $request) {
		
		$session = $request->getSession();

		if (!$session->has('order')) {
			$session->set('order', new Orders());
		}

		$order = $session->get('order');

		if (!$session->has('cart')) {
			$session->set('cart', array());
		}

		$cart = $session->get('cart');
		
		// computing the total amount of the cart
		$amount = 0;
		foreach ($cart as $orderProduct) {
			$amount += $orderProduct->getAmount();
		}
		$order->setAmount($amount);

		return $this->render('FSPlatformBundle:Cart:view.html.twig', array(
					'cart' => $cart,
					'order' => $order
				)
		);
	}
	
	/**
	 * Add the product given by id to the cart.
	 * 
	 * @param Request $request
	 * @param integer $id	Product id
	 * @return Response		Rendered page
	 * @throws NotFoundHttpException
	 */
	public function addAction(Request $request, $id) {
		
		$translator = $this->get('translator');

		$repository = $this->getDoctrine()
				->getManager()
				->getRepository('FSPlatformBundle:Product')
		;

		$product = $repository->find($id);
		if ($product === null) {
			throw new NotFoundHttpException($translator->trans('product.not_found', array('%id%' => $id), 'FSPlatformBundle'));
		}

		$image = $product->getImage();

		$session = $request->getSession();
		
		/*
		 * If the current session doesn't exist or doesn't have a shopping scart,
		 * it creates it
		 */
		if (!$session->has('order')) {
			$order = new Orders();
			$session->set('order', $order);
		}

		$order = $session->get('order');

		if (!$session->has('cart')) {
			$session->set('cart', array());
		}

		$cart = $session->get('cart');
		
		/*
		 * $cart is an array of orderProduct entity indexed by the product's id
		 */
		if (array_key_exists($id, $cart)) {
			$orderProduct = $cart[$id];
		} else {
			$orderProduct = new OrderProduct();
			$orderProduct->setOrder($order);
			$orderProduct->setProduct($product);
		}
		
		// $source is the page used by the user to call this method
		$source = $request->get('source');
				
		if ($request->isMethod('POST')) {
			
			$orderProductRequest = $request->request->get('order_product');
			$quantity = intval($orderProductRequest['quantity']);
			$orderProduct->setQuantity($quantity);
			if (array_key_exists($id, $cart)) {
				$request->getSession()->getFlashBag()->add('success', $translator->trans('product.quantity.updated', array(), 'FSPlatformBundle'));
			} else {
				$request->getSession()->getFlashBag()->add('success', $translator->trans('product.quantity.added', array(), 'FSPlatformBundle'));
			}
			$cart[$id] = $orderProduct;
			/*
			 *  We need to do this because of Doctrine's lazy load
			 * (Image doesn't have an url and we need to show this in the cart)
			 */
			$cart[$id]->getProduct()->getImage()->setUrl($image->getUrl());
			$session->set('cart', $cart);

			if ($source == 'view') { // if the request comes from the view of the product
				return $this->redirectToRoute('fs_platform_view', array('id' => $id));
			} else { // otherwise, it comes from the view of the cart
				return $this->redirectToRoute('fs_cart_view');
			}
		}

		$form = $this->get('form.factory')->create(OrderProductType::class, $orderProduct, array(
			'action' => $this->generateUrl('fs_cart_add', array('id' => $id, 'source' => $source)),
			'method' => 'POST'
		));
		

		return $this->render('FSPlatformBundle:Product:quantity_form.html.twig', array(
					'source' => $source,
					'form' => $form->createView()
		));
	}
	
	/**
	 * Delete a product from a cart.
	 * 
	 * @param Request $request
	 * @param type $id	Product id
	 * @return Response
	 * @throws NotFoundHttpException
	 */
	public function deleteAction(Request $request, $id) {
		$translator = $this->get('translator');
		$session = $request->getSession();
		$cart = $session->get('cart');
		if (array_key_exists($id, $cart)) {
			unset($cart[$id]);
			$session->set('cart', $cart);
			$request->getSession()->getFlashBag()->add('success', $translator->trans('product.quantity.removed', array(), 'FSPlatformBundle'));
		} else {
			throw new NotFoundHttpException($translator->trans('product.quantity.not_found', array(), 'FSPlatformBundle'));
		}

		return $this->redirectToRoute('fs_cart_view');
	}

	/**
	 * Proceed the checkout.
	 * 
	 * @Security("has_role('ROLE_CUSTOMER')")
	 * @param Request request
	 * @return Response
	 */
	public function checkoutAction(Request $request) {
		
		$translator = $this->get('translator');
		$repository = $this->getDoctrine()
				->getManager()
				->getRepository('FSPlatformBundle:Product')
		;

		$session = $request->getSession();
		$order = $session->get('order');
		$cart = $session->get('cart');
		
		// if the cart is not empty
		if ($order->getAmount() > 0) {
			$em = $this->getDoctrine()->getManager();
			$user = $this->getUser();
			$order->setDate(new \Datetime());
			$order->setUser($user);
			$em->persist($order);
			foreach ($cart as $orderProduct) {
				// Doctrine doesn't manage entities from session, so we must retrieve them from the repositories
				$productSession = $orderProduct->getProduct();
				$product = $repository->find($productSession->getId());
				$orderProduct->setProduct($product);
				$em->persist($orderProduct);
			}
			$em->flush();
			// removing the whole cart because this order has been saved in the database
			$session->remove('order');
			$session->remove('cart');
			$request->getSession()->getFlashBag()->add('success', $translator->trans('order.placed', array(), 'FSPlatformBundle'));
		}
		
		return $this->redirectToRoute('fs_user_view_orders');
	}

}
