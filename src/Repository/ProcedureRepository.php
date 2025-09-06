<?php

namespace App\Repository;

use App\Entity\Procedure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProcedureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Procedure::class);
    }

    public function findRecentBySpecialty(string $specialty, int $days): array
    {
        $date = new \DateTime("-{$days} days");

        return $this->createQueryBuilder('p')
            ->andWhere('p.specialty = :specialty')
            ->andWhere('p.createdAt >= :date')
            ->setParameter('specialty', $specialty)
            ->setParameter('date', $date)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countRecentBySpecialty(string $specialty, int $days): int
    {
        $date = new \DateTime("-{$days} days");

        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.specialty = :specialty')
            ->andWhere('p.createdAt >= :date')
            ->setParameter('specialty', $specialty)
            ->setParameter('date', $date)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
