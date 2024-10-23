<?php

namespace App\Entity;

use App\Repository\ClienteRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=ClienteRepository::class)
 */
class Cliente
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
     * @ORM\OneToOne(targetEntity="Usuario", cascade={"persist"}, inversedBy="cliente")
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    private $usuario;

    /**
     * @ORM\OneToMany(targetEntity="Cobro", mappedBy="cliente")
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
            // $cobro->setCliente($this);
        // }

        return $this;
    }

    public function toArrayAsociativo(): array{
        return array(
            "id"    => $this->getId(),
            "nombre"    => $this->getNombre(),
            "telefono"  => $this->getTelefono(),
            "fechanac"  => $this->getFechaNac() ? $this->getFechaNac() : '',
            "saldo"     => 0,
        );
    }

    /** @Ignore() */
    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }
}
