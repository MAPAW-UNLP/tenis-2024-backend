<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UsuarioRepository::class)
 */
class Usuario
{
    /**
     * @ORM\Id
     * @Groups("usuario")
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $password;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechapagos;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechareplica;


    
    /**
     * @Groups("usuario")
     * @ORM\OneToOne(targetEntity="Profesor", mappedBy="usuario")
     */
    private $profesor;

    /**
     * @Groups("usuario")
     * @ORM\OneToOne(targetEntity="Administrador", mappedBy="usuario")
     */
    private $administrador;

    /**
     * @Groups("usuario")
     * @ORM\OneToOne(targetEntity="Alumno", mappedBy="usuario")
     */
    private $alumno;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $rolPorDefecto;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFechapagos(): ?\DateTimeInterface
    {
        return $this->fechapagos;
    }

    public function setFechapagos(?\DateTimeInterface $fechapagos): self
    {
        $this->fechapagos = $fechapagos;

        return $this;
    }

    public function getFechareplica(): ?\DateTimeInterface
    {
        return $this->fechareplica;
    }

    public function setFechareplica(?\DateTimeInterface $fechareplica): self
    {
        $this->fechareplica = $fechareplica;

        return $this;
    }
    public function getProfesor(): ?Profesor
    {
        return $this->profesor;
    }

    public function setProfesor(?Profesor $profesor): void
    {
        $this->profesor = $profesor;
    }

    public function getAdministrador(): ?Administrador
    {
        return $this->administrador;
    }

    public function setAdministrador(?Administrador $administrador): void
    {
        $this->administrador = $administrador;
    }

    public function getAlumno(): ?Alumno
    {
        return $this->alumno;
    }

    public function setAlumno(?Alumno $alumno): void
    {
        $this->alumno = $alumno;
    }

    public function getRolPorDefecto(): ?string
    {
        return $this->rolPorDefecto;
    }

    public function setRolPorDefecto(?string $rolPorDefecto): self
    {
        if (in_array($rolPorDefecto, $this->getRoles())) {
            $this->rolPorDefecto = $rolPorDefecto;
        }

        return $this;
    }

    public function getRoles(): array
    {
        $roles = [];

        if ($this->profesor) {
            $roles[] = "Profesor";
        }

        if ($this->alumno) {
            $roles[] = "Alumno";
        }

        if ($this->administrador) {
            $roles[] = "Administrador";
        }

        return $roles;
    }




}
