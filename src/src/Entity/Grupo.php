<?php

namespace App\Entity;

use App\Repository\GrupoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

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
     * @ORM\ManyToOne(targetEntity="Cliente", inversedBy="cobros")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     * @Ignore
     */
    private $cliente;

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
        return $this->cliente->getId();
    }

    public function setClienteId(int $cliente_id): self
    {
        $this->cliente = $cliente_id;

        return $this;
    }

    public function setCliente(?Cliente $cliente): self
    {
        $this->cliente = $cliente;
        return $this;
    }

    public function getCliente(): ?Cliente
    {
        return $this->cliente;
    }
}
