<?php

include_once('funcionesBD.php');

//traer e interpretar datos POST
$mensaje = json_decode(file_get_contents("php://input"));
$datos = array();

$SQLInsert = "INSERT INTO paciente_rapido (id, primer_apellido, segundo_apellido, primer_nombre, segundo_nombre, tipo_identificacion, identificacion, email, celular, telefono, bl_telefono, bl_mail)  
			VALUES (NULL, '".$mensaje->primer_apellido."', '".$mensaje->segundo_apellido."', '".$mensaje->primer_nombre."', '".$mensaje->segundo_nombre."', '".$mensaje->tipo_identificacion."', '".$mensaje->identificacion."', '".$mensaje->email."', '".$mensaje->celular."', '".$mensaje->telefono."', '".$mensaje->bl_telefono."', '".$mensaje->bl_email."')";
$id_nuevo = ejecutarQuery($SQLInsert);

if ( intval($id_nuevo) != 0 ){
	$datos[mensaje] = "OK";
	$datos[nombre] = "El señor(a) ".$mensaje->primer_nombre." ".$mensaje->primer_apellido." ha sido creado satisfactoriamente!";
	$datos[id] = $id_nuevo;
}else{
	$datos[mensaje] = "ERROR";
	$datos[id] = $id_nuevo;
}

echo json_encode($datos);

?>