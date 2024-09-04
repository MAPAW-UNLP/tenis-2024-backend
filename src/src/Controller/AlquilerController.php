<?php

namespace App\Controller;

use App\Entity\Alquiler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

    /**
     * @Route(path="/api")
     */

class AlquilerController extends AbstractController
{
    /**
     * @Route("/alquiler", name="app_alquileres", methods={"GET"})
     */
    public function getAlquileres(): Response
    {
        $alquileres = $this->getDoctrine()->getRepository( Alquiler::class )->findAll();
        return $this->json($alquileres);
    }
}
