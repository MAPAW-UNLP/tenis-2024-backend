<?php

namespace App\Controller;

use App\Entity\Grupo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

    /**
     * @Route(path="/api")
     */

class GrupoController extends AbstractController
{
    /**
     * @Route("/grupos", name="app_Grupos", methods={"GET"})
     */
    public function getGrupos(): Response
    {
        $grupos = $this->getDoctrine()->getRepository( Grupo::class )->findAll();
        return $this->json($grupos);
    }
}
