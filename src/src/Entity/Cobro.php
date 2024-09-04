<?php

namespace App\Entity;

use App\Repository\CobroRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CobroRepository::class)
 */
class Cobro
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idPersona;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idTipoClase;

    /**
     * @ORM\Column(type="float")
     */
    private $monto;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha;

    /**
     * @ORM\Column(type="time")
     */
    private $hora;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $concepto;

    /**
     * @ORM\ManyToOne(targetEntity="Alumno", inversedBy="cobros")
     * @ORM\JoinColumn(name="alumno_id", referencedColumnName="id", nullable=true)
     */
    private $alumno;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $descripcion;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPersona(): ?int
    {
        return $this->idPersona;
    }

    public function setIdPersona(int $idPersona): self
    {
        $this->idPersona = $idPersona;

        return $this;
    }

    public function getIdTipoClase(): ?int
    {
        return $this->idTipoClase;
    }

    public function setIdTipoClase(int $idTipoClase): self
    {
        $this->idTipoClase = $idTipoClase;

        return $this;
    }

    public function getMonto(): ?int
    {
        return $this->monto;
    }

    public function setMonto(int $monto): self
    {
        $this->monto = $monto;

        return $this;
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

    /**
     * @return mixed
     */
    public function getHora()
    {
        return $this->hora;
    }

    /**
     * @param mixed $hora
     */
    public function setHora($hora): void
    {
        $this->hora = $hora;
    }

    public function getConcepto(): ?string
    {
        return $this->concepto;
    }

    public function setConcepto(string $concepto): self
    {
        $this->concepto = $concepto;

        return $this;
    }

    public function getAlumno(): ?Alumno
    {
        return $this->alumno;
    }

    public function setAlumno(?Alumno $alumno): self
    {
        $this->alumno = $alumno;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }
}
