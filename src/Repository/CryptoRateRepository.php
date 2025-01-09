<?php

namespace App\Repository;

use App\Entity\CryptoRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CryptoRate>
 */
class CryptoRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CryptoRate::class);
    }

    /**
     * @return CryptoRate
     */
    public function findCurrencyPairRateByDate($currencyPair, $date)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.timestamp >= :timestamp_date')
            ->andWhere('c.currencyPair = :currencyPair')
            ->setParameter('timestamp_date', $date)
            ->setParameter('currencyPair', $currencyPair)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    //    public function findOneBySomeField($value): ?CryptoRate
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
