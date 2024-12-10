<?php

namespace App\Service;

date_default_timezone_set('America/Buenos_Aires');

use App\Entity\Alquiler;
use App\Entity\Cliente;
use App\Entity\Cancha;
use App\Entity\Clases;
use App\Entity\Estado;
use App\Entity\Grupo;
use App\Entity\Pagos;
use App\Entity\Replicas;
use App\Entity\Reserva;
use App\Entity\Usuario;
use App\Entity\Cobro;
use App\Entity\Profesor;
use App\Entity\Proveedor;
use App\Service\DateTimeFormatterService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Time;

class CustomService
{

    private $doctrine;
    private $estadosArr = ['ASIGNADO', 'CANCELADO', 'CONSUMIDO'];
    private $em;
    private $formatter;

    public function __construct(ManagerRegistry $doctrine)
    {

        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getManager();
        $this->formatter = new DateTimeFormatterService();
    }


    public function reservaFromObject(Reserva $reserva)
    {
        $canchaNombre = $this->em->getRepository(Cancha::class)->findOneById($reserva->getCanchaId())->getNombre();

        $grupo = [];
        $personaTitular = null;
        $clienteRepository = $this->em->getRepository(Cliente::class);


        if ($reserva->getProfesorId() !== null && $reserva->getProfesorId() != 0) {
            // Obtener el persona_id desde el objeto Reserva
            $profesorId = $reserva->getProfesorId();

            // Usar el personaId para buscar la persona correspondiente
            $personaTitular = $this->em->getRepository(Profesor::class)->findOneById($profesorId);

            $grupoPersonasId = $this->em->getRepository(Grupo::class)->findPersonasGrupoByReservaId($reserva->getId());

            if (count($grupoPersonasId) > 0) {
                foreach ($grupoPersonasId as $persona) {
                    $miembro = $clienteRepository->findOneById($persona->getClienteId())->toArrayAsociativo();
                    array_push($grupo, $miembro);
                }
            }
        } else { //es alquiler
            $personaTitular = $this->em->getRepository(Alquiler::class)->findAlquilerByReservaId($reserva->getId());
            if ($personaTitular != null) {
                $personaTitular = $personaTitular->getCliente();
            }
        }

        $reservaObj = array(
            "reservaId" => $reserva->getId(),
            "canchaId" => $reserva->getCanchaid(),
            "canchaNombre" => $canchaNombre,
            "fecha" => $this->formatter->getFormattedDate($reserva->getFecha()),
            "horaIni" => $this->formatter->getFormattedTime($reserva->getHoraIni()),
            "horaFin" => $this->formatter->getFormattedTime($reserva->getHoraFin()),
            "profesorId" => $reserva->getProfesorId(),
            "titular" => $personaTitular,
            "replica" => $reserva->isReplica(),
            "estado" => $this->estadosArr[$reserva->getEstadoId()],
            "tipo" => $reserva->getIdTipoClase(),
            "idTipo" => $reserva->getIdTipoClase() != null ? $reserva->getIdTipoClase() : '0',
            "grupo" => $grupo

        );

        return $reservaObj;
    }
    /*no lo toco porque otro grupo tiene como tarea hacer el ABM de tipos de clase*/
    public function getInfoTipoClase($idTipoClase)
    {
        $clase = $this->em->getRepository(Clases::class)->findOneById($idTipoClase);

        // dd($clase);
        return array($clase->getTipo(), $clase->getImporte());
    }
    /**
     * @deprecated Este método no debe usarse y debería eliminarse. Usa `DateTimeFormatterService->getFormattedDate()` en su lugar.
     */
    public function getFormattedDate(DateTime $fecha)
    {
        return $fecha->format('Y-m-d');
    }

