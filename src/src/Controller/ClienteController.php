<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\Usuario;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ClienteRepository;
use App\Service\CustomService as ServiceCustomService;
use App\Service\DateTimeFormatterService;



/**
 * @Route(path="/api")
*/
class ClienteController extends AbstractController
{
    
    /**
     * @Route("/clientes", name="app_get_clientes", methods={"GET"})
    */
    public function getClientes(): Response
    {   
        $clientes = $this->getDoctrine()->getRepository( Cliente::class )->findAll();

        return $this->json($clientes);
    }

    /**
     * @Route("/cliente", name="app_get_cliente", methods={"GET"})
    */
    public function getCliente(Request $request, ManagerRegistry $doctrine): Response
    {
        $clientesId = $request->query->get('clienteId');
        $em = $doctrine->getManager();
        $cliente = $em->getRepository( Cliente::class )->findOneById($clientesId);
        return $this->json($cliente);
    }


    /**
     * @Route("/cliente", name="app_alta_cliente", methods={"POST"})
    */
    public function addCliente(Request $request, ManagerRegistry $doctrine,
    EntityManagerInterface $entityManager): Response
   {

       $data = json_decode( $request->getContent());
       $nombre = $data->nombre;
       $telefono = $data->telefono;
       $fecha_nac = isset($data->fechaNac) &&  strlen($data->fechaNac) > 0 ? new DateTime($data->fechaNac): null;
       $esCliente = isset($data->esAlumno) && $data->esAlumno == 'true'? true: false;

       $cliente = new Cliente();
       $usuario = new Usuario();
       $cliente->setNombre($nombre)->setTelefono($telefono);
       $cliente -> setFechaNac($fecha_nac);
       $cliente->setEsAlumno($esAlumno);
       $cliente->setVisible(true);

       $usuario->setUsername($cliente->getNombre() + $cliente->getTelefono()); // cambiar método desde el cliente
       $usuario->setCliente($cliente); // Idem
       $usuario->setRolPorDefecto('ROLE_CLIENTE'); // seteo el nombre del rol, para podes acceder a las rutas
       $cliente->setUsuario($usuario);

       $em = $doctrine->getManager();
       $em->persist($cliente);
       $em->flush();
     
       if ($cliente->getId() > 0){

           $resp['rta'] =  "ok";
           $resp['detail'] = "Cliente dado de alta exitosamente.";
           
           // Persiste las entidades en la base de datos
           $entityManager->persist($cliente);
           $entityManager->persist($usuario);
           // Aplico los cambios en la base de datos
           $entityManager->flush();

       } else {
           $resp['rta'] =  "error";
           $resp['detail'] = "Se produjo un error en el alta de al cliente.";
       }

       return $this->json(($resp));
   }
    /**
     * @Route("/cliente", name="app_mod_cliente", methods={"PUT"})
    */
    public function modCliente(Request $request, ManagerRegistry $doctrine): Response {
        $data = json_decode( $request->getContent());
        $clienteId = $data->id;
        $resp = array();

        if ($clienteId != null) {
            $em = $doctrine->getManager();
            $cliente = $em->getRepository( Cliente::class )->findOneById($clienteId);

            if ($cliente!=null){
                if (isset($data->nombre)){
                    $cliente->setNombre($data->nombre);
                }
                if (isset($data->telefono)){
                    $cliente->setTelefono($data->telefono);
                }
                if (isset($data->fechaNac)){
                    $fechaNac = strlen($data->fechaNac) > 0 ? new DateTime($data->fechaNac): null;
                    $cliente->setFechaNac($fechaNac);
                }
                if (isset($data->visible)){
                    $cliente->setVisible($data->visible);
                }

                $em->persist($cliente);
                $em->flush();

                $resp['rta'] =  "ok";
                $resp['detail'] = "Cliente modificado correctamente";
            } else {
                $resp['rta'] =  "error";
                $resp['detail'] = "No existe el cliente";
            }
        } else {
            $resp['rta'] =  "error";
            $resp['detail'] = "Debe proveer un id";
        }
        return $this->json($resp);
    }

