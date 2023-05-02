<?php

namespace App\Repository;

use App\Entity\PropertySearchSkill;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Config\Doctrine\Orm\EntityManagerConfig\QueryCacheDriverConfig;

/**
 * @extends ServiceEntityRepository<PropertySearchSkill>
 *
 * @method PropertySearchSkill|null find($id, $lockMode = null, $lockVersion = null)
 * @method PropertySearchSkill|null findOneBy(array $criteria, array $orderBy = null)
 * @method PropertySearchSkill[]    findAll()
 * @method PropertySearchSkill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PropertySearchSkill::class);
    }

    public function save(PropertySearchSkill $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PropertySearchSkill $entity, bool $flush = false): void
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


        public function findAllVisibleQuery(PropertySearchSkill $search): Query{
            $query = $this->findALlVisibleQuery($search); 
            if ($search->getLevel()){
                $query = $query 
                    ->where('s.level = :val')
                    ->setParameter('val', $search->getLevel())

                    //
                    ->setMaxResults(10)
                ->getQuery()
                ->getResult(); 
            }
        }


          



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
