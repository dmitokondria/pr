<?php

include_once('funcionesBD.php');

//traer e interpretar datos POST
$mensaje = json_decode(file_get_contents("php://input"));
$usuario = $mensaje->usuario;
$clave = $mensaje->clave;

$login = array(
	'id' => '',
	'nombres' => '',
	'tipo_usuario' => '',
	'mensaje' => 'ERROR'
);

//usuarios
$SQLUsuario  = "SELECT u.id, CONCAT(u.nombres,' ', u.apellidos) as nombre, u_t.nombre as tipo_usuario, e.nombre AS especialidad, e.id AS especialidad_id
				FROM usuarios u
				LEFT JOIN usuario_tipos u_t ON u_t.id = u.sl_tipo
				LEFT JOIN especialidades e ON e.id = u.sl_profesional
				WHERE u.usuario='".$usuario."' AND u.clave='".$clave."' ";
insertarTablaArray_v2($usuarios, $SQLUsuario,'usuarios');

//Buscando el paciente
$SQLPaciente = "SELECT id, CONCAT(primer_nombre,' ', primer_apellido) as nombre, 'paciente' as tipo_usuario, edad, ocupacion
				FROM pacientes
				WHERE numero_identificacion='".$usuario."' AND clave='".$clave."'";
insertarTablaArray_v2($pacientes, $SQLPaciente, 'pacientes');

//paciente u otro usuario
$tipo_usuario = "";
if ( sizeof($usuarios[usuarios]) != 0 ){
	//setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
	//setcookie('especialidad', $usuarios[usuarios][0][especialidad], time() + (10400 * 30), "/");
	//setcookie('especialidad_id', $usuarios[usuarios][0][especialidad_id], time() + (10400 * 30), "/");
	//$login[especialidad] = $usuarios[usuarios][0][especialidad];
	//$login[especialidad_id] = $usuarios[usuarios][0][especialidad_id];
	$login[tipo_usuario] = $usuarios[usuarios][0][tipo_usuario];
	$login[id] = $usuarios[usuarios][0][id];
	$login[nombres] = $usuarios[usuarios][0][nombre];
	$login[especialidad] = $usuarios[usuarios][0][especialidad];
	$login[mensaje] = 'OK';
}else if ( sizeof($pacientes[pacientes]) != 0 ){
	$login[tipo_usuario] = 'Paciente';
	$login[id] = $pacientes[pacientes][0][id];
	$login[nombres] = $pacientes[pacientes][0][nombre];
	$login[edad] = $pacientes[pacientes][0][edad];
	$login[ocupacion] = $pacientes[pacientes][0][ocupacion];
	$login[mensaje] = 'OK';
}else if ( strcmp($usuario, 'campaña') == 0 && strcmp($clave, 'c4mp4ñ4') == 0){
	$login[tipo_usuario] = 'Campaña';
	$login[id] = 1234;
	$login[nombres] = 'Levantamiento Datos Pacientes';
	$login[mensaje] = 'OK';
}

echo json_encode($login);