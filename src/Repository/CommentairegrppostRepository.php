<?php

namespace App\Repository;

use App\Entity\Commentairegrppost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commentairegrppost>
 *
 * @method Commentairegrppost|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commentairegrppost|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commentairegrppost[]    findAll()
 * @method Commentairegrppost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentairegrppostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commentairegrppost::class);
    }

    public function add(Commentairegrppost $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Commentairegrppost $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Commentairegrppost[] Returns an array of Commentairegrppost objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Commentairegrppost
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
