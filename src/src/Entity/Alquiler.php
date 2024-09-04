<?php

namespace App\Entity;

use App\Repository\AlquilerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AlquilerRepository::class)
 */
class Alquiler
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
     * @ORM\Column(type="integer")
     */
    private $reserva_id;

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

    public function getReservaId(): ?int
    {
        return $this->reserva_id;
    }

    public function setReservaId(int $reserva_id): self
    {
        $this->reserva_id = $reserva_id;

        return $this;
    }
}
