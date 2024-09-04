<?php

namespace App\Controller;

use App\Entity\Clases;
use App\Entity\Profesor;
use App\Entity\Usuario;
use App\Repository\ClasesRepository;
use App\Repository\ReservaRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CorreoService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route(path="/api")
 */
class ProfesorController extends AbstractController
{

    // public function index(): Response
    // {
    //     return $this->render('profesor/index.html.twig', [
    //         'controller_name' => 'ProfesorController',
    //     ]);
    // }

    // private $correoService;

    // public function __construct(CorreoService $correoService)
    // {
    //     $this->correoService = $correoService;
    // }


    /**
     * @Route("/profesoress", name="app_profesoress", methods={"GET"})
     */
    public function getProfesores(): Response // CHEQUEAR QUE ISGRANTED NO ROMPA EN EL FRONT
    {   // NOTE: SEGURAMENTE LARGUE ALGUNA EXCEP SI UN USUARIO QUE NO TIENE EL ROL QUIERA ENTRAR, 
        //VEIRIFCAR CON ISGRANTED ACA ADENTRO
        $profesores = $this->getDoctrine()->getRepository(Profesor::class)->findAll();

        return $this->json($profesores);
    }

    /**
     * @Route("/profesorr", name="app_personass", methods={"GET"})
     */
    public function getProfesor(Request $request, ManagerRegistry $doctrine): Response
    {
        $profesorId = $request->query->get('profesorId');
        $em = $doctrine->getManager();
        $profesor = $em->getRepository(Profesor::class)->findOneById($profesorId); // pensar en cambiar a Email
        return $this->json($profesor);
    }

    /**
     * @Route("/profesorr", name="app_alta_profesorr", methods={"POST"})
     */
    public function addProfesor(
        Request $request,
        ManagerRegistry $doctrine,
        EntityManagerInterface $entityManager
    ): Response {

        $data = json_decode($request->getContent());
        $nombre = $data->nombre;
        $telefono = $data->telefono;
        $email = $data->email;

        $profesor = new Profesor();
        $usuario = new Usuario();
        $profesor->setTelefono($telefono);
        $profesor->setNombre($nombre);
        $profesor->setEmail($email);

        $contrasenaAleatoria = $this->generarContrasenaAleatoria();
        $usuario->setPassword($contrasenaAleatoria);

        $usuario->setUsername($profesor->getEmail()); // cambiar método desde el profesor
        $usuario->setProfesor($profesor); // Idem
        $usuario->setRol('ROLE_PROFESOR'); // seteo el nombre del rol, para podes acceder a las rutas
        $profesor->setUsuario($usuario);
        // $this->correoService->enviarCorreoCreacionProfesor($email, $nombre, $password);

        $em = $doctrine->getManager();
        $em->persist($profesor);
        $em->flush();

        if ($profesor->getId() > 0) {

            $resp['rta'] =  "ok";
            $resp['detail'] = "Persona dada de alta exitosamente.";

            // Persiste las entidades en la base de datos
            $entityManager->persist($usuario);
            $entityManager->persist($profesor);
            // Aplico los cambios en la base de datos
            $entityManager->flush();
        } else {
            $resp['rta'] =  "error";
            $resp['detail'] = "Se produjo un error en el alta de la persona.";
        }

        return $this->json(($resp));
    }

    /**
     * @Route("/profesorr", name="app_mod_persona", methods={"PUT"})
     */
    public function modProfesor(Request $request, ManagerRegistry $doctrine): Response
    {
        $data = json_decode($request->getContent());
        $profesorId = $data->id;
        $resp = array();

        if ($profesorId != null) {
            $em = $doctrine->getManager();
            $profesor = $em->getRepository(Profesor::class)->findOneById($profesorId);

            if ($profesor != null) {
                if (isset($data->nombre)) {
                    $profesor->setNombre($data->nombre);
                }
                if (isset($data->telefono)) {
                    $profesor->setTelefono($data->telefono);
                }
                if (isset($data->email)) {
                    $profesor->setEmail($data->email);
                }

                $em->persist($profesor);
                $em->flush();

                $resp['rta'] =  "ok";
                $resp['detail'] = "Profesor modificado correctamente";
            } else {
                $resp['rta'] =  "error";
                $resp['detail'] = "No existe el profesor";
            }
        } else {
            $resp['rta'] =  "error";
            $resp['detail'] = "Debe proveer un id";
        }
        return $this->json($resp);
    }

    private function generarContrasenaAleatoria()
    {
        $length = 12; // Longitud de la contraseña aleatoria
        $bytes = random_bytes($length);
        return base64_encode($bytes);
    }

    /**
     * @Route("/saldo-profesor-adeudado", name="app_saldo_profesor_adeudado", methods={"GET"})
     */
    public function getSaldoAdeudado(
        Request $request,
        ClasesRepository $clasesRepository,
        ReservaRepository $reservaRepository
    ): Response {
        $profesorId = 1;

        $fechaInicio = $request->query->get('fechaInicio');
        $fechaFin = $request->query->get('fechaFin');

        $primerDia = \DateTime::createFromFormat('Y-m-d', $fechaInicio);
        $ultimoDia = \DateTime::createFromFormat('Y-m-d', $fechaFin);

        $clasesAdeudadas = $reservaRepository->findReservasProfesorSinPagoId($profesorId, $primerDia, $ultimoDia);
        $cantClases = count($clasesAdeudadas);

        $total = 0;
        foreach ($clasesAdeudadas as $clase) {
            $total += $clasesRepository->findOneById($clase->getIdTipoClase())->getImporte();
        }

        return new JsonResponse([
            'periodo' => $primerDia->format('d-m-Y') . ' - ' . $ultimoDia->format('d-m-Y'),
            'cantClases' => $cantClases,
            'total' => $total,
        ]);
    }

    /**
     * @Route("/saldo-profesor", name="app_saldo_profesor", methods={"GET"})
     */
    public function getSaldoProfesor(
        Request $request,
        ClasesRepository $clasesRepository,
        ReservaRepository $reservaRepository
    ): Response {
        $profesorId = 1;

        $fechaInicio = $request->query->get('fechaInicio');
        $fechaFin = $request->query->get('fechaFin');

        $primerDia = \DateTime::createFromFormat('Y-m-d', $fechaInicio);
        $ultimoDia = \DateTime::createFromFormat('Y-m-d', $fechaFin);

        $clasesAdeudadas = $reservaRepository->findReservasProfesorConPagoId($profesorId, $primerDia, $ultimoDia);
        $cantClases = count($clasesAdeudadas);

        $total = 0;
        foreach ($clasesAdeudadas as $clase) {
            $total += $clasesRepository->findOneById($clase->getIdTipoClase())->getImporte();
        }

        return new JsonResponse([
            'periodo' => $primerDia->format('d-m-Y') . ' - ' . $ultimoDia->format('d-m-Y'),
            'cantClases' => $cantClases,
            'total' => $total,
        ]);
    }
}
