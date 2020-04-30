<?php

namespace App\Repository;

use App\Entity\Tenant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Tenant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tenant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tenant[]    findAll()
 * @method Tenant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TenantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($registry, Tenant::class);
    }
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function save(Tenant $tenant): void
    {
        $this->entityManager->persist($tenant);
        $this->entityManager->flush();
    }

    public function getLastInserted(int $ownerId): Tenant
    {
        return $this->createQueryBuilder('t')
            ->where('t.owner = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return Tenant[] Returns an array of Tenant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Tenant
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
