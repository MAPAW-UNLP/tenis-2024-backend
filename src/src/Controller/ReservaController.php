<?php

namespace App\Controller;

use DateTime;
use DateInterval;
use App\Entity\Grupo;
use App\Entity\Cancha;
use App\Entity\Persona;
use App\Entity\Reserva;
use App\Entity\Alquiler;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\CustomService as ServiceCustomService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route(path="/api")
 */

class ReservaController extends AbstractController
{
    /**
     * @Route("/reservas", name="app_reservas", methods={"GET"})
     */
    public function getReservas(
        ServiceCustomService $cs
    ): Response {
        $reservas = $this->getDoctrine()->getRepository(Reserva::class)->findAll();

        $rtaReservas =  array();
        foreach ($reservas as $reserva) {
            $reservaRta = $cs->reservaFromObject($reserva);
            array_push($rtaReservas, $reservaRta);
        }


        $resp = array(
            "rta" => "error",
            "detail" => "Se produjo un error en la consulta de las reservas."
        );
        if (isset($rtaReservas)) {

            $resp['rta'] =  "ok";
            $resp['detail'] = $rtaReservas;
        }


        return $this->json($resp);
    }

    //Objeto con reservas de cada cancha en una fecha dada
    /**
     * @Route("/reservas_por_canchas_por_fecha", name="app_reservas_por_canchas_por_fecha", methods={"GET"})
     */
    public function getReservasPorCanchasPorFecha(
        ServiceCustomService $cs,
        Request $request
    ): Response {
        $canchaId = $request->query->get('cancha');
        $fecha = $request->query->get('fecha');

        $fechaPhp = new DateTime(date("Y-m-d", strtotime($fecha)));

        $reservasPorCanchaObj = [];

        $canchas = $this->getDoctrine()->getRepository(Cancha::class)->findAll();

        foreach ($canchas as $cancha) {
            if ((isset($canchaId) && ($cancha->getId() != $canchaId))) {
                continue;
            }

            $reservas = $this->getDoctrine()->getRepository(Reserva::class)->findReservasBycanchaIdAndDate($cancha->getId(), $fechaPhp);
            // dd($reservas, $cancha->getId(), $fechaPhp);
            $reservasObj = [];
            foreach ($reservas as $reserva) {

                array_push($reservasObj, $cs->reservaFromObject($reserva));
            }

            $canchaObj = array(
                "canchaId" => $cancha->getId(),
                "nombre" => $cancha->getNombre(),
                "reservas" => $reservasObj,
            );

            array_push($reservasPorCanchaObj, $canchaObj);
        }

        $resp = array(
            "rta" => "error",
            "detail" => "Se produjo un error en la consulta de las reservas por fecha y cancha."
        );
        if (isset($reservasPorCanchaObj) && (count($reservasPorCanchaObj) > 0)) {

            $resp['rta'] =  "ok";
            $resp['detail'] = $reservasPorCanchaObj;
        }


        return $this->json($resp);
    }


    /**
     * @Route("/reserva", name="app_alta_reserva", methods={"POST"})
     */
    public function postReserva(
        Request $request,
        ManagerRegistry $doctrine,
        ServiceCustomService $cs
    ): Response {

        $parametros = $request->request->all();


        $clienteParam = array(
            "nombre"    => isset($parametros['nombre']) ? $parametros['nombre'] : null,
            "telefono"    => isset($parametros['telefono']) ? $parametros['telefono'] : null,
        );

        $persona_id = null;
        if (isset($parametros['persona_id'])) {
            if ((int) $parametros['persona_id'] > 0) {
                $persona_id = (int) $parametros['persona_id'];
            }
        }

        $reservaParam = array(
            "cancha_id"     =>  $parametros['cancha_id'],
            "fecha"         =>  new DateTime($parametros['fecha']),
            "hora_ini"      =>  new DateTime($parametros['hora_ini']),
            "hora_fin"      =>  new DateTime($parametros['hora_fin']),
            "persona_id"    =>  $persona_id,
            "replica"       => (isset($parametros['replica']) && $parametros['replica'] == 'true') ? true : false,
            "estado_id"     =>  0,
            "grupo"         => isset($parametros['grupo_ids']) ? $parametros['grupo_ids'] : null,
            "tipo"          =>  $parametros['tipo'],
        );

        $em = $doctrine->getManager();

        $reserva = new Reserva(
            $reservaParam['fecha'],
            $reservaParam['hora_ini'],
            $reservaParam['hora_fin'],
            $reservaParam['persona_id'],
            $reservaParam['cancha_id'],
            $reservaParam['tipo'],
            $reservaParam['replica'],
            $reservaParam['estado_id']
        );

        $reservaId =  $em->persist($reserva);


        $lastReservaId = (int) $cs->getLastReservaId();
        $idReserva = $lastReservaId + 1;

        $procesarReplicas = false;

        if ($reservaParam['persona_id'] != null) {
            $ids_grupo = explode(',', $reservaParam['grupo']);
            foreach ($ids_grupo as $alumno_id) {
                if (is_numeric($alumno_id)) {
                    $grupo_alumno = new Grupo();
                    $grupo_alumno->setReservaId($idReserva);
                    $grupo_alumno->setPersonaId($alumno_id);
                    $em->persist($grupo_alumno);
                }
            }

            if ($reservaParam['replica']) $procesarReplicas = true;
        } else {
            $alquiler = new Alquiler();
            $alquiler->setNombre($clienteParam['nombre']);
            $alquiler->setTelefono($clienteParam['telefono']);
            $alquiler->setReservaId($idReserva);
            $em->persist($alquiler);
        }


        $em->flush();


        $cs->replicarReservaNueva($idReserva); //lo hace si esta en true replica

        $resp = array();

        $resp['rta'] =  "ok";
        $resp['detail'] = "Reserva registrada correctamente";


        return $this->json($resp);
    }

