<?php

namespace App\Controller;

use App\Entity\ItemAlquiler;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route(path="/api")
 */


class ItemAlquilerController extends AbstractController
{
    /**
     * @Route("/itemalquiler", name="app_itemalquileres", methods={"GET"})
     */
    public function index(): Response
    {
        $itemalquiler = $this->getDoctrine()->getRepository(ItemAlquiler::class)->findAll();
        return $this->json($itemalquiler);
    }

    /**
     * @Route("/addItemAlquiler", name="altaItemAlquiler", methods= {"POST"})
     */
    public function addItemAlquiler(
        Request $request,
        ManagerRegistry $doctrine,
        EntityManagerInterface $entityManager
    ): Response {
        try {
            $data = json_decode($request->getContent());
            $desc = $data->desc;
            $importe = $data->importe;

            $itemalquiler = new ItemAlquiler();
            $itemalquiler->setDescription($desc);
            $itemalquiler->setImporte($importe);

            $em = $doctrine->getManager();
            $em->persist($itemalquiler);
            $em->flush();

            return $this->json([
                'status' => 'ok',
                'message' => 'Item para alquilar creado exitosamente'
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Error al crear un nuevo item para alquilar.',
                'details' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/modItemAlquiler", name="ModificarItemAlquiler", methods={"PUT"})
     */
    public function editTipo(Request $request, ManagerRegistry $doctrine): Response
    {
        $data = json_decode($request->getContent());
        $idItemAlquiler = $data->id ?? null;
        $importeItemAlquiler = $data->importe ?? null;
        $descriptionItemAlquiler = $data->desc ?? null;
        if ($idItemAlquiler === null) {
            return $this->json(['status' => 'error', 'message' => 'El ID del item de alquiler es obligatorio'], Response::HTTP_BAD_REQUEST);
        }
        $em = $doctrine->getManager();
        $itemAlquiler = $em->getRepository(ItemAlquiler::class)->find($idItemAlquiler);
        if (!$itemAlquiler) {
            return $this->json(['status' => 'error', 'message' => 'No se encontró el objeto para alquilar'], Response::HTTP_NOT_FOUND);
        }
        $isUpdated = false;
        if ($descriptionItemAlquiler !== null) {
            $itemAlquiler->setDescription($descriptionItemAlquiler);
            $isUpdated = true;
        }

        if ($importeItemAlquiler !== null && $importeItemAlquiler > 0 && $importeItemAlquiler < 100000) {
            $itemAlquiler->setImporte($importeItemAlquiler);
            $isUpdated = true;
        }
        if ($isUpdated) {
            $em->flush();
            return $this->json(['status' => 'ok', 'message' => 'Item de alquiler actualizado exitosamente'], Response::HTTP_OK);
        }
        return $this->json(['status' => 'error', 'message' => 'No se realizaron cambios en el item de alquiler'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/bajaItemAlquiler", name="EliminarItemAlquiler", methods={"DELETE"})
     */
    public function deleteItem(Request $request, ManagerRegistry $doctrine): Response
    {
        $data = json_decode($request->getContent());
        $idItemAlquiler = $data->id ?? null;

        if ($idItemAlquiler != null) {
            $em = $doctrine->getManager();
            $itemalquiler = $em->getRepository(ItemAlquiler::class)->find($idItemAlquiler);
            if ($itemalquiler) {
                $em->remove($itemalquiler);
                $em->flush();
                return $this->json(['status' => 'ok', 'message' => 'Item para alquilar eliminado satisfactoriamente'], Response::HTTP_OK);
            } else {
                return $this->json(['status' => 'error', 'message' => 'No se encontro el objeto que desea eliminar'], Response::HTTP_NOT_FOUND);
            }
        } else {
            return $this->json(['status' => 'error', 'message' => 'Error al eliminar el tipo de clase '], Response::HTTP_BAD_REQUEST);
        }
    }
}
