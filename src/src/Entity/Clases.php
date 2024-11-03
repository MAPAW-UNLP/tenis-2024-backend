<?php

namespace App\Entity;

use App\Repository\ClasesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ClasesRepository::class)
 */
class Clases
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"clases"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     * @Groups({"clases"})
     */
    private $tipo;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"clases"})
     */
    private $importe;

    /**
     * @ORM\ManyToOne(targetEntity="Cliente", inversedBy="clases")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     * @Groups({"clases"})
     * @Ignore()
     */
    private $cliente;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"clases"})
     */
    private $cancha_id;
    
    /**
     * @ORM\Column(type="date")
     * @Groups({"clases"})
     */
    private $fecha;

    /**
     * @ORM\Column(type="time")
     * @Groups({"clases"})
     */
    private $hora_ini;

    /**
     * @ORM\Column(type="time")
     * @Groups({"clases"})
     */
    private $hora_fin;

    /**
     * @ORM\ManyToOne(targetEntity="Profesor", inversedBy="pagos")
     * @ORM\JoinColumn(name="profesor_id", referencedColumnName="id", nullable=true)
     * @Groups({"clases"})
     */
    private $profesor;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getImporte(): ?int
    {
        return $this->importe;
    }

    public function setImporte(int $importe): self
    {
        $this->importe = $importe;

        return $this;
    }

    public function getCliente(): ?Cliente
    {
        return $this->cliente;
    }

    public function setCliente(?Cliente $cliente): self
    {
        $this->cliente = $cliente;
        return $this;
    }

    public function getCanchaId(): ?int
    {
        return $this->cancha_id;
    }

    public function setCanchaId(int $cancha_id): self
    {
        $this->cancha_id = $cancha_id;

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

    public function getHoraIni(): ?\DateTimeInterface
    {
        return $this->hora_ini;
    }

    public function setHoraIni(\DateTimeInterface $hora_ini): self
    {
        $this->hora_ini = $hora_ini;

        return $this;
    }

    public function getHoraFin(): ?\DateTimeInterface
    {
        return $this->hora_fin;
    }

    public function setHoraFin(\DateTimeInterface $hora_fin): self
    {
        $this->hora_fin = $hora_fin;

        return $this;
    }

    public function getProfesor(): ?Profesor
    {
        return $this->profesor;
    }

    public function setProfesor(?Profesor $profesor): self
    {
        $this->profesor = $profesor;

        return $this;
    }
}