    /**
     * @Route("/profesor-reserva", name="app_alta_profesor_reserva", methods={"POST"})
     */
    public function storeProfesorReserva(Request $request, ManagerRegistry $doctrine, ServiceCustomService $cs): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            $em = $doctrine->getManager();

            $fechaInicio = new DateTime($data['fecha_ini']);
            $fechaFin = new DateTime($data['fecha_fin']);
            $horaIni = new DateTime($data['hora_ini']);
            $horaFin = new DateTime($data['hora_fin']);
            $canchaId = $data['cancha_id'];
            $alumnos = $data['alumnos'];

            if ($fechaFin > $fechaInicio) {
                return $this->rangeReservationProfesor($canchaId, $fechaInicio, $fechaFin, $horaIni, $horaFin, $alumnos, $em, $cs, $data);
            } else {
                return $this->singleReservationProfesor($canchaId, $fechaInicio, $horaIni, $horaFin, $alumnos, $em, $cs, $data);
            }

            return new JsonResponse(['data' => [
                'message' => 'Creado con éxito',
                'data' => $data,
            ]], 201);
        } catch (\Throwable $th) {
            return new JsonResponse(['data' => [
                'message' => 'Error al crear',
                'data' => $data,
                'error' => $th->getMessage()
            ]], 409);
        }
    }

    public function rangeReservationProfesor($canchaId, $fechaInicio, $fechaFin, $horaIni, $horaFin, $alumnos, $em, $cs, $data)
    {
        $fechas_ocupadas = [];
        $nueva_cancha_reservada = [];

        // Funcion para filtrar antes de reservar
        /* $reservas = $this->getDoctrine()->getRepository(Reserva::class)->findReservasBycanchaIdBetweenDatesAndTime($data['cancha_id'], $fechaInicio, $fechaFin, $horaIni, $horaFin); */

        while ($fechaInicio <= $fechaFin) {

            $reserva = new Reserva(
                $fechaInicio,
                $horaIni,
                $horaFin,
                1, // TODO: cambiar por el usuario autenticado del momento (Profesor)
                $canchaId,
                count($alumnos) > 1 ? 2 : 1, // tipo_clase_id (Siempre en grupo)
                0, // replica
                0 // estado
            );

            if ($cs->without_reservations($canchaId, $fechaInicio, $horaIni, $horaFin)) {
                // Clase grupal
                $em->persist($reserva);
                $em->flush();
                $cs->add_people_to_group($alumnos);
            } else {
                $reserva_otra_cancha = $cs->getIdCanchaDisponible($reserva, $fechaInicio);

                if ($reserva_otra_cancha != 0) {
                    $reserva->setCanchaId($reserva_otra_cancha);
                    $em->persist($reserva);
                    $em->flush();
                    $cs->add_people_to_group($alumnos);
                    array_push($nueva_cancha_reservada, ['cancha_id' => $reserva_otra_cancha, 'fecha' => $fechaInicio->format('Y-m-d')]);
                } else {
                    array_push($fechas_ocupadas, $fechaInicio->format('Y-m-d'));
                }
            }

            if ($data['repite'] == 'Todas las semanas') {
                $fechaInicio->add(new DateInterval('P1W'));
            } elseif ($data['repite'] == 'Todos los meses') {
                $fechaInicio->add(new DateInterval('P1M'));
            } else {
                $fechaInicio->add(new DateInterval('P1D'));
            }
        }

        if ($fechas_ocupadas != null) {
            if ($nueva_cancha_reservada != null) {
                return new JsonResponse(['data' => [
                    'message' => 'Hemos reservado las fechas y horario en las canchas disponibles.',
                    'fechas_ocupadas' => $fechas_ocupadas,
                    'nueva_reservas' => $nueva_cancha_reservada,
                ]], 409);
            } else {
                return new JsonResponse(['data' => [
                    'error' => 'Fecha y Horario no disponible',
                    'message' => 'Lo sentimos, no tenemos canchas disponibles en las fechas seleccionadas.',
                    'fechas_ocupadas' => $fechas_ocupadas,
                ]], 409);
            }
            return new JsonResponse(['data' => [
                'message' => 'Se reservaran únicamente las fechas disponibles, en el horario indicado',
                'fechas_ocupadas' => $fechas_ocupadas,
                'nueva_reservas' => $nueva_cancha_reservada,
            ]], 409);
        }

        return new JsonResponse(['data' => [
            'message' => 'Reserva registrada con éxito',
            'nueva_cancha_reservada' => $nueva_cancha_reservada
        ]], 201);
    }

    public function singleReservationProfesor($canchaId, $fechaInicio, $horaIni, $horaFin, $alumnos, $em, $cs, $data)
    {
        // Fecha inicio = Fecha fin
        $reserva = new Reserva(
            $fechaInicio,
            $horaIni,
            $horaFin,
            1, // TODO: cambiar por el usuario autenticado del momento (Profesor)
            $canchaId,
            count($alumnos) > 1 ? 2 : 1, // tipo_clase_id (1 = individual, 2 = grupal)
            0, // replica
            0 // estado
        );

        if ($cs->without_reservations($canchaId, $fechaInicio, $horaIni, $horaFin)) {

            $em->persist($reserva);
            $em->flush();

            $cs->add_people_to_group($alumnos);

            return new JsonResponse([
                'data' => [
                    'message' => 'Reserva registrada con éxito',
                    'data' => $data
                ]
            ], 201);
        } else {
            $reserva_otra_cancha = $cs->getIdCanchaDisponible($reserva, $fechaInicio);
            if ($reserva_otra_cancha != 0) {
                $reserva->setCanchaId($reserva_otra_cancha);
                $em->persist($reserva);
                $em->flush();

                $cs->add_people_to_group($alumnos);
                $data['cancha_id'] = $reserva_otra_cancha;

                return new JsonResponse(['data' => [
                    'error' => 'Cancha ' . $canchaId . ' no disponible',
                    'message' => 'La cancha seleccionada no estaba disponible y le reservamos la cancha ' . $reserva_otra_cancha,
                    'data' => $data,
                ]], 201);
            } else {
                return new JsonResponse(['data' => [
                    'error' => 'Horario no disponible',
                    'message' => 'El horario seleccionado ya ha sido reservado. Por favor, elige otro horario.'
                ]], 409);
            }
        }
    }

    /**
     * @Route("/mis-reservas", name="app_mis_reservas", methods={"GET"})
     */
    public function profesorReservas(ServiceCustomService $cs): Response
    {
        $profesorId = 1; // TODO: Cambiar por el usuario autenticado del momento

        $reservas = $cs->get_my_reservations($profesorId);

        $rtaReservas =  array();
        foreach ($reservas as $reserva) {
            $reservaRta = $cs->reservaFromObject($reserva);
            array_push($rtaReservas, $reservaRta);
        }

        if ($rtaReservas == null) {
            return new JsonResponse(['data' => [
                'message' => 'No se encontraron resultados que coincidan con los criterios de búsqueda.',
                'mis_reservas' => $rtaReservas
            ]], 200);
        } else {
            return new JsonResponse(['data' => [
                'message' => 'Estas son sus reservas:',
                'mis_reservas' => $rtaReservas
            ]], 200);
        }
    }


    /**
     * @Route("/profe_reserva", name="mod_profe_reserva", methods={"PUT"})
     */
    public function modProfeReserva(Request $request, ManagerRegistry $doctrine): Response
    {
        $reservaId = $request->request->get('reserva_id');
        $personaId = $request->request->get('persona_id');

        $em = $doctrine->getManager();
        $reserva = $em->getRepository(Reserva::class)->findOneById($reservaId);
        $reserva->setPersonaId($personaId);
        $em->persist($reserva);
        $em->flush();

        $resp = array();

        $resp['rta'] =  "ok";
        $resp['detail'] = "Reserva (profesor) modificada correctamente";


        return $this->json($resp);
    }

    /**
     * @Route("/grupo_reserva", name="mod_grupo_reserva", methods={"PUT"})
     */
    public function modGrupoReserva(Request $request, ManagerRegistry $doctrine): Response
    {
        $reservaId = $request->request->get('reserva_id');
        $grupoIds  = $request->request->get('grupo_ids');

        $ids_grupo = explode(',', $grupoIds);

        $em = $doctrine->getManager();

        $grupoViejo = $em->getRepository(Grupo::class)->findPersonasGrupoIdByReservaId($reservaId);
        // dd($grupoViejo, $ids_grupo);
        foreach ($grupoViejo as $personaGrupoViejo) {
            $em->getRepository(Grupo::class)->remove($personaGrupoViejo);
        }

        foreach ($ids_grupo as $alumno_id) {
            if (is_numeric($alumno_id)) {
                $grupo_alumno = new Grupo();
                $grupo_alumno->setReservaId($reservaId);
                $grupo_alumno->setPersonaId($alumno_id);
                $em->persist($grupo_alumno);
            }
        }

        $reserva = $em->getRepository(Reserva::class)->findOneById($reservaId);
        $em->flush();

        $resp = array();

        $resp['rta'] =  "ok";
        $resp['detail'] = "Reserva (grupo) modificada correctamente";


        return $this->json($resp);
    }

    /**
     * @Route("/clase_reserva", name="mod_clase_reserva", methods={"PUT"})
     */
    public function modClaseReserva(Request $request, ManagerRegistry $doctrine): Response
    {
        $reservaId = $request->request->get('reserva_id');
        $profesorId  = $request->request->get('persona_id');
        $grupoIds  = $request->request->get('grupo_ids');

        $ids_grupo = explode(',', $grupoIds);

        $em = $doctrine->getManager();

        $grupoViejo = $em->getRepository(Grupo::class)->findPersonasGrupoIdByReservaId($reservaId);
        // dd($grupoViejo, $ids_grupo);
        foreach ($grupoViejo as $personaGrupoViejo) {
            $em->getRepository(Grupo::class)->remove($personaGrupoViejo);
        }

        foreach ($ids_grupo as $alumno_id) {
            if (is_numeric($alumno_id)) {
                $grupo_alumno = new Grupo();
                $grupo_alumno->setReservaId($reservaId);
                $grupo_alumno->setPersonaId($alumno_id);
                $em->persist($grupo_alumno);
            }
        }

        $reserva = $em->getRepository(Reserva::class)->findOneById($reservaId);
        $reserva->setPersonaId($profesorId);

        if ($request->get('fecha') != null) {
            $reserva->setFecha(new DateTime($request->get('fecha')));
            $reserva->setHoraIni(new DateTime($request->get('hora_ini')));
            $reserva->setHoraFin(new DateTime($request->get('hora_fin')));
        }

        $em->flush();

        $resp = array();

        $resp['rta'] =  "ok";
        $resp['detail'] = "Reserva (clase) modificada correctamente";


        return $this->json($resp);
    }


    /**
     * @Route("/liquidar_reservas", name="app_liquidar_reservas", methods={"POST"})
     */
    public function liquidarReservas(
        ServiceCustomService $cs
    ): Response {

        $cs->liquidarReservas();


        $resp['rta'] =  "ok";
        $resp['detail'] = "Se liquidaron las clases hasta ayer";


        return $this->json($resp);
    }

    // endpoint de desarrollo y pruebas
    /**
     * @Route("/reservas_test", name="app_reservas_test", methods={"GET"})
     */
    public function getReservasTest(
        Request $request,
        ServiceCustomService $cs
    ): Response {
        $reservaId = $request->query->get('reservaId');

        $cs->procesarReplicas();

        // $cs->liquidarReservas();



        return $this->json(array());
    }
}
// TODO: hacer metodo que replique reservas desde la fecha guardada en usuarios hasta ayer
// analogo a la liquidacion de reservas