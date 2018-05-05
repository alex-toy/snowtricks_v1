<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    public function getId()
    {
        return $this->id;
    }
    
    
    
    
    /**
     * @ORM\Column(type="boolean")
     *
     */
    private $isMasterImage;
    public function getIsMasterImage()
    {
        return $this->isMasterImage;
    }
    public function setIsMasterImage()
    {
        $this->isMasterImage = true;
        return $this;
    }
    public function unSetIsMasterImage()
    {
        $this->isMasterImage = false;
        return $this;
    }
    
    
    

    
    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Please, upload the product brochure as a PDF file.")
     * @Assert\File(mimeTypes={ "image/jpeg" })
     */
    private $link;
    public function getLink()
    {
        return $this->link;
    }
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }
    
    
    
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Figure", inversedBy="images", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $figure;

    public function getFigure(): ?Figure
    {
        return $this->figure;
    }

    public function setFigure(?Figure $figure): self
    {
        $this->figure = $figure;

        return $this;
    }
    
    
}









