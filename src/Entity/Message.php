<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateType;
/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    
    
    /**
     * @ORM\Column(type="string", length=500, name="contenu")
     */
    private $contenu;
    
    
    /**
     * @ORM\Column(type="string", length=20, name="author")
     */
    private $author;
    
    
    /**
     * @ORM\Column(type="date", length=500, name="dateCreation", nullable=true)
     */
    private $dateCreation;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Figure", inversedBy="messages")
     * @ORM\JoinColumn(nullable=true)
     */
    private $figure;
    
    public function getFigure(): Figure
    {
        return $this->figure;
    }
    public function setFigure(Figure $figure)
    {
        $this->figure = $figure;
    }
    
    
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getContenu()
    {
        return $this->contenu;
    }
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;
    }
    
    public function getDateCreation()
    {
        return $this->dateCreation;
    }
    public function setDateCreation(date $d)
    {
        $this->dateCreation = $d;
    }
    
    public function getAuthor()
    {
        return $this->author;
    }
    public function setAuthor($author)
    {
        $this->author = $author;
    }
    
}