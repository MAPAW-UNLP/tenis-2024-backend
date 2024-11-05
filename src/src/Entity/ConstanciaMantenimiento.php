<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

/**
 * @ORM\Entity(repositoryClass=ConstanciaMantenimientoRepository::class)
 */

 /*No busco generar multiples tuplas porque no hay necesidad de auditar esto.
Solo es para no sobrecargar cada logueo: se ejecuta 1 vez al día, a menos que alguien quiera
meter mano y utilizar el setter para lo que necesite
 */

class ConstanciaMantenimiento
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha;

    public function getFecha(): ?DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(DateTimeInterface $fecha): ?DateTimeInterface
    {
        $this->fecha = $fecha;
        return $this->fecha;
    }
}