<?php

namespace App\Repository;

use App\Entity\Tenant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MeterReadingRepository;

/**
 * @method Tenant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tenant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tenant[]    findAll()
 * @method Tenant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TenantRepository extends ServiceEntityRepository
{

    /**
     * @var MeterReadingRepository
     */
    private $meterReadingRepository;


    public function __construct(ManagerRegistry $registry, 
        EntityManagerInterface $entityManager, 
        MeterReadingRepository $meterReadingRepository)
    {
        $this->entityManager = $entityManager;
        $this->meterReadingRepository = $meterReadingRepository;
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

    public function isOwner(int $tenantId, int $ownerId): bool
    {
        return $this->createQueryBuilder('t')
            ->where('t.id = :id')
            ->andWhere('t.owner = :ownerId')
            ->setParameter('id', $tenantId)
            ->setParameter('ownerId', $ownerId)
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult() !== null ? true : false;
    }


    public function remove($tenant) {
        $this->entityManager->remove($tenant);
        $this->entityManager->flush();
    }

    public function transformMany($tenants): ?array {
        $self = $this;
        return array_map(function ($tenant) use ($self){
            $meterReading = $self->meterReadingRepository->getLastInsertedByOwner($tenant->getId());

            $reading = new \stdClass();
            if (!is_null($meterReading)) {
                $reading->id = $meterReading->getId();
                $reading->previousReadingDate = $meterReading->getFromDate()->format('Y-m-d');
                $reading->previousReading = $meterReading->getPresentReadingKwh();
                $reading->consumedKwh = $meterReading->getConsumedKwh();
            }

            return [
                'id'                    => $tenant->getId(),
                'name'                  => $tenant->getName(),
                'meterNumber'           => $tenant->getMeterNumber(),
                'meterInitialReading'   => $tenant->getMeterInitialReading(),
                'created'               => $tenant->getCreated()->format('Y-m-d'),
                'meterReadings'         => $reading,
            ];
        }, $tenants);
    }

    /*public function find($id): Tenant
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.id = :val')
            ->setParameter('val', $id)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getOneOrNullResult();
    }*/

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
