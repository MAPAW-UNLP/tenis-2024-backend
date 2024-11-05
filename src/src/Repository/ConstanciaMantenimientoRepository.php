<?php

namespace App\Repository;

use App\Entity\ConstanciaMantenimiento;
use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ConstanciaMantenimientoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConstanciaMantenimiento::class);
    }

    public function getLastConstancia(): ConstanciaMantenimiento
    {
        try{
            $ultima = $this->createQueryBuilder('c')
                ->orderBy('c.fecha', 'DESC') //ordenar por fecha (el que tenga la mayor primero)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        
            if ($ultima != null){
                return $ultima;
            }

            $ultima = new ConstanciaMantenimiento();
            $ultima->setFecha(new DateTime("1999-10-10"));
            $em = $this->getEntityManager();
            $em->persist($ultima);
            $em->flush();

            return $ultima;

        } catch (\Exception $e) {
            throw new \RuntimeException("Error al obtener la última constancia: " . $e->getMessage());
        }
    }

    public function updateLastConstancia(DateTimeInterface $fecha): ConstanciaMantenimiento
    {
        try{
            $ultima = $this->getLastConstancia();
            $ultima->setFecha($fecha);
            $em = $this->getEntityManager();
            $em->persist($ultima);
            $em->flush();

            return $ultima;

        } catch (\Exception $e) {
            throw new \RuntimeException("Error al actualizar la última constancia: " . $e->getMessage());
        }
    }

}