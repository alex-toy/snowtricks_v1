<?php
namespace App\Repository;
use App\Entity\Figure;
use App\Entity\Message;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;


class ImageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Figure::class);
    }
    
    
	
	
	
	
}