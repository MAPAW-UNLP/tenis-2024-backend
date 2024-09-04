<?php

namespace App\Controller;

use App\Entity\PeriodoAusencia;
use App\Repository\ReservaRepository;
use App\Repository\PeriodoAusenciaRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route(path="/api")
 */
class PeriodoAusenciaController extends AbstractController
{
    /**
     * @Route("/periodo-ausencia", name="app_periodo_ausencia", methods={"POST"})
     */
    public function storePeriodoAusencia(Request $request, ReservaRepository $reservaRepository, PeriodoAusenciaRepository $periodoAusenciaRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $reservas = $reservaRepository->findReservasBetweenDates($data['fecha_ini'], $data['fecha_fin']);

        if ($reservas != null) {
            $periodoAusencia = $periodoAusenciaRepository->findPeriodoAusenciaByFechaIniFechaFin($data['fecha_ini'], $data['fecha_fin']);

            if ($periodoAusencia == null) {
                $periodoAusencia = new PeriodoAusencia();
                $periodoAusencia->setFechaIni(new \DateTime($data['fecha_ini']));
                $periodoAusencia->setFechaFin(new \DateTime($data['fecha_fin']));
                // Pendiente = 1
                $periodoAusencia->setEstadoId(1);
                // TODO: cambiar 1 por usuario logueado del momento.
                $periodoAusencia->setProfesorId(1);
                $periodoAusencia->setMotivo($data['motivo']);
                $periodoAusenciaRepository->add($periodoAusencia, true);

                return $this->json([
                    'message' => 'Se ha enviado al administrador un pedido de ausencia.',
                    'data' => $data,
                ], 201);
            } else {
                $data['estado_id'] = $periodoAusencia[0]->getEstadoId();
                return $this->json([
                    'message' => 'Ya existe un pedido de ausencia para el rango de fechas ingresado.',
                    'data' => $data,
                ], 409);
            }
        } else {
            // Retornar rango de posibles horarios de reserva.
            return $this->json([
                'message' => 'No existen reservas para el rango de fechas ingresado.',
                'data' => $data,
            ], 404);
        }
    }

    /**
     * @Route("/mis-periodos-ausencia", name="app_mis_periodos_ausencia", methods={"GET"})
     */
    public function indexMisPeriodosAusencia(PeriodoAusenciaRepository $periodoAusenciaRepository): Response
    {
        // TODO: Cambiar 1 por usuario logueado del momento.
        $solicitudesAusencia = $periodoAusenciaRepository->findPeriodoAusenciaByProfesorId(1);
        if ($solicitudesAusencia) {
            return $this->json([
                'message' => 'Se han encontrado solicitudes de ausencia.',
                'data' => $solicitudesAusencia,
            ], 200);
        } else {
            return $this->json([
                'message' => 'No se han encontrado solicitudes de ausencia.',
                'data' => $solicitudesAusencia,
            ], 404);
        }
    }

    /**
     * @Route("/eliminar-periodo-ausencia/{id}", name="app_periodo_ausencia_show", methods={"DELETE"})
     */
    public function deletePeriodoAusencia($id, PeriodoAusenciaRepository $periodoAusenciaRepository): Response
    {
        $periodoAusencia = $periodoAusenciaRepository->findPeriodoAusenciaById($id);

        if ($periodoAusencia) {
            if ($periodoAusencia[0]->getEstadoId() == 1) {
                $periodoAusenciaRepository->remove($periodoAusencia[0], true);
                return $this->json([
                    'message' => 'Se ha eliminado el pedido de ausencia.',
                    'data' => $periodoAusencia,
                ], 200);
            } else {
                return $this->json([
                    'message' => 'No se puede eliminar el pedido de ausencia. Ya que no se encuentra en estado pendiente.',
                    'data' => $periodoAusencia,
                ], 409);
            }
        } else {
            return $this->json([
                'message' => 'No existe el pedido de ausencia.',
                'data' => $periodoAusencia,
            ], 404);
        }
    }

    /**
     * @Route("/aprobar-periodo-ausencia/{id}", name="app_periodo_ausencia_aprobar", methods={"PUT"})
     */
    public function aprobarPeriodoAusencia($id, PeriodoAusenciaRepository $periodoAusenciaRepository, ReservaRepository $reservaRepository): Response
    {
        $periodoAusencia = $periodoAusenciaRepository->findPeriodoAusenciaById($id);

        if ($periodoAusencia) {
            if ($periodoAusencia[0]->getEstadoId() == 1) {
                $reservas = $reservaRepository->findReservasBetweenDates($periodoAusencia[0]->getFechaIni(), $periodoAusencia[0]->getFechaFin());
                foreach ($reservas as $reserva) {
                    $reserva->setEstadoId(1);
                    $reservaRepository->edit($reserva, true);
                }
                $periodoAusencia[0]->setEstadoId(2);
                $periodoAusenciaRepository->edit($periodoAusencia[0], true);
                return $this->json([
                    'message' => 'Se ha aprobado el pedido de ausencia.',
                    'data' => ['periodoAusencia' => $periodoAusencia[0], 'reservas' => $reservas]
                ], 200);
            } else {
                return $this->json([
                    'message' => 'No se puede aprobar el pedido de ausencia. Ya que no se encuentra en estado pendiente.',
                    'data' => $periodoAusencia,
                ], 409);
            }
        } else {
            return $this->json([
                'message' => 'No existe el pedido de ausencia.',
                'data' => $periodoAusencia,
            ], 404);
        }
    }

    /**
     * @Route("/mostrar-clases-entre-fechas", name="app_show_class_between_dates", methods={"GET"})
     */
    public function showClasesEntreFechas(Request $request, ReservaRepository $reservaRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $fecha_ini = new \DateTime($data['fecha_ini']);
        $fecha_fin = new \DateTime($data['fecha_fin']);
        $reservas = $reservaRepository->findReservasBetweenDates($fecha_ini, $fecha_fin);

        if ($reservas) {
            return $this->json([
                'message' => 'Se han encontrado reservas.',
                'data' => $reservas,
            ], 200);
        } else {
            return $this->json([
                'message' => 'No se han encontrado reservas para las fechas indicadas.',
                'data' => $reservas,
            ], 404);
        }
    }
}
