<?php

include_once('funcionesBD.php');

$datos = array();

if ( isset($_GET['listados']) ){
	$SQLCIE = "SELECT id, CONCAT(codigo,' ',descripcion) as nombre FROM cie ORDER BY descripcion ASC";
	insertarTablaArray_v2($datos, $SQLCIE, 'cies');
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

		$SQLNivelesEscolaridad = "SELECT * FROM escolaridad ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLNivelesEscolaridad, 'niveles_escolaridad');

		$SQLPaquetes = "SELECT * FROM paquetes ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLPaquetes, 'paquetes');

		$SQLAfiliacionEstados = "SELECT * FROM afiliacion_estados ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLAfiliacionEstados, 'afiliacion_estados');

		$datos[fecha][anios] = array();
		for ($i=1900; $i < 2041; $i++) array_push($datos[fecha][anios], $i);

		$datos[fecha][meses] = array(array('id'=>1,'nombre'=>'Enero'),array('id'=>2,'nombre'=>'Febrero'),array('id'=>3,'nombre'=>'Marzo'),array('id'=>4,'nombre'=>'Abril'),array('id'=>5,'nombre'=>'Mayo'),array('id'=>6,'nombre'=>'Junio'),array('id'=>7,'nombre'=>'Julio'),array('id'=>8,'nombre'=>'Agosto'),array('id'=>9,'nombre'=>'Septiembre'),array('id'=>10,'nombre'=>'Octubre'),array('id'=>11,'nombre'=>'Noviembre'),array('id'=>12,'nombre'=>'Diciembre'));

		$datos[fecha][dias] = array();
		for ($i=1; $i < 32; $i++) array_push($datos[fecha][dias], $i);

		/// DAVID TRATÓ DE HACER ESTO Consulta para ver la historia clinica diligenciada en la cita N
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
			$SQLInsertBasicos = "UPDATE pacientes SET sl_estado_civil = '$formulario->sl_estado_civil', ocupacion = '$formulario->ocupacion' WHERE id = $formulario->id";
			if ( ejecutarQuery_v2($SQLInsertBasicos) == true ) {
				$datos[status] = "OK";
				$datos[mensaje] = "Datos almacenados correctamente.";
			}else{
				$datos[status] = "ERROR";
				$datos[mensaje] = "Los datos no se pudieron almacenar correctamente.";
			}

			//existe el registro de la cita de psicologia con el id ....?
			$SQLExisteHistoriaCitaPsicologia = "SELECT id FROM hcpsi_ WHERE id_cita = '$formulario->cita'";
			insertarTablaArray_v2($existeHC, $SQLExisteHistoriaCitaPsicologia, 'existe');
			if( isset($existeHC[existe][0]) ){
				ECHO "SI EXISTE!! ";
				//UPDATE
				$SQLUpdate = "";
				if ( ejecutarQuery_v2($SQLUpdate) == true ) {
					$datos[status] = "OK";
					$datos[mensaje] = "Datos actualizados correctamente.";
				}else{
					$datos[status] = "ERROR";
					$datos[mensaje] = "Los datos no se pudieron actualizar correctamente.";
				}
			}else{
				ECHO "NO EXISTE! :(";
				//INSERT
				$SQLInsert = "";
				if ( ejecutarQuery_v2($SQLInsert) == true ) {
					$datos[status] = "OK";
					$datos[mensaje] = "Datos almacenados correctamente.";
				}else{
					$datos[status] = "ERROR";
					$datos[mensaje] = "Los datos no se pudieron almacenar correctamente.";
				}
			}
			//UPDATE o Insert para Acompañante y motivo


			/*INSERT INTO `pacientes` (`id`, ``, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `rd_tipo_identificacion`, `numero_identificacion`, `sl_departamento`, `sl_municipio`, `da_nacimiento`, `edad`, `email`, `clave`, `sl_cliente`, `rd_genero`, ``, `ocupacion`, `direccion`, `telefono`, `celular`, `sl_eps`, `sl_tipo_vinculacion`, `sl_escolaridad`, `acudiente`, `acudiente_parentesco`, `acudiente_celular`, `servicios`, `sl_estado_afiliacion`) VALUES
(9, 'Guillermo', 'Andrés', 'Malagón', 'García', 1, 80198817, 14, 525, '1984-03-29', 30, 'guillomal373@gmail.com', '1234', 1, 2, 3, 'Ingeniero', 'Calle 8 bis No. 79 c 77', '2923791', '3006595458', 3, 2, 4, 'David', 'Amigo', '3003453434', 2, 1);
*/
		}
	}
}

if ( isset($_GET[debug]) ){
	echo "<pre>";
	print_r($datos);
	echo "</pre>";
} else echo json_encode($datos);
