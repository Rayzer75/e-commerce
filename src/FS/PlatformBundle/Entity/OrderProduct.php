<?php

namespace FS\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="fs_order_product")
 * @ORM\Entity(repositoryClass="FS\PlatformBundle\Repository\OrderProductRepository")
 */
class OrderProduct
{
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(name="quantity", type="integer")
   * @Assert\Range(min=1)
   */
  private $quantity;

  /**
   * @ORM\ManyToOne(targetEntity="FS\PlatformBundle\Entity\Orders",cascade={"persist"})
   * @ORM\JoinColumn(nullable=false)
   */
  private $order;

  /**
   * @ORM\ManyToOne(targetEntity="FS\PlatformBundle\Entity\Product", fetch="EAGER")
   * @ORM\JoinColumn(nullable=false)
   */
  private $product;
  
  /**
   * @ORM\Column(name="initial_price", type="integer")
   */
  private $initialPrice;
  
  private $amount;
    
  function getId() {
	  return $this->id;
  }

  function getQuantity() {
	  return $this->quantity;
  }

  function getOrder() {
	  return $this->order;
  }

  function getProduct() {
	  return $this->product;
  }
  
  function getInitialPrice() {
	  return $this->initialPrice;
  }

  function setId($id) {
	  $this->id = $id;
  }

  function setQuantity($quantity) {
	  $this->quantity = $quantity;
	  $this->amount = $quantity*$this->product->getPrice();
  }

  function setOrder($order) {
	  $this->order = $order;
  }

  function setProduct($product) {
	  $this->product = $product;
	  $this->initialPrice = $product->getPrice();
  }
  
  function getAmount() {
	  return $this->amount;
  }

}