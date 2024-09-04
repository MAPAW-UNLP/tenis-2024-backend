<?php

namespace App\Controller;

use App\Entity\Usuario;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CustomService as ServiceCustomService;


    /**
     * @Route(path="/api")
     */

class UsuarioController extends AbstractController
{
    /**
     * @Route("/usuarios", name="app_usuarios", methods={"GET"})
     */
    public function getUsuarios(): Response
    {
        $usuarios = $this->getDoctrine()->getRepository( Usuario::class )->findAll();
        return $this->json($usuarios);
    }

    /**
     * @Route("/usuario", name="check_login", methods={"POST"})
     */
    public function checkLogin(
        Request $request, 
        ManagerRegistry $doctrine ,
        ServiceCustomService $cs): Response
    {

        // $user = $request->request->get('user');
        $data = json_decode( $request->getContent());
        $user = $data->user;
        $pass = $data->password;
        

        $userDB = $this->getDoctrine()->getRepository( Usuario::class )->findOneByUsername($user);
       
        if (! isset($userDB)){
            $userDB = array(
                "rta" => "error",
                "detail"=> "nombre de usuario y/o contrase&ntilde;a es invalido(1)."
            );
        } else if ($userDB->getPassword() !== $pass){
            $userDB = array(
                "rta" => "error",
                "detail"=> "nombre de usuario y/o contrase&ntilde;a es invalido(2)."
            );
        } else {

            $cs->procesamientoInicial();

            $userDB = array(
                "rta" => "ok",
                "detail"=> $userDB->getId()
            );

        }
        
        return $this->json(($userDB));
    }

}
