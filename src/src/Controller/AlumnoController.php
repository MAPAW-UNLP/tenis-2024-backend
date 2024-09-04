<?php

namespace App\Controller;

use App\Entity\Alumno;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;



/**
 * @Route(path="/api")
*/
class AlumnoController extends AbstractController
{
    
    /**
     * @Route("/alumnos", name="app_get_alumnos", methods={"GET"})
    */
    public function getAlumnos(): Response
    {   
        $alumnos = $this->getDoctrine()->getRepository( Alumno::class )->findAll();

        return $this->json($alumnos);
    }

    /**
     * @Route("/alumno", name="app_get_alumno", methods={"GET"})
    */
    public function getAlumno(Request $request, ManagerRegistry $doctrine): Response
    {
        $alumnosId = $request->query->get('alumnoId');
        $em = $doctrine->getManager();
        $alumno = $em->getRepository( Alumno::class )->findOneById($alumnosId);
        return $this->json($alumno);
    }


    /**
     * @Route("/alumno", name="app_alta_alumno", methods={"POST"})
    */
    public function addAlumno(Request $request, ManagerRegistry $doctrine,
     EntityManagerInterface $entityManager): Response
    {

        $data = json_decode( $request->getContent());
        $nombre = $data->nombre;
        $telefono = $data->telefono;
        $fecha_nac = isset($data->fechaNac) &&  strlen($data->fechaNac) > 0 ? new DateTime($data->fechaNac): null;

        $alumno = new Alumno();
        $alumno->setNombre($nombre)->setTelefono($telefono);
        $alumno -> setFechaNac($fecha_nac);

        $em = $doctrine->getManager();
        $em->persist($alumno);
        $em->flush();
      
        if ($alumno->getId() > 0){

            $resp['rta'] =  "ok";
            $resp['detail'] = "Alumno dado de alta exitosamente.";
            
            // Persiste las entidades en la base de datos
            $entityManager->persist($alumno);
            // Aplico los cambios en la base de datos
            $entityManager->flush();

        } else {
            $resp['rta'] =  "error";
            $resp['detail'] = "Se produjo un error en el alta de al alumno.";
        }

        return $this->json(($resp));
    }

    /**
     * @Route("/alumno", name="app_mod_alumno", methods={"PUT"})
    */
    public function modAlumno(Request $request, ManagerRegistry $doctrine): Response {
        $data = json_decode( $request->getContent());
        $alumnoId = $data->id;
        $resp = array();

        if ($alumnoId != null) {
            $em = $doctrine->getManager();
            $alumno = $em->getRepository( Alumno::class )->findOneById($alumnoId);

            if ($alumno!=null){
                if (isset($data->nombre)){
                    $alumno->setNombre($data->nombre);
                }
                if (isset($data->telefono)){
                    $alumno->setTelefono($data->telefono);
                }

                $em->persist($alumno);
                $em->flush();

                $resp['rta'] =  "ok";
                $resp['detail'] = "Alumno modificado correctamente";
            } else {
                $resp['rta'] =  "error";
                $resp['detail'] = "No existe el alumno";
            }
        } else {
            $resp['rta'] =  "error";
            $resp['detail'] = "Debe proveer un id";
        }
        return $this->json($resp);
    }



}
