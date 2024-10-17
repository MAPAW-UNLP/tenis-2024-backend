<?php

namespace App\Entity;

use App\Repository\AdministradorRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

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
     * @ORM\OneToOne(targetEntity="Usuario", cascade={"persist"}, inversedBy="administrador")
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    private $usuario;

    public function getId(): ?int
    {
        return $this->id;
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
