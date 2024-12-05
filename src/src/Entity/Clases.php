<?php

namespace App\Entity;

use App\Repository\ClasesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ClasesRepository::class)
 */
class Clases
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"clases"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     * @Groups({"clases"})
     */
    private $tipo;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"clases"})
     */
    private $importe;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getImporte(): ?int
    {
        return $this->importe;
    }

    public function setImporte(int $importe): self
    {
        $this->importe = $importe;

        return $this;
    }
}
