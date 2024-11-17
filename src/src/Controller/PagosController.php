<?php

namespace App\Controller;

use App\Entity\Pagos;
use App\Entity\Profesor;
use App\Repository\PagosRepository;
use App\Repository\ProfesorRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CustomService as ServiceCustomService;
use App\Service\DateTimeFormatterService;
use DateTime;
use Doctrine\ORM\EntityManager;
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
        DateTimeFormatterService $cs,
        PagosRepository $pagosRepository
    ): Response
    {

        $pagos = $pagosRepository->findAll();

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
        PagosRepository $pagosRepository,
        DateTimeFormatterService $cs
    ): Response
    {
        $profesorId = $request->query->get('profesorId');

        $pagos = $pagosRepository->findBy(['profesor' => $profesorId]);

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
        PagosRepository $pagosRepository
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
            $pago = new Pagos($data[0], $data[1], $descripcion, $fecha);
            $pagosRepository->add($pago,true);
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
        } else if(isset($data->idProveedor)){
            $cs->registrarPagoProveedor($data->idProveedor, $descripcion, $motivo, $monto);
        } else{
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
        ManagerRegistry $doctrine,
        ProfesorRepository $profesorRepository
         ): Response
    {

        $data = json_decode( $request->getContent());
        $profesor = $profesorRepository->find($data->idProfesor);
        $descripcion = $data->descripcion;
        $motivo = $data->motivo;
        $pagos =  explode(',',$data->pagos);
        $fecha =  isset($data->fecha)? new DateTime($data->fecha) : null;
        
        foreach($pagos as $pago){
            $data = explode(':', $pago );//data[0] motivo, data[1] = monto 
            $doctrine->getManager()->getRepository(Pagos::class)->registrarPagoProfesor($profesor,$motivo, $data[1], $descripcion, $fecha, $doctrine);
        }
    
        $resp = array(
            "rta"=> "ok",
            "detail"=> "Registro de pagos a un profesor exitoso."
        );

        return $this->json(($resp));
    }

    /**
     * @Route("/pagos_por_proveedor/{id}", name="app_Pagos_proveedorId", methods={"GET"})
    */
    public function getPagosByProveedorId(
        $id,
        PagosRepository $pagosRepository,
        DateTimeFormatterService $cs
    ): Response
    {

        $pagos = $pagosRepository->findBy(['proveedor' => $id]);

        $objPagos = array();
        foreach($pagos as $pago){

           array_push($objPagos, array(
            "nombreProveedor" => $pago->getProveedor() ? $pago->getProveedor()->getNombre() : "",
            "monto" => $pago->getMonto(), // = monto
            "fecha" => $cs->getFormattedDate($pago->getFecha()),
            "motivo" => $pago->getMotivo(),
            'descripcion' => $pago->getDescripcion() 
            ));
        }

        return $this->json($objPagos);
    }

}
