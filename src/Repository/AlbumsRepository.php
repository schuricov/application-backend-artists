<?php

namespace App\Repository;

use App\Entity\Albums;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Albums|null find($id, $lockMode = null, $lockVersion = null)
 * @method Albums|null findOneBy(array $criteria, array $orderBy = null)
 * @method Albums[]    findAll()
 * @method Albums[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlbumsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Albums::class);
    }

//    /**
//     * @return Albums[] Returns an array of Albums objects
//     */

    public function findByField($field, $value, $select = null)
    {
//        $select = ['a.description', 'a.token'];
        return $this->createQueryBuilder('a')
            ->select($select)
//            ->select('a.description', 'a.cover')
            ->andWhere("a.$field = :val")
            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
            ->getQuery()
//            ->getResult()
            ->getOneOrNullResult()

            ;
    }

    public function findOneBySomeField($value = '267OCY1')//: ?Albums
    {
//        return $value;
        return $this->createQueryBuilder('a')
            ->select('a.description')
//            ->andWhere('a.token = 267OCY1')
//            ->andWhere('a.group_id = :val')
//            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
//            ->getOneOrNullResult()
        ;
    }
}
