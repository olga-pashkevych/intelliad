<?php

namespace App\Repository;

use App\Entity\Rate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Rate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rate[]    findAll()
 * @method Rate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RateRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Rate::class);
    }

    public function persist(Rate $rate)
    {
        $this->_em->persist($rate);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function findAllWithCurrency()
    {
        return $this->createQueryBuilder('r')
            ->select('c.name, r.rate, r.date')
            ->leftJoin('r.currency', 'c')
            ->orderBy('r.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByCurrencyName(string $name)
    {
        return $this->createQueryBuilder('r')
            ->select('c.name, r.date, r.rate')
            ->leftJoin('r.currency', 'c')
            ->where('c.name = :name')
            ->setParameter('name', $name)
            ->orderBy('r.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
