<?php

namespace App\Repository;

use App\Entity\MeterReading;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method MeterReading|null find($id, $lockMode = null, $lockVersion = null)
 * @method MeterReading|null findOneBy(array $criteria, array $orderBy = null)
 * @method MeterReading[]    findAll()
 * @method MeterReading[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeterReadingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($registry, MeterReading::class);
    }

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function save(MeterReading $meterReading): void
    {
        $this->entityManager->persist($meterReading);
        $this->entityManager->flush();
    }

    public function transform($request, $tenant): ?MeterReading 
    {
        $data =json_decode($request->getContent(), true);

        $meterReading = new MeterReading();
  
        $meterReading->setTenant($tenant);
        $meterReading->setFromDate(new \DateTime($data['previousReadingDate']));
        $meterReading->setToDate(new \DateTime($data['currentReadingDate']));
        $meterReading->setReadingKwh($data['currentReading']);
        $meterReading->setConsumedKwh($data['kwhConsumed']);
        $meterReading->setRate($data['ratePerKwh']);
        $bill = (float)$data['ratePerKwh'] * (float)$data['currentReading'];
        $meterReading->setBill($bill);

        return $meterReading;
    }

    public function getLastInsertedByOwner(int $tenantId): MeterReading
    {
        return $this->createQueryBuilder('m')
            ->where('m.tenant = :tenantId')
            ->setParameter('tenantId', $tenantId)
            ->orderBy('m.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return MeterReading[] Returns an array of MeterReading objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MeterReading
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
