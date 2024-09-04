<?php

namespace App\Controller;

use App\Entity\Pagos;
use App\Entity\Persona;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CustomService as ServiceCustomService;
use DateTime;
use Symfony\Component\HttpFoundation\Request;

    /**
     * @Route(path="/api")
     */

class PagosController extends AbstractController
{

     /**
     * @Route("/pagos", name="get_pagos", methods={"GET"})
     */
    public function getPagos(
        Request $request,
        ManagerRegistry $doctrine,
        ServiceCustomService $cs
    ): Response
    {

        $em = $doctrine->getManager();

        $pagos = $em->getRepository( Pagos::class )->findAll();

        $objPagos = array();

        foreach ($pagos as $pago) {
            if ($pago -> getMotivo() === '1'){
                $concepto_desc = 'Profesor';
            }
            elseif ($pago -> getMotivo() === '2'){
                $concepto_desc = 'Proveedor';
            }
            else{
                $concepto_desc = 'Varios';
            }
            array_push($objPagos, array(
                'id' => $pago->getId(),
                'idTipoClase' => $pago->getIdTipoClase() ? $pago -> getIdTipoClase() : null,
                'monto' => $pago->getMonto(), // monto
                'fecha' => $cs->getFormattedDate($pago->getFecha()),
                'fecha_format' => $pago->getFecha()->format('d/m/y'),
                'hora' => $pago->getHora()->format('H:i'),
                'concepto_id' => $pago->getMotivo(),
                'concepto_desc' => $concepto_desc,
                'idProfesor' => $pago->getProfesor() ? $pago->getProfesor()->getId() : null,
                'nomProfesor' => $pago->getProfesor() ? $pago->getProfesor()->getNombre() : "",
                'descripcion' => $pago->getDescripcion()
            ));

        }

        return $this->json($objPagos);
    }


    /**
     * @Route("/pagos_por_profesor", name="app_Pagos_profesorId", methods={"GET"})
    */
    public function getPagosByProfesorid(
        Request $request,
        ManagerRegistry $doctrine,
        ServiceCustomService $cs
    ): Response
    {
        $profesorId = $request->query->get('profesorId');

        // que el profesorId no sea nulo y sea un id válido
        // if ($profesorId === null || !is_numeric($profesorId)) {
        //     return new JsonResponse(['error' => 'ID de profesor no válido'], JsonResponse::HTTP_BAD_REQUEST);
        // }

        $em = $doctrine->getManager();

        $pagos = $em->getRepository( Pagos::class )->findBy(['profesor' => $profesorId]);

        $objPagos = array();
        foreach($pagos as $pago){

           array_push($objPagos, array(
            "nomProfesor" => $pago->getProfesor() ? $pago->getProfesor()->getNombre() : "",
            "idTipoClase" => $pago->getIdTipoClase(), 
            "monto" => $pago->getMonto(), // = monto
            "fecha" => $cs->getFormattedDate($pago->getFecha()),
            "motivo" => $pago->getMotivo(),
            'descripcion' => $pago->getDescripcion() 
            ));
        }

        return $this->json($objPagos);
    }


     /**
     * @Route("/pagos", name="add_pagos", methods={"POST"})
     */
    public function addPagos(
        Request $request, 
        ServiceCustomService $cs
        ): Response
    {

        $data = json_decode( $request->getContent());
        $descripcion = $data->descripcion;
        $pagos = $data->pagos;
        $pagosArray =  explode(',',$pagos);
        $fecha =  isset($data->fecha)? new DateTime($data->fecha) : null;
        
        foreach($pagosArray as $pago){
            $data = explode(':', $pago );
            //data[0] motivo, data[1]  = monto
            $cs->registrarPago($data[0], $data[1], $descripcion,$fecha);
        }
    
        $resp = array(
            "rta"=> "ok",
            "detail"=> "Registro de pagos exitoso."
        );

        return $this->json(($resp));
    }

    /**
     * @Route("/nuevo_pago", name="add_nuevo_pago", methods={"POST"})
     */
    public function addPago(
        Request $request,
        ServiceCustomService $cs
    ): Response
    {
        // PAGO GENERICO SIN PROFESOR
        $data = json_decode($request->getContent());

        $descripcion = $data->descripcion;
        $monto = $data -> monto;
        $motivo = $data -> concepto;

        $fecha =  isset($data->fecha) ? new DateTime($data -> fecha) : null;

        if (isset($data->profesorId)){
            $cs->registrarPagoProfesor($data->profesorId, $descripcion, $motivo, $monto, $fecha);
        }
        else{
            $cs->registrarPago($motivo, $monto, $descripcion, $fecha);
        }

        $resp = array(
            "rta"=> "ok",
            "detail"=> "Registro de pagos exitoso."
        );

        return $this->json(($resp));
    }

     /**
     * @Route("/pagosProfesor", name="add_pagosProfesor", methods={"POST"})
     */
    public function addPagoProfesor(
        Request $request, 
        ServiceCustomService $cs
         ): Response
    {

        $data = json_decode( $request->getContent());
        $idProfesor = $data->idProfesor;
        $descripcion = $data->descripcion;
        $motivo = $data->motivo;
        $pagos = $data->pagos; 
        $pagosArray =  explode(',',$pagos);
        $fecha =  isset($data->fecha)? new DateTime($data->fecha) : null;
        
        foreach($pagosArray as $pago){
            $data = explode(':', $pago );
            $cs->registrarPagoProfesor($idProfesor,$data[0],$descripcion ,$motivo,$data[1], $fecha);
        }
    
        $resp = array(
            "rta"=> "ok",
            "detail"=> "Registro de pagos a un profesor exitoso."
        );

        return $this->json(($resp));
    }

}
