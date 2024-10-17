<?php

namespace App\Controller;

use App\Entity\Cliente;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;



/**
 * @Route(path="/api")
*/
class ClienteController extends AbstractController
{
    
    /**
     * @Route("/clientes", name="app_get_clientes", methods={"GET"})
    */
    public function getClientes(): Response
    {   
        $clientes = $this->getDoctrine()->getRepository( Cliente::class )->findAll();

        return $this->json($clientes);
    }

    /**
     * @Route("/cliente", name="app_get_cliente", methods={"GET"})
    */
    public function getCliente(Request $request, ManagerRegistry $doctrine): Response
    {
        $clientesId = $request->query->get('clienteId');
        $em = $doctrine->getManager();
        $cliente = $em->getRepository( Cliente::class )->findOneById($clientesId);
        return $this->json($cliente);
    }


    /**
     * @Route("/cliente", name="app_alta_cliente", methods={"POST"})
    */
    public function addCliente(Request $request, ManagerRegistry $doctrine,
    EntityManagerInterface $entityManager): Response
   {

       $data = json_decode( $request->getContent());
       $nombre = $data->nombre;
       $telefono = $data->telefono;
       $fecha_nac = isset($data->fechaNac) &&  strlen($data->fechaNac) > 0 ? new DateTime($data->fechaNac): null;

       $cliente = new Cliente();
       $usuario = new Usuario();
       $cliente->setNombre($nombre)->setTelefono($telefono);
       $cliente -> setFechaNac($fecha_nac);

       $usuario->setUsername($cliente->getNombre() + $cliente->getTelefono()); // cambiar método desde el cliente
       $usuario->setCliente($cliente); // Idem
       $usuario->setRolPorDefecto('ROLE_CLIENTE'); // seteo el nombre del rol, para podes acceder a las rutas
       $cliente->setUsuario($usuario);

       $em = $doctrine->getManager();
       $em->persist($cliente);
       $em->flush();
     
       if ($cliente->getId() > 0){

           $resp['rta'] =  "ok";
           $resp['detail'] = "Cliente dado de alta exitosamente.";
           
           // Persiste las entidades en la base de datos
           $entityManager->persist($cliente);
           $entityManager->persist($usuario);
           // Aplico los cambios en la base de datos
           $entityManager->flush();

       } else {
           $resp['rta'] =  "error";
           $resp['detail'] = "Se produjo un error en el alta de al cliente.";
       }

       return $this->json(($resp));
   }
    /**
     * @Route("/cliente", name="app_mod_cliente", methods={"PUT"})
    */
    public function modCliente(Request $request, ManagerRegistry $doctrine): Response {
        $data = json_decode( $request->getContent());
        $clienteId = $data->id;
        $resp = array();

        if ($clienteId != null) {
            $em = $doctrine->getManager();
            $cliente = $em->getRepository( Cliente::class )->findOneById($clienteId);

            if ($cliente!=null){
                if (isset($data->nombre)){
                    $cliente->setNombre($data->nombre);
                }
                if (isset($data->telefono)){
                    $cliente->setTelefono($data->telefono);
                }

                $em->persist($cliente);
                $em->flush();

                $resp['rta'] =  "ok";
                $resp['detail'] = "Cliente modificado correctamente";
            } else {
                $resp['rta'] =  "error";
                $resp['detail'] = "No existe el cliente";
            }
        } else {
            $resp['rta'] =  "error";
            $resp['detail'] = "Debe proveer un id";
        }
        return $this->json($resp);
    }



}
