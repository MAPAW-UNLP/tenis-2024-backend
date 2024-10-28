<?php

namespace App\Repository;

use App\Entity\ItemAlquiler;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<src/src/Entity/ItemAlquiler>
 *
 * @method ItemAlquiler|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemAlquiler|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemAlquiler[]    findAll()
 * @method ItemAlquiler[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemAlquilerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemAlquiler::class);
    }

    public function add(ItemAlquiler $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ItemAlquiler $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneById($id): ?ItemAlquiler
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
    * @return ItemAlquiler[] Returns an array of ItemAlquiler objects
    */
    public function findAll(): array
    {
        return $this->createQueryBuilder('c')
            ->getQuery()
            ->getResult()
        ;
    }
}