    public function replicarReservaNueva($reservaId)
    {
        $clase = $this->em->getRepository(Reserva::class)->findOneById($reservaId);

        if (!$clase->isReplica()) {
            return;
        }

        $grupo = $this->em->getRepository(Grupo::class)->findPersonasGrupoByReservaId($reservaId);
        $fecha = clone $clase->getFecha();
        $nroMesActual = date('m');
        $nroMesProximo = (new DateTime('first day of next month'))->format('m');

        $reservasParaPersistir = [];
        $itemsGrupoParaPersistir = []; //hasta aca va

        for ($i = 0; $i < 10; $i++) {
            date_add($fecha, date_interval_create_from_date_string("7 days"));

            $idCancha = $this->getIdCanchaDisponible($clase, $fecha);

            if (($fecha->format('m') == $nroMesActual || $fecha->format('m') == $nroMesProximo) && $idCancha > 0) {
                $reservaReplicada = clone $clase;
                $reservaReplicada->setFecha(clone $fecha);
                $reservaReplicada->setCanchaId($idCancha);
                $reservasParaPersistir[] = $reservaReplicada;

                $idUltimaReserva = $this->em->getRepository(Reserva::class)->getLastReservaId();

                foreach ($grupo as $itemGrupo) {
                    $itemReplicado = clone $itemGrupo;
                    $itemReplicado->setReservaId($idUltimaReserva);
                    $itemsGrupoParaPersistir[] = $itemReplicado;
                }
            }
        }

        // Persistir todas las reservas y grupos en un solo flush
        foreach ($reservasParaPersistir as $reserva) {
            $this->em->persist($reserva);
        }

        foreach ($itemsGrupoParaPersistir as $itemGrupo) {
            $this->em->persist($itemGrupo);
        }

        $this->em->flush();
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

    /**
     * @deprecated Este método no debe usarse y debería eliminarse. Usa el método de CanchaRepository `getIdCanchaDisponible()` en su lugar.
     */
    public function getIdCanchaDisponible($reserva, $fecha)
    { // devolver id de la cancha disponible, preferentemente la original (si no hay ninguna, retorna 0)

        $canchaRepository = $this->em->getRepository(Cancha::class);
        $canchaPreferidaId = $reserva->getCanchaId();

        if ($canchaRepository->isCanchaDisponibleEnTurno($canchaPreferidaId, $fecha, $reserva->getHoraIni(), $reserva->getHoraFin())) {
            return $canchaPreferidaId;
        }

        $canchasDisponibles = $canchaRepository->canchasDisponiblesEnFechaYTuno($fecha, $reserva->getHoraIni(), $reserva->getHoraFin());

        // Buscar la próxima cancha disponible que no sea la preferida, es mas eficiente que un array_filter ya que no genera otro array
        foreach ($canchasDisponibles as $cancha) {
            if ($cancha->getId() !== $canchaPreferidaId) {
                return $cancha->getId(); // Devolver el ID de la primera cancha que no es la preferida
            }
        }
        return 0;
    }

    public function registrarPagoProveedor($idProveedor, $descripcion, $motivo, $monto)
    {
        $proveedor = $this->em->getRepository(Proveedor::class)->find($idProveedor); 

        $pago = new Pagos();
        //$proveedor->addPago($pago);
        $pago->setProveedor($proveedor)->setMonto($monto);
        
        $pago->setMotivo($motivo);
        $pago->setDescripcion($descripcion);
        $fechaPago = new DateTime();
        $pago->setFecha($fechaPago);
        $pago->setHora(new DateTime());

        $this->em->persist($pago);
        $this->em->persist($proveedor);
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

    /**
     * @deprecated Este método no debe usarse y debería eliminarse. Fue movido a CobroRepository
     */
    public function registrarCobroCliente($idCliente, $idTipoClase, $concepto, $descripcion, $monto, $fecha)
    {
        $cliente = $this->em->getRepository(Cliente::class)->find($idCliente);

        $cobro = new Cobro();
        //        $cliente->addCobro($cobro);
        $cobro->setCliente($cliente)->setMonto($monto);
        $fechaCobro = isset($fecha) ? $fecha : new Date();
        $cobro->setFecha($fechaCobro);
        $cobro->setConcepto($concepto);
        $cobro->setDescripcion($descripcion);
        $cobro->setIdTipoClase($idTipoClase);
        $cobro->setHora(new DateTime());

        $this->em->persist($cobro);
        $this->em->persist($cliente);
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

                $idPersonasGrupo = $this->em->getRepository(Grupo::class)->findPersonasGrupoByReservaId($reserva->getId());

                foreach ($idPersonasGrupo as $personaId) {


                    $pago = new Pagos();
                    $pago->setIdCliente($personaId->getClienteId());
                    $pago->setFecha($reserva->getFecha());
                    $tipoClase = $reserva->getIdTipoClase() != null ? $reserva->getIdTipoClase() : 2;
                    $pago->setIdTipoClase($tipoClase);
                    $pago->setCantidad(-1);
                    $pago->setMonto(0);//Se debe definir un monto
                    $pago->setHora(new \DateTime()); //Se debe definir una hora
                    $pago->setMotivo("Default"); //Se debe definir un motivo
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

        //$replicas = $this->em->getRepository(Replicas::class)->findAll();
        $fechaActual = new DateTime();
        $replicas = $this->em->getRepository(Replicas::class)->finAllByLastMonth($fechaActual->format("m"));

        foreach ($replicas as $replica) {

            $this->replicarReservaReplicada($replica->getIdReserva(), $replica->getUltimoMes());
        }
    }

    public function replicarReservaReplicada($reservaId, $replicadaHastaMes)
    {
        /* pasé esta lógica a una query de ReplicaRepository
        $fechaHoy = new DateTime();
        $mesActual = $fechaHoy->format('m');

        if ($mesActual != $replicadaHastaMes) {
            return;
        }
        */

        $clase = $this->em->getRepository(Reserva::class)->findOneById($reservaId);

        $grupo = $this->em->getRepository(Grupo::class)->findPersonasGrupoByReservaId($reservaId);

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

                $idUltimaReserva = $this->em->getRepository(Reserva::class)->getLastReservaId();
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


    public function add_people_to_group($clientes)
    {
        $lastReservaId = $this->em->getRepository(Reserva::class)->getLastReservaId();

        foreach ($clientes as $cliente_id) {
            $grupo_cliente = new Grupo();
            $grupo_cliente->setReservaId($lastReservaId);
            $grupo_cliente->setClienteId($cliente_id);
            $this->em->persist($grupo_cliente);
            $this->em->flush();
        }
    }

    public function without_reservations($canchaId, $fechaInicio, $horaIni, $horaFin)
    {
        return $this->em->getRepository(Reserva::class)->findReservasBycanchaIdAndDateAndTime($canchaId, $fechaInicio, $horaIni, $horaFin) == null;
    }

    /** 
     * @deprecated Este método no debe usarse y debería eliminarse. BalanzaController ya tiene un método privado idéntico para ello
     */
    public function totalMontos($collection)
    {
        $total = 0;
        foreach ($collection as $item) {
            $total += $item->getMonto();
        }
        return $total;
    }

    public function getNextReservasOfCliente(\DateTime $startDate, \DateTime $endDate, $cliente) 
    {
        $reservas = $this->em->getRepository(Reserva::class)->findReservasBetweenStartDateAndEndDate($startDate, $endDate, $cliente);
        $reservasPorDia = [
            0 => [], // domingo
            1 => [], // lunes
            2 => [], // martes
            3 => [], // miércoles
            4 => [], // jueves
            5 => [], // viernes
            6 => [], // sábado
        ];

        foreach ($reservas as $reserva) {
            $dia = $reserva->getFecha()->format('w'); // 'w' devuelve el día de la semana (0=domingo, 6=sábado)

            $reservaFormateada = [
                'id' => $reserva->getId(),
                'cancha' => $this->em->getRepository(Cancha::class)->findOneById($reserva->getCanchaId())->getNombre(),
                'fecha' => $reserva->getFecha()->format('Y-m-d'),
                'hora_ini' => $reserva->getHoraIni()->format('H:i:s'),
                'hora_fin' => $reserva->getHoraFin()->format('H:i:s'),
                'estado' => $this->em->getRepository(Estado::class)->findOneById($reserva->getEstadoId())->getDescripcion(),
                'tipo' => $this->em->getRepository(Clases::class)->findOneById($reserva->getIdTipoClase())->getTipo(),
                'profesor' => $this->em->getRepository(Profesor::class)->findOneById($reserva->getProfesorId())->getNombre()
            ];

            $reservasPorDia[$dia][] = $reservaFormateada;
        }

        // Obtener el día de inicio como número (0=domingo, 6=sábado)
        $startDayNumber = $startDate->format('w'); 

        // Reordenar el array a partir del día de inicio
        $diasSemana = [
            0 => 'domingo',
            1 => 'lunes',
            2 => 'martes',
            3 => 'miércoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sábado',
        ];

        $reservasPorDiaOrdenadas = [];
        for ($i = 0; $i < 7; $i++) {
            $diaIndex = ($startDayNumber + $i) % 7;
            $diaSemana = $diasSemana[$diaIndex];
            $reservasPorDiaOrdenadas[$diaSemana] = $reservasPorDia[$diaIndex];
        }

        return $reservasPorDiaOrdenadas;
    }

    public function findCanceledReservasByClienteId($cliente){
        $reservas = $this->em->getRepository(Reserva::class)->findReservasByClientIdAndEstadoCanceled($cliente);
        /*
        $reservasFormateadas = [];
        foreach ($reservas as $reserva) {
            $reservaFormateada = [
                'id' => $reserva->getId(),
                'cancha' => $this->em->getRepository(Cancha::class)->findOneById($reserva->getCanchaId())->getNombre(),
                'fecha' => $reserva->getFecha()->format('Y-m-d'),
                'hora_ini' => $reserva->getHoraIni()->format('H:i:s'),
                'hora_fin' => $reserva->getHoraFin()->format('H:i:s'),
                'estado' => $this->em->getRepository(Estado::class)->findOneById($reserva->getEstadoId())->getDescripcion(),
                'tipo' => $this->em->getRepository(Clases::class)->findOneById($reserva->getIdTipoClase())->getTipo(),
                'profesor' => $this->em->getRepository(Profesor::class)->findOneById($reserva->getProfesorId())->getNombre()
            ];
            $reservasFormateadas[] = $reservaFormateada;
        }
        return $reservasFormateadas;
        */
        return count($reservas);
    }
}
