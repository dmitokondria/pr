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

		$SQLNivelesEscolaridad = "SELECT * FROM escolaridad ORDER BY id ASC";
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

		if ( strcmp ($paquete->accion, 'ver') == 0 ){
		}

	}else{
		$accion = $paquete->accion;
		$formulario = $paquete->formulario;

		if ( strcmp($accion, 'guardar_basicos_nutri') == 0 ){

			$SQLInsertBasicos = "UPDATE pacientes SET sl_estado_civil = '$formulario->sl_estado_civil', ocupacion = '$formulario->ocupacion', sl_escolaridad = '$formulario->sl_escolaridad', direccion = '$formulario->direccion', telefono = '$formulario->telefono', celular = '$formulario->celular', acudiente = '$formulario->acudiente', acudiente_parentesco = '$formulario->acudiente_parentesco', acudiente_celular = '$formulario->acudiente_celular' WHERE id = $formulario->id";
			if ( ejecutarQuery_v2($SQLInsertBasicos) == true ){
				$datos[estado] = "OK";
				$datos[mensaje] = "Datos almacenados correctamente.";
			}else{
				$datos[estado] = "ERROR";
				$datos[mensaje] = "Los datos no fueron almacenados.";
			}
			
			$acompanante = $formulario->acompanante;
			$ant = $formulario->ant;

			$SQLExisteHistoriaCitaNutri = "SELECT id FROM hcnutri_ WHERE id_cita ='$formulario->cita'";
			insertarTablaArray_v2($existeHC, $SQLExisteHistoriaCitaNutri, 'existe');

			if (isset($existeHC[existe][0]) ) {
				$SQLUpdate = "UPDATE hcnutri_ SET anombre = '$acompanante->anombre', acelular = '$acompanante->acelular', aparentesco = '$acompanante->aparentesco', ch_bajopeso = '$ant->ch_bajopeso', ch_alteraciones = '$ant->ch_alteraciones', ch_sobrepeso = '$ant->ch_sobrepeso', ch_hipertension = '$ant->ch_hipertension', ch_diabetes = '$ant->ch_diabetes', ch_hipotiroidismo = '$ant->ch_hipotiroidismo', ch_insufrenal = '$ant->ch_insufrenal', ch_otros = '$ant->ch_otros', otro_cual = '$ant->otro_cual' WHERE id_cita = '$formulario->cita'";// rd_bajopeso rd_alteraciones rd_sobrepeso rd_hipertension rd_diabetes rd_hipotiroidismo rd_insufrenal rd_otros otro_cual
				if ( ejecutarQuery_v2($SQLUpdate) == true ){
					$datos[estado] = "OK";
					$datos[mensaje] = "Datos almacenados (actualizados) correctamente.";
				}else{
					$datos[estado] = "ERROR";
					$datos[mensaje] = "Los datos no fueron almacenados.";
				}
			}else{
				$SQLInsert = "INSERT INTO hcnutri_ (id, id_cita, anombre, acelular, aparentesco, ch_bajopeso, ch_alteraciones, ch_sobrepeso, ch_hipertension, ch_diabetes, ch_hipotiroidismo, ch_insufrenal, ch_otros, otro_cual) 
							VALUES ( NULL, ".$formulario->cita.", '".$acompanante->anombre."', '".$acompanante->acelular."', '".$acompanante->aparentesco."', '".$ant->ch_bajopeso."', '".$ant->ch_alteraciones."', '".$ant->ch_sobrepeso."', '".$ant->ch_hipertension."', '".$ant->ch_diabetes."', '".$ant->ch_hipotiroidismo."', '".$ant->ch_insufrenal."', '".$ant->ch_otros."', '".$ant->otro_cual."')";

				if ( ejecutarQuery_v2($SQLInsert) == true ){
					$datos[estado] = "OK";
					$datos[mensaje] = "Datos almacenados (insertados) correctamente.";
				}else{
					$datos[estado] = "ERROR";
					$datos[mensaje] = "Los datos no fueron almacenados.";
				}
			}
		} else if (strcmp($accion, 'guardar_antropo') == 0 ) {
		
			$SQLUpdateAntropo = "UPDATE hcnutri_ SET peso = '$formulario->peso', talla = '$formulario->talla', per_toracico = '$formulario->per_toracico', per_abdomen = '$formulario->per_abdomen', per_cadera = '$formulario->per_cadera', rel_cintura = '$formulario->rel_cintura', creatinina = '$formulario->creatinina', tfg = '$formulario->tfg' WHERE id_cita = '$formulario->cita'";
			insertarTablaArray_v2($updateantropo, $SQLUpdateAntropo, 'update_antropo');

			if (ejecutarQuery_v2($SQLUpdateAntropo) == true ) {
				$datos[estado] = "OK";
				$datos[mensaje] = "Datos antropométricos almacenados correctamente.";
			}else{
				$datos[estado] = "ERROR";
				$datos[mensaje] = "Los datos antropométricos no fueron almacenados.";
			}
		} else if (strcmp($accion, 'guardar_nutricion') == 0 ) {
			
			$semanal = $formulario->semanal;
			$diaria = $formulario->diaria;
			$habitos = $formulario->habitos;

			$SQLUpdateNutricion = "UPDATE hcnutri_ SET cereales = '$semanal->cereales', verduras = '$semanal->verduras', frutas = '$semanal->frutas', carnes = '$semanal->carnes', lacteos = '$semanal->lacteos', azucares = '$semanal->azucares', grasas = '$semanal->grasas', desayuno = '$diaria->desayuno', des_hora = '$diaria->des_hora', merienda = '$diaria->merienda', mer_hora = '$diaria->mer_hora', almuerzo = '$diaria->almuerzo', alm_hora = '$diaria->alm_hora', onces = '$diaria->onces', onc_hora = '$diaria->onc_hora', cena = '$diaria->cena', cen_hora = '$diaria->cen_hora', falto_dinero = '$habitos->falto_dinero', menos_comida = '$habitos->menos_comida', fuera_desa = '$habitos->fuera_desa', fuera_almu = '$habitos->fuera_almu', fuera_cena = '$habitos->fuera_cena', lugar_consumo = '$habitos->lugar_consumo', rd_frecuencia = '$habitos->rd_frecuencia', ch_paquete = '$habitos->ch_paquete', ch_panaderia = '$habitos->ch_panaderia', ch_rapidas = '$habitos->ch_rapidas', ch_gaseosas = '$habitos->ch_gaseosas', ch_fritos = '$habitos->ch_fritos', ch_ffc_otros = '$habitos->ch_ffc_otros', ffc_cuales = '$habitos->ffc_cuales' WHERE id_cita = '$formulario->cita'";
			insertarTablaArray_v2($updatenutricion, $SQLUpdateNutricion, 'update_nutricion');

			if (ejecutarQuery_v2($SQLUpdateNutricion) == true ) {
				$datos[estado] = "OK";
				$datos[mensaje] = "Datos antropométricos almacenados correctamente.";
			}else{
				$datos[estado] = "ERROR";
				$datos[mensaje] = "Los datos antropométricos no fueron almacenados.";
			}
		} else if (strcmp($accion, 'guardar_lactancia') == 0 ) {

			//idRegistro////
			$SQLIdCita = "SELECT id FROM hcnutri_ WHERE id_cita = $formulario->cita";
			insertarTablaArray_v2($cita, $SQLIdCita, 'info');
			$id_registro = $cita[info][0][id];

			$ejecucion = actualizarRegistro('hcnutri_', $id_registro, $formulario->lactancia);
			if ( $ejecucion[status] == "OK" ){
				$datos[status] = "OK";
				$datos[mensaje] = "Datos de lactancia almacenados correctamente.";
			}else{
				$datos[status] = "ERROR";
				echo "<pre>";
				print_r($ejecucion);
				echo "</pre>";
			}
		} else if (strcmp($accion, 'guardar_hallazgos') == 0 ) {

			//idRegistro////
			$SQLIdCita = "SELECT id FROM hcnutri_ WHERE id_cita = $formulario->cita";
			insertarTablaArray_v2($cita, $SQLIdCita, 'info');
			$id_registro = $cita[info][0][id];

			$ejecucion = actualizarRegistro('hcnutri_', $id_registro, $formulario->hallazgos);
			if ( $ejecucion[status] == "OK" ){
				$datos[status] = "OK";
				$datos[mensaje] = "Datos de hallazgos almacenados correctamente.";
			}else{
				$datos[status] = "ERROR";
				echo "<pre>";
				print_r($ejecucion);
				echo "</pre>";
			}

		}
	}		
}

if ( isset($_GET[debug]) ){
	echo "<pre>";
	print_r($datos);
	echo "</pre>";
} else echo json_encode($datos);
