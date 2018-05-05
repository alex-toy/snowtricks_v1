<?php
namespace App\Repository;
use App\Entity\Snowboarder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
class SnowboarderRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Snowboarder::class);
    }
    
    
    
    public function findOneWithNameAndEmail($name, $password): array
	{
    	$entityManager = $this->getEntityManager();
    	$query = $entityManager->createQuery(
        	'SELECT p
        	FROM App\Entity\Snowboarder p
        	WHERE p.name = :name AND p.password = :password')
        ->setParameter('name', $name)
        ->setParameter('password', $password);
    	// returns an array of Product objects
    	return $query->execute();
	}
	
	
	
	public function validateAccount($token)
	{
    	$entityManager = $this->getEntityManager();
    	$query = $entityManager->createQuery(
        	'SELECT p
        	FROM App\Entity\Snowboarder p
        	WHERE p.token = :token')
        ->setParameter('token', $token);
    	// returns an array of Product objects
    	return $query->execute();
	}
	
	
	
	
	
	
	
	
	
	
	
    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('s')
            ->where('s.something = :value')->setParameter('value', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}