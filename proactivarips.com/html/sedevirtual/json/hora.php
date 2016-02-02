<?php
setlocale(LC_TIME, 'es_ES.utf-8');


$hora = array();
$hora[hora_hoy] = date('h:i:s A');
echo json_encode($hora);

