<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
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

    // /**
    //  * @return Task[] Returns an array of Task objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
    public function CountRowsByDateRange($date1,$date2)
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->add('where', "t.deadline between '$date1' and '$date2'")
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function CountAllRows()
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function CountAllDone()
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->andWhere('t.status = :val')
            ->setParameter('val', '1')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function Countoverdue()
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->andWhere('t.status = :val')
            ->andWhere('t.deadline < :now ')
            ->setParameter('val', '0')
            ->setParameter('now', new \DateTime('now'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /*
    public function findOneBySomeField($value): ?Task
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
