<?php

namespace App\Service;

date_default_timezone_set('America/Buenos_Aires');

use App\Entity\Alquiler;
use App\Entity\Alumno;
use App\Entity\Cancha;
use App\Entity\Clases;
use App\Entity\Grupo;
use App\Entity\Pagos;
use App\Entity\Persona;
use App\Entity\Replicas;
use App\Entity\Reserva;
use App\Entity\Usuario;
use App\Entity\Cobro;
use App\Entity\Profesor;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Time;

class CustomService
{

    private $doctrine;
    private $estadosArr = ['ASIGNADO', 'CANCELADO', 'CONSUMIDO'];
    private $em;

    public function __construct(ManagerRegistry $doctrine)
    {

        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getManager();
    }


    public function reservaFromObject(Reserva $reserva)
    {

        // dd($reserva);

        $canchaNombre = $this->em->getRepository(Cancha::class)->findOneById($reserva->getCanchaId())->getNombre();


        $grupo = [];
        $titularReservaObj = null;

        if ($reserva->getPersonaId() != null && $reserva->getPersonaId() != 0) {
            // es clase

            $titularReservaObj = $this->getPersonaByPersonaId($reserva->getPersonaId());


            $grupoPersonasId = $this->em->getRepository(Grupo::class)->findPersonasGrupoIdByReservaId($reserva->getId());

            if (count($grupoPersonasId) > 0) {

                foreach ($grupoPersonasId as $itemGrupo) {

                    $personaObj = $this->getPersonaByPersonaId($itemGrupo->getPersonaId());
                    array_push($grupo, $personaObj);
                }
            }
        } else { //es alquiler

            $titularReservaObj = $this->getClienteByReservaId($reserva->getId());
        }


        $reservaObj = array(
            "reservaId" => $reserva->getId(),
            "canchaId" => $reserva->getCanchaid(),
            "canchaNombre" => $canchaNombre,
            "fecha" => $this->getFormattedDate($reserva->getFecha()),
            "horaIni" => $this->getFormattedTime($reserva->getHoraIni()),
            "horaFin" => $this->getFormattedTime($reserva->getHoraFin()),
            "profesorId" => $reserva->getPersonaId(),
            "titular" => $titularReservaObj,
            "replica" => $reserva->isReplica(),
            "estado" => $this->estadosArr[$reserva->getEstadoId()],
            "tipo" => $reserva->getIdTipoClase() != null ? $this->getInfoTipoClase($reserva->getIdTipoClase())[0] : 'ALQUILER',
            "idTipo" => $reserva->getIdTipoClase() != null ? $reserva->getIdTipoClase() : '0',
            "grupo" => $grupo

        );

        return $reservaObj;
    }

    public function getInfoTipoClase($idTipoClase)
    {
        $clase = $this->em->getRepository(Clases::class)->findOneById($idTipoClase);

        // dd($clase);
        return array($clase->getTipo(), $clase->getImporte());
    }

    public function getFormattedTime(DateTime $time)
    {

        return $time->format('H:i');
    }

    public function getFormattedDate(DateTime $fecha)
    {

        return $fecha->format('Y-m-d');
    }

    public function getPersonaByPersonaId($personaId)
    {

        $persona = $this->em->getRepository(Persona::class)->findOneById($personaId);
        // dd($persona);
        $personaObj = array(
            "id" => $persona->getId(),
            "nombre" => $persona->getNombre(),
            // "apellido" => $persona->getApellido(),
            "telefono" => $persona->getTelefono(),
            // "fechanac" => $persona->getFechaNac() != null ? $this->getFormattedDate($persona->getFechaNac()) : null,
            "esalumno" => $persona->isEsAlumno(),
            "visible" => $persona->isVisible(),
        );

        return $personaObj;
    }

    public function getClienteByReservaId($reservaId)
    {

        $cliente = $this->em->getRepository(Alquiler::class)->findAlquilerByReservaId($reservaId);

        if ($cliente != null) {

            $clienteObj = array(
                "nombre" => $cliente->getNombre(),
                // "apellido" => $cliente->getApellido(),
                "telefono" => $cliente->getTelefono(),
            );
            return $clienteObj;
        } else {
            return null;
        }
        // dd($cliente, $clienteObj);
    }

