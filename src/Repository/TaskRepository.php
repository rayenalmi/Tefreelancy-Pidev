<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function save(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }



    public function getTasksForWorkspace(int $workspaceId): array
    {
        $entityManager = $this->getEntityManager();
        $dql = '
            SELECT t
            FROM App\Entity\Task t
            WHERE t.id IN (
                SELECT wt.taskId
                FROM App\Entity\WorkspaceTask wt
                WHERE wt.workspaceId = :workspaceId
            )
        ';

        $query = $entityManager->createQuery($dql);
        $query->setParameter('workspaceId', $workspaceId);

        $tasks = $query->getResult();

        return $tasks;
    }

    public function findByName($name)
    {
        return $this->createQueryBuilder('s')
            ->where('s.title = :title')
            ->setParameter('title', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

  
    public function findBySearchQuery($query)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->where('t.title LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->orWhere('t.description LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->andWhere('t.completed = 0')
            ->orderBy('t.deadline', 'DESC');

        return $qb->getQuery()->getResult();
    }



    //    /**
    //     * @return Task[] Returns an array of Task objects
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

    //    public function findOneBySomeField($value): ?Task
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

}
