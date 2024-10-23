<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\Clases;
use App\Entity\Cobro;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CustomService as ServiceCustomService;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

    /**
     * @Route(path="/api")
     */

class CobroController extends AbstractController
{
    /**
     * @Route("/cobro", name="app_cobro")
     */
    // public function index(): Response
    // {
    //     return $this->render('cobro/index.html.twig', [
    //         'controller_name' => 'CobroController',
    //     ]);
    // }

    
    /**
     * @Route("/cobros", name="get_cobros", methods={"GET"})
     */
    public function getCobros(
        Request $request,
        ManagerRegistry $doctrine,
        ServiceCustomService $cs
    ): Response
    {

        $em = $doctrine->getManager();

        $cobros = $em->getRepository( Cobro::class )->findAll();

        $objCobros = array();
        foreach($cobros as $cobro){
            if ($cobro -> getConcepto() === '1'){
               $concepto_desc = 'Cliente';
            }
            elseif ($cobro -> getConcepto() === '2'){
                $concepto_desc = 'Alquiler';
            }
            else{
                $concepto_desc = 'Varios';
            }
           array_push($objCobros, array(
            "id" => $cobro -> getId(),
            "idCliente" => $cobro->getCliente() ? $cobro->getCliente()->getId() : null,
            "nombreCliente" => $cobro->getCliente() ? $cobro->getCliente()->getNombre() : "",
            "monto" => $cobro->getMonto(), // monto
            "fecha" => $cs->getFormattedDate($cobro->getFecha()),
            "fecha_format" => $cobro->getFecha()->format('d/m/y'),
            "hora" => $cobro->getHora()->format('H:i'),
            "concepto_id" => $cobro->getConcepto(),
            "concepto_desc" => $concepto_desc,
            "descripcion" => $cobro->getDescripcion()
            ));
        }
        return $this->json($objCobros);
    }


    /**
     * @Route("/cobros_por_cliente", name="app_Cobros_clienteId", methods={"GET"})
    */
    public function getCobrosByClienteId(
        Request $request,
        ManagerRegistry $doctrine,
        ServiceCustomService $cs
    ): Response
    {
        $clienteId = $request->query->get('clienteId');

        $em = $doctrine->getManager();

    // //  Con el ID que recibimos por parametro consultamos por la entidad Cliente e iteramos directament en su coleccion
    //     $cliente = $em->getRepository( Cliente::class )->findOneById($clienteId);

    //     $cobros = $cliente -> getCobros();

    //     $objCobros = array();

    //     if ($cobros){
    //         foreach($cobros as $cobro){
    //            array_push($objCobros, array(
    //             // utilizar el atributo concepto
    //             "concepto" => $cobro->getConcepto(),
    //             "monto" => $cobro->getMonto(), // = monto
    //             'descripcion' => $cobro->getDescripcion(),
    //             "fecha" => $cs->getFormattedDate($cobro->getFecha())
    //             ));
    //         }
    //     }
    //     return $this->json($objCobros);
    

        $cobros = $em->getRepository( Cobro::class )->findAll();
        $objCobros = [];
        $cobroRepository = $em->getRepository('App\Entity\Cobro');

        // ------------------- SOLUCION 1 --------------------------

        $cobros = $cobroRepository->createQueryBuilder('c')
        ->where('c.cliente = :clienteId')
        ->setParameter('clienteId', $clienteId)
        ->getQuery()
        ->getResult();

        
        foreach($cobros as $cobro){
            $objCobros[] = [
                "concepto" => $cobro->getConcepto(),
                "monto" => $cobro->getMonto(),
                "descripcion" => $cobro->getDescripcion(),
                "fecha" => $cs->getFormattedDate($cobro->getFecha())        
            ];
        
        }


        // ------------------- SOLUCION 2 --------------------------

        // $todosLosCobros = $cobroRepository->findAll();
        // $objCobros = [];
        // foreach ($todosLosCobros as $cobro) {
        //     // Verifica si el id del cliente matchea
        //     if ($cobro->getCliente()->getId() === $clienteId) {
                
        //         if ($cobro->getCliente()->getCobros()){ // si existen cobros para ese cliente
        //             $cobrosDeAlu = $cobro->getCliente()->getCobros();
        //             foreach ($cobrosDeAlu as $ca ) {
        //                 $objCobros[] = [
        //                     "concepto" => $ca->getConcepto(),
        //                     "monto" => $ca->getMonto(),
        //                     "descripcion" => $ca->getDescripcion(),
        //                     "fecha" => $cs->getFormattedDate($ca->getFecha())     
        //                 ];
        //             }

        //         }
        //     }
        // }

        return new JsonResponse($objCobros);

    }

