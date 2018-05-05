<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;





/**
 * @ORM\Entity(repositoryClass="App\Repository\FigureRepository")
 */
class Figure
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
	 * @Assert\NotBlank()
     * @ORM\Column(type="string", length=100)
     */
    private $name;
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    
    
    
    /**
     * @Assert\DateTime()
     */
    private $createdAt;
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
    
    
    
    /**
     * @ORM\Column(type="string", length=500)
     */
    private $description;
    public function getDescription()
    {
        return $this->description;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    
    


    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="figure", orphanRemoval=true)
     */
    private $images;
    /**
     * @return Collection|Image[]
     */
    public function getImages()
    {
        return $this->images;
    }
    /**
     * Add imageToAdd
     *
     * @param Image $imageToAdd
     */
    public function addImage($imageToAdd)
    {
        $imageToAdd->setFigure($this);
        $this->images[] = $imageToAdd;
    }
    
    
    
    
    
    
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="figure", orphanRemoval=true, cascade={"persist"})
     */
    private $messages;
    /**
     * @return Collection|Message[]
     */
    public function getMessages()
    {
        return $this->messages;
    }
    
    
    
    
    
    
    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    private $difficulty;
    public function getDifficulty()
    {
        return $this->difficulty;
    }
    public function setDifficulty($difficulty)
    {
        $this->difficulty = $difficulty;
    }
    
    
    
    
    /**
 	* @var ArrayCollection
 	*/
	private $uploadedFiles;
	public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }
    public function setUploadedFiles($uploadedFiles)
    {
        $this->uploadedFiles = $uploadedFiles;
    }
	/**
 	* @ORM\PreFlush()
 	*/
	public function upload($directory)
	{
    	foreach($this->uploadedFiles as $uploadedFile)
    	{
        	$image = new Image();

        	$path = sha1(uniqid(mt_rand(), true)).'.'.$uploadedFile->guessExtension();
        	$image->setLink($path);

        	$uploadedFile->move( $directory, $path  );

        	$this->addImage($image);

        	unset($uploadedFile);
    	}
	}
    
    
    
    
    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->uploadedFiles = new ArrayCollection();
    }
    
    
    
    
}









