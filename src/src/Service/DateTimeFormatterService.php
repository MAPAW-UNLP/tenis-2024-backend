<?php
namespace App\Service;
use DateTime;

class DateTimeFormatterService{

    public function getFormattedTime(DateTime $time) :string
    {
        return $time->format('H:i');
    }

    public function getFormattedDate(DateTime $fecha): string
    {
        return $fecha->format('Y-m-d');
    }

}