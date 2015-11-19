<?php

include_once('funcionesBD.php');
include_once('funcionesProactivar.php');

$datos = array();

//traer e interpretar datos POST
$formulario = json_decode(file_get_contents("php://input"));

if ( strcmp($formulario->accion, 'iniciales') == 0 ){
	$SQLEspecialidades = "SELECT id, nombre
						  FROM especialidades
						  WHERE id != 0";
	insertarTablaArray_v2($datos, $SQLEspecialidades, 'especialidades');

	$SQLProfesionales = "SELECT id, CONCAT(nombres,' ',apellidos) AS nombre, sl_profesional
						 FROM usuarios
						 WHERE sl_tipo != 4";
	insertarTablaArray_v2($datos ,$SQLProfesionales, 'profesionales');
}else if ( strcmp($formulario->accion, 'disponibles') == 0 ){
	$id_profesional = $formulario->profesional;
	$fecha = substr($formulario->fecha, 0,10);
	$dias_semana_nombre = array(1=>'lunes', 2=>'martes', 3=>'miercoles', 4=>'jueves', 5=>'viernes', 6=>'sabado', 7=>'domingo');
	$dia_semana = intval(date("N", strtotime($fecha)))-1; // 1.lunes, 2.martes, ..., 7.domingo

	//Las citas que ya tiene agendadas este doctor para este día.
	$SQLCitasDoctorDia = "	SELECT * 
							FROM r2_pacientes_citas
							WHERE sl_usuario = $id_profesional AND dt_fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59'";
	insertarTablaArray_v2($citas_doctor_dia, $SQLCitasDoctorDia, 'citas_doctor_dia');

	/*echo "{{$SQLCitasDoctorDia}}<pre>";
	print_r($citas_doctor_dia[citas_doctor_dia]);
	echo "</pre>";*/

	//Contrato para este doctor en la fecha
	$SQLContrato = "SELECT * 
					FROM r2_usuarios_labores
					WHERE hd_usuarios = $id_profesional AND  '$fecha' >= da_fecha_inicio AND '$fecha' <= da_fecha_final";
	insertarTablaArray_v2($contrato, $SQLContrato, 'contrato');
	$contrato = $contrato[contrato][0];

	//trabaja este dia de la semana
	if ( intval($contrato[ 'bl_'.$dias_semana_nombre[$dia_semana] ]) == 1 ){

		$hora_cita = date('H:i', strtotime($contrato[hora_inicio]));
		$hora_final = date('H:i', strtotime($contrato[hora_final]));
		$datos[citas] = array();

		do {
			//recorrer vector de citas_doctor_dia Y preguntar la hora, si es la misma entonces no la puedo usar
			$cita_libre = true;
			foreach ($citas_doctor_dia[citas_doctor_dia] as $cita_agendada) {
				if ( $hora_cita == date("H:i" , strtotime( $cita_agendada[dt_fecha]) ) ) {
					$cita_libre = false;
				}
			}

			if ( $cita_libre ){
				array_push($datos[citas], $hora_cita);
			}

			$hora_cita = date("H:i", strtotime("+$contrato[tiempo_cita] minutes", strtotime($hora_cita)));
			//echo "<br />".$hora_cita;
		} while ($hora_cita < $hora_final );

	}

	$datos[mensaje] = "Doctor ".$id_profesional." el día de ".$fecha." (".$dia_semana.")";
}else if ( strcmp($formulario->accion, 'agendar') == 0 ){
	$id_paciente = $formulario->usuario->id;
	$fecha = substr($formulario->fecha, 0, 10);
	$hora = substr($formulario->cita, 5, 6);
	$id_profesional = $formulario->profesional;
	$SQLInsertarCita = "INSERT INTO r2_pacientes_citas (id, hd_pacientes, dt_fecha, sl_usuario, rd_estado, sl_eps) VALUES (NULL, '$id_paciente', '".$fecha.$hora."', '$id_profesional', '1', NULL)"; 
	$id_cita_agendada = insertarFila($SQLInsertarCita);
	if ( intval($id_cita_agendada) != 0 ){
		$datos[estado] = "OK";
		$datos[mensaje] = "La cita se ha agendado satisfactoriamente!";
	}else{
		$datos[estado] = "ERROR";
		$datos[mensaje] = "----";
	}
}else if ( strcmp($formulario->accion, 'agendadas') == 0 ){
	$id_paciente = $formulario->id_paciente;
	$SQLAgendadasPaciente = "SELECT * FROM r2_pacientes_citas WHERE hd_pacientes = $id_paciente AND dt_fecha > '".date('Y-m-d')." 00:00:00' ORDER BY dt_fecha ASC";
	insertarTablaArray_v2($datos, $SQLAgendadasPaciente, 'agendadas');

	$datos[proxima_cita] = array();
	$datos[citas] = array();
	$datos[proxima_cita] = array(id=>'1', fecha=>array(dia=>'07', nombre_dia=>'Miercoles', mes=>'Octubre', anio=>'2015'), hora=>'10:00',ampm=>'am', profesional=>'Liset Sánchez', especialidad=>'Nutrición');
	array_push($datos[citas], array(id=>'1', fecha=>array(dia=>'10', nombre_dia=>'Sábado', mes=>'Octubre', anio=>'2015'), hora=>'8:00',ampm=>'am', profesional=>'Leonardo Bautista', especialidad=>'Medicina General'));
	array_push($datos[citas], array(id=>'1', fecha=>array(dia=>'10', nombre_dia=>'Lunes', mes=>'Noviembre', anio=>'2015'), hora=>'8:00',ampm=>'am', profesional=>'Leonardo Bautista', especialidad=>'Medicina General'));
	array_push($datos[citas], array(id=>'1', fecha=>array(dia=>'17', nombre_dia=>'Jueves', mes=>'Diciembre', anio=>'2015'), hora=>'9:00',ampm=>'am', profesional=>'Liset Sánchez', especialidad=>'Nutrición'));
}

if ( isset($_GET[debug]) ){
	/*echo "<pre>";
	print_r($datos);
	echo "</pre>";*/
}else {
	echo json_encode($datos);
}

?>