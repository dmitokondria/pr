<?php
setlocale(LC_TIME, 'es_ES.utf-8');

$fecha = array();
$fecha[info] = ucfirst(strftime("%A, "))." ".ucfirst(strftime("%B %#d de %Y"));

echo json_encode($fecha);