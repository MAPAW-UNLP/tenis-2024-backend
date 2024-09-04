<?php

namespace App\Controller;

use PHPUnit\Util\Json;
use App\Repository\PagosRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route(path="/api")
 */
class PagosProfesorController extends AbstractController
{
    /**
     * @Route("/pagos/profesor", name="app_pagos_profesor", methods={"GET"})
     */
    public function getPagosProfesor(PagosRepository $pagosRepository): Response
    {
        $fechaActual = new \DateTime();
        $primerDia = new \DateTime($fechaActual->format('Y-m-01'));
        $ultimoDia = new \DateTime($fechaActual->format('Y-m-t'));

        $pagos = $pagosRepository->findPagosByProfesorIdInDates(1, $primerDia, $ultimoDia);
        $numeroDeRegistros = count($pagos);

        $totalPagos = 0;
        foreach ($pagos as $pago) {
            $totalPagos += $pago->getMonto();
        }

        return new JsonResponse([
            'periodo' => $primerDia->format('d-m-Y') . ' - ' . $ultimoDia->format('d-m-Y'),
            'cantClases' => $numeroDeRegistros,
            'total' => $totalPagos,
        ]);
    }

    /**
     * @Route("/pagos/profesor/filtrar", name="app_pagos_profesor_filtrar", methods={"GET"})
     */
    public function filtrarPagosProfesor(PagosRepository $pagosRepository, Request $request): Response
    {
        // Obtiene los parámetros de fecha de la solicitud
        $fechaInicio = $request->query->get('fechaInicio');
        $fechaFin = $request->query->get('fechaFin');

        $primerDia = \DateTime::createFromFormat('Y-m-d', $fechaInicio);
        $ultimoDia = \DateTime::createFromFormat('Y-m-d', $fechaFin);

        $pagos = $pagosRepository->findPagosByProfesorIdInDates(1, $primerDia, $ultimoDia);
        $numeroDeRegistros = count($pagos);

        $totalPagos = 0;
        foreach ($pagos as $pago) {
            $totalPagos += $pago->getMonto();
        }

        return new JsonResponse([
            'periodo' => $primerDia->format('d-m-Y') . ' - ' . $ultimoDia->format('d-m-Y'),
            'cantClases' => $numeroDeRegistros,
            'total' => $totalPagos,
        ]);
    }
}
