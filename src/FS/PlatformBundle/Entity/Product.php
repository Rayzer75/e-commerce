<?php

namespace FS\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="FS\PlatformBundle\Repository\ProductRepository")
 */
class Product
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
	 * @Assert\Length(min=2)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
	 * @Assert\Range(min=0.01)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
	 * @Assert\NotBlank()
     */
    private $description;

	/**
	 * @ORM\OneToOne(targetEntity="FS\PlatformBundle\Entity\Image", cascade={"persist","remove"}, fetch="EAGER")
	 * @Assert\NotNull()
	 */
	private $image;
	
	/**
	 * @ORM\ManyToOne(targetEntity="FS\PlatformBundle\Entity\Category")
	 * @ORM\JoinColumn(nullable=false)
	 * @Assert\Valid()
	 */
	private $category;
	
	/**
	 * @ORM\Column(name="available", type="boolean")
	 */
	private $available = true;
	
	/**
	 * @ORM\Column(name="nb_reviews", type="integer")
	 */
	private $nbReviews = 0;
	
	/**
	 * @ORM\Column(name="total_mark", type="integer")
	 */
	private $totalMark = 0;

	/**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
	
	public function setImage(Image $image) {
		$this->image = $image;
	}

	public function getImage() {
		return $this->image;
	}
	
	public function setCategory(Category $category) {
		$this->category = $category;
	}
	
	public function getCategory() {
		return $this->category;
	}
	
	public function getNbReviews() {
		return $this->nbReviews;
	}
		
	public function increaseReviews() {
		$this->nbReviews++;
	}

	public function decreaseReviews() {
		$this->nbReviews--;
	}
	
	public function increaseTotalMark($mark) {
		$this->totalMark = $this->totalMark + $mark;
	}

	public function decreaseTotalMark($mark) {
		$this->totalMark = $this->totalMark - $mark;
	}
	
	public function getAverageMark() {
		if ($this->nbReviews == 0) {
			return 0;
		}
		return round($this->totalMark/$this->nbReviews);
	}
	
	function getAvailable() {
		return $this->available;
	}

	function setAvailable($available) {
		$this->available = $available;
	}

}