    public function getLastReservaId()
    {
        $id = $this->em->getRepository(Reserva::class)->getLastRecord();
        if ($id == null) {
            return 0;
        }
        return $id[0]->getId();
    }

    public function formatearAlumno($alumno)
    {

        $fechaNac = $alumno->getFechaNac() ? $this->getFormattedDate($alumno->getFechaNac()) : '';

        $alumnoFormateado = array(
            "id"    => $alumno->getId(),
            "nombre"    => $alumno->getNombre(),
            "telefono"  => $alumno->getTelefono(),
            "fechanac"  => $fechaNac,
            "saldo"     => 0,
        );

        return $alumnoFormateado;
    }

    public function replicarReservaNueva($reservaId)
    {

        $clase = $this->em->getRepository(Reserva::class)->findOneById($reservaId);

        if (!$clase->isReplica()) {
            return;
        }

        $grupo = $this->em->getRepository(Grupo::class)->findPersonasGrupoIdByReservaId($reservaId);

        $fechaReserva = $clase->getFecha();
        $fecha = clone $clase->getFecha();

        $nroMesActual = date('m');

        $mesProximo = new DateTime();
        date_add($mesProximo, date_interval_create_from_date_string("1 month"));
        $nroMesProximo = $mesProximo->format('m');


        for ($i = 0; $i < 10; $i++) {

            date_add($fecha, date_interval_create_from_date_string("7 days"));

            $idCancha = $this->getIdCanchaDisponible($clase, $fecha);

            if (($fecha->format('m') == $nroMesActual || $fecha->format('m') == $nroMesProximo) && $idCancha > 0) {

                $reservaReplicada = clone $clase;
                $reservaReplicada->setFecha(clone $fecha);
                $reservaReplicada->setCanchaId($idCancha);
                $this->em->persist($reservaReplicada);
                $this->em->flush();

                $idUltimaReserva = $this->getLastReservaId();
                foreach ($grupo as $itemGrupo) {
                    $itemReplicado = clone $itemGrupo;
                    $itemReplicado->setReservaId($idUltimaReserva);
                    $this->em->persist($itemReplicado);
                }
                $this->em->flush();
            }
        }

        $this->guardarOActualizarReplicas($reservaId, $nroMesProximo);
    }

    public function guardarOActualizarReplicas($idReserva, $ultimoMes)
    {

        $replicaEncontrada = $this->em->getRepository(Replicas::class)->findOneByReservaId($idReserva);

        if (isset($replicaEncontrada)) {

            $replicaEncontrada->setUltimoMes($ultimoMes);
            $this->em->persist($replicaEncontrada);
        } else {

            $replica = new Replicas();
            $replica->setIdReserva($idReserva);
            $replica->setUltimoMes($ultimoMes);
            $this->em->persist($replica);
        }

        $this->em->flush();
    }

    public function getIdCanchaDisponible($claseOrig, $fecha)
    {

        // devolver id de la cancha disponible, preferentemente la original
        // devolver 0 si no hay turno disponible en ninguna cancha


        $clase = clone $claseOrig;
        $clase->setFecha($fecha);

        $canchaPreferida = $this->em->getRepository(Cancha::class)->findOneById($clase->getCanchaId());
        $canchas =  $this->em->getRepository(Cancha::class)->findAll();


        if ($this->isCanchaDisponibleEnTurno($canchaPreferida->getId(), $clase)) {

            // dd("cancha original disponible", $clase->getCanchaId(), $clase, $fecha); die; // TODO: quitar

            return $clase->getCanchaId();
        } else {

            foreach ($canchas as $cancha) {

                if ($cancha->getId() == $canchaPreferida->getId()) {
                    continue;
                }

                if ($this->isCanchaDisponibleEnTurno($cancha->getId(), $clase)) {
                    // dd("cancha alternativa disponible",  $cancha->getId(), $clase, $fecha); die; // TODO: quitar

                    return $cancha->getId();
                }
            }

            // dd("no hay cancha disponible",  $cancha->getId(), $clase, $fecha, ); die; // TODO: quitar

            return 0; // si llego aca es que no hay cancha disponible
        }



        // dd($canchaPreferida,$canchas);



    }

