<?php

include_once('funcionesBD.php');
include_once('funcionesProactivar.php');

$semana = intval($_GET[pagina]);
$datos = array();

$cookie_usuario = stripslashes($_COOKIE['usuario']);
$usuario = json_decode($cookie_usuario, true);

//if ( intval($pagina) == 0 ){

	//semana
	if ( !isset($_GET[semana]) ) {
		$numSemana = intval(date("W")); 
		$numAnio = intval(date("Y"));
	}else{
		$numSemana = intval($_GET[semana]); 
		$numAnio = intval($_GET[anio]);
	}

	//calculando anterior y siguiente
	$diasSemana = daysInWeekYear($numSemana, $numAnio);
	unset($diasSemana[6]);
	$semana = array();
	foreach ($diasSemana as $key => $value) {
		$diaTemp = array();
		$diaTemp[titulo] = fechaEspanol($diasSemana[$key], 'D d M Y');
		array_push($semana, $diaTemp);
	}

	$datos[dias_semana] = $semana;

	$SQLCitasHoy = "SELECT r2pc.id, r2pc.dt_fecha, u.id AS doctor_id, e.nombre AS especialidad, CONCAT(u.nombres,' ',u.apellidos) AS doctor, p.numero_identificacion AS identificacion, CONCAT(p.primer_nombre, ' ', p.primer_apellido) AS paciente, p.edad, ce.id AS estado_id, ce.nombre AS estado
					FROM r2_pacientes_citas r2pc
					LEFT JOIN citas_estado ce ON ce.id = r2pc.rd_estado
					LEFT JOIN pacientes p ON p.id = r2pc.hd_pacientes 
					LEFT JOIN usuarios u ON u.id = r2pc.sl_usuario
					LEFT JOIN especialidades e ON e.id = u.sl_profesional
					WHERE r2pc.dt_fecha BETWEEN '".$diasSemana[0]." 00:00:00' AND '".$diasSemana[5]." 23:59:59'";
	$SQLCitasHoy .=" ORDER BY r2pc.dt_fecha ASC, u.id ASC";
	insertarTablaArray_v2($citas_hoy, $SQLCitasHoy, 'citas_hoy'); //echo "{{".$SQLCitasHoy."}}";
	$datos[fechas] = "'".$diasSemana[0]." 00:00:00' AND '".$diasSemana[5]." 23:59:59'";

/*	echo "citas_hoy[citas_hoy]<pre>";
	print_r($citas_hoy[citas_hoy]);
	echo "</pre>";*/

	foreach ($citas_hoy[citas_hoy] as $key => $value) {

		$pos_dia = array_search(substr($citas_hoy[citas_hoy][$key][dt_fecha],0,10), $diasSemana);

		/*echo substr($citas_hoy[citas_hoy][$key][dt_fecha],0,10)." en ---> ";
		echo " diasSemana<pre>";
		print_r($diasSemana);
		echo "</pre>";*/

		if ( !is_array($datos[dias_semana][$pos_dia][profesionales]) ){
			$datos[dias_semana][$pos_dia][profesionales] = array();
		}

		$datos[dias_semana][$pos_dia][profesionales][ $citas_hoy[citas_hoy][$key][doctor_id] ][nombre] = $citas_hoy[citas_hoy][$key][doctor];
		$datos[dias_semana][$pos_dia][profesionales][ $citas_hoy[citas_hoy][$key][doctor_id] ][especialidad] = $citas_hoy[citas_hoy][$key][especialidad];
		$datos[dias_semana][$pos_dia][profesionales][ $citas_hoy[citas_hoy][$key][doctor_id] ][id] = $citas_hoy[citas_hoy][$key][doctor_id];

		if ( !is_array( $datos[dias_semana][$pos_dia][profesionales][ $citas_hoy[citas_hoy][$key][doctor_id] ][citas] ) ){
			$datos[dias_semana][$pos_dia][profesionales][ $citas_hoy[citas_hoy][$key][doctor_id] ][citas] = array();
		}

		$citaTemp = array();
		$citaTemp[id] = $citas_hoy[citas_hoy][$key][id];
		$citaTemp[dt_fecha] = substr($citas_hoy[citas_hoy][$key][dt_fecha],11,5);
		$citaTemp[identificacion] = $citas_hoy[citas_hoy][$key][identificacion];
		$citaTemp[paciente] = $citas_hoy[citas_hoy][$key][paciente];
		$citaTemp[edad] = $citas_hoy[citas_hoy][$key][edad];
		$citaTemp[estado_id] = $citas_hoy[citas_hoy][$key][estado_id];
		$citaTemp[estado] = $citas_hoy[citas_hoy][$key][estado];
		array_push($datos[dias_semana][$pos_dia][profesionales][ $citas_hoy[citas_hoy][$key][doctor_id] ][citas], $citaTemp);
	}

	//diccionario a Array
	foreach ($datos[dias_semana] as $key => $value) {
		$datos[dias_semana][$key][pro] = array();
		if ( count( $datos[dias_semana][$key][profesionales] ) > 0 ){
			foreach ($datos[dias_semana][$key][profesionales] as $ke => $va) {
				array_push($datos[dias_semana][$key][pro],$datos[dias_semana][$key][profesionales][$ke]);
			}
		}
		unset($datos[dias_semana][$key][profesionales]);
	}

	//listado profesionales
	$SQLProfesionales ="SELECT u.id, concat(u.nombres,' ',u.apellidos) as nombres
						FROM usuarios u
						LEFT JOIN usuario_tipos ut ON ut.id = u.sl_tipo
						WHERE ut.id = 3";
	insertarTablaArray_v2($datos, $SQLProfesionales, 'profesionales');

//}

if ( isset($_GET[debug]) ){
	/*echo "<pre>";
	print_r($datos);
	echo "</pre>";*/
}else {
	echo json_encode($datos);
}

?>