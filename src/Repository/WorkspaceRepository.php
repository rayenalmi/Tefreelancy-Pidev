<?php

namespace App\Repository;

use App\Entity\Workspace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\ArrayType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Workspace>
 *
 * @method Workspace|null find($id, $lockMode = null, $lockVersion = null)
 * @method Workspace|null findOneBy(array $criteria, array $orderBy = null)
 * @method Workspace[]    findAll()
 * @method Workspace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkspaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Workspace::class);
    }

    public function save(Workspace $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Workspace $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getFreelancersForWorkspace($workspaceId)
    {
        $entityManager =  $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select('u.idUser', 'u.lastName', 'u.firstName', 'u.phone', 'u.email', 'u.password', 'u.photo')
            ->from('App\Entity\User', 'u')
            ->innerJoin('App\Entity\WorkspaceFreelancer', 'wf', 'WITH', 'u.idUser = wf.freelancerId')
            ->where('wf.workspaceId = :workspaceId')
            ->setParameter('workspaceId', $workspaceId)
            ->getQuery();

        $freelancers = $query->getResult();

        return $freelancers;
    }

    public function getWorkspacesForFreelancer($freelancerId)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select('w.id', 'w.name', 'w.description')
            ->from('App\Entity\Workspace', 'w')
            ->innerJoin('App\Entity\WorkspaceFreelancer', 'wf', 'WITH', 'w.id = wf.workspaceId')
            ->where('wf.freelancerId = :freelancerId')
            ->setParameter('freelancerId', $freelancerId)
            ->getQuery();

        $workspaces = $query->getResult();

        return $workspaces;
    }

    public function getFreelancerByEmail($email)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT f
         FROM App\Entity\User f
         WHERE f.email = :email'
        )->setParameter('email', $email);

        $freelancer = $query->getOneOrNullResult();

        return $freelancer;
    }


    //    /**
    //     * @return Workspace[] Returns an array of Workspace objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Workspace
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

}
