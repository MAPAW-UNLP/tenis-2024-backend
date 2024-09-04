<?php

namespace App\Repository;

use App\Entity\HorarioDisponible;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HorarioDisponible>
 *
 * @method HorarioDisponible|null find($id, $lockMode = null, $lockVersion = null)
 * @method HorarioDisponible|null findOneBy(array $criteria, array $orderBy = null)
 * @method HorarioDisponible[]    findAll()
 * @method HorarioDisponible[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HorarioDisponibleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HorarioDisponible::class);
    }

    public function add(HorarioDisponible $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(HorarioDisponible $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findFechaHoraIniHoraFinProfesorId($fecha, $hora_ini, $hora_fin, $profesor_id): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.fecha = :fecha')
            ->andWhere('h.hora_ini = :hora_ini')
            ->andWhere('h.hora_fin = :hora_fin')
            ->andWhere('h.profesor_id = :profesor_id')
            ->setParameter('fecha', $fecha)
            ->setParameter('hora_ini', $hora_ini)
            ->setParameter('hora_fin', $hora_fin)
            ->setParameter('profesor_id', $profesor_id)
            ->orderBy('h.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findHorariosDisponiblesProfesorId($profesor_id): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.profesor_id = :profesor_id')
            ->setParameter('profesor_id', $profesor_id)
            ->orderBy('h.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return HorarioDisponible[] Returns an array of HorarioDisponible objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('h.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?HorarioDisponible
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
