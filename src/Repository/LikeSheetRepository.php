<?php

namespace App\Repository;

use App\Entity\LikeSheet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LikeSheet|null find($id, $lockMode = null, $lockVersion = null)
 * @method LikeSheet|null findOneBy(array $criteria, array $orderBy = null)
 * @method LikeSheet[]    findAll()
 * @method LikeSheet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeSheetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LikeSheet::class);
    }


    
   
    

    /*
    public function findOneBySomeField($value): ?LikeSheet
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
