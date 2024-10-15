<?php

namespace App\Controller;

use App\Entity\PagoProveedor;
use App\Entity\Proveedor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CustomService as ServiceCustomService;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Time;

date_default_timezone_set('America/Buenos_Aires');

/**
 * @Route(path="/api")
 */
class PagoProveedorController extends AbstractController
{

     /**
     * @Route("/pagos/proveedor", name="app_pagos_proveedor", methods={"GET"})
     */
    public function getPagosProveedor(
        Request $request,
        ManagerRegistry $doctrine,
        ServiceCustomService $cs
    ): Response
    {

        $em = $doctrine->getManager();

        $pagos = $em->getRepository( PagoProveedor::class )->findAll();

        $objPagos = array();

        foreach ($pagos as $pago) {
            array_push($objPagos, array(
                'id' => $pago->getId(),
                'monto' => $pago->getMonto(), // monto
                'fecha' => $cs->getFormattedDate($pago->getFecha()),
                'hora' => $pago->getHora()->format('H:i'),
                'descripcion' => $pago->getDescripcion()
            ));

        }

        return $this->json($objPagos);
    }


    /**
     * @Route("/pagos_por_proveedor", name="app_Pagos_proveedorId", methods={"GET"})
    */
    public function getPagosByProveedorId(
        Request $request,
        ManagerRegistry $doctrine,
        ServiceCustomService $cs
    ): Response
    {
        $proveedorId = $request->query->get('proveedorId');

        $em = $doctrine->getManager();

        $pagos = $em->getRepository( PagoProveedor::class )->findPagosByProveedorId($proveedorId);

        $objPagos = array();
        foreach($pagos as $pago){

           array_push($objPagos, array(
            'id' => $pago->getId(),
            "monto" => $pago->getMonto(), // = monto
            "fecha" => $cs->getFormattedDate($pago->getFecha()),
            'hora' => $pago->getHora()->format('H:i'),
            'descripcion' => $pago->getDescripcion() 
            ));
        }

        return $this->json($objPagos);
    }


     /**
     * @Route("/pagosProveedor", name="add_pagosProveedor", methods={"POST"})
     */
    public function addPagoProveedor(
        Request $request, 
        ManagerRegistry $doctrine,
        ServiceCustomService $cs
         ): Response
    {
        $em = $doctrine->getManager();

        $data = json_decode( $request->getContent());
        $proveedor = $em->getRepository(Proveedor::class)->find($data->idProveedor); 
        $pago = new PagoProveedor();
        $pago->setIdProveedor($data->idProveedor);
        $pago->setProveedor($proveedor);
        $pago->setDescripcion($data->descripcion);
        $pago->setMonto($data->monto);
        $fechaPago = new DateTime();
        $pago->setFecha($fechaPago);
        $pago->setHora(new DateTime());
    
        $em->persist($pago);
        $em->flush();

        $resp = array(
            "rta"=> "ok",
            "detail"=> "Registro de pagos a un proveedor exitoso."
        );

        return $this->json(($resp));
    }

}
