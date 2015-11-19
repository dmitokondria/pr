<?php

header('Content-Type: application/json');

include_once('funcionesBD.php');
include_once('funcionesProactivar.php');

$datos = array();

if ( isset($_GET[identificacion]) ){
	//buscar
	$SQLPaciente = "SELECT id, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido FROM pacientes WHERE numero_identificacion = ".$_GET[identificacion];
	insertarTablaArray_v2($paciente, $SQLPaciente, 'paciente');
	$datos[paciente] = $paciente[paciente][0];

}else{
	//alimentar
	$mensaje = json_decode(file_get_contents("php://input"));

	$SQLInsertarSeguimiento = "INSERT INTO pacientes_seguimientos (id, id_paciente, motivo, sintomas, antecedentes, medicamentos, observaciones, fecha) 
	    											 	VALUES (NULL, '".$mensaje->id."', '".$mensaje->motivo."', '".$mensaje->sintomas."', '".$mensaje->antecedentes."', '".$mensaje->medicamentos."', '".$mensaje->observaciones."', CURRENT_TIMESTAMP)";
	$id_seguimiento = insertarFila($SQLInsertarSeguimiento);

	if ( $id_seguimiento != 0 ){
		$datos[estado] = "OK";
		$datos[mensaje] = "El registro se ha creado satisfactoriamente!";
	}else{
		$datos[estado] = "ERROR";
		$datos[mensaje] = "UPS!. Algún dato es erroneo";
	}
}

if ( isset($_GET[debug]) ){
	echo "<pre>";
	print_r($datos);
	echo "</pre>";
}else {
	echo json_encode($datos);
}

?>