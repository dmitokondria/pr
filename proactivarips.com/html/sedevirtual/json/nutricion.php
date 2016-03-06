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

		$SQLInfoPaciente = "SELECT p.*, YEAR(CURDATE())-YEAR(p.da_nacimiento) + IF(DATE_FORMAT(CURDATE(),'%m-%d') > DATE_FORMAT(p.da_nacimiento,'%m-%d'), 0, -1) AS edad_actual 
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

		//tipos diagnostico
		$SQLTiposDiagnostico = "SELECT * FROM diagnostico_tipos ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLTiposDiagnostico, 'tipos_diagnostico');

		//Contingencia Diagnósticos == Causa Externa en HC MEDICINA
		$SQLCausasExt = "SELECT * FROM hc_causaext ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLCausasExt, 'causas_ext');

		//IMC
		$datos[imc_clasificaciones] = array( array('id'=>0, 'nombre'=>'Sin Datos'), array('id'=>1, 'nombre'=>'Delgadez Severa'), array('id'=>2, 'nombre'=>'Delgadez Moderada'), array('id'=>3, 'nombre'=>'Delgadez Leve'), array('id'=>4, 'nombre'=>'Bajo Peso'), array('id'=>5, 'nombre'=>'Normal'), array('id'=>6, 'nombre'=>'Sobrepeso'), array('id'=>7, 'nombre'=>'Obesidad Grado I'), array('id'=>8, 'nombre'=>'Obesidad Grado II'), array('id'=>9, 'nombre'=>'Obesidad Grado III') );

		$datos[fecha][anios] = array();
		for ($i=1900; $i < 2041; $i++) array_push($datos[fecha][anios], $i);

		$datos[fecha][meses] = array(array('id'=>1,'nombre'=>'Enero'),array('id'=>2,'nombre'=>'Febrero'),array('id'=>3,'nombre'=>'Marzo'),array('id'=>4,'nombre'=>'Abril'),array('id'=>5,'nombre'=>'Mayo'),array('id'=>6,'nombre'=>'Junio'),array('id'=>7,'nombre'=>'Julio'),array('id'=>8,'nombre'=>'Agosto'),array('id'=>9,'nombre'=>'Septiembre'),array('id'=>10,'nombre'=>'Octubre'),array('id'=>11,'nombre'=>'Noviembre'),array('id'=>12,'nombre'=>'Diciembre'));

		$datos[fecha][dias] = array();
		for ($i=1; $i < 32; $i++) array_push($datos[fecha][dias], $i);

		if ( strcmp ($paquete->accion, 'ver') == 0 ){
			$SQLVerCita = "SELECT hcn.*, hc_imc.id AS imc_id, hc_imc.nombre AS imc_clasificacion  
						   FROM hcnutri_ hcn
						   LEFT JOIN hc_imc ON hc_imc.id = hcn.imc_clasificacion 
						   WHERE id_cita = $id_cita";
			insertarTablaArray_v2($verCita, $SQLVerCita, 'ver_cita');
			$verCita = $verCita[ver_cita][0];

			$SQLInfoDiag = "SELECT hcd.*, descripcion, dt.nombre AS tipo_diag, hcc.nombre AS cont_nombre 
							FROM hc_cita_diagnosticos hcd
							LEFT JOIN cie ON cie.id = hcd.id 
							LEFT JOIN diagnostico_tipos dt ON dt.id = hcd.tipo 
							LEFT JOIN hc_causaext hcc ON hcc.id = hcd.contingencia
							WHERE hcd.id_cita = $id_cita";
			insertarTablaArray_v2($info_diag, $SQLInfoDiag, 'info_diag');
			$info_diag = $info_diag[info_diag];
		/*
			echo "<pre>";
			print_r ($info_diag);
			echo "</pre>";
*/
			$datos[acompanante] = array();

			$datos[acompanante][anombre] = $verCita[anombre];
			$datos[acompanante][acelular] = $verCita[acelular];
			$datos[acompanante][aparentesco] = $verCita[aparentesco];

			$datos[ant] = array();

			$datos[ant][ch_bajopeso] = $verCita[ch_bajopeso];
			$datos[ant][ch_alteraciones] = $verCita[ch_alteraciones];
			$datos[ant][ch_sobrepeso] = $verCita[ch_sobrepeso];
			$datos[ant][ch_hipertension] = $verCita[ch_hipertension];
			$datos[ant][ch_diabetes] = $verCita[ch_diabetes];
			$datos[ant][ch_hipotiroidismo] = $verCita[ch_hipotiroidismo];
			$datos[ant][ch_insufrenal] = $verCita[ch_insufrenal];
			$datos[ant][ch_otros] = $verCita[ch_otros];
			$datos[ant][otro_cual] = $verCita[otro_cual];

			$datos[peso] = $verCita[peso];
			$datos[talla] = $verCita[talla];
			$datos[per_toracico] = $verCita[per_toracico];
			$datos[per_abdomen] = $verCita[per_abdomen];
			$datos[per_cadera] = $verCita[per_cadera];
			$datos[rel_cintura] = $verCita[rel_cintura];
			$datos[creatinina] = $verCita[creatinina];
			$datos[tfg] = $verCita[tfg];
			$datos[imc_clasificacion] = $verCita[imc_clasificacion];
			//$datos[imc_nombre] = $verCita[imc_nombre]; // Pendiente de mostrar el JSON trae la info bien, pero no se muestra.			
			$datos[imc] = $verCita[imc];

			$datos[semanal] = array();
			$datos[semanal][cereales] = $verCita[cereales];
			$datos[semanal][verduras] = $verCita[verduras];
			$datos[semanal][frutas] = $verCita[frutas];
			$datos[semanal][carnes] = $verCita[carnes];
			$datos[semanal][lacteos] = $verCita[lacteos];
			$datos[semanal][azucares] = $verCita[azucares];
			$datos[semanal][grasas] = $verCita[grasas];

			$datos[diaria] = array();
			$datos[diaria][desayuno] = $verCita[desayuno];
			$datos[diaria][des_hora] = $verCita[des_hora];
			$datos[diaria][merienda] = $verCita[merienda];
			$datos[diaria][mer_hora] = $verCita[mer_hora];
			$datos[diaria][almuerzo] = $verCita[almuerzo];
			$datos[diaria][alm_hora] = $verCita[alm_hora];
			$datos[diaria][onces] = $verCita[onces];
			$datos[diaria][onc_hora] = $verCita[onc_hora];
			$datos[diaria][cena] = $verCita[cena];
			$datos[diaria][cen_hora] = $verCita[cen_hora];

			$datos[habitos] = array();
			$datos[habitos][falto_dinero] = $verCita[falto_dinero];
			$datos[habitos][menos_comida] = $verCita[menos_comida];
			$datos[habitos][fuera_desa] = $verCita[fuera_desa]; //Pendiente por misma razon
			$datos[habitos][fuera_almu] = $verCita[fuera_almu]; //Pendiente por misma razon
			$datos[habitos][fuera_cena] = $verCita[fuera_cena]; //Pendiente por misma razon
			$datos[habitos][lugar_consumo] = $verCita[lugar_consumo];
			$datos[habitos][rd_frecuencia] = $verCita[rd_frecuencia];
			$datos[habitos][ch_paquete] = $verCita[ch_paquete]; //Pendiente por misma razon
			$datos[habitos][ch_panaderia] = $verCita[ch_panaderia]; //Pendiente por misma razon
			$datos[habitos][ch_rapidas] = $verCita[ch_rapidas]; //Pendiente por misma razon
			$datos[habitos][ch_gaseosas] = $verCita[ch_gaseosas]; //Pendiente por misma razon
			$datos[habitos][ch_fritos] = $verCita[ch_fritos]; //Pendiente por misma razon
			$datos[habitos][ch_ffc_otros] = $verCita[ch_ffc_otros]; //Pendiente por misma razon
			$datos[habitos][ffc_cuales] = $verCita[ffc_cuales];

			
			$datos[lactancia] = array();
			$datos[lactancia][recibe] = $verCita[recibe];
			$datos[lactancia][recibe_dia] = $verCita[recibe_dia];
			$datos[lactancia][noche] = $verCita[noche];
			$datos[lactancia][noche_motivo] = $verCita[noche_motivo];
			$datos[lactancia][problemas] = $verCita[problemas];
			$datos[lactancia][problemas_cuales] = $verCita[problemas_cuales];
			$datos[lactancia][capacitacion] = $verCita[capacitacion];
			$datos[lactancia][alimentos] = $verCita[alimentos];
			$datos[lactancia][alimentos_que] = $verCita[alimentos_que];
			$datos[lactancia][leche] = $verCita[leche];
			$datos[lactancia][suplementacion] = $verCita[suplementacion];
			$datos[lactancia][suplementacion_tipo] = $verCita[suplementacion_tipo];
			$datos[lactancia][biberon] = $verCita[biberon];
			$datos[lactancia][bebida_que] = $verCita[bebida_que];

			$datos[diagnostico_cita] = $info_diag;
			/*$datos[diagnostico_cita][ppal] = $info_diag[0][bl_principal];
			$datos[diagnostico_cita][codigo] = $info_diag[0][codigo];
			$datos[diagnostico_cita][diagnostico] = $info_diag[0][descripcion];
			$datos[diagnostico_cita][tipo] = $info_diag[0][tipo_diag];
			$datos[diagnostico_cita][contingencia] = $info_diag[0][cont_nombre];*/

			$datos[hallazgos] = array();
			$datos[hallazgos][conducta] = $verCita[conducta];
			$datos[hallazgos][recomendacion] = $verCita[recomendacion];
		}

	}else{
		$accion = $paquete->accion;
		$formulario = $paquete->formulario;

		if ( strcmp($accion, 'guardar_basicos_nutri') == 0 ){

			$SQLInsertBasicos = "UPDATE pacientes SET sl_estado_civil = '$formulario->sl_estado_civil', ocupacion = '$formulario->ocupacion', sl_escolaridad = '$formulario->sl_escolaridad', direccion = '$formulario->direccion', telefono = '$formulario->telefono', celular = '$formulario->celular', acudiente = '$formulario->acudiente', acudiente_parentesco = '$formulario->acudiente_parentesco', acudiente_celular = '$formulario->acudiente_celular' WHERE id = $formulario->id";
			if ( ejecutarQuery_v2($SQLInsertBasicos) == true ){
				$datos[estado] = "OK";
				$datos[mensaje] = "Datos básicos almacenados correctamente.";
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
					$datos[mensaje] = "Datos básicos almacenados correctamente.";
				}else{
					$datos[estado] = "ERROR";
					$datos[mensaje] = "Los datos no fueron almacenados.";
				}
			}else{
				$SQLInsert = "INSERT INTO hcnutri_ (id, id_cita, anombre, acelular, aparentesco, ch_bajopeso, ch_alteraciones, ch_sobrepeso, ch_hipertension, ch_diabetes, ch_hipotiroidismo, ch_insufrenal, ch_otros, otro_cual) 
							VALUES ( NULL, ".$formulario->cita.", '".$acompanante->anombre."', '".$acompanante->acelular."', '".$acompanante->aparentesco."', '".$ant->ch_bajopeso."', '".$ant->ch_alteraciones."', '".$ant->ch_sobrepeso."', '".$ant->ch_hipertension."', '".$ant->ch_diabetes."', '".$ant->ch_hipotiroidismo."', '".$ant->ch_insufrenal."', '".$ant->ch_otros."', '".$ant->otro_cual."')";

				if ( ejecutarQuery_v2($SQLInsert) == true ){
					$datos[estado] = "OK";
					$datos[mensaje] = "Datos básicos almacenados correctamente.";
				}else{
					$datos[estado] = "ERROR";
					$datos[mensaje] = "Los datos no fueron almacenados.";
				}
			}
		} else if (strcmp($accion, 'guardar_antropo') == 0 ) {
		
			$SQLUpdateAntropo = "UPDATE hcnutri_ SET peso = '$formulario->peso', talla = '$formulario->talla', per_toracico = '$formulario->per_toracico', per_abdomen = '$formulario->per_abdomen', per_cadera = '$formulario->per_cadera', rel_cintura = '$formulario->rel_cintura', creatinina = '$formulario->creatinina', tfg = '$formulario->tfg', imc_clasificacion = '$formulario->imc_clasificacion', imc = '$formulario->imc' WHERE id_cita = '$formulario->cita'";
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
				$datos[mensaje] = "Datos nutricionales almacenados correctamente.";
			}else{
				$datos[estado] = "ERROR";
				$datos[mensaje] = "Los datos nutricionales no fueron almacenados.";
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
				$datos[mensaje] = "Hallazgos y recomendaciones almacenados correctamente.";
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
