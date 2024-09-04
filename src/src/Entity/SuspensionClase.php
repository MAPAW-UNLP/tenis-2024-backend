<?php

namespace App\Entity;

use App\Repository\SuspensionClaseRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SuspensionClaseRepository::class)
 */
class SuspensionClase
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha;

    /**
     * @ORM\Column(type="time")
     */
    private $hora;

    /**
     * @ORM\Column(type="integer")
     */
    private $profesor_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $estado_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $reserva_id;

    /**
     * @ORM\Column(type="text")
     */
    private $motivo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getHora(): ?\DateTimeInterface
    {
        return $this->hora;
    }

    public function setHora(\DateTimeInterface $hora): self
    {
        $this->hora = $hora;

        return $this;
    }

    public function getProfesorId(): ?int
    {
        return $this->profesor_id;
    }

    public function setProfesorId(int $profesor_id): self
    {
        $this->profesor_id = $profesor_id;

        return $this;
    }

    public function getEstadoId(): ?int
    {
        return $this->estado_id;
    }

    public function setEstadoId(int $estado_id): self
    {
        $this->estado_id = $estado_id;

        return $this;
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

    public function getMotivo(): ?string
    {
        return $this->motivo;
    }

    public function setMotivo(string $motivo): self
    {
        $this->motivo = $motivo;

        return $this;
    }
}
