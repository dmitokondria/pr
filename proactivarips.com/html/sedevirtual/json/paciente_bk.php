<?php
header('Content-Type: application/json');

include_once('funcionesBD.php');

//traer e interpretar datos POST
$mensaje = json_decode(file_get_contents("php://input"));

$datos = array();

if ( !isset($mensaje->accion) ){//información transversal

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
}else if ( strcmp($mensaje->accion, 'crear') == 0 ){

		$SQLInsertPaciente = "INSERT INTO pacientes (id, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, rd_tipo_identificacion, numero_identificacion, sl_departamento, sl_municipio, da_nacimiento, edad, email, clave, sl_cliente, rd_genero, sl_estado_civil, ocupacion, direccion, telefono, celular, sl_eps, sl_tipo_vinculacion, sl_escolaridad, acudiente, acudiente_parentesco, acudiente_celular, servicios, sl_estado_afiliacion) VALUES
													(NULL, '".$mensaje->primer_nombre."', '".$mensaje->segundo_nombre."', '".$mensaje->primer_apellido."', '".$mensaje->segundo_apellido."', ".$mensaje->tipo_identificacion.", ".$mensaje->identificacion.", ".$mensaje->departamento.", ".$mensaje->ciudad.", '".$mensaje->anio."-".$mensaje->mes."-".$mensaje->dia."', 100, '".$mensaje->email."', '1234', 1, ".$mensaje->genero.", ".$mensaje->estadocivil.", '".$mensaje->ocupacion."', '".$mensaje->direccion."', '".$mensaje->telefono."', '".$mensaje->celular."', ".$mensaje->entidad.", ".$mensaje->tipovinculacion.", ".$mensaje->escolaridad.", '".$mensaje->acudiente."', '".$mensaje->parentesco."', '".$mensaje->celacudiente."', ".$mensaje->paquete.", ".$mensaje->afiliacion.")";
		$id_paciente = insertarFila($SQLInsertPaciente);

		if ( intval($id_paciente) != 0 ){
			$datos[estado] = "OK";
			$datos[mensaje] = "Paciente agregado con id ".$id_paciente;
		}else{
			$datos[estado] = "ERROR";
		}
}else if ( strcmp($mensaje->accion, 'paciente') == 0 && strcmp($mensaje->seccion, 'datos') == 0 ){

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
						 WHERE p.id = ".$mensaje->usuario->id;
	insertarTablaArray_v2($datos, $SQLDatosPaciente, 'paciente');
	$datos[paciente] = $datos[paciente][0];
}else if ( strcmp($mensaje->accion, 'paciente') == 0 && strcmp($mensaje->seccion, 'clave') == 0 ){
	echo "clave<pre>";
	print_r($mensaje);
	echo "</pre>";
}else if ( strcmp($mensaje->accion, 'paciente') == 0 && strcmp($mensaje->seccion, 'historial') == 0 ){
	echo "historial<pre>";
	print_r($mensaje);
	echo "</pre>";
}else if ( strcmp($mensaje->accion, 'resumen_paciente') == 0 ){
	
}

echo json_encode($datos);



/*SELECT p.id, p.primer_nombre, p.primer_apellido, p.rd_tipo_identificacion, it.nombre AS tipo, it.alias
FROM pacientes p
LEFT JOIN identificacion_tipos it ON p.rd_tipo_identificacion = it.id

$SQLDatosPaciente = "SELECT * FROM pacientes  WHERE id = ".$mensaje->usuario->id;*/