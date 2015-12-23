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
		insertarTablaArray_v2($pacientes, $SQLInfoPaciente, 'pacientes');
		$datos[paciente] = $pacientes[pacientes][0];

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


		////// motivo
		$SQLFinalidades = "SELECT * FROM hc_finalidad ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLFinalidades, 'finalidades');

		$SQLCausasExt = "SELECT * FROM hc_causaext ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLCausasExt, 'causas_ext');

		$SQLEventos = "SELECT * FROM hc_evento ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLEventos, 'eventos');

		////// examen fisico
		$SQLEstGenerales = "SELECT * FROM hc_estadogral ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLEstGenerales, 'est_generales');

		$SQLEstadosHidratacion = "SELECT * FROM hc_hidratacion ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLEstadosHidratacion, 'estados_hidratacion');

		$SQLEstadosResp = "SELECT * FROM hc_resp ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLEstadosResp, 'estados_resp');

		$SQLGlasgows = "SELECT * FROM hc_glasgow ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLGlasgows, 'glasgows');

		$SQLEstadosConciencia = "SELECT * FROM hc_conciencia ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLEstadosConciencia, 'estados_conciencia');
	}
}

if ( isset($_GET[debug]) ){
	echo "<pre>";
	print_r($datos);
	echo "</pre>";
} else echo json_encode($datos);
