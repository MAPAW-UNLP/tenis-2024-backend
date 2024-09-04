<?php

namespace App\Repository;

use App\Entity\Persona;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Persona>
 *
 * @method Persona|null find($id, $lockMode = null, $lockVersion = null)
 * @method Persona|null findOneBy(array $criteria, array $orderBy = null)
 * @method Persona[]    findAll()
 * @method Persona[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Persona::class);
    }

    public function add(Persona $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Persona $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneById($id): ?Persona
   {
       return $this->createQueryBuilder('p')
           ->andWhere('p.id = :val')
           ->setParameter('val', $id)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }

    /**
    * @return Persona[] Returns an array of Persona objects (alumnos)
    */
   public function findAllAlumnos(): array
   {
       return $this->createQueryBuilder('p')
           ->andWhere('p.esalumno = :val')
           ->setParameter('val', 1)
           ->orderBy('p.id', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }

    /**
    * @return Persona[] Returns an array of Persona objects (alumnos)
    */
    public function findAllProfesores(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.esalumno = :val')
            ->setParameter('val', 0)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Persona[] Returns an array of Persona objects
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

//    public function findOneBySomeField($value): ?Persona
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
