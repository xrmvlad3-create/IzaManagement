<?php
// src/Repository/DermConditionRepository.php

namespace App\Repository;

use App\Entity\DermCondition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DermCondition>
 *
 * @method DermCondition|null find($id, $lockMode = null, $lockVersion = null)
 * @method DermCondition|null findOneBy(array $criteria, array $orderBy = null)
 * @method DermCondition[]    findAll()
 * @method DermCondition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DermConditionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DermCondition::class);
    }
}