    public function isCanchaDisponibleEnTurno($id, $clase)
    {



        $reservasEnCanchaYFecha = $this->em->getRepository(Reserva::class)->findReservasBycanchaIdAndDate($id, $clase->getFecha());
        $disponible = true;

        $claseHoraIni = clone $clase->getHoraIni();
        $claseHoraIni->setDate(2000, 01, 01);
        $claseHoraFin = clone $clase->getHoraFin();
        $claseHoraFin->setDate(2000, 01, 01);

        // dd( $clase->getHoraFin(),  $clase->getHoraIni());

        foreach ($reservasEnCanchaYFecha as $reserva) {

            $reservaHoraIni = clone $reserva->getHoraIni();
            $reservaHoraIni->setDate(2000, 01, 01);
            $reservaHoraFin =  clone $reserva->getHoraFin();
            $reservaHoraFin->setDate(2000, 01, 01);

            // dd ($reservaHoraIni, $reservaHoraFin , $claseHoraIni, $claseHoraFin, $reservaHoraIni > $claseHoraIni, $reservaHoraIni >= $claseHoraIni);die;


            if (!($claseHoraFin <= $reservaHoraIni) && !($claseHoraIni >= $reservaHoraFin)) {
                $disponible = false;
                break;
            }
        }


        return $disponible;
    }

    public function registrarPago($motivo, $monto, $descripcion, $fecha)
    {

        $pago = new Pagos();
        $pago -> setMonto($monto);
        $pago -> setMotivo($motivo);
        $pago -> setDescripcion($descripcion);
        $fechaPago = isset($fecha) ? $fecha : new Date();
        $pago->setFecha($fechaPago);
        $pago->setHora(new DateTime());

        $this->em->persist($pago);
        $this->em->flush();
    }
    
    public function registrarPagoProfesor($idProfesor, $descripcion, $motivo, $monto, $fecha)
    {
        $profesor = $this->em->getRepository(Profesor::class)->find($idProfesor); 

        $pago = new Pagos();
        $profesor->addPago($pago);
        $pago->setProfesor($profesor)->setMonto($monto);
        
        $pago->setMotivo($motivo);
        $pago->setDescripcion($descripcion);
        $fechaPago = isset($fecha) ? $fecha : new Date();
        $pago->setFecha($fechaPago);
        $pago->setHora(new DateTime());

        $this->em->persist($pago);
        $this->em->persist($profesor);
        $this->em->flush();
    }

    public function registrarCobro($concepto, $monto, $descripcion, $fecha)
    {

        $cobro = new Cobro();
        $cobro->setMonto($monto)->setConcepto($concepto)->setDescripcion($descripcion);
        $fechaCobro = isset($fecha) ? $fecha : new Date();
        $cobro->setFecha($fechaCobro);
        $cobro->setHora(new DateTime());

        $this->em->persist($cobro);
        $this->em->flush();
    }

    public function registrarCobroAlumno($idAlumno, $idTipoClase, $concepto, $descripcion,$monto, $fecha)
    {
        $alumno = $this->em->getRepository(Alumno::class)->find($idAlumno); 

        $cobro = new Cobro();
//        $alumno->addCobro($cobro);
        $cobro->setAlumno($alumno)->setMonto($monto);
        $fechaCobro = isset($fecha) ? $fecha : new Date();
        $cobro->setFecha($fechaCobro);
        $cobro->setConcepto($concepto);
        $cobro->setDescripcion($descripcion);
        $cobro->setIdTipoClase($idTipoClase);
        $cobro->setHora(new DateTime());

        $this->em->persist($cobro);
        $this->em->persist($alumno);
        $this->em->flush();
    }

