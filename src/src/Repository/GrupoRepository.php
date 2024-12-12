<?php

namespace App\Repository;

use App\Entity\Grupo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Grupo>
 *
 * @method Grupo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Grupo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Grupo[]    findAll()
 * @method Grupo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GrupoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Grupo::class);
    }

    public function add(Grupo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Grupo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    

   public function findPersonasGrupoByReservaId($reservaId): array
   {
       return $this->createQueryBuilder('g')
           ->andWhere('g.reserva_id = :val')
           ->setParameter('val', $reservaId)
           ->orderBy('g.id', 'ASC')
        //    ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }

   /**
     * @return Reserva[] Returns an array of Reserva objects
     */
    public function findReservasBetweenStartDateAndEndDate(\DateTime $startDate, \DateTime $endDate, $cliente)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('r')
            ->from('App\Entity\Reserva', 'r')
            ->where('g.cliente = :cliente')
            ->andWhere('g.reserva_id = r.id')
            ->andWhere('r.fecha BETWEEN :startDate AND :endDate')
            ->setParameter('cliente', $cliente)
            ->setParameter('startDate', $startDate->format('Y-m-d'))
            ->setParameter('endDate', $endDate->format('Y-m-d'))
            ->orderBy('r.fecha', 'ASC');

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Grupo[] Returns an array of Grupo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Grupo
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
