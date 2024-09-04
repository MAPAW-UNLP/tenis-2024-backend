<?php

namespace App\Controller;

use App\Entity\Cancha;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

    /**
     * @Route(path="/api")
     */

class CanchaController extends AbstractController
{
    /**
     * @Route("/canchas", name="app_canchas", methods={"GET"})
     */
    public function getCanchas(): Response
    {
        $canchas = $this->getDoctrine()->getRepository( Cancha::class )->findAll();

        $resp = array(
            "rta"=> "error",
            "detail"=> "Se produjo un error en el alta de la cancha."
        );
        if (isset($canchas)){

            $resp['rta'] =  "ok";
            $resp['detail'] = $canchas;

        }

        return $this->json($resp);
    }



    /**
     * @Route("/cancha", name="add_canchas", methods={"POST"})
     */
    public function addCancha(Request $request, ManagerRegistry $doctrine ): Response
    {

        $data = json_decode( $request->getContent());
        $nombreCancha = $data->nombre;
        $tipoCancha = $data->tipo;
        

        $cancha = new Cancha();
        $cancha->setNombre($nombreCancha);
        $cancha->setTipo($tipoCancha);

        $em = $doctrine->getManager();
        $em->persist($cancha);
        $em->flush();

    
        $resp = array(
            "rta"=> "error",
            "detail"=> "Se produjo un error en el alta de la cancha."
        );
        if ($cancha->getId() > 0){

            $resp['rta'] =  "ok";
            $resp['detail'] = $cancha;

        }

        return $this->json(($resp));
    }



    /**
     * @Route("/canchas", name="mod_canchas", methods={"PUT"})
     */
    public function modCancha(Request $request, ManagerRegistry $doctrine ): Response
    {

        $id = $request->request->get('id');
        $name = $request->request->get('nombre');

        $em = $doctrine->getManager();
        $cancha = $em->getRepository(Cancha::class)->findOneById($id);
        // dd($cancha);
        $cancha->setNombre($name);

        $em = $doctrine->getManager();
        $em->persist($cancha);
        $em->flush();

        return $this->json(($name));
    }

}
