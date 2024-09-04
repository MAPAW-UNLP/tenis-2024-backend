<?php

namespace App\Entity;

use App\Repository\PeriodoAusenciaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PeriodoAusenciaRepository::class)
 */
class PeriodoAusencia
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
    private $fecha_ini;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha_fin;

    /**
     * @ORM\Column(type="text")
     */
    private $motivo;

    /**
     * @ORM\Column(type="integer")
     */
    private $profesor_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $estado_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaIni(): ?\DateTimeInterface
    {
        return $this->fecha_ini;
    }

    public function setFechaIni(\DateTimeInterface $fecha_ini): self
    {
        $this->fecha_ini = $fecha_ini;

        return $this;
    }

    public function getFechaFin(): ?\DateTimeInterface
    {
        return $this->fecha_fin;
    }

    public function setFechaFin(\DateTimeInterface $fecha_fin): self
    {
        $this->fecha_fin = $fecha_fin;

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
}
