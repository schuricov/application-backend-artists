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
        return $this->createQueryBuilder('a')
            ->select($select)
            ->andWhere("a.$field = :val")
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

}
