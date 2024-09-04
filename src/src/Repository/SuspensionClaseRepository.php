<?php

namespace App\Repository;

use App\Entity\SuspensionClase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SuspensionClase>
 *
 * @method SuspensionClase|null find($id, $lockMode = null, $lockVersion = null)
 * @method SuspensionClase|null findOneBy(array $criteria, array $orderBy = null)
 * @method SuspensionClase[]    findAll()
 * @method SuspensionClase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuspensionClaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuspensionClase::class);
    }

    public function add(SuspensionClase $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function edit(SuspensionClase $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SuspensionClase $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findSuspensionClaseByFechaHoraProfesor($fecha, $hora, $profesor_id): array
    {
        return $this->createQueryBuilder('clase')
            ->andWhere('clase.fecha = :val')
            ->setParameter('val', $fecha)
            ->andWhere('clase.hora = :val1')
            ->setParameter('val1', $hora)
            ->andWhere('clase.profesor_id = :val2')
            ->setParameter('val2', $profesor_id)
            ->getQuery()
            ->getResult();
    }

    public function findSuspensionClaseById($id): array
    {
        return $this->createQueryBuilder('clase')
            ->andWhere('clase.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult();
    }

    public function findSuspensionesClasesByProfesorId($profesor_id): array
    {
        return $this->createQueryBuilder('clase')
            ->andWhere('clase.profesor_id = :val')
            ->setParameter('val', $profesor_id)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return SuspensionClase[] Returns an array of SuspensionClase objects
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

    //    public function findOneBySomeField($value): ?SuspensionClase
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
