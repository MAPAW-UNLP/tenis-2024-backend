<?php

namespace App\Entity;

use App\Repository\GrupoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GrupoRepository::class)
 */
class Grupo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $reserva_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $persona_id;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPersonaId(): ?int
    {
        return $this->persona_id;
    }

    public function setPersonaId(int $persona_id): self
    {
        $this->persona_id = $persona_id;

        return $this;
    }
}
