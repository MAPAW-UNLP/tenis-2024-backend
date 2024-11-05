<?php

namespace App\Repository;

use App\Entity\Cancha;
use App\Entity\Reserva;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cancha>
 *
 * @method Cancha|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cancha|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cancha[]    findAll()
 * @method Cancha[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CanchaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cancha::class);
    }

    public function add(Cancha $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Cancha $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneById($id): ?Cancha
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }


        /**
        * @return Cancha[] Returns an array of Cancha objects
        */
       public function findAll(): array
       {
           return $this->createQueryBuilder('c')
               ->getQuery()
               ->getResult()
           ;
       }

       public function getIdCanchaDisponible($canchaPreferidaId, $fecha, $horaInicio, $horaFin) 
       {
           // devolver id de la cancha disponible, preferentemente la original (si no hay ninguna, retorna 0)
   
           if ($this->isCanchaDisponibleEnTurno($canchaPreferidaId, $fecha, $horaInicio, $horaFin)) {
               return $canchaPreferidaId->getCanchaId();
            }
   
           $canchasDisponibles = $this->canchasDisponiblesEnFechaYTuno($fecha, $horaInicio, $horaFin);
   
           // Buscar la próxima cancha disponible que no sea la preferida, es mas eficiente que un array_filter ya que no genera otro array
           foreach ($canchasDisponibles as $cancha) {
               if ($cancha->getId() !== $canchaPreferidaId) {
                   return $cancha->getId(); // Devolver el ID de la primera cancha que no es la preferida
               }
           }
           return 0;
       }

       public function isCanchaDisponibleEnTurno($canchaId, $fecha, $horaDeInicio, $horaDeFin)
       {
           // Realizar la consulta para verificar si hay reservas que se superpongan en la base de datos
           $choques = $this->getEntityManager()->createQueryBuilder()
               ->select('COUNT(r.id)')
               ->from(Reserva::class, 'r')
               ->where('r.cancha = :canchaId')
               ->andWhere('r.fecha = :fecha')
               ->andWhere('r.horaIni < :horaDeFin')
               ->andWhere('r.horaFin > :horaDeInicio')
               ->setParameter('canchaId', $canchaId)
               ->setParameter('fecha', $fecha)
               ->setParameter('horaDeFin', $horaDeFin)
               ->setParameter('horaDeInicio', $horaDeInicio)
               ->getQuery()
               ->getSingleScalarResult();
       
           // La cancha está disponible si no hay choques
           return $choques == 0;
       }

        public function canchasDisponiblesEnFechaYTuno($fecha, $horaDeInicio, $horaDeFin): array {
            /*busca canchas con NO cuenten con reservas ese dia que tengan colisiones de horario*/
            return $this->createQueryBuilder('c')
                ->where('c.id NOT IN (
                    SELECT r.cancha FROM App\Entity\Reserva r
                    WHERE r.fecha = :fecha
                    AND (
                        (r.horaIni < :horaDeFin AND r.horaFin > :horaDeInicio)
                    )
                )')
                ->setParameter('fecha', $fecha)
                ->setParameter('horaDeFin', $horaDeFin)
                ->setParameter('horaDeInicio', $horaDeInicio)
                ->getQuery()
                ->getResult();
        }

    //    /**
    //     * @return Cancha[] Returns an array of Cancha objects
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

    //    public function findOneBySomeField($value): ?Cancha
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
