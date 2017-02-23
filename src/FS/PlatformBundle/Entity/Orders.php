<?php

namespace FS\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Orders
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="FS\PlatformBundle\Repository\OrdersRepository")
 */
class Orders {

	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="FS\UserBundle\Entity\User")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $user;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="amount", type="integer")
	 */
	private $amount;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date", type="datetime")
	 */
	private $date;
	
	public function __construct() {
		// Par défaut, la date de la commande est aujourd'hui
		$this->date = new \Datetime();
		$this->amount = 0;
	}

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Get amount
	 *
	 * @return int
	 */
	public function getAmount() {
		return $this->amount;
	}

	/**
	 * Set date
	 *
	 * @param \DateTime $date
	 *
	 * @return Orders
	 */
	public function setDate($date) {
		$this->date = $date;

		return $this;
	}

	/**
	 * Get date
	 *
	 * @return \DateTime
	 */
	public function getDate() {
		return $this->date;
	}

	function getUser() {
		return $this->user;
	}

	function setUser($user) {
		$this->user = $user;
	}
	
	function setAmount($amount) {
		$this->amount = $amount;
	}

}
