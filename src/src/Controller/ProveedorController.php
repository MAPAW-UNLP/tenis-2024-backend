<?php

namespace App\Controller;

use App\Entity\Proveedor;
use App\Repository\ProveedorRepository;
use App\Repository\PagosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route(path="/api")
 */
class ProveedorController extends AbstractController {
    /**
     * @Route("/proveedores", name="app_get_proveedores", methods={"GET"})
     */
    public function getProveedores(): Response
    {
        $proveedor = $this->getDoctrine()->getRepository(Proveedor::class)->findAll();
        return $this->json($proveedor);
    }

    /**
     * @Route("/proveedores/{id}", name="app_get_proveedor_by_id", methods={"GET"})
    */
    public function getProveedor(Request $request,$id, ProveedorRepository $proveedorRepository): Response
    {
        $proveedor = $proveedorRepository->findOneById($id);
        if(!$proveedor){
            return $this->json([
                'message' => 'No se ha encontrado el proveedor.',
            ])->setStatusCode(404);
        }
        return $this->json($proveedor);
    }


    /**
     * @Route("/proveedores", name="app_alta_proveedor", methods={"POST"})
    */
    public function addProveedor(Request $request, ManagerRegistry $doctrine,
     EntityManagerInterface $entityManager): Response
    {

        $data = json_decode($request->getContent());
        $nombre = $data->nombre;
        $telefono = $data->telefono;

        $proveedor = new Proveedor();
        $proveedor->setNombre($nombre)->setTelefono($telefono);

        $em = $doctrine->getManager();
        $em->persist($proveedor);
        $em->flush();

        if ($proveedor->getId() > 0) {
            $resp['rta'] = "ok";
            $resp['detail'] = "Proveedor dado de alta exitosamente.";
            // Persiste las entidades en la base de datos
            $entityManager->persist($proveedor);
            // Aplico los cambios en la base de datos\
        } else {
            $resp['rta'] = "error";
            $resp['detail'] = "Se produjo un error en el alta de al proveedor.";
        }
        return $this->json(($resp));
    }

    /**
    * @Route("/proveedores/{id}", name="app_modificar_proveedor", methods={"PUT"})
    */
    public function updateProveedor(Request $request, $id, ProveedorRepository $proveedorRepository): Response {
      $data = json_decode($request->getContent());
      $nombre = $data->nombre ?? null;
      $telefono = $data->telefono ?? null;

      // Buscar proveedor por ID
      if (null !== $proveedor = $proveedorRepository->find($id)) {
        if ($nombre !== null) {
            $proveedor->setNombre($nombre);
        }

        if ($telefono !== null) {
            $proveedor->setTelefono($telefono);
        }

        $proveedorRepository->add($proveedor, true);

        return $this->json($proveedor)->setStatusCode(200);
      }

      // Proveedor no encontrado
      return $this->json([
        'message' => 'No se ha encontrado el proveedor.',
      ])->setStatusCode(404);
    }

      
    /**
     * @Route("/proveedores/{id}", name="app_baja_proveedor", methods={"DELETE"})
     */
    public function deleteProveedor($id, ProveedorRepository $proveedorRepository, PagosRepository $pagosRepository): Response
    {
        $proveedor = $proveedorRepository->findOneById($id);

        if ($proveedor) {
            $pagos = $pagosRepository->findPagosByProveedorId($id);
            foreach ($pagos as $pago) {
                $pagosRepository->remove($pago, true);
            }
            $proveedorRepository->remove($proveedor, true);
            return $this->json([
                'message' => 'Se ha eliminado el proveedor.',
                'data' => $proveedor,
            ], 200);
        } else {
            return $this->json([
                'message' => 'No se pudo eliminar el proveedor.',
                'data' => $proveedor,
            ], 400);
        }
    }
}