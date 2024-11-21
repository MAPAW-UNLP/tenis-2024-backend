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
    private $cobros;

    /**
     * @ORM\OneToMany(targetEntity="Clases", mappedBy="cliente")
     */
    private $clases;

    /**
     * @ORM\Column(type="boolean")
     */
    private $esAlumno;//esAlumno

    /**
     * @ORM\Column(type="boolean")
     */
    private $visible;

    public function __construct()
    {
        $this->cobros = new ArrayCollection();
        $this->clases = new ArrayCollection();
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

    /*
    Para reemplazar "getPersonaByPersonaId" de customService luego de que se busque en el repo de Persona
    */
    public function toArrayAsociativo(): array{
        return array(
            "id"    => $this->getId(),
            "nombre"    => $this->getNombre(),
            "telefono"  => $this->getTelefono(),
            "fechanac"  => $this->getFechaNac() ? $this->getFechaNac() : '',
            "saldo"     => 0,
            "esalumno" => $this->isEsAlumno(),
            "visible" => $this->isVisible(),
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

    public function getClases(): ?Collection
    {
        return $this->clases;
    }

    public function addClase(Clases $clase): self
    {
        if (!$this->clases->contains($clase)) {
            $this->clases[] = $clase;
            $clase->setCliente($this);
        }
        return $this;
    }

    public function removeClase(Clases $clase): self
    {
        if ($this->clases->removeElement($clase)) {
            if ($clase->getCliente() === $this) {
                $clase->setCliente(null);
            }
        }
        return $this;
    }

    public function isEsAlumno(): ?bool
    {
        return $this->esAlumno;
    }

    public function setEsAlumno(bool $esAlumno): self
    {
        $this->esAlumno = $esAlumno;

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

      
}