<?php

namespace App\Repository;

use App\Entity\Reserva;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reserva>
 *
 * @method Reserva|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reserva|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reserva[]    findAll()
 * @method Reserva[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reserva::class);
    }

    public function add(Reserva $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function edit(Reserva $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reserva $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findReservasBycanchaIdAndDate($canchaId, $fecha): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.cancha_id = :val')
            ->setParameter('val', $canchaId)
            ->andWhere('r.fecha = :val1')
            ->setParameter('val1', $fecha)
            ->orderBy('r.hora_ini', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findReservasBycanchaIdAndDateAndTime($canchaId, $fecha, $hora_ini, $hora_fin): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.cancha_id = :val')
            ->setParameter('val', $canchaId)
            ->andWhere('r.fecha = :val1')
            ->setParameter('val1', $fecha)
            ->andWhere('r.hora_ini = :val2')
            ->setParameter('val2', $hora_ini)
            ->andWhere('r.hora_fin = :val3')
            ->setParameter('val3', $hora_fin)
            ->orderBy('r.hora_ini', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getLastRecord(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findOneById($id): ?Reserva
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Reserva[] Returns an array of Reserva objects
     */
    public function findReservasBetweenDates($fecha1, $fecha2): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.fecha >= :val1')
            ->setParameter('val1', $fecha1)
            ->andWhere('u.fecha <= :val2')
            ->setParameter('val2', $fecha2)
            ->getQuery()
            ->getResult();
    }

    public function findReservasBycanchaIdBetweenDatesAndTime($canchaId, $fecha1, $fecha2, $hora_ini, $hora_fin): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.cancha_id = :val')
            ->setParameter('val', $canchaId)
            ->andWhere('r.fecha >= :val1')
            ->setParameter('val1', $fecha1)
            ->andWhere('r.fecha <= :val2')
            ->setParameter('val2', $fecha2)
            ->andWhere('r.hora_ini = :val3')
            ->setParameter('val3', $hora_ini)
            ->andWhere('r.hora_fin = :val4')
            ->setParameter('val4', $hora_fin)
            ->orderBy('r.hora_ini', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Reserva[] Returns an array of Reserva objects
     */
    public function findReservasProfesor($persona_id): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.persona_id = :val1')
            ->setParameter('val1', $persona_id)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Reserva[] Returns an array of Reserva objects
     */
    public function findReservasProfesorByDateAndTime($persona_id, $fecha, $hora): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.persona_id = :val1')
            ->setParameter('val1', $persona_id)
            ->andWhere('u.fecha = :val2')
            ->setParameter('val2', $fecha)
            ->andWhere('u.hora_ini = :val3')
            ->setParameter('val3', $hora)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Reserva[] Returns an array of Reserva objects
     */
    public function findThreeClassBefore($fecha): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.fecha < :val1')
            ->setParameter('val1', $fecha)
            ->andWhere('u.estado_id = 0')
            ->orderBy('u.fecha', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Reserva[] Returns an array of Reserva objects
     */
    public function findThreeClassAfter($fecha): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.fecha > :val1')
            ->setParameter('val1', $fecha)
            ->andWhere('u.estado_id = 0')
            ->orderBy('u.fecha', 'ASC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    public function findReservasProfesorSinPagoId($persona_id, $primerDia, $ultimoDia): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.persona_id = :personaId')
            ->andWhere('u.fecha >= :primerDia')
            ->andWhere('u.fecha <= :ultimoDia')
            ->setParameter('personaId', $persona_id)
            ->setParameter('primerDia', $primerDia)
            ->setParameter('ultimoDia', $ultimoDia)
            ->andWhere('u.pago_id is null')
            ->getQuery()
            ->getResult();
    }

    public function findReservasProfesorConPagoId($persona_id, $primerDia, $ultimoDia): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.persona_id = :personaId')
            ->andWhere('u.fecha >= :primerDia')
            ->andWhere('u.fecha <= :ultimoDia')
            ->setParameter('personaId', $persona_id)
            ->setParameter('primerDia', $primerDia)
            ->setParameter('ultimoDia', $ultimoDia)
            ->andWhere('u.pago_id is not null')
            ->getQuery()
            ->getResult();
    }


    /* public function changeEstadoReserva($id, $estado_id): void
    {
        $this->createQueryBuilder('r')
            ->update()
            ->set('r.estado_id', '?1')
            ->where('r.id = ?2')
            ->setParameter(1, $estado_id)
            ->setParameter(2, $id)
            ->getQuery()
            ->execute();
    } */

    //    public function findOverlap($fecha, $horaIni, $horaFin){ // TODO: implementar funcion
    //     $entityManager = $this->getEntityManager();

    //     $query = $entityManager->createQuery(
    //         'SELECT  r
    //         FROM App\Entity\Reserva r
    //         WHERE r.fecha = :fecha
    //         and (r.hora_ini <= :horafin
    //         or r.hora_fin >= :horaini )
    //         '
    //     )->setParameter('fecha', $fecha)
    //     ->setParameter('horafin', $horaFin)
    //     ->setParameter('horaini', $horaIni);

    //     // returns an array of Product objects
    //     $busqueda = $query->getResult();

    //     if (count($busqueda) > 0)
    //         return $query->getResult()[0];
    //     else 
    //         return null;
    //    }



    //    /**
    //     * @return Reserva[] Returns an array of Reserva objects
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

    //    public function findOneBySomeField($value): ?Reserva
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
