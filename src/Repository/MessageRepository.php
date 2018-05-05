<?php
namespace App\Repository;
use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }
    
    public function findAllMessages($figureId)
	{
    	$entityManager = $this->getEntityManager();
		$query = $entityManager->createQuery(
			'SELECT m.figure_id
			FROM App\Entity\Message m'
		);
		return $query->execute();
	}
	
	
	public function findAllMessagesold($figureId)
	{
    	$entityManager = $this->getEntityManager();
		$query = $entityManager->createQuery(
			'SELECT m
			FROM App\Entity\Message m
			WHERE m.figure_id = :figureId'
		)->setParameter('figureId', $figureId);
		return $query->execute();
	}
    
}
