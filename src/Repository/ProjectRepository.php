<?php

namespace App\Repository;

use App\Entity\Project;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method PortProjectfolio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function save(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }



    public function findByFreelancerId($value): array
        {
            return $this->createQueryBuilder('s')
                ->andWhere('s.idFreelancer = :val')
                ->setParameter('val', $value)
                //->orderBy('s.id', 'ASC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult()
            ;
        }


        // public function findByOfferId($id): ?Offer
        // {
        //     return $this->createQueryBuilder('f')
        //     ->join('f.idOffer', 'o')
        //     ->addSelect('o')
        //         ->where('o.idOffer = f.idOffer')
        //         //->setParameter('val', $id)
        //         //->orderBy('s.id', 'ASC')
        //         //->select('SUM(s.rate) as rate')
        //         ->setMaxResults(10)
        //         ->getQuery()
        //         ->getResult()
        //     ;
        // }    



//    /**
//     * @return History[] Returns an array of History objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.id_freelancer = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Student
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


//php bin/console doctrine:database:create
//➔ php bin/console make:entity
//php bin/console make:migration 
//php bin/console doctrine:migrations:migrate
//composer require symfony/form
//php bin/console make:form FormName



}
