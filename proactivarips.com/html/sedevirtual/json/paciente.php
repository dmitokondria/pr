<?php
header('Content-Type: application/json');

include_once('funcionesBD.php');
include_once('funcionesProactivar.php');

//traer e interpretar datos POST
$formulario = json_decode(file_get_contents("php://input"));

$datos = array();

if ( !isset($formulario->accion) ){//información transversal

	$SQLDepartamentos = "SELECT * FROM departamentos ORDER BY nombre ASC";
	insertarTablaArray_v2($datos, $SQLDepartamentos, 'departamentos');

	$SQLCiudades = "SELECT * FROM municipios ORDER BY nombre ASC";
	insertarTablaArray_v2($datos, $SQLCiudades, 'ciudades');

	$SQLTiposIdentificacion = "SELECT * FROM identificacion_tipos ORDER BY id";
	insertarTablaArray_v2($datos, $SQLTiposIdentificacion, 'tipos_identificacion');

	$datos[fecha][anios] = array();
	for ($i=1900; $i < 2041; $i++) array_push($datos[fecha][anios], $i);

	$datos[fecha][meses] = array(array('id'=>1,'nombre'=>'Enero'),array('id'=>2,'nombre'=>'Febrero'),array('id'=>3,'nombre'=>'Marzo'),array('id'=>4,'nombre'=>'Abril'),array('id'=>5,'nombre'=>'Mayo'),array('id'=>6,'nombre'=>'Junio'),array('id'=>7,'nombre'=>'Julio'),array('id'=>8,'nombre'=>'Agosto'),array('id'=>9,'nombre'=>'Septiembre'),array('id'=>10,'nombre'=>'Octubre'),array('id'=>11,'nombre'=>'Noviembre'),array('id'=>12,'nombre'=>'Diciembre'));

	$datos[fecha][dias] = array();
	for ($i=1; $i < 32; $i++) array_push($datos[fecha][dias], $i);

	$SQLEstadosCiviles = "SELECT * FROM estados_civiles ORDER BY nombre ASC";
	insertarTablaArray_v2($datos, $SQLEstadosCiviles, 'estados_civiles');

	$SQLEpss = "SELECT * FROM epss ORDER BY nombre ASC";
	insertarTablaArray_v2($datos, $SQLEpss, 'epss');

	$SQLTiposVinculacion = "SELECT * FROM vinculaciones ORDER BY nombre ASC";
	insertarTablaArray_v2($datos, $SQLTiposVinculacion, 'tipos_vinculacion');

	$SQLNivelesEscolaridad = "SELECT * FROM escolaridad ORDER BY nombre ASC";
	insertarTablaArray_v2($datos, $SQLNivelesEscolaridad, 'niveles_escolaridad');

	$SQLPaquetes = "SELECT * FROM paquetes ORDER BY nombre ASC";
	insertarTablaArray_v2($datos, $SQLPaquetes, 'paquetes');

	$SQLAfiliacionEstados = "SELECT * FROM afiliacion_estados ORDER BY nombre ASC";
	insertarTablaArray_v2($datos, $SQLAfiliacionEstados, 'afiliacion_estados');
}else if ( strcmp($formulario->accion, 'crear') == 0 ){
	$SQLInsertPaciente = "INSERT INTO pacientes (id, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, rd_tipo_identificacion, numero_identificacion, sl_departamento, sl_municipio, da_nacimiento, edad, email, clave, sl_cliente, rd_genero, sl_estado_civil, ocupacion, direccion, telefono, celular, sl_eps, sl_tipo_vinculacion, sl_escolaridad, acudiente, acudiente_parentesco, acudiente_celular, servicios, sl_estado_afiliacion) VALUES
												(NULL, '".$formulario->primer_nombre."', '".$formulario->segundo_nombre."', '".$formulario->primer_apellido."', '".$formulario->segundo_apellido."', ".$formulario->tipo_identificacion.", ".$formulario->identificacion.", ".$formulario->departamento.", ".$formulario->ciudad.", '".$formulario->anio."-".$formulario->mes."-".$formulario->dia."', 100, '".$formulario->email."', '1234', 1, ".$formulario->genero.", ".$formulario->estadocivil.", '".$formulario->ocupacion."', '".$formulario->direccion."', '".$formulario->telefono."', '".$formulario->celular."', ".$formulario->entidad.", ".$formulario->tipovinculacion.", ".$formulario->escolaridad.", '".$formulario->acudiente."', '".$formulario->parentesco."', '".$formulario->celacudiente."', ".$formulario->paquete.", ".$formulario->afiliacion.")";
	$id_paciente = insertarFila($SQLInsertPaciente);

	if ( intval($id_paciente) != 0 ){
		$datos[estado] = "OK";
		$datos[mensaje] = "El paciente ".$formulario->primer_nombre." ".$formulario->segundo_nombre." ".$formulario->primer_apellido." ".$formulario->segundo_apellido." ha sido agregado al sistema.";
		$datos[id_paciente_nuevo] = $id_paciente;
	}else{
		$datos[estado] = "ERROR";
	}
}else if ( strcmp($formulario->accion, 'paciente') == 0 && strcmp($formulario->seccion, 'Datos Personales') == 0 ){

	$SQLDatosPaciente = "SELECT p.*, it.nombre, it.alias, d.nombre dpto, m.nombre municipio, ec.nombre estadocivil, e.nombre escolaridad, g.nombre genero, epss.nombre eps, v.nombre vinculacion, paq.nombre servicio, ae.nombre estadoafiliacion
						 FROM pacientes p 
						 LEFT JOIN identificacion_tipos it ON it.id = p.rd_tipo_identificacion
						 LEFT JOIN departamentos d ON d.id = p.sl_departamento
						 LEFT JOIN municipios m ON m.id = p.sl_municipio
						 LEFT JOIN estados_civiles ec ON ec.id = p.sl_estado_civil
						 LEFT JOIN escolaridad e ON e.id = p.sl_escolaridad
						 LEFT JOIN generos g ON g.id = p.rd_genero
						 LEFT JOIN epss ON epss.id = p.sl_eps
						 LEFT JOIN vinculaciones v ON v.id = p.sl_tipo_vinculacion
						 LEFT JOIN paquetes paq ON paq.id = p.servicios
						 LEFT JOIN afiliacion_estados ae ON ae.id = p.sl_estado_afiliacion
						 WHERE p.id = ".$formulario->usuario->id;
	insertarTablaArray_v2($datos, $SQLDatosPaciente, 'paciente');
	$datos[paciente] = $datos[paciente][0];
}else if ( strcmp($formulario->accion, 'paciente') == 0 && strcmp($formulario->seccion, 'clave') == 0 ){
	echo "clave<pre>";
	print_r($formulario);
	echo "</pre>";
}else if ( strcmp($formulario->accion, 'paciente') == 0 && strcmp($formulario->seccion, 'historial') == 0 ){
	echo "historial<pre>";
	print_r($formulario);
	echo "</pre>";
}else if ( strcmp($formulario->accion, 'resumen_paciente') == 0 ){
	$identificacion = $formulario->identificacion;

	$SQLExistePaciente = "SELECT * FROM pacientes WHERE numero_identificacion = $identificacion";
	insertarTablaArray_v2($existe_paciente, $SQLExistePaciente, 'existe_paciente');

	if ( $existe_paciente[existe_paciente] == null ){
		$datos[estado] = "ERROR";
		$datos[mensaje] = "El paciente no existe en nuestro sistema";
	}else{

		$informacion_paciente = array();
		$informacion_paciente[nombre_completo] = $existe_paciente[existe_paciente][0][primer_nombre]." ".$existe_paciente[existe_paciente][0][segundo_nombre]." ".$existe_paciente[existe_paciente][0][primer_apellido]." ".$existe_paciente[existe_paciente][0][segundo_apellido];
		$informacion_paciente[edad] = $existe_paciente[existe_paciente][0][edad];
		$informacion_paciente[ocupacion] = $existe_paciente[existe_paciente][0][ocupacion];
		$datos[informacion_paciente] = $informacion_paciente;

		$id_paciente = $existe_paciente[existe_paciente][0][id];


		$SQLCitasAtendidas = "	SELECT r2pc.id, r2pc.dt_fecha, e.nombre AS especialidad, CONCAT(u.nombres,' ', u.apellidos) AS profesional, e.id AS id_especialidad
								FROM r2_pacientes_citas r2pc
								LEFT JOIN usuarios u ON u.id = r2pc.sl_usuario
								LEFT JOIN especialidades e ON e.id = u.sl_profesional
								WHERE r2pc.hd_pacientes = $id_paciente AND r2pc.rd_estado = 3 AND u.sl_profesional != 4
								ORDER BY u.sl_profesional DESC, r2pc.dt_fecha DESC";
		insertarTablaArray_v2($citas_atendidas, $SQLCitasAtendidas, 'citas_atendidas');

		//arreglar la estructura para el acordeon
		$especialidades = array();
		foreach ($citas_atendidas[citas_atendidas] as $cita_atendida){
			if ( !isset($especialidades[ $cita_atendida[id_especialidad] ]) ) {
				$especialidades[ $cita_atendida[id_especialidad] ] = array();
				$especialidades[ $cita_atendida[id_especialidad] ][nombre] = $cita_atendida[especialidad];
				$especialidades[ $cita_atendida[id_especialidad] ][atendidas] = array();
			}
			$cita_atendida[fecha] = fechaEspanol($cita_atendida[dt_fecha], 'D d M Y h:i:s a');
			unset($cita_atendida[dt_fecha]);
			array_push($especialidades[ $cita_atendida[id_especialidad] ][atendidas], $cita_atendida);
		}

		if ( $citas_atendidas[citas_atendidas] == null ){
			$datos[estado] = "ERROR";
			$datos[mensaje] = "El paciente no tiene citas cumplidas en nuestro sistema";
		}else{
			$datos[especialidades] = $especialidades;
		}
	}
}else if( strcmp($formulario->accion, 'basica_paciente') == 0 ){
	$identificacion = $formulario->identificacion;

	$SQLExistePaciente = "SELECT * FROM pacientes WHERE numero_identificacion = $identificacion";
	insertarTablaArray_v2($existe_paciente, $SQLExistePaciente, 'existe_paciente');

	if ( $existe_paciente[existe_paciente] == null ){
		$datos[estado] = "ERROR";
		$datos[mensaje] = "El paciente no existe en nuestro sistema";
	}else{
		$datos[estado] = "OK";
		$datos[info] = $existe_paciente[existe_paciente][0];
	}
}

echo json_encode($datos);



/*SELECT p.id, p.primer_nombre, p.primer_apellido, p.rd_tipo_identificacion, it.nombre AS tipo, it.alias
FROM pacientes p
LEFT JOIN identificacion_tipos it ON p.rd_tipo_identificacion = it.id

$SQLDatosPaciente = "SELECT * FROM pacientes  WHERE id = ".$formulario->usuario->id;*/