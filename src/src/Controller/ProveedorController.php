<?php

namespace App\Controller;

use App\Entity\Proveedor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route(path="/api")
*/
class ProveedorController extends AbstractController
{
    /**
     * @Route("/proveedor", name="app_get_proveedores", methods={"GET"})
    */
    public function getProveedores(): Response
    {   
        $proveedor = $this->getDoctrine()->getRepository( Proveedor::class )->findAll();
        return $this->json($proveedor);
    }

    /**
     * @Route("/proveedor", name="app_get_proveedor", methods={"GET"})
    */
    public function getProveedor(Request $request, ManagerRegistry $doctrine): Response
    {
        $proveedoresId = $request->query->get('proveedoresId');
        $em = $doctrine->getManager();
        $proveedor = $em->getRepository( Proveedor::class )->findOneById($proveedoresId);
        return $this->json($proveedor);
    }


    /**
     * @Route("/proveedor", name="app_alta_proveedor", methods={"POST"})
    */
    public function addProveedor(Request $request, ManagerRegistry $doctrine,
     EntityManagerInterface $entityManager): Response
    {

        $data = json_decode( $request->getContent());
        $nombre = $data->nombre;
        $telefono = $data->telefono;

        $proveedor = new Proveedor();
        $proveedor->setNombre($nombre)->setTelefono($telefono);

        $em = $doctrine->getManager();
        $em->persist($proveedor);
        $em->flush();
      
        if ($proveedor->getId() > 0){
            $resp['rta'] =  "ok";
            $resp['detail'] = "Proveedor dado de alta exitosamente.";
            // Persiste las entidades en la base de datos
            $entityManager->persist($proveedor);
            // Aplico los cambios en la base de datos
            $entityManager->flush();
        } else {
            $resp['rta'] =  "error";
            $resp['detail'] = "Se produjo un error en el alta de al proveedor.";
        }
        return $this->json(($resp));
    }
}