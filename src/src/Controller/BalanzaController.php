<?php

namespace App\Controller;

use App\Service\CustomService as ServiceCustomService;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityRepository;
use App\Entity\Profesor;
use App\Entity\Alumno;
use App\Entity\Cobro;
use App\Entity\Pagos;

/**
 * @Route(path="/api")
*/

class BalanzaController extends AbstractController
{

    /**
     * @Route("/balance-general", name="app_balance", methods={"GET"})
    */    
    public function getBalanceGeneral(ManagerRegistry $doctrine, ServiceCustomService $cs) : Response {
        
        $em = $doctrine->getManager();

        $cobrosRepository = $em->getRepository( Cobro::class );
        $cobrosG = $cobrosRepository->findAll();
        
        $pagosRepository = $em->getRepository( Pagos::class );
        $pagosG = $pagosRepository->findAll();

        $alumnoRepository = $em->getRepository( Alumno::class );
        $alumnos = $alumnoRepository->findAll();

        $profesorRepository = $em->getRepository( Profesor::class );
        $profesores = $profesorRepository->findAll();


        $totalCobros = 0;
        $totalPagos = 0;

        // Pagos y cobros generales        
        $totalCobros += $cs->totalMontos($cobrosG);
        $totalPagos += $cs->totalMontos($pagosG);

        // Calcula la suma de los montos de los cobros de los alumnos
        foreach ($alumnos as $alumno) {
            $cobros = $alumno->getCobros();
            if($cobros){
                $totalCobros += $cs->totalMontos($cobros);
            }

        }

        //Idem, pero con profesores
        foreach ($profesores as $profesor) {
            $pagos = $profesor->getPagos();
            if($pagos){
                $totalPagos += $cs->totalMontos($pagos);
            }
        }

        $balanceGeneral = $totalCobros - $totalPagos;

        $responseData = [
            'totalCobros' => $totalCobros,
            'totalPagos' => $totalPagos,
            'balanceGeneral' => $balanceGeneral
        ];

        $response = new JsonResponse($responseData);

        return $response;
    }

    /**
     * @Route("/balance-list-try", name="app_balance_list", methods={"GET"})
     */
    public function getBalanceListTry(ManagerRegistry $doctrine, ServiceCustomService $cs) : Response {

        $em = $doctrine->getManager();

        $cobrosRepository = $em->getRepository( Cobro::class );
        $cobrosG = $cobrosRepository->findAll();

        $pagosRepository = $em->getRepository( Pagos::class );
        $pagosG = $pagosRepository->findAll();

        $statement = $em->getConnection()->prepare(
            "SELECT mov.fecha FROM (
                (SELECT c.id, c.fecha, c.concepto, c.monto, c.descripcion, c.alumno_id as persona_id, a.nombre, 'Cobro' AS tipo
                FROM cobro c INNER JOIN alumno a ON a.id = c.alumno_id) as cobrodata
                UNION
                (SELECT p.id, p.fecha, p.motivo as concepto, p.monto, p.descripcion, p.profesor_id as persona_id, prof.nombre, 'Pago' AS tipo
                FROM pago p INNER JOIN profesor prof ON prof.id = p.profesor_id) as pagodata
            ) as mov 
            ORDER BY mov.fecha DESC"
        );
        $result = $statement->execute();
        $results = $result-> fetchAll();

        // $result = $query->getResult();

