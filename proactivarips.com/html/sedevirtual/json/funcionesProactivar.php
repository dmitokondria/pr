<?php

function daysInWeekDate($date) {

	$weekNum = date('W', strtotime($date));
	$yearNum = date('Y', strtotime($date));

    $result = array();
    $datetime = new DateTime();
    $datetime->setISODate((int) $yearNum, $weekNum, 1);
    $interval = new DateInterval('P1D');
    $week = new DatePeriod($datetime, $interval, 6);
    foreach ($week as $day) {
        $result[] = $day->format('Y-m-d');
    }
    return $result;
}

function daysInWeekYear($weekNum, $yearNum) {
    $result = array();
    $datetime = new DateTime();
    $datetime->setISODate((int) $datetime->format('Y'), $weekNum, 1);
    $interval = new DateInterval('P1D');
    $week = new DatePeriod($datetime, $interval, 6);
    foreach ($week as $day) {
        $result[] = $day->format('Y-m-d');
    }
    return $result;
}
function fechaEspanol($fecha, $format){ //'D d M Y', 'M dd Y', 'M d Y', 'M dd', 'D d M Y H:i:s','Mmm', 'h:i',
    $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
    $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
    $d = new DateTime($fecha);
    $timestamp = $d->getTimestamp(); // Unix timestamp
    $formatted_date = $d->format('Y-m-d'); // 2003-10-16
    if ( strcmp($format, 'D d M Y') == 0 ) return $dias[$d->format('w')]." ".$d->format('d')." de ".$meses[$d->format('n')-1]. " del ".$d->format('Y');
    if ( strcmp($format, 'D d M Y H:i:s') == 0 ) return $dias[$d->format('w')]." ".$d->format('d')." de ".$meses[$d->format('n')-1]. " del ".$d->format('Y')." ".$d->format('H:i:s');
    if ( strcmp($format, 'D d M Y h:i:s a') == 0 ) return $dias[$d->format('w')]." ".$d->format('d')." de ".$meses[$d->format('n')-1]. " del ".$d->format('Y')." ".$d->format('h:i a');
    if ( strcmp($format, 'h:i') == 0 ) return $d->format('h:i');
    if ( strcmp($format, 'M dd Y') == 0 ) return $meses[$d->format('n')-1]." ".$d->format('d')." de ".$d->format('Y');
    if ( strcmp($format, 'D') == 0 ) return $dias[$d->format('w')];
    if ( strcmp($format, 'Mmm') == 0 ) return substr($meses[$d->format('n')-1],0,3);
    if ( strcmp($format, 'M d Y') == 0 ) return $meses[$d->format('n')-1]." ".$d->format('j')." de ".$d->format('Y');
    if ( strcmp($format, 'M dd') == 0 ) return $meses[$d->format('n')-1]." ".$d->format('d');
    if ( strcmp($format, 'dd de M') == 0 ) return $d->format('d')." de ".$meses[$d->format('n')-1];
    return "Format Error";
}//Sábado 26 de Septiembre del 2015 13:00:00