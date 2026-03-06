<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findFilteredTasks(?string $search, ?string $status, User $user): array
    {
        $qb = $this->createQueryBuilder('t')
                   ->andWhere('t.owner = :user')
                   ->setParameter('user', $user);

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
