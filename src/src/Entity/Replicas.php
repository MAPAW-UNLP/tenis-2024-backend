<?php

namespace App\Entity;

use App\Repository\ReplicasRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReplicasRepository::class)
 */
class Replicas
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $idReserva;

    /**
     * @ORM\Column(type="integer")
     */
    private $ultimoMes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdReserva(): ?int
    {
        return $this->idReserva;
    }

    public function setIdReserva(int $idReserva): self
    {
        $this->idReserva = $idReserva;

        return $this;
    }

    public function getUltimoMes(): ?int
    {
        return $this->ultimoMes;
    }

    public function setUltimoMes(int $ultimoMes): self
    {
        $this->ultimoMes = $ultimoMes;

        return $this;
    }
}
