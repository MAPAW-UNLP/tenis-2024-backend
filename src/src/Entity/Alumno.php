<?php

namespace App\Entity;

use App\Repository\AlumnoRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=AlumnoRepository::class)
 */
class Alumno
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $telefono;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaNac;

    /**
     * @ORM\OneToMany(targetEntity="Cobro", mappedBy="alumno")
    */
    /** @Ignore() */
    private $cobros;

    public function __construct()
    {
        $this->cobros = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }


    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getFechaNac(): ?\DateTimeInterface
    {
        return $this->fechaNac;
    }

    public function setFechaNac(?\DateTimeInterface $fechaNac): self
    {
        $this->fechaNac = $fechaNac;

        return $this;
    }

    /** @Ignore() */
    public function getCobros(): ?Collection
    {
        return $this->cobros;
    }

    public function addCobro(Cobro $cobro): self
    {
//        if (!$this->cobros->contains($cobro)) {
        // if (!$this->getCobros()->contains($cobro)) {
            $this->cobros[] = $cobro;
            // $cobro->setAlumno($this);
        // }

        return $this;
    }

}
