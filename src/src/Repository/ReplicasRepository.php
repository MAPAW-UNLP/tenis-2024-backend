<?php

namespace App\Repository;

use App\Entity\Replicas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Replicas>
 *
 * @method Replicas|null find($id, $lockMode = null, $lockVersion = null)
 * @method Replicas|null findOneBy(array $criteria, array $orderBy = null)
 * @method Replicas[]    findAll()
 * @method Replicas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReplicasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Replicas::class);
    }

    public function add(Replicas $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Replicas $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByReservaId($id): ?Replicas
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.idReserva = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

       /**
    * @return Replicas[] Returns an array of Replicas objects
    */
   public function findAll(): array
   {
       return $this->createQueryBuilder('r')
           ->getQuery()
           ->getResult()
       ;
   }

//    /**
//     * @return Replicas[] Returns an array of Replicas objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Replicas
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