        return new JsonResponse($results);
    }

    /**
     * @Route("/balance-list-alt", name="app_balance_alt", methods={"GET"})
     */
    public function getBalanceList(ManagerRegistry $doctrine, ServiceCustomService $cs) : Response {

        $em = $doctrine->getManager();

        $cobrosRepository = $em->getRepository( Cobro::class );
        $arrResult = $cobrosRepository->findAll();

        $pagosRepository = $em->getRepository( Pagos::class );
        $pagosG = $pagosRepository->findAll();


        // Calcula la suma de los montos de los cobros de los alumnos
        foreach ($pagosG as $pago) {
            $arrResult[] = $pago;
        }

        return new JsonResponse($arrResult);
    }

    /**
     * @Route("/balance-en-fecha", name="app_balance_fecha", methods={"GET"})
     */
    public function getBalanceEnFecha(Request $request, ManagerRegistry $doctrine, ServiceCustomService $cs): Response {

        $fechaDesde = $request->query->get('fecha_inicio');
        $fechaHasta = $request->query->get('fecha_fin');
        $descripcion = $request->query->get('descripcion');

        $em = $doctrine->getManager();
        
        $totalPagos = 0;
        $dataPagos = [];
        $dataCobros = [];
        $totalCobros = 0;

        // MODULARIZAR LA QUERY
        if(empty ($descripcion)){
            $query = $em->createQuery(
                "SELECT c 
                FROM App\Entity\Cobro c 
                WHERE c.fecha 
                BETWEEN :desde AND :hasta");
            $query->setParameter('desde', $fechaDesde);
            $query->setParameter('hasta', $fechaHasta);         
        
            $resultadoCobros = $query->getResult();
            
            $query = $em->createQuery(
                "SELECT p 
                FROM App\Entity\Pagos p 
                WHERE p.fecha 
                BETWEEN :desde AND :hasta");
            $query->setParameter('desde', $fechaDesde);
            $query->setParameter('hasta', $fechaHasta);       
        
            $resultadoPagos = $query->getResult();

        }else{

            // Filtra de cobros por descripcion y fecha
            $query = $em->createQuery(
                "SELECT c 
                FROM App\Entity\Cobro c 
                WHERE c.descripcion 
                LIKE :desc AND (c.fecha BETWEEN :desde AND :hasta)");
            $query->setParameter('desde', $fechaDesde);
            $query->setParameter('hasta', $fechaHasta);
            $query->setParameter('desc', '%' . $descripcion . '%'); // cualquier string que contenga $descripcion en algún lugar

            $resultadoCobros = $query->getResult();
            
            // Filtrado de pagos con descripcion
            $query = $em->createQuery(
                "SELECT p 
                FROM App\Entity\Pagos p 
                WHERE p.descripcion 
                LIKE :desc AND (p.fecha BETWEEN :desde AND :hasta)");
            $query->setParameter('desde', $fechaDesde);
            $query->setParameter('hasta', $fechaHasta);
            $query->setParameter('desc', '%' . $descripcion . '%');
            
            $resultadoPagos = $query->getResult();
        }

        // NO USAR METODOS OBTENIDOS DE CLASES HIJAS PORQUE NO FUNCIONA
        if($resultadoCobros){
            foreach ($resultadoCobros as $cobro) {
                $totalCobros += $cobro->getMonto();
                $dataCobros[] = [
                    'id' => $cobro->getId(),
                    'fecha' => $cs->getFormattedDate($cobro->getFecha()),
                    'fecha_format' => $cobro->getFecha()->format('d/m/y'),
                    "hora" => $cobro->getHora()->format('H:i'),
                    'concepto' => $cobro->getConcepto(),
                    'concepto_desc' => intval($cobro->getConcepto()) === 1 ? 'Alumno' :
                        (intval($cobro->getConcepto()) === 2 ? 'Alquiler' : 'Varios'),
                    'descripcion' => $cobro->getDescripcion() ? $cobro->getDescripcion() : null,
                    'monto' => $cobro->getMonto(),
                    'nombre' => $cobro->getAlumno() ? $cobro->getAlumno()->getNombre() : "",
                    'movimiento_id' => 1 // Cobro
                ];
            }
        }

        if($resultadoPagos) {
            foreach ($resultadoPagos as $pagos) {
                $totalPagos += $pagos->getMonto();
                $dataPagos[] = [
                    'id' => $pagos->getId(),
                    'fecha' => $cs->getFormattedDate($pagos->getFecha()),
                    'fecha_format' => $pagos->getFecha()->format('d/m/y'),
                    "hora" => $pagos->getHora()->format('H:i'),
                    'concepto' => $pagos->getMotivo(),
                    'concepto_desc' => intval($pagos->getMotivo()) === 1 ? 'Profesor' :
                        (intval($pagos->getMotivo()) === 2 ? 'Proveedor' : 'Varios'),
                    'descripcion' => $pagos->getDescripcion() ? $pagos->getDescripcion() : null,
                    'monto' => $pagos->getMonto(),
                    'nombre' => $pagos->getProfesor() ? $pagos->getProfesor()->getNombre() : "",
                    'movimiento_id' => 2 // Pago
                ];
            }

        }

        $balanceGeneral = $totalCobros - $totalPagos;

        // Con las dos colecciones procedemos a mergearlas en una sola ordenando los elementos por fecha
        $merged = new ArrayCollection(
            array_merge($dataCobros, $dataPagos)
        );

        $iterator = $merged->getIterator();
        $iterator->uasort(function ($a, $b) {
            $fechaA = $a['fecha'];
            $fechaB = $b['fecha'];

            // Primero, ordenar por fecha
            if ($fechaA != $fechaB) {
                return ($fechaA > $fechaB) ? -1 : 1;
            }

            // Si las fechas son iguales, ordenar por hora
            $horaA = $a['hora'];
            $horaB = $b['hora'];

            return ($horaA > $horaB) ? -1 : 1;
        });

        $merged_result = iterator_to_array($iterator, false);
        $responseData = [
            'movimientos' => $merged_result,
            'total' => $balanceGeneral
        ];

        return new JsonResponse($responseData);
    }


    // private function obtenerDatos($em, $entidad, $campo, $fechaDesde, $fechaHasta, $descripcion, $cs){

    //     $total = 0;
    //     $data = [];

    //     $query = $em->createQuery(
    //         "SELECT e 
    //         FROM App\Entity\\$entidad e 
    //         WHERE e.$campo LIKE :desc AND (e.fecha BETWEEN :desde AND :hasta)"
    //     );

    //     $query->setParameter('desde', $fechaDesde);
    //     $query->setParameter('hasta', $fechaHasta);
    //     $query->setParameter('desc', '%' . $descripcion . '%'); // cualquier string que contenga $descripcion en algún lugar

    //     $resultados = $query->getResult();
    
    //     if ($resultados) {
    //         foreach ($resultados as $resultado) {
    //             $entidadRelacionada = $resultados->getAlumno()->getCobros();
    //             if ($entidadRelacionada) {
    //                 $total += $cs->totalMontos($entidadRelacionada);
    //             }
    
    //             $data[] = [
    //                 'Dia' => $resultado->getFecha(),
    //                 'Concepto' => $resultado->getConcepto(),
    //                 'Descripcion' => $resultado->getDescripcion(),
    //                 $entidad === 'Cobro' ? 'Debe' : 'Haber' => $resultado->getMonto(),
    //             ];
    //         }
    //     }




    // }


}