    public function liquidarReservas()
    {

        $usuarioDB = $this->em->getRepository(Usuario::class)->findOneByUsername('admin');
        $fechaPagos = $usuarioDB->getFechapagos();

        $fechaDesde = $fechaPagos != null ? $fechaPagos : new DateTime('2022-01-01');

        $fechaHasta = new DateTime('yesterday');

        if ($fechaDesde != $fechaHasta) {


            $reservas =  $this->em->getRepository(Reserva::class)->findReservasBetweenDates($fechaDesde, $fechaHasta);
            foreach ($reservas as $reserva) {

                $idPersonasGrupo = $this->em->getRepository(Grupo::class)->findPersonasGrupoIdByReservaId($reserva->getId());

                foreach ($idPersonasGrupo as $personaId) {


                    $pago = new Pagos();
                    $pago->setIdPersona($personaId->getPersonaId());
                    $pago->setFecha($reserva->getFecha());
                    $tipoClase = $reserva->getIdTipoClase() != null ? $reserva->getIdTipoClase() : 2;
                    $pago->setIdTipoClase($tipoClase);
                    $pago->setCantidad(-1);
                    $this->em->persist($pago);
                }
            }
            $usuarioDB->setFechapagos($fechaHasta);
            $this->em->persist($usuarioDB);
            $this->em->flush();
        }
    }


    public function procesarReplicas()
    {

        $replicas = $this->em->getRepository(Replicas::class)->findAll();

        foreach ($replicas as $replica) {

            $this->replicarReservaReplicada($replica->getIdReserva(), $replica->getUltimoMes());
        }
    }

    public function replicarReservaReplicada($reservaId, $replicadaHastaMes)
    {

        $fechaHoy = new DateTime();
        $mesActual = $fechaHoy->format('m');

        if ($mesActual != $replicadaHastaMes) {
            return;
        }


        $clase = $this->em->getRepository(Reserva::class)->findOneById($reservaId);

        $grupo = $this->em->getRepository(Grupo::class)->findPersonasGrupoIdByReservaId($reservaId);

        $fecha = clone $clase->getFecha();

        $nroMesProximo = $replicadaHastaMes < 12 ? $replicadaHastaMes + 1 : 1;



        do { // sumo una semana hasta llegar al mes que hay que replicar
            date_add($fecha, date_interval_create_from_date_string("7 days"));
        } while ($fecha->format('m') != $nroMesProximo);


        do { // guardo clases nuevas hasta terminar el mes

            $idCancha = $this->getIdCanchaDisponible($clase, $fecha);

            if ($idCancha > 0) {

                $reservaReplicada = clone $clase;
                $reservaReplicada->setFecha(clone $fecha);
                $reservaReplicada->setCanchaId($idCancha);
                $this->em->persist($reservaReplicada);
                $this->em->flush();

                $idUltimaReserva = $this->getLastReservaId();
                foreach ($grupo as $itemGrupo) {
                    $itemReplicado = clone $itemGrupo;
                    $itemReplicado->setReservaId($idUltimaReserva);
                    $this->em->persist($itemReplicado);
                }
                $this->em->flush();
            }

            date_add($fecha, date_interval_create_from_date_string("7 days"));
        } while ($fecha->format('m') == $nroMesProximo);

        $this->guardarOActualizarReplicas($reservaId, $nroMesProximo);
    }

    public function procesamientoInicial()
    {

        $this->procesarReplicas();
        $this->liquidarReservas();
    }


    public function add_people_to_group($alumnos)
    {
        $lastReservaId = (int) $this->getLastReservaId();

        foreach ($alumnos as $alumno_id) {
            $grupo_alumno = new Grupo();
            $grupo_alumno->setReservaId($lastReservaId);
            $grupo_alumno->setPersonaId($alumno_id);
            $this->em->persist($grupo_alumno);
            $this->em->flush();
        }
    }

    public function without_reservations($canchaId, $fechaInicio, $horaIni, $horaFin)
    {
        return $this->em->getRepository(Reserva::class)->findReservasBycanchaIdAndDateAndTime($canchaId, $fechaInicio, $horaIni, $horaFin) == null;
    }

    public function get_my_reservations($profesorId)
    {
        return $this->em->getRepository(Reserva::class)->findReservasProfesor($profesorId);
    }

    public function totalMontos($collection){
        $total = 0;
        foreach ($collection as $item) {
            $total += $item->getMonto();
        }
        return $total;

    }


}
