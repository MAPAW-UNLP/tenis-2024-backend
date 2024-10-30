<?php

namespace App\Service;

use App\Entity\ConstanciaMantenimiento;
use App\Repository\ConstanciaMantenimientoRepository;
use DateTime;

class MantenimientoService{

    private $repo;
    private $customService;

    public function __construct(ConstanciaMantenimientoRepository $repo, CustomService $customService) {
        $this->repo = $repo;
        $this->customService = $customService;
    }

    private function hayMantenimientoPendiente(): bool{
        $ultimoMantenimiento = $this->repo->getLastConstancia()->getFecha(); //siempre devuelve una Constancia, SIEMPRE
        $hoy = new DateTime();
        return $ultimoMantenimiento < $hoy; //se hace 1 vez al día
    }

    public function realizarMantenimiento(){
        if($this->hayMantenimientoPendiente($this->repo)){
            $this->customService->procesamientoInicial();
        }
        $this->repo->updateLastConstancia(new DateTime()); //actualiza a la fecha actual
    }
}