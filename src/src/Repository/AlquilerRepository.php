<?php

namespace App\Repository;

use App\Entity\Alquiler;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Alquiler>
 *
 * @method Alquiler|null find($id, $lockMode = null, $lockVersion = null)
 * @method Alquiler|null findOneBy(array $criteria, array $orderBy = null)
 * @method Alquiler[]    findAll()
 * @method Alquiler[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlquilerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alquiler::class);
    }

    public function add(Alquiler $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Alquiler $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findAlquilerByReservaId($reservaId): ?Alquiler
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.reserva_id = :val')
            ->setParameter('val', $reservaId)
            ->getQuery()
            ->getOneOrNullResult();
    }


    //    /**
    //     * @return Alquiler[] Returns an array of Alquiler objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Alquiler
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
