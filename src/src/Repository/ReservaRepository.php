<?php

namespace App\Repository;

use Exception;
use App\Entity\Reserva;
use App\Entity\PeriodoAusencia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

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
    public function findProfesorReservasBetweenDates($fecha1, $fecha2, $personaId): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.fecha >= :val1')
            ->setParameter('val1', $fecha1)
            ->andWhere('u.fecha <= :val2')
            ->setParameter('val2', $fecha2)
            ->andWhere('u.persona_id = :persona_id') // Usa persona_id
            ->setParameter('persona_id', $personaId)
            ->getQuery()
            ->getResult();
    }


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

    public function findReservasBycanchaIdBetweenTime($canchaId, $fecha, DateTime $hora_ini, DateTime $hora_fin): array
{
    return $this->createQueryBuilder('r')
        ->andWhere('r.cancha_id = :canchaId')
        ->andWhere('r.fecha = :fecha')
       // ->andWhere('r.estado_id = :estadoId')
        ->andWhere('r.hora_ini < :horaFin')
        ->andWhere('r.hora_fin > :horaIni')
        ->setParameter('canchaId', $canchaId)
        ->setParameter('fecha', $fecha)
        //->setParameter('estadoId', 0)
        ->setParameter('horaIni', $hora_ini->format('H:i:s'))
        ->setParameter('horaFin', $hora_fin->format('H:i:s'))
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

    /**
     * @return Reserva[] Returns an array of Reserva objects
     */
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




    /**
     * @return Reserva[] Returns an array of Reserva objects
     */
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

    public function findReservasPorPersonaIdYFecha($personaId, $fecha): array 
    {
        //  // Obtener los periodos de ausencia del profesor
        $ausente = $this->getEntityManager()->getRepository(PeriodoAusencia::class)
            ->isProfesorAusente($personaId,$fecha);
        
        if ($ausente) {
            return array();
        }

        //dd($personaId, $fecha);

        // Crear la consulta base para las reservas
        $queryBuilder = $this->createQueryBuilder('r')
            ->andWhere('r.persona_id = :personaId')
            ->setParameter('personaId', $personaId)
            ->andWhere('r.fecha = :fecha')
            ->setParameter('fecha', $fecha);
           // ->andWhere('r.estado_id = :estadoId')
            //->setParameter('estadoId',0);
            
        return $queryBuilder->getQuery()->getResult();

    }

    public function findReservasPorPersonaIdFechaYHora($personaId, $fecha, $horaIni, $horaFin): array 
    {
        //  // Obtener los periodos de ausencia del profesor
        $ausente = $this->getEntityManager()->getRepository(PeriodoAusencia::class)
            ->isProfesorAusente($personaId,$fecha);
        
        if ($ausente) {
            return array();
        }

        //dd($personaId, $fecha);

        // Crear la consulta base para las reservas
        $queryBuilder = $this->createQueryBuilder('r')
        ->andWhere('r.persona_id = :personaId')
        ->setParameter('personaId', $personaId)
        ->andWhere('r.fecha = :fecha')
        ->setParameter('fecha', $fecha)
       // ->andWhere('r.estado_id = :estadoId')
       // ->setParameter('estadoId', 0)
        ->andWhere('r.hora_ini < :horaFin')
        ->andWhere('r.hora_fin > :horaIni')
        ->setParameter('horaIni', $horaIni)
        ->setParameter('horaFin', $horaFin);
            
        return $queryBuilder->getQuery()->getResult();
    }


    public function getLastReservaId(): ?int
    {
        $record = $this->createQueryBuilder('r')
        ->orderBy('r.id', 'DESC')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();

        return $record ? $record->getId() : 0;
    }

    public function hasOverlappingReservas($personaId, $fecha, $horaIni, $horaFin): bool
    {
        $reservas = $this->createQueryBuilder('r')
            ->andWhere('r.persona_id = :personaId')
            ->setParameter('personaId', $personaId)
            ->andWhere('r.fecha = :fecha')
            ->setParameter('fecha', $fecha)
            ->andWhere('r.hora_ini < :horaFin')
            ->setParameter('horaFin', $horaFin)
            ->andWhere('r.hora_fin > :horaIni')
            ->setParameter('horaIni', $horaIni)
            ->getQuery()
            ->getResult();

        return count($reservas) > 0; 
    }

    public function validarReserva(array $reservaParam): array
    {
        $result = ['success' => true, 'message' => 'Reserva creada con éxito', 'status_code' => 200];
    
        try {
            $horaActual = new DateTime();
    
            // Convertir fecha y horas a objetos DateTime
            $fechaReserva = $reservaParam['fecha'] instanceof DateTime ? $reservaParam['fecha'] : new DateTime($reservaParam['fecha']);
            $horaIni = $reservaParam['hora_ini'] instanceof DateTime ? $reservaParam['hora_ini'] : new DateTime($reservaParam['hora_ini']);
            $horaFin = $reservaParam['hora_fin'] instanceof DateTime ? $reservaParam['hora_fin'] : new DateTime($reservaParam['hora_fin']);
            // Crear objeto DateTime para la hora de inicio y fin de la reserva
            $fechaHoraIniReserva = new DateTime($fechaReserva->format('Y-m-d') . ' ' . $horaIni->format('H:i:s'));

    
            // Validar que la fecha y hora sean futuras
            if ($fechaHoraIniReserva <= $horaActual) {
                $result['success'] = false;
                $result['message'] = 'La fecha y hora de la reserva deben ser futuras.';
                $result['status_code'] = 400;
                return $result;
            }
        } catch (Exception $e) {
            // Manejar la excepción
            $result['success'] = false;
            $result['message'] = 'Error al procesar la reserva: ' . $e->getMessage();
            $result['status_code'] = 500; // Código de error interno
            return $result;
        }

        $reservasExistentes = $this->findReservasBycanchaIdBetweenTime($reservaParam['cancha_id'], $reservaParam['fecha'], $horaIni, $horaFin);
        // // Validar si la cancha ya está reservada en la fecha/hora
        if (count($reservasExistentes) > 0) {
            $result['success'] = false;
            $result['message'] = 'La cancha ya está reservada en ese horario.';
            $result['status_code'] = 401;
            return $result;
        }

        $reservasDelProfe = $this->findReservasPorPersonaIdFechaYHora($reservaParam['persona_id'], $reservaParam['fecha'], $horaIni, $horaFin);
        if (count($reservasDelProfe) > 0) {
            $result['success'] = false;
            $result['message'] = 'El profesor ya tiene una clase en ese horario.';
            $result['status_code'] = 403;
            return $result;
        }

        $ausente = $this->getEntityManager()->getRepository(PeriodoAusencia::class)
            ->isProfesorAusente($reservaParam['persona_id'],$reservaParam['fecha']);

        if ($ausente){
            $result['success'] = false;
            $result['message'] = 'El profesor tiene un periodo de ausencia activo para la fecha seleccionada.';
            $result['status_code'] = 409;
            return $result;
        }

        return $result;

    }

    
    

    //  Agregar condiciones para filtrar solapamientos
    //  if ($periodosAusencia) {
    //     $notInAbsencePeriod = $queryBuilder->expr()->andX();
        
    //     foreach ($periodosAusencia as $periodo) {
    //         // Comprobamos que la fecha no está dentro del periodo de ausencia
    //         $notInAbsencePeriod->add(
    //             $queryBuilder->expr()->orX(
    //                 $queryBuilder->expr()->lt('r.fecha', ':fecha_inicio_'.$periodo->getId()), // Antes de la ausencia
    //                 $queryBuilder->expr()->gt('r.fecha', ':fecha_fin_'.$periodo->getId())    // Después de la ausencia
    //             )
    //         );

    //         // Asignar los parámetros de tiempo de ausencia
    //         $queryBuilder->setParameter('fecha_inicio_'.$periodo->getId(), $periodo->getFechaIni()->format('Y-m-d'));
    //         $queryBuilder->setParameter('fecha_fin_'.$periodo->getId(), $periodo->getFechaFin()->format('Y-m-d'));
    //     }
        
    //     // Agregar la condición al query builder
    //     $queryBuilder->andWhere($notInAbsencePeriod);
    // }

    // return $queryBuilder->getQuery()->getResult();
 

 
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