    /**
     * @Route("/cliente/next-clases", methods={"GET"}, name="app_get_next_clases")
     */
    public function getNextReservas(Request $request, ManagerRegistry $doctrine, ServiceCustomService $cs): Response
    {
        $clienteId = $request->query->get('clienteId');
        $startDate = new \DateTime($request->query->get('startDate'));

        $em = $doctrine->getManager();
        $cliente = $em->getRepository(Cliente::class)->findOneById($clienteId);
        
        if (!$cliente) {
            $resp['rta'] = "error";
            $resp['detail'] = "No existe el cliente";
        } else {
            $today = new \DateTime();
            $today->setTime(0, 0);
            if ($startDate < $today) {
                $resp['rta'] = "error";
                $resp['detail'] = "La fecha no debe ser anterior a hoy";
            } else {
                $endDate = (clone $startDate)->modify('+6 days');
                $reservasFormateadas = $cs->getNextReservasOfCliente($startDate, $endDate, $cliente);
                $resp['rta'] = "ok";
                $resp['detail'] = $reservasFormateadas;
            }
        }
        return $this->json($resp);
    }

    /**
     * @Route("/personas", name="app_personas", methods={"GET"})
     */
    public function getPersonas(): Response
    {
        $personas = $this->getDoctrine()->getRepository( Cliente::class )->findAll();
        return $this->json($personas);
    }

    /**
     * @Route("/persona", name="app_personas", methods={"GET"})
     */
    public function getPersona(
        Request $request,
        ManagerRegistry $doctrine
    ): Response
    {
        $clienteId = $request->query->get('personaId');
        $em = $doctrine->getManager();
        $cliente = $em->getRepository( Cliente::class )->findOneById($clienteId);
        return $this->json($cliente);
    }

    /**
     * @Route("/persona/alumnos", name="app_alumnos", methods={"GET"})
     */
    public function getAlumnos(
        ClienteRepository $clienteRepository,
        DateTimeFormatterService $formatter
    ): Response
    {
        $clientes = $clienteRepository->findAllAlumnos();
        $clientesFormateado=[];
        
        foreach($clientes as $cliente){
            $clienteFormateado = $cliente->toArrayAsociativo();
            if ($clienteFormateado["fechanac"] != ""){
                $clienteFormateado["fechanac"] = $formatter->getFormattedDate($clienteFormateado["fechanac"]);
            }
            array_push($clientesFormateado, $clienteFormateado);
        }
        $resp = array(
            "rta"=> "error",
            "detail"=> "Se produjo un error en el alta de la cancha."
        );
        if (isset($clientesFormateado)){

            $resp['rta'] =  "ok";
            $resp['detail'] = $clientesFormateado;

        }
        return $this->json($resp);
    }

    
    /**
     * @Route("/cliente/clasesAFavor", name="cliente_credits", methods={"GET"})
     */
    public function getClasesAFavor(Request $request, ManagerRegistry $doctrine, ServiceCustomService $cs): Response
    {
        $clienteId = $request->query->get('clienteID');
        $em = $doctrine->getManager();
        $cliente = $em->getRepository( Cliente::class )->findOneById($clienteId);

        if (!$cliente) {
            $resp['rta'] =  "error";
            $resp['detail'] = "No se encontró al cliente";
        }
        else {
            $reservas = $cs->findCanceledReservasByClienteId($cliente);
            $resp['rta'] =  "ok";
            $resp['detail'] = $reservas;
        }       

        return $this->json($resp);
    }   
    
    
    /**
     * @Route("/cliente/reservarClaseAFavor", name="cliente_reservar_clase_a_favor", methods={"POST"})
     */
    public function reservarClaseAFavor(Request $request, ServiceCustomService $cs): Response
    {
        $data = json_decode($request->getContent(), true);

        try {
            $fecha = $data['date'] ? new \DateTime($data['date']) : null;
            $hora_ini = $data['startTime'] ? new \DateTime($data['startTime']) : null;
            $hora_fin = $data['endTime'] ? new \DateTime($data['endTime']) : null;
            $clienteId = $data['clienteID'] ? $data['clienteID'] : null;                 
        } catch (\Exception $e) {
            $resp['rta'] =  "error";
            $resp['detail'] = "Parámetros inválido1s";
            return $this->json($resp);
        }

        if (!$fecha || !$hora_ini || !$hora_fin || !$clienteId) {
            $resp['rta'] =  "error";
            $resp['detail'] = "Parámetros inválidos";
        }
        else{
            try{
                $cs->ModificarClaseAFavor($fecha, $hora_ini, $hora_fin, $clienteId);            
                $resp['rta'] =  "ok";
                $resp['detail'] = "Se cambió la fecha y hora de la clase";
            }
            catch (\Exception $e){
                $resp['rta'] =  "error";
                $resp['detail'] = "No hay clases a favor";
            }
        }

        return $this->json($resp);
    }

}
