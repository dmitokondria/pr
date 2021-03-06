<?php

include_once('funcionesBD.php');

$datos = array();

if ( isset($_GET['listados']) ){
	/*$SQLCIE = "SELECT id, CONCAT(codigo,' ',descripcion) as nombre FROM cie ORDER BY descripcion ASC";
	insertarTablaArray_v2($datos, $SQLCIE, 'cies');*/
	if ( strcmp($_GET['listados'], '1') == 0 ){
		if ( isset($_GET['palabra']) ){
			$SQLCIE = "SELECT id, CONCAT(codigo,' ',descripcion) as nombre, codigo, descripcion FROM cie WHERE CONCAT(codigo,' ',descripcion) LIKE '%$_GET[palabra]%' ORDER BY descripcion ASC";
			insertarTablaArray_v2($datos, $SQLCIE, 'cies');
		}else{
			$SQLCIE = "SELECT id, CONCAT(codigo,' ',descripcion) as nombre, codigo, descripcion FROM cie ORDER BY descripcion ASC";
			insertarTablaArray_v2($datos, $SQLCIE, 'cies');
		}
	}else if ( strcmp($_GET['listados'], '2') == 0 ){
		if ( isset($_GET['palabra']) ){
			$SQLCups = "SELECT id, CONCAT(cups,' ',procedimiento) as nombre, cups, procedimiento FROM cups WHERE CONCAT(cups,' ',procedimiento) LIKE '%$_GET[palabra]%' ORDER BY procedimiento ASC";
			insertarTablaArray_v2($datos, $SQLCups, 'cups');
		}else{
			$SQLCups = "SELECT id, CONCAT(cups,' ',procedimiento) as nombre, cups, procedimiento FROM cups ORDER BY procedimiento ASC";
			insertarTablaArray_v2($datos, $SQLCups, 'cups');
		}
	}
}else{
	//traer e interpretar datos POST
	$paquete = json_decode(file_get_contents("php://input"));
	//informacio básica del paciente o de la cita para VER
	if ( isset($paquete->cita) ){
		$id_cita = $paquete->cita;

		$SQLInfoPaciente = "SELECT p.*
							FROM r2_pacientes_citas r2pc
							LEFT JOIN pacientes p ON p.id = r2pc.hd_pacientes
							WHERE r2pc.id = ".$id_cita;
		insertarTablaArray_v2($infoPacientes, $SQLInfoPaciente, 'pacientes');
		$datos[paciente] = $infoPacientes[pacientes][0];

		//descomponer fecha de nacimiento
		$fecha_nacimiento = $datos[paciente][da_nacimiento];
		$explode_fecha = explode("-", $fecha_nacimiento);
		$datos[paciente][da_nacimiento] = array();
		$datos[paciente][da_nacimiento][dia] = $explode_fecha[2];
		$datos[paciente][da_nacimiento][mes] = intval($explode_fecha[1]);
		$datos[paciente][da_nacimiento][anio] = $explode_fecha[0];

		///información de la base de datos para listados en formulario
		$SQLTiposIdentificacion = "SELECT * FROM identificacion_tipos ORDER BY id";
		insertarTablaArray_v2($datos, $SQLTiposIdentificacion, 'tipos_identificacion');

		$SQLGeneros = "SELECT * FROM generos ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLGeneros, 'generos');

		$SQLDepartamentos = "SELECT * FROM departamentos ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLDepartamentos, 'departamentos');

		$SQLCiudades = "SELECT * FROM municipios ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLCiudades, 'ciudades');

		$SQLEstadosCiviles = "SELECT * FROM estados_civiles ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLEstadosCiviles, 'estados_civiles');

		$SQLEpss = "SELECT * FROM epss ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLEpss, 'epss');

		$SQLTiposVinculacion = "SELECT * FROM vinculaciones ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLTiposVinculacion, 'tipos_vinculacion');

		$SQLNivelesEscolaridad = "SELECT * FROM escolaridad ORDER BY id";
		insertarTablaArray_v2($datos, $SQLNivelesEscolaridad, 'niveles_escolaridad');

		$SQLPaquetes = "SELECT * FROM paquetes ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLPaquetes, 'paquetes');

		$SQLAfiliacionEstados = "SELECT * FROM afiliacion_estados ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLAfiliacionEstados, 'afiliacion_estados');

		//tipos diagnostico
		$SQLTiposDiagnostico = "SELECT * FROM diagnostico_tipos ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLTiposDiagnostico, 'tipos_diagnostico');

		//Contingencia Diagnósticos == Causa Externa en HC MEDICINA
		$SQLCausasExt = "SELECT * FROM hc_causaext ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLCausasExt, 'causas_ext');

		$datos[fecha][anios] = array();
		for ($i=1900; $i < 2041; $i++) array_push($datos[fecha][anios], $i);

		$datos[fecha][meses] = array(array('id'=>1,'nombre'=>'Enero'),array('id'=>2,'nombre'=>'Febrero'),array('id'=>3,'nombre'=>'Marzo'),array('id'=>4,'nombre'=>'Abril'),array('id'=>5,'nombre'=>'Mayo'),array('id'=>6,'nombre'=>'Junio'),array('id'=>7,'nombre'=>'Julio'),array('id'=>8,'nombre'=>'Agosto'),array('id'=>9,'nombre'=>'Septiembre'),array('id'=>10,'nombre'=>'Octubre'),array('id'=>11,'nombre'=>'Noviembre'),array('id'=>12,'nombre'=>'Diciembre'));

		$datos[fecha][dias] = array();
		for ($i=1; $i < 32; $i++) array_push($datos[fecha][dias], $i);

		/// DAVID TRATÓ DE HACER ESTO Consulta para ver la historia clinica diligenciada en la cita N | Revisar la accion 'VER' pues probablemente se dañó al realizar los insert.
		if ( strcmp($paquete->accion, 'ver') == 0 ){
			$SQLInfoCita = "SELECT * FROM hcpsi_ WHERE id_cita = $id_cita";
			insertarTablaArray_v2($info_cita, $SQLInfoCita, 'info_cita');
			$info_cita = $info_cita[info_cita][0];

			$datos[basicos] = array();
			$datos[basicos][acompanante][nombre] = $info_cita[anombre];
			$datos[basicos][acompanante][celular] = $info_cita[acelular];
			$datos[basicos][acompanante][parentesco] = $info_cita[aparentesco];
			$datos[basicos][motivo] = $info_cita[motivo];
			$datos[basicos][observaciones] = $info_cita[observaciones];

			$datos[emocionalidad] = array();
			$datos[emocionalidad][ansiedad] = $info_cita[rd_ansiedad];
			$datos[emocionalidad][tristeza] = $info_cita[rd_tristeza];
			$datos[emocionalidad][irritabilidad] = $info_cita[rd_irritable];
			$datos[emocionalidad][dolor] = $info_cita[rd_dolor];
			$datos[emocionalidad][ajuste][familiar] = $info_cita[familiar];
			$datos[emocionalidad][ajuste][social] = $info_cita[social];
			$datos[emocionalidad][ajuste][laboral] = $info_cita[laboral];
			$datos[emocionalidad][ajuste][academica] = $info_cita[academica];
			$datos[emocionalidad][ajuste][afectiva] = $info_cita[afectiva];
			$datos[emocionalidad][ajuste][recreacion] = $info_cita[recreacion];
			$datos[emocionalidad][analisis] = $info_cita[analisis_prof];

			$datos[recomendaciones] = array();
			$datos[recomendaciones] = $info_cita[recomendaciones];
		}
	}else{
		$accion = $paquete->accion;
		$formulario = $paquete->formulario;

		if ( strcmp($accion , 'guardar_basicos') == 0 ){
			$SQLInsertBasicos = "UPDATE pacientes SET sl_estado_civil = '$formulario->sl_estado_civil', ocupacion = '$formulario->ocupacion', sl_escolaridad = '$formulario->sl_escolaridad', direccion = '$formulario->direccion', telefono = '$formulario->telefono', celular = '$formulario->celular', acudiente = '$formulario->acudiente', acudiente_parentesco = '$formulario->acudiente_parentesco', acudiente_celular = '$formulario->acudiente_celular' WHERE id = $formulario->id";
			if ( ejecutarQuery_v2($SQLInsertBasicos) == true ) {
				$datos[estado] = "OK";
				$datos[mensaje] = "Datos almacenados correctamente.";
			}else{
				$datos[estado] = "ERROR";
				$datos[mensaje] = "Los datos no se pudieron almacenar correctamente.";
			}

			$acompanante = $formulario->acompanante; //Esta línea la hice para descomponer la llave 'acompanante': David

			//existe el registro de la cita de psicologia con el id ....?
			$SQLExisteHistoriaCitaPsicologia = "SELECT id FROM hcpsi_ WHERE id_cita = '$formulario->cita'";
			insertarTablaArray_v2($existeHC, $SQLExisteHistoriaCitaPsicologia, 'existe');
			if( isset($existeHC[existe][0]) ){
				//UPDATE
				$SQLUpdate = "UPDATE hcpsi_ SET anombre = '$acompanante->nombre', acelular = '$acompanante->celular', aparentesco = '$acompanante->parentesco', motivo = '$formulario->motivo', observaciones = '$formulario->observaciones' WHERE id_cita = '$formulario->cita'";
				if ( ejecutarQuery_v2($SQLUpdate) == true ) {
					$datos[estado] = "OK";
					$datos[mensaje] = "Datos actualizados correctamente.";
				}else{
					$datos[estado] = "ERROR";
					$datos[mensaje] = "Los datos no se pudieron actualizar correctamente.";
				}
			}else{
				//INSERT
				$SQLInsert = "INSERT INTO hcpsi_ (id, id_cita, anombre, acelular, aparentesco, motivo, observaciones) 
							  VALUES ( NULL, ".$formulario->cita.", '".$acompanante->anombre."', '".$acompanante->acelular."', '".$acompanante->aparentesco."', '".$formulario->motivo."', '".$formulario->observaciones."')";
				if ( ejecutarQuery_v2($SQLInsert) == true ) {
					$datos[estado] = "OK";
					$datos[mensaje] = "Datos almacenados correctamente.";
				}else{
					$datos[estado] = "ERROR";
					$datos[mensaje] = "Los datos no se pudieron almacenar correctamente.";
				}
			}
		} else if ( strcmp( $accion, 'guardar_emocion') == 0 ){
			
			$ajuste = $formulario->ajuste;
			
			$SQLUpdateEmocion = "UPDATE hcpsi_ SET rd_ansiedad = '$formulario->rd_ansiedad', rd_tristeza = '$formulario->rd_tristeza', rd_irritable = '$formulario->rd_irritable', rd_dolor = '$formulario->rd_dolor', familiar = '$ajuste->familiar', social = '$ajuste->social', laboral = '$ajuste->laboral', academica = '$ajuste->academica', afectiva = '$ajuste->afectiva', recreacion = '$ajuste->recreacion', analisis_prof = '$formulario->analisis_prof' WHERE id_cita = '$formulario->cita'";
		
			if ( ejecutarQuery_v2($SQLUpdateEmocion) == true ) {
				//ECHO ($formulario->cita);
					$datos[estado] = "OK";
					$datos[mensaje] = "Datos almacenados correctamente.";
				}else{
					$datos[estado] = "ERROR";
					$datos[mensaje] = "Los datos no se pudieron almacenar correctamente.";
				}
		} else if ( strcmp( $accion, 'guardar_recomendacion') == 0 ){

			$SQLUpdateRecomen = "UPDATE hcpsi_ SET recomendaciones = '$formulario->recomendaciones' WHERE id_cita = '$formulario->cita'";

			if (ejecutarQuery_v2($SQLUpdateRecomen) == true ){
				$datos[estado] = "OK";
				$datos[mensaje] = "Datos almacenados correctamente.";
			}else{
				$datos[estado] = "ERROR";
				$datos[mensaje] = "Los datos no se pudieron almacenar correctamente.";
			}
		}
	}
}

if ( isset($_GET[debug]) ){
	echo "<pre>";
	print_r($datos);
	echo "</pre>";
} else {
	echo json_encode($datos);
}

?>
