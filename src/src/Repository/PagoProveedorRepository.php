<?php

namespace App\Repository;

use App\Entity\PagoProveedor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pagos>
 *
 * @method PagoProveedor|null find($id, $lockMode = null, $lockVersion = null)
 * @method PagoProveedor|null findOneBy(array $criteria, array $orderBy = null)
 * @method PagoProveedor[]    findAll()
 * @method PagoProveedor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagoProveedorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PagoProveedor::class);
    }

    public function add(PagoProveedor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PagoProveedor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * @return PagoProveedor[] Returns an array of Pagos objects
     */
    public function findPagosByProveedorId($proveedorId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.idProveedor = :val')
            ->setParameter('val', $proveedorId)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return PagoProveedor[] Returns an array of Pagos objects
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('c')
            ->getQuery()
            ->getResult();
    }

    // public function findPagosByProfesorIdInDates($profesorId, $primerDia, $ultimoDia): array
    // {
    //     return $this->createQueryBuilder('p')
    //         ->andWhere('p.profesor = :profesorId')
    //         ->andWhere('p.fecha >= :primerDia') // Para obtener solo pagos futuros
    //         ->andWhere('p.fecha <= :ultimoDia') // Para obtener solo pagos futuros
    //         ->setParameter('profesorId', $profesorId)
    //         ->setParameter('primerDia', $primerDia) // Corregido aquí
    //         ->setParameter('ultimoDia', $ultimoDia) // Corregido aquí
    //         ->getQuery()
    //         ->getResult();
    // }


    //    /**
    //     * @return Pagos[] Returns an array of Pagos objects
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

    //    public function findOneBySomeField($value): ?Pagos
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
