<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Images
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ImagesRepository")
 * @UniqueEntity("title")
 */
class Images
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     * 
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="image_url", type="string", length=255, nullable=true)
     */
    private $image_url;

    /**
     * @var string
     *
     * @ORM\Column(name="image_name", type="string", length=600, nullable=true)
     */
    private $image_name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", options={"default" : "CURRENT_TIMESTAMP"})
     */
    private $created;

    /**
     * @var \Boolean
     *
     * @ORM\Column(name="valid", type="boolean", options={"default" : false})
     */
    private $valid;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Images
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Images
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

    /**
     * Set image_url
     *
     * @param string $image_url
     * @return Images
     */
    public function setImage_url($image_url)
    {
        $this->image_url = $image_url;

        return $this;
    }

    /**
     * Get image_url
     *
     * @return string 
     */
    public function getImage_url()
    {
        return $this->image_url;
    }

    /**
     * Set image_name
     *
     * @param string $image_name
     * @return Images
     */
    public function setImage_name($image_name)
    {
        $this->image_name = $image_name;

        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage_name()
    {
        return $this->image_name;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Images
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

     /**
     * Set valid
     *
     * @param \Boolean $valid
     * @return Images
     */
    public function setValid($valid)
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * Get valid
     *
     * @return \Boolean
     */
    public function getValid()
    {
        return $this->valid;
    }
}
