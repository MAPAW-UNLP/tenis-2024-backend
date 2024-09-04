<?php

namespace App\Entity;

use App\Repository\AdministradorRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AdministradorRepository::class)
 */
class Administrador
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
    private $user;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $password;
    /**
     * @ORM\OneToOne(targetEntity="Usuario", cascade={"persist"}, inversedBy="administrador")
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    private $usuario;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

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
