<?php

namespace App\Entity;

use App\Repository\PersonaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PersonaRepository::class)
 */
class Persona
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nombre;

    // /**
    //  * @ORM\Column(type="string", length=50)
    //  */
    // private $apellido;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $telefono;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechanac;

    /**
     * @ORM\Column(type="boolean")
     */
    private $escliente;

    /**
     * @ORM\Column(type="boolean")
     */
    private $visible;

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

    // public function getApellido(): ?string
    // {
    //     return $this->apellido;
    // }

    // public function setApellido(string $apellido): self
    // {
    //     $this->apellido = $apellido;

    //     return $this;
    // }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getFechanac(): ?\DateTimeInterface
    {
        return $this->fechanac;
    }

    public function setFechanac(?\DateTimeInterface $fechanac): self
    {
        $this->fechanac = $fechanac;

        return $this;
    }

    public function isEscliente(): ?bool
    {
        return $this->escliente;
    }

    public function setEscliente(bool $escliente): self
    {
        $this->escliente = $escliente;

        return $this;
    }

    public function isVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    /*
    Para reemplazar "getPersonaByPersonaId" de customService luego de que se busque en el repo de Persona
    */
    public function toArrayAsociativo(): array
    {
        return array(
            "id" => $this->getId(),
            "nombre" => $this->getNombre(),
            "telefono" => $this->getTelefono(),
            "esalumno" => $this->isEsAlumno(),
            "visible" => $this->isVisible(),
        );
    }
}
