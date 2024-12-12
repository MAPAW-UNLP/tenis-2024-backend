<?php

namespace App\Repository;

use App\Entity\Clases;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Clases>
 *
 * @method Clases|null find($id, $lockMode = null, $lockVersion = null)
 * @method Clases|null findOneBy(array $criteria, array $orderBy = null)
 * @method Clases[]    findAll()
 * @method Clases[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClasesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Clases::class);
    }

    public function add(Clases $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Clases $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneById($id): ?Clases
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
    * @return Clases[] Returns an array of Clases objects
    */
    public function findAll(): array
    {
        return $this->createQueryBuilder('c')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Clases[] Returns an array of Clases objects
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

//    public function findOneBySomeField($value): ?Clases
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
