<?php

include_once('funcionesBD.php');

if ( isset($_GET['listados']) ){
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
	$formulario = json_decode(file_get_contents("php://input"));
	if ( isset($formulario->cita) ){
		$id_cita = $formulario->cita;

		$SQLInfoPaciente = "SELECT p.*, YEAR(CURDATE())-YEAR(p.da_nacimiento) + IF(DATE_FORMAT(CURDATE(),'%m-%d') > DATE_FORMAT(p.da_nacimiento,'%m-%d'), 0, -1) AS edad_actual
							FROM r2_pacientes_citas r2pc
							LEFT JOIN pacientes p ON p.id = r2pc.hd_pacientes
							WHERE r2pc.id = ".$id_cita;
		insertarTablaArray_v2($pacientes, $SQLInfoPaciente, 'pacientes');
		$datos[paciente] = $pacientes[pacientes][0];

		//descomponer fecha de nacimiento
		$fecha_nac = $datos[paciente][da_nacimiento];
		$explode_fecha = explode("-", $fecha_nac);
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

		$datos[imc_clasificaciones] = array( array('id'=>0, 'nombre'=>'Sin Datos'), array('id'=>1, 'nombre'=>'Delgadez Severa'), array('id'=>2, 'nombre'=>'Delgadez Moderada'), array('id'=>3, 'nombre'=>'Delgadez Leve'), array('id'=>4, 'nombre'=>'Bajo Peso'), array('id'=>5, 'nombre'=>'Normal'), array('id'=>6, 'nombre'=>'Sobrepeso'), array('id'=>7, 'nombre'=>'Obesidad Grado I'), array('id'=>8, 'nombre'=>'Obesidad Grado II'), array('id'=>9, 'nombre'=>'Obesidad Grado III') );

		//tipos diagnostico
		$SQLTiposDiagnostico = "SELECT * FROM diagnostico_tipos ORDER BY nombre ASC";
		insertarTablaArray_v2($datos, $SQLTiposDiagnostico, 'tipos_diagnostico');

		//medicamentos pos / no pos
		$SQLMedicamentos = "SELECT *, CONCAT(descripcion,' - ',principio_activo, ' - ', forma_farmaceutica) AS nombre FROM medicamentos ORDER BY descripcion ASC";
		insertarTablaArray_v2($datos, $SQLMedicamentos, 'medicamentos');

		//----------------------------------ver la historia clinica para la cita N
		if ( strcmp($formulario->accion, 'ver') == 0 ){
			$SQLInfoCita = "SELECT hc.*, hc_imc.nombre AS imc_clasificacion 
							FROM hc
							LEFT JOIN hc_imc ON hc_imc.id = hc.sl_imc 
							WHERE id_cita = $id_cita";
			insertarTablaArray_v2($info_cita, $SQLInfoCita, 'info_cita');
			$info_cita = $info_cita[info_cita][0];

			$SQLEvoluciones = "SELECT id AS numero, descripcion FROM hc_evoluciones WHERE id_cita = $id_cita";
			insertarTablaArray_v2($datos, $SQLEvoluciones, 'evoluciones_cita');
			/*echo "evoluciones<pre>";
			print_r($info_cita);
			echo "</pre>";
			Array
			(
			    [anombre] => nombre 71
			    [acelular] => celular 71
			    [aparentesco] => parentesco 71
			    [motivo] => mtivo 71
			    [enfermedad] => enf 71
			    [sl_finalidad] => 3
			    [sl_causaext] => 3
			    [sl_evento] => 0
			)*/
			$datos[motivo] = array();
			// nombre completo como esta en la vista (ng-model) = nombre de la columna como está en la BD
				$datos[motivo][acompanante] = $info_cita[anombre];
				$datos[motivo][cel_acompanante] = $info_cita[acelular];
				$datos[motivo][acom_parentesco] = $info_cita[aparentesco];
				$datos[motivo][motivo] = $info_cita[motivo];
				$datos[motivo][enfermedad] = $info_cita[enfermedad];
				$datos[motivo][finalidad] = $info_cita[sl_finalidad];
				$datos[motivo][causa_ext] = $info_cita[sl_causaext];
				$datos[motivo][evento] = $info_cita[sl_evento];

			//antecedentes
			$datos[antecedentes] = array();

				$datos[antecedentes][ant_familiares] = $info_cita[ant_familiares];
				$datos[antecedentes][psicosociales] = $info_cita[ant_psicoso];
				$datos[antecedentes][patologicos] = $info_cita[ant_patolog];
				$datos[antecedentes][quirurgicos] = $info_cita[ant_quirurg];
				$datos[antecedentes][traumaticos] = $info_cita[ant_traumat];
				$datos[antecedentes][alergicos] = $info_cita[ant_toxico];
				$datos[antecedentes][farmacologicos] = $info_cita[ant_farmacolog];
				$datos[antecedentes][gineco] = $info_cita[ant_gineco];
				$datos[antecedentes][transfusionales] = $info_cita[ant_transf];
				$datos[antecedentes][hereditarios] = $info_cita[ant_enfhereditario];
				$datos[antecedentes][otros_ant] = $info_cita[ant_otros];
				$datos[antecedentes][cabeza_sist] = $info_cita[rev_cabeza];
				$datos[antecedentes][sentidos_sist] = $info_cita[rev_sentidos];
				$datos[antecedentes][cuello_sist] = $info_cita[rev_cuello];
				$datos[antecedentes][cardiovascular_sist] = $info_cita[rev_cardio];
				$datos[antecedentes][respiratorio_sist] = $info_cita[rev_resp];
				$datos[antecedentes][gastro_sist] = $info_cita[rev_gastro];
				$datos[antecedentes][genito_sist] = $info_cita[rev_genitouri];
				$datos[antecedentes][osteomuscular_sist] = $info_cita[rev_osteo];
				$datos[antecedentes][nervioso_sist] = $info_cita[rev_nervioso];
				$datos[antecedentes][endocrinologico_sist] = $info_cita[rev_endocrino];
				$datos[antecedentes][psiquiatricos_sist] = $info_cita[rev_psiquiat];
				$datos[antecedentes][hematologicos_sist] = $info_cita[rev_hemato];
				$datos[antecedentes][piel_faneras_sist] = $info_cita[rev_piel];
				$datos[antecedentes][oral_sist] = $info_cita[rev_cavoral];

			//Examen Físico
			$datos[examenfisico] = array();

			$datos[examenfisico][est_general] = $info_cita[sl_estadogral];
			$datos[examenfisico][est_resp] = $info_cita[sl_resp];
			$datos[examenfisico][est_hidratacion] = $info_cita[sl_hidratacion];
			$datos[examenfisico][tanner] = $info_cita[sl_tanner];
			$datos[examenfisico][est_conciencia] = $info_cita[sl_conciencia];
			$datos[examenfisico][glasgow] = $info_cita[sl_glasgow];
			$datos[examenfisico][pa_sistolica] = $info_cita[part_senta];
			$datos[examenfisico][pa_diastolica] = $info_cita[part_sentb];
			$datos[examenfisico][pa_decubito] = $info_cita[part_decu];
			$datos[examenfisico][pa_media] = $info_cita[part_media];
			$datos[examenfisico][fa_cardiaca] = $info_cita[fr_cardiaca];
			$datos[examenfisico][fa_resp] = $info_cita[fr_resp];
			$datos[examenfisico][presion] = $info_cita[pr_pulso];
			$datos[examenfisico][temperatura] = $info_cita[temp];
			$datos[examenfisico][temperatura_rectal] = $info_cita[temp_rectal];
			$datos[examenfisico][temperatura_amb] = $info_cita[temp_amb];
			$datos[examenfisico][peso] = $info_cita[peso];
			$datos[examenfisico][talla] = $info_cita[talla];
			$datos[examenfisico][perimetro_tor] = $info_cita[per_toracicoç];
			$datos[examenfisico][perimetro_abd] = $info_cita[per_abdomen];
			$datos[examenfisico][perimetro_cadera] = $info_cita[per_cadera];
			$datos[examenfisico][relacion] = $info_cita[rel_cintura];
			$datos[examenfisico][creatinina] = $info_cita[creatinina];
			$datos[examenfisico][tfg] = $info_cita[tfg];
			$datos[examenfisico][imc_clasificacion] = $info_cita[imc_clasificacion];
			$datos[examenfisico][imc] = $info_cita[imc];
			$datos[examenfisico][observaciones_ef] = $info_cita[examen_fisico];
			$datos[examenfisico][adicionales_ef] = $info_cita[adicionales_examen_fisico];
		}
	}else if ( isset($formulario->pagina) ){
		$SQLExisteHcCita = "SELECT * FROM hc WHERE id_cita = ".$formulario->id_cita;
		insertarTablaArray_v2($existe_hc_cita, $SQLExisteHcCita, 'existe_hc_cita');
		if ( intval($formulario->pagina) == 0 ){
			if ( !isset($existe_hc_cita[existe_hc_cita][0]) ){//insert
				$SQLInsert = "	INSERT INTO hc (id, id_cita, anombre, acelular, aparentesco, motivo, enfermedad, sl_finalidad, sl_causaext, sl_evento)
								VALUES (NULL, '".$formulario->id_cita."', '".$formulario->acompanante."', '".$formulario->cel_acompanante."', '".$formulario->acom_parentesco."', '".$formulario->motivo."', '".$formulario->enfermedad."', '".$formulario->finalidad."', '".$formulario->causa_ext."', '".$formulario->evento."')";
				$datos[id_hc] = insertarFila($SQLInsert);
				if ($datos[id_hc] != 0){
					$datos[estado] = "ok";
				}else {
					$datos[estado] = "error";
				}
			}else{//update

				$SQLUpdate = " 	UPDATE hc
								SET anombre = '".$formulario->acompanante."',
									acelular = '".$formulario->cel_acompanante."',
									aparentesco = '".$formulario->acom_parentesco."',
									motivo = '".$formulario->motivo."',
									enfermedad = '".$formulario->enfermedad."',
									sl_finalidad = ".$formulario->finalidad.",
									 sl_causaext = ".$formulario->causa_ext.",
									 sl_evento = ".$formulario->evento."
								WHERE id_cita = ".$formulario->id_cita;
				if (ejecutarQuery_v2($SQLUpdate)){
					$datos[estado] = "ok";
				}else {
					$datos[estado] = "error";
				}
			}
		} else if ( intval($formulario->pagina) == 1 ){
			$SQLAntecedentes = "UPDATE hc
								SET ant_familiares = '$formulario->ant_familiares',
									ant_psicoso = '$formulario->psicosociales',
									ant_patolog = '$formulario->patologicos',
									ant_quirurg = '$formulario->quirurgicos',
									ant_traumat = '$formulario->traumaticos',
									ant_toxico = '$formulario->alergicos',
									ant_farmacolog = '$formulario->farmacologicos',
									ant_gineco = '$formulario->gineco',
									ant_transf = '$formulario->transfusionales',
									ant_enfhereditario = '$formulario->hereditarios',
									ant_otros = '$formulario->otros_ant',
									rev_cabeza = '$formulario->cabeza_sist',
									rev_sentidos = '$formulario->sentidos_sist',
									rev_cuello = '$formulario->cuello_sist',
									rev_cardio = '$formulario->cardiovascular_sist',
									rev_resp = '$formulario->respiratorio_sist',
									rev_gastro = '$formulario->gastro_sist',
									rev_genitouri = '$formulario->genito_sist',
									rev_osteo = '$formulario->osteomuscular_sist',
									rev_nervioso = '$formulario->nervioso_sist',
									rev_endocrino = '$formulario->endocrinologico_sist',
									rev_psiquiat = '$formulario->psiquiatricos_sist',
									rev_hemato = '$formulario->hematologicos_sist',
									rev_piel = '$formulario->piel_faneras_sist',
									rev_cavoral = '$formulario->oral_sist'
								WHERE id_cita = $formulario->id_cita";
			if (ejecutarQuery_v2($SQLAntecedentes)){
				$datos[estado] = "ok";
			}else {
				$datos[estado] = "error";
			}
		} else if ( intval($formulario->pagina) == 2 ){
			$SQLUpdateExamenes = "	UPDATE hc
									SET sl_estadogral = '".$formulario->est_general."',
										sl_hidratacion = '".$formulario->est_hidratacion."',
										sl_conciencia = '".$formulario->est_conciencia."',
										sl_resp = '".$formulario->est_resp."',
										sl_tanner = '".$formulario->tanner."',
										sl_glasgow = '".$formulario->glasgow."',
										part_senta = '".$formulario->pa_sistolica."',
										part_sentb = '".$formulario->pa_diastolica."',
										part_decu = '".$formulario->pa_decubito."',
										part_media = '".$formulario->pa_media."',
										fr_cardiaca = '".$formulario->fa_cardiaca."',
										fr_resp = '".$formulario->fa_resp."',
										pr_pulso = '".$formulario->presion."',
										temp = '".$formulario->temperatura."',
										temp_rectal = '".$formulario->temperatura_rectal."',
										temp_amb = '".$formulario->temperatura_amb."',
										peso = '".$formulario->peso."',
										talla = '".$formulario->talla."',
										per_toracico = '".$formulario->perimetro_tor."',
										per_abdomen = '".$formulario->perimetro_abd."',
										per_cadera = '".$formulario->perimetro_cadera."',
										rel_cintura = '".$formulario->relacion."',
										creatinina = '".$formulario->creatinina."',
										tfg = '".$formulario->tfg."',
										sl_imc = '".$formulario->imc_clasificacion."',
										imc = '".$formulario->imc."',
										examen_fisico = '".$formulario->observaciones_ef."',
										adicionales_examen_fisico = '".$formulario->adicionales_ef."'
									WHERE id_cita = $formulario->id_cita";
			$datos[query] = $SQLUpdateExamenes;
			if (ejecutarQuery_v2($SQLUpdateExamenes)){
				$datos[estado] = "ok";
			}else {
				$datos[estado] = "error";
			}
		} else if ( intval($formulario->pagina) == 3 ){
			if ( strcmp('crear', $formulario->accion) == 0 ){
				$id_cita = $formulario->id_cita;
				$diagnosticos = $formulario->diagnosticos_cita;
				foreach ($diagnosticos as $diagnostico) {
					$SQLExisteDiagnosticoCita = "SELECT * FROM hc_cita_diagnosticos WHERE id_cita = $id_cita AND codigo = '".$diagnostico->codigo."'";
					insertarTablaArray_v2($existe_diagnostico_cita, $SQLExisteDiagnosticoCita, 'existe_diagnostico_cita');
					if ( empty($existe_diagnostico_cita[existe_diagnostico_cita]) ){
						$SQLDiagnostico = "INSERT INTO hc_cita_diagnosticos (id, id_cita, bl_principal, codigo, tipo, contingencia) VALUES (NULL,$id_cita,".intval($diagnostico->ppal).",'".$diagnostico->codigo."',".$diagnostico->tipo->id.",".$diagnostico->contingencia->id.")";
						if (ejecutarQuery_v2($SQLDiagnostico)){
							$datos[estado] = "ok";
							$datos[mensaje] = "Diagnósticos almacenados correctamente.";
						}else{
							$datos[estado] = "error";
						}
					}else{
						$datos[estado] = "ok";
						$datos[mensaje] = "Diagnósticos almacenados correctamente.";
					}
				}
			}else if ( strcmp('eliminar', $formulario->accion) == 0 ){
				//echo "---{".$formulario->pagina."}---";
				$id_cita = $formulario->id_cita;
				$codigo = $formulario->codigo;
				$SQLEliminar = "DELETE FROM hc_cita_diagnosticos WHERE id_cita = $id_cita AND codigo = '".$codigo."'";
				$numEliminadas = eliminarFilas($SQLEliminar);
			}else if ( strcmp('principal', $formulario->accion) == 0 ){
				$id_cita = $formulario->id_cita;
				$codigo = $formulario->codigo;
				$SQLUpdateCeroTodos = "UPDATE hc_cita_diagnosticos SET bl_principal = 0 WHERE id_cita = $id_cita";
				//echo "{{{{".$SQLUpdateCeroTodos."}}}}}";
				ejecutarQuery($SQLUpdateCeroTodos);
				$SQLUpdatePpal = "UPDATE hc_cita_diagnosticos SET bl_principal = ".intval($formulario->ppal)." WHERE id_cita = $id_cita AND codigo = '".$codigo."'";
				ejecutarQuery($SQLUpdatePpal);
				//echo "{{{{".$SQLUpdatePpal."}}}}";
			}
			//echo "{{".$SQLDiagnostico."}}";
		} else if ( intval($formulario->pagina) == 4 ){
			if ( strcmp($formulario->accion, 'crear') == 0 ){
				$SQLInsertar = "INSERT INTO hc_evoluciones (id, id_cita, descripcion) VALUES (NULL,".$formulario->id_cita.",'".$formulario->descripcion."')";
				$datos[numero] = insertarFila($SQLInsertar);
				$datos[mensaje] = "La evolución Nº ".$datos[numero]." ha sido almacenada correctamente.";
			}else if ( strcmp($formulario->accion, 'eliminar') == 0 ){
				$SQLEliminar = "DELETE FROM hc_evoluciones WHERE id = ".$formulario->id_evolucion;
				$gg = eliminarFilas($SQLEliminar);
				if ( $gg != 0 ) {
					$datos[status] = "OK";
					$datos[mensaje_borrar] = "La evolución ha sido eliminada correctamente.";
				}
			}
		} else if ( intval($formulario->pagina) == 5 ){
			if ( strcmp($formulario->accion, 'agregar_medicamento') == 0 ){
				$SQLExisteCitaFormula = "SELECT * FROM cita_formula WHERE id_cita = ".$formulario->id_cita;
				insertarTablaArray_v2($existe_cita_formula, $SQLExisteCitaFormula, 'existe_cita_formula');
				if ( !empty($existe_cita_formula[existe_cita_formula]) ){
					$id_formula = $existe_cita_formula[existe_cita_formula][0][id];
				}else{
					$SQLCrearCitaFormula = "INSERT INTO cita_formula(id, id_cita) VALUES(NULL, ".$formulario->id_cita.")";
					$id_formula = insertarFila($SQLCrearCitaFormula);
				}
				$datos[id_formula] = $id_formula;

				//agregando medicamento
				$medicamento = $formulario->medicamento;
				$SQLCrearMedicamento = "INSERT INTO formula_medicamentos(id, id_formula, id_medicamento, verificar, cantidad, dosis, salidas)
				                     	VALUES (NULL,$id_formula,$medicamento->id,$medicamento->verificar,$medicamento->cantidad,'$medicamento->dosis','$medicamento->salidas')";
				                     	//echo "{{$SQLCrearMedicamento}}";
				$id_medicamento_formula = insertarFila($SQLCrearMedicamento);
				$datos[id_medicamento_formula] = $id_medicamento_formula;

				$datos[mensaje] = "Medicamento agregado correctamente.";
			}
		} else if ( intval($formulario->pagina) == 6 ){
			if ( strcmp($formulario->accion, 'agregar_orden') == 0 ){
				$SQLExisteCitaOrden = "SELECT * FROM citas_ordenes WHERE id_cita = ".$formulario->id_cita." AND id_cup = ".$formulario->orden->id;

				insertarTablaArray_v2($existe, $SQLExisteCitaOrden, 'cita_orden');
				if ( count($existe[cita_orden]) == 0 ){
					//INSERT
					$SQLInsertCitaOrden = "INSERT INTO citas_ordenes(id_cita, id_cup) VALUES(".$formulario->id_cita.", ".$formulario->orden->id.")";
					if (insertarFila($SQLInsertCitaOrden) != 0 ){
						$datos[status] = "OK";
						$datos[mensaje] = "La orden ha sido agregada correctamente.";
					}else{
						$datos[status] = "ERROR";
						$datos[mensaje] = "La orden no ha sido agregada.";
					}
				}else{
					//ERROR
					$datos[status] = "ERROR";
					$datos[mensaje] = "La orden ya ha sido generada previamente.";
				}
				/*{
				    "id_cita": "93",
				    "pagina": 6,
				    "accion": "agregar_orden",
				    "orden": {
				        "id": "10616",
				        "nombre": "781601 APLICACION DE TUTOR EXTERNO RODILLA",
				        "cups": "781601",
				        "procedimiento": "APLICACION DE TUTOR EXTERNO RODILLA"
				    }
				}:*/
			}
		}
	}
}

if ( isset($_GET[debug]) ){
	echo "<pre>";
	print_r($datos);
	echo "</pre>";
} else echo json_encode($datos);
