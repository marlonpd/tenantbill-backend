<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\PowerRate;

/**
 * @method PowerRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method PowerRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method PowerRate[]    findAll()
 * @method PowerRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PowerRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($registry, PowerRate::class);
    }

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function save(PowerRate $powerRate): void
    {
        $this->entityManager->persist($powerRate);
        $this->entityManager->flush();
    }

    public function getLastInsertedByOwner(int $ownerId): ?PowerRate
    {
        return $this->createQueryBuilder('t')
            ->where('t.owner = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByOwer(int $ownerId): ?array
    {
        return $this->createQueryBuilder('t')
            ->where('t.owner = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findCurrentRateByOwner(int $ownerId): ?PowerRate
    {
        return $this->createQueryBuilder('t')
            ->where('t.owner = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function transform($request, $user): ?PowerRate 
    {
        $data =json_decode($request->getContent(), true);

        $powerRate = new PowerRate();
  
        $powerRate->setRate($data['rate']);
        $powerRate->setOwner($user);

        return $powerRate;
    }

    // /**
    //  * @return PowerRate[] Returns an array of PowerRate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PowerRate
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
