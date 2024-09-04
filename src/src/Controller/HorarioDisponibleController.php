<?php

namespace App\Controller;

use PHPUnit\Util\Json;
use App\Entity\HorarioDisponible;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\HorarioDisponibleRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route(path="/api")
 */
class HorarioDisponibleController extends AbstractController
{
    /**
     * @Route("/horario/disponible", name="app_horario_disponible", methods={"POST"})
     */
    public function create(Request $request, HorarioDisponibleRepository $horarioDisponibleRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $currentDateTime = new \DateTimeImmutable();
        // $twoYearsLater = $currentDateTime->modify('+2 year');
        $twoYearsLater = $currentDateTime->modify('+1 month');

        // Obtener la fecha de inicio (actual) y la fecha de fin (2 años después)
        $startDate = new \DateTimeImmutable();
        $endDate = $twoYearsLater;

        $busyDates = [];

        // Iterar semana por semana a lo largo de 2 años
        $date = $startDate;
        while ($date <= $endDate) {
            foreach ($data['selectedDays'] as $day) {
                $dayOfWeek = $this->getNumericDayOfWeek($day);

                // Crear una nueva instancia de DateTimeImmutable para cada reserva
                $dateWithDay = $date->setISODate($date->format('Y'), $date->format('W'), $dayOfWeek);

                if ($horarioDisponibleRepository->findFechaHoraIniHoraFinProfesorId($dateWithDay->format('Y-m-d'), new \DateTime($data['horaInicio']), new \DateTime($data['horaFin']), 1)) {
                    $busyDates[] = $dateWithDay->format('Y-m-d') . ' ' . $data['horaInicio'] . ' ' . $data['horaFin'];
                } else {
                    $horarioDisponible = new HorarioDisponible();
                    $horarioDisponible->setFecha($dateWithDay);
                    $horarioDisponible->setHoraIni(new \DateTime($data['horaInicio']));
                    $horarioDisponible->setHoraFin(new \DateTime($data['horaFin']));
                    $horarioDisponible->setProfesorId(1);
                    $horarioDisponibleRepository->add($horarioDisponible, true);
                }
            }

            // Avanzar a la semana siguiente
            $date = $date->modify('+1 week');
        }

        if ($busyDates) {
            return new JsonResponse(['message' => 'No se registro horario disponible para las siguientes fechas', 'busyDates' => $busyDates], 400);
        }
        return new JsonResponse(['message' => 'Horario disponible creado'], 201);
    }

    /**
     * Convierte el nombre del día de la semana a su equivalente numérico.
     *
     * @param string $day Nombre del día de la semana.
     * @return int Número del día de la semana (1 para lunes, 2 para martes, etc.).
     */
    private function getNumericDayOfWeek($day): int
    {
        $daysOfWeek = [
            'lunes' => 1,
            'martes' => 2,
            'miércoles' => 3,
            'jueves' => 4,
            'viernes' => 5,
            'sábado' => 6,
            'domingo' => 7,
        ];

        // Convertir el nombre del día a minúsculas para hacer la comparación insensible a mayúsculas
        $lowercaseDay = strtolower($day);

        // Devolver el número del día de la semana o 0 si no se encuentra
        return $daysOfWeek[$lowercaseDay] ?? 0;
    }

    /**
     * @Route("/horario/disponible", name="app_horario_disponible_get", methods={"GET"})
     */
    public function getHorarioDisponible(HorarioDisponibleRepository $horarioDisponibleRepository): Response
    {
        $horarioDisponible = $horarioDisponibleRepository->findHorariosDisponiblesProfesorId(1);
        $data = [];

        foreach ($horarioDisponible as $horario) {
            $data[] = [
                'id' => $horario->getId(),
                'fecha' => $horario->getFecha()->format('Y-m-d'),
                'horaIni' => $horario->getHoraIni()->format('H:i'),
                'horaFin' => $horario->getHoraFin()->format('H:i'),
                'profesorId' => $horario->getProfesorId(),
            ];
        }
        if (!$data) {
            return new JsonResponse(['message' => 'No se encontraron horarios disponibles'], 404);
        }
        return new JsonResponse($data, 200);
    }
}