    /**
     * @Route("/cobros_por_cliente_v2", name="app_cbros_por_cliente_v2", methods={"GET"})
     */
    public function getCobrosByClienteV2(
        Request $request,
        ManagerRegistry $doctrine,
        ServiceCustomService $cs
    ): Response
    {
        $clienteId = $request->query->get('clienteId');
        $em = $doctrine->getManager();

        //  Con el ID que recibimos por parametro consultamos por la entidad Cliente e iteramos directament en su coleccion
        $cobros = $em->getRepository( Cobro::class )->findBy(
            ['cliente' => strval($clienteId)],
            ['fecha' => 'DESC']
        );

        $objCobros = array();

        if ($cobros){
            foreach($cobros as $cobro){
                $tipoClase = null;
                if($cobro->getIdTipoClase() !== null){
                    $tipoClase = $em->getRepository(Clases::class)->find($cobro->getIdTipoClase());
                }
                $objCobros[] = array(
                    // utilizar el atributo concepto
                    "id" => $cobro->getId(),
                    "concepto" => $cobro->getConcepto(),
                    "monto" => $cobro->getMonto(), // = monto
                    'descripcion' => $cobro->getDescripcion(),
                    "fecha" => $cs->getFormattedDate($cobro->getFecha()),
                    "idTipoClase" => $cobro->getIdTipoClase(),
                    "tipoClase" => $tipoClase !== null ? ucfirst(strtolower($tipoClase->getTipo())) : null
                );
            }
        }
        return $this->json($objCobros);
    }

     /**
     * @Route("/cobros", name="add_cobros", methods={"POST"})
     */
    public function addCobros(
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
            //data[0] motivo, data[1] = monto
            $cs->registrarCobro($data[0], $data[1], $descripcion,$fecha);
        }
    
        $resp = array(
            "rta"=> "ok",
            "detail"=> "Registro de crobo exitoso."
        );

        return $this->json(($resp));
    }

    /**
     * @Route("/nuevo_cobro", name="add_nuevo_cobro", methods={"POST"})
     */
    public function addCobro(
        Request $request,
        ManagerRegistry $doctrine,
        ServiceCustomService $cs
    ): Response
    {
        // COBRO GENERICO SIN CLIENTE
        $data = json_decode( $request->getContent());

        $concepto = $data -> concepto;
        $monto = $data -> monto;
        $descripcion = $data -> descripcion;
        $fecha =  isset($data -> fecha) ? new DateTime($data->fecha) : null;

        if (isset($data -> clienteId)){
            $idTipoClase = $data->idTipoClase ?? null;
            $cs->registrarCobroCliente($data -> clienteId, $idTipoClase, $concepto, $descripcion, $monto, $fecha);
        }
        else {
            $cs->registrarCobro($concepto, $monto, $descripcion, $fecha);
        }

        $resp = array(
            "rta"=> "ok",
            "detail"=> "Registro de crobo exitoso."
        );

        return $this->json(($resp));
    }


    /**
     * @Route("/cobrosCliente", name="add_cobrosCliente", methods={"POST"})
    */
    public function addCobrosCliente(
        Request $request, 
        ServiceCustomService $cs
        ): Response
    {

        $data = json_decode( $request->getContent());
        $idCliente = $data->idCliente;
        $cobros = $data->cobros;
        $concepto = $data->concepto;
        $descripcion = $data->descripcion;
        $cobrosArray =  explode(',',$cobros);
        $fecha =  isset($data->fecha)? new DateTime($data->fecha) : null;
        
        foreach($cobrosArray as $cobros){
            $data = explode(':', $cobros );
            $cs->registrarCobroCliente($idCliente,$data[0], $data[1], $concepto, $descripcion,$fecha);
        }
    
        $resp = array(
            "rta"=> "ok",
            "detail"=> "Registro de cobro al cliente exitoso."
        );

        return $this->json(($resp));
    }
    



}
