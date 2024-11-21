<?php

namespace App\Entity;

use App\Repository\GrupoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GrupoRepository::class)
 */
class Grupo
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
    private $reserva_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $cliente_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReservaId(): ?int
    {
        return $this->reserva_id;
    }

    public function setReservaId(int $reserva_id): self
    {
        $this->reserva_id = $reserva_id;

        return $this;
    }

    public function getClienteId(): ?int
    {
        return $this->cliente_id;
    }

    public function setClienteId(int $cliente_id): self
    {
        $this->cliente_id = $cliente_id;

        return $this;
    }
}
