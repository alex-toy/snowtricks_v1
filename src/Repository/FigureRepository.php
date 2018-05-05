<?php
namespace App\Repository;
use App\Entity\Figure;
use App\Entity\Message;
use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
class FigureRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Figure::class);
    }
    
    
	public function findAllMessages($figureId)
	{
    	$entityManager = $this->getEntityManager();
		$query = $entityManager->createQuery(
			'SELECT f.name
			FROM App\Entity\Figure f
			WHERE f.id = :figureId'
		)->setParameter('figureId', $figureId);
		return $query->execute();
	}
	
	
	
	
	public function findAllMessagesold($figureId)
	{
    	$entityManager = $this->getEntityManager();
		$query = $entityManager->createQuery(
			'SELECT IDENTITY(m)
			FROM App\Entity\Figure f
			WHERE IDENTITY(f.id) = :figureId
			AND m MEMBER OF f.messages'
		)->setParameter('figureId', $figureId);
		return $query->execute();
	}
	
	
	
	
	
	
}