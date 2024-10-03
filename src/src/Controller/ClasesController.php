<?php

namespace App\Controller;

use App\Entity\Clases;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
        $clases = $this->getDoctrine()->getRepository(Clases::class)->findAll();
        return $this->json($clases);
    }

    /**
     * @Route("/addClase", name="AltaTipoClase", methods= {"POST"})
     */
    public function addTipo(
        Request $request,
        ManagerRegistry $doctrine,
        EntityManagerInterface $entityManager
    ): Response {
        try {
            $data = json_decode($request->getContent());
            $tipo = $data->tipo;
            $importe = $data->importe;

            $clase = new Clases();
            $clase->setTipo($tipo);
            $clase->setImporte($importe);

            $em = $doctrine->getManager();
            $em->persist($clase);
            $em->flush();

            return $this->json([
                'status' => 'ok',
                'message' => 'Tipo de clase creado exitosamente'
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Error al crear el tipo de clase',
                'details' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/modClase", name="ModificarTipoClase", methods={"PUT"})
     */
    public function editTipo(Request $request, ManagerRegistry $doctrine): Response
    {

        $data = json_decode($request->getContent());
        $idTipoClase = $data->id ?? null;
        $importeClase = $data->importe ?? null;

        if ($idTipoClase != null && $importeClase > 0 && $importeClase < 100000) {
            $em = $doctrine->getManager();
            $clase = $em->getRepository(Clases::class)->find($idTipoClase);

            if ($clase) {
                $clase->setImporte($importeClase);
                $em->persist($clase);
                $em->flush();
                return $this->json(['status' => 'ok', 'message' => 'Importe actualizado exisosamente'], Response::HTTP_OK);
            } else {
                return $this->json(['status' => 'error', 'message' => 'No se encontro el tipo de clase'], Response::HTTP_NOT_FOUND);
            }
        } else {
            return $this->json(['status' => 'error', 'message' => 'Error al actualizar importe'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/bajaClase", name="EliminarTipoClase", methods={"DELETE"})
     */
    public function deleteTipo(Request $request, ManagerRegistry $doctrine): Response
    {
        $data = json_decode($request->getContent());
        $idTipoClase = $data->id ?? null;

        if ($idTipoClase != null) {
            $em = $doctrine->getManager();
            $clase = $em->getRepository(Clases::class)->find($idTipoClase);
            if ($clase) {
                $em->remove($clase);
                $em->flush();
                return $this->json(['status' => 'ok', 'message' => 'Tipo de clase eliminado satisfactoriamente'], Response::HTTP_OK);
            } else {
                return $this->json(['status' => 'error', 'message' => 'No se encontro el tipo de clase'], Response::HTTP_NOT_FOUND);
            }
        } else {
            return $this->json(['status' => 'error', 'message' => 'Error al eliminar el tipo de clase '], Response::HTTP_BAD_REQUEST);
        }
    }
}
