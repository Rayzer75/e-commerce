<?php

namespace FS\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="FS\UserBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
	/**
	 * @ORM\Column(name="lastname", type="string", length=255)
	 */
	protected $lastname;
	
	/**
	 * @ORM\Column(name="firstname", type="string", length=255)
	 */
	protected $firstname;
	
	/**
	 * @ORM\Column(name="address", type="string", length=255)
	 */
	protected $address;
	
	public function __construct() {
		parent::__construct();
		$this->roles = array('ROLE_CUSTOMER');
	}
	
	function getAddress() {
		return $this->address;
	}

	function setAddress($address) {
		$this->address = $address;
	}
	
	function getLastName() {
		return $this->lastname;
	}

	function getFirstName() {
		return $this->firstname;
	}

	function setLastName($lastname) {
		$this->lastname = $lastname;
	}

	function setFirstName($firstname) {
		$this->firstname = $firstname;
	}
}

