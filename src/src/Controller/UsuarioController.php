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
        

        $userDB = $this->getDoctrine()->getManager()->getRepository( Usuario::class )->findOneByUsername($user);
       
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
                "detail" => [
                    "id" => $userDB->getId(),
                    "roles" => $userDB->getRoles(),
                    "rolPorDefecto" => $userDB->getRolPorDefecto()
                ]
            );

        }
        
        return $this->json(($userDB));
    }

    

    /**
     * @Route("/usuario", name="change_rolPorDefecto", methods={"PUT"})
     */
    public function cambiarRolPorDefecto(Request $request, ManagerRegistry $doctrine): Response {
        $data = json_decode($request->getContent());
        $usuarioId = $data->id;
        $resp = array();

        if ($usuarioId != null) {
            $em = $doctrine->getManager();
            $usuario = $em->getRepository(Usuario::class)->findOneById($usuarioId);

            if ($usuario != null) {
                if (isset($data->rolPorDefecto)) {
                    if (in_array($data->rolPorDefecto, $usuario->getRoles())) {
                        if ($data->rolPorDefecto != $usuario->getRolPorDefecto()) {
                            $usuario->setRolPorDefecto($data->rolPorDefecto);
                            
                            $em->persist($usuario);
                            $em->flush();
                            
                            $resp['rta'] = "ok";
                            $resp['detail'] = "Rol por defecto actualizado";
                        }
                        else{
                            $resp['rta'] = "error";
                            $resp['detail'] = "El rol ya esta seleccionado como defecto";
                        }
                    }
                    else{
                        $resp['rta'] = "error";
                        $resp['detail'] = "No se pudo asignar el rol";
                    }
                } 
                else {
                    $resp['rta'] = "error";
                    $resp['detail'] = "No existe el usuario";
                }
            } 
        else {
            $resp['rta'] = "error";
            $resp['detail'] = "Debe proveer un id";
        }

        }
        return $this->json($resp);
    }
}
