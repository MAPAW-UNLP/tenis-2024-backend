<?php

namespace App\Entity;

use App\Repository\ProfesorRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=ProfesorRepository::class)
 */
class Profesor
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

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $telefono;


    /**
     * @ORM\OneToOne(targetEntity="Usuario", cascade={"persist"}, inversedBy="profesor")
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */

     private $usuario;

    /**
     * @ORM\OneToMany(targetEntity="Pagos", mappedBy="profesor")
    */
    private $pagos;

    /**
     * @ORM\OneToMany(targetEntity="Clases", mappedBy="profesor")
    */
    private $clases;


    public function __construct()
    {
        $this->pagos = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    /** @Ignore() */
    public function getPagos(): Collection
    {
        return $this->pagos;
    }

    public function addPago(Pagos $pago): self
    {
        if (!$this->pagos->contains($pago)) {
            $this->pagos[] = $pago;
            // $pago->setProfesor($this);
        }

        return $this;
    }

    /** @Ignore() */
    public function getClases(): Collection
    {
        return $this->clases;
    }

    public function addClase(Clases $clase): self
    {
        if (!$this->clases->contains($clase)) {
            $this->clases[] = $clase;
            // $clase->setProfesor($this);
        }

        return $this;
    }
}
