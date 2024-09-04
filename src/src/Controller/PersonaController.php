<?php

namespace App\Controller;

use App\Entity\Persona;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CustomService as ServiceCustomService;

    /**
     * @Route(path="/api")
     */

class PersonaController extends AbstractController
{
    /**
     * @Route("/personas", name="app_personas", methods={"GET"})
     */
    public function getPersonas(): Response
    {
        $personas = $this->getDoctrine()->getRepository( Persona::class )->findAll();
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
        $personaId = $request->query->get('personaId');
        $em = $doctrine->getManager();
        $persona = $em->getRepository( Persona::class )->findOneById($personaId);
        return $this->json($persona);
    }

    /**
     * @Route("/persona", name="app_alta_persona", methods={"POST"})
     */
    public function addPersona(Request $request, ManagerRegistry $doctrine ): Response
    {

        $data = json_decode( $request->getContent());
        $nombre = $data->nombre;
        $telefono = $data->telefono;
        $esAlumno = isset($data->esalumno) && $data->esalumno == 'true'? true: false;
        $fechaNac = $esAlumno && isset($data->fechanac) &&  strlen($data->fechanac) > 0 ? new DateTime($data->fechanac): null;

        $persona = new Persona();
        $persona->setNombre($nombre);
        $persona->setTelefono($telefono);
        $persona->setFechanac($fechaNac);
        $persona->setEsalumno($esAlumno);
        $persona->setVisible(true);

        $em = $doctrine->getManager();
        $em->persist($persona);
        $em->flush();
      
        if ($persona->getId() > 0){

            $resp['rta'] =  "ok";
            $resp['detail'] = "Persona dada de alta exitosamente.";

        } else {
            $resp['rta'] =  "error";
            $resp['detail'] = "Se produjo un error en el alta de la persona.";
        }

        return $this->json(($resp));
    }

    /**
     * @Route("/persona", name="app_mod_persona", methods={"PUT"})
     */
    public function modPersona(
        Request $request,
        ManagerRegistry $doctrine
    ): Response {
        $data = json_decode( $request->getContent());
        $personaId = $data->id;
        $resp = array();
        if ($personaId != null) {
            $em = $doctrine->getManager();
            $persona = $em->getRepository( Persona::class )->findOneById($personaId);
            if ($persona!=null){
                if (isset($data->nombre)){
                    $persona->setNombre($data->nombre);
                }
                if (isset($data->telefono)){
                    $persona->setTelefono($data->telefono);
                }
                if (isset($data->fechanac)){
                    $fechaNac = strlen($data->fechanac) > 0 ? new DateTime($data->fechanac): null;
                    $persona->setFechanac($fechaNac);
                }
                if (isset($data->visible)){
                    $persona->setVisible($data->visible);
                }

                $em->persist($persona);
                $em->flush();

                $resp['rta'] =  "ok";
                $resp['detail'] = "Persona modificada correctamente";
            } else {
                $resp['rta'] =  "error";
                $resp['detail'] = "No existe la persona";
            }
        } else {
            $resp['rta'] =  "error";
            $resp['detail'] = "Debe proveer un id";
        }
        return $this->json($resp);
    }

    /**
     * @Route("/persona/alumnos", name="app_alumnos", methods={"GET"})
     */
    public function getAlumnos(
        ServiceCustomService $cs
    ): Response
    {
        $alumnos = $this->getDoctrine()->getRepository( Persona::class )->findAllAlumnos();
        $alumnosFormateado=[];

        foreach($alumnos as $alumno){
            $alumnoFormateado = $cs->formatearAlumno($alumno);
            array_push($alumnosFormateado, $alumnoFormateado);
        }
        $resp = array(
            "rta"=> "error",
            "detail"=> "Se produjo un error en el alta de la cancha."
        );
        if (isset($alumnosFormateado)){

            $resp['rta'] =  "ok";
            $resp['detail'] = $alumnosFormateado;

        }
        return $this->json($resp);
    }

    /**
     * @Route("/profesores", name="app_profesores", methods={"GET"})
     */
    public function getProfesores(): Response
    {
        $personas = $this->getDoctrine()->getRepository( Persona::class )->findAllProfesores();
        return $this->json($personas);
    }

}
