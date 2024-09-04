<?php

namespace App\Repository;

use App\Entity\PeriodoAusencia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PeriodoAusencia>
 *
 * @method PeriodoAusencia|null find($id, $lockMode = null, $lockVersion = null)
 * @method PeriodoAusencia|null findOneBy(array $criteria, array $orderBy = null)
 * @method PeriodoAusencia[]    findAll()
 * @method PeriodoAusencia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeriodoAusenciaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PeriodoAusencia::class);
    }

    public function add(PeriodoAusencia $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function edit(PeriodoAusencia $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PeriodoAusencia $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findPeriodoAusenciaByFechaIniFechaFin($fechaIni, $fechaFin): ?array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.fecha_ini = :val')
            ->setParameter('val', $fechaIni)
            ->andWhere('p.fecha_fin = :val1')
            ->setParameter('val1', $fechaFin)
            ->getQuery()
            ->getResult();
    }

    public function findPeriodoAusenciaByProfesorId($profesorId): ?array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.profesor_id = :val')
            ->setParameter('val', $profesorId)
            ->getQuery()
            ->getResult();
    }

    public function findPeriodoAusenciaById($id): ?array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return PeriodoAusencia[] Returns an array of PeriodoAusencia objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PeriodoAusencia
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
