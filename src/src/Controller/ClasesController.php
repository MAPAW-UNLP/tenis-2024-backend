<?php

namespace App\Controller;

use App\Entity\Clases;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


    /**
     * @Route(path="/api")
     */

     
class ClasesController extends AbstractController
{
    /**
     * @Route("/clases", name="app_clases", methods={"GET"})
     */
    public function index(): Response
    {
        $clases = $this->getDoctrine()->getRepository( Clases::class )->findAll();
        return $this->json($clases);
    }
}
