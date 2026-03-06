<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findFilteredTasks(?string $search, ?string $status)
    {
        $qb = $this->createQueryBuilder('t');

        // $qb->andWhere('t.owner = :user')
        //    ->setParameter('user', $this->getUser());

        if ($status === 'done') {
            $qb->andWhere('t.isDone = :done')
                ->setParameter('done', true);
        } elseif ($status === 'open') {
            $qb->andWhere('t.isDone = :done')
                ->setParameter('done', false);
        }

        if ($search) {
            $qb->andWhere('t.title LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $qb->orderBy('t.createdAt', 'DESC')
                  ->getQuery()
                  ->getResult();
    }

    //    /**
    //     * @return Task[] Returns an array of Task objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Task
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
