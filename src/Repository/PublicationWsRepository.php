<?php

namespace App\Repository;

use App\Entity\PublicationWs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PublicationWs>
 *
 * @method PublicationWs|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublicationWs|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublicationWs[]    findAll()
 * @method PublicationWs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicationWsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublicationWs::class);
    }

    public function save(PublicationWs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PublicationWs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }



    public function getPublicationWssForWorkspace(int $workspaceId): array
    {
        $entityManager = $this->getEntityManager();
        $dql = '
        SELECT p
        FROM App\Entity\PublicationWs p
        JOIN App\Entity\WorkspacePost wp WITH wp.publicationId = p.id
        WHERE wp.workspaceId = :workspaceId
        ORDER BY p.creationdate DESC
        
        ';

        $query = $entityManager->createQuery($dql);
        $query->setParameter('workspaceId', $workspaceId);

        $PublicationWss = $query->getResult();

        return $PublicationWss;
    }

    public function getLastPost(int $workspaceId): array
    {
        $entityManager = $this->getEntityManager();
        $dql = '
        SELECT p
        FROM App\Entity\PublicationWs p
        JOIN App\Entity\WorkspacePost wp WITH wp.publicationId = p.id
        WHERE wp.workspaceId = :workspaceId
        AND p.creationdate = CURRENT_DATE()
        ORDER BY p.creationdate DESC
        
    ';

        $query = $entityManager->createQuery($dql);
        $query->setParameter('workspaceId', $workspaceId);
        $PublicationWss = $query->getResult();
        return $PublicationWss;
    }








    //    /**
    //     * @return PublicationWs[] Returns an array of PublicationWs objects
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

    //    public function findOneBySomeField($value): ?PublicationWs
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

}
