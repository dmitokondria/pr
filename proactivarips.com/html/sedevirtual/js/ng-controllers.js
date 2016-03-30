var controllers = angular.module('sedevirtual.controllers', []);

controllers.controller('ingresoCTRL', function($scope, $http, $location, $cookieStore, $route){

    $scope.hoy = moment().locale('es');
    $scope.semana = moment().week();
    $scope.anio = moment().year();

    $scope.seccion = "Ingreso";
    $http.get('json/fecha.php').then(function(result) {
        $scope.fecha = result.data.info;
    });

    $scope.formulario = {};
    $scope.mensaje = '';

	$scope.ingreso = function(){
		$http({
            url: 'json/ingreso.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function(result){
            if(result.data.tipo_usuario=='Campaña'){
                $cookieStore.put('usuario', {tipo_usuario: result.data.tipo_usuario, id: result.data.id, nombres: result.data.nombres, mensaje: result.data.mensaje});
                $location.path('/registro_rapido');
            }else if(result.data.tipo_usuario=='Recepción'){
                $cookieStore.put('usuario', {tipo_usuario: result.data.tipo_usuario, id: result.data.id, nombres: result.data.nombres, mensaje: result.data.mensaje});
                $location.path('/recepcion/agenda/'+$scope.semana+'/'+$scope.anio);
            }else if(result.data.tipo_usuario=='Profesional'){
                $cookieStore.put('usuario', {tipo_usuario: result.data.tipo_usuario, id: result.data.id, nombres: result.data.nombres, mensaje: result.data.mensaje, especialidad:result.data.especialidad});
                $location.path('/profesional/agenda/'+$scope.semana+'/'+$scope.anio);
            }else if (result.data.tipo_usuario=='Paciente' ){
                $cookieStore.put('usuario', {tipo_usuario: result.data.tipo_usuario, id: result.data.id, nombres: result.data.nombres, mensaje: result.data.mensaje, especialidad:result.data.especialidad, edad: result.data.edad_actual, ocupacion: result.data.ocupacion});
                $location.path('/miperfil/datos/');
            }else {
                $scope.mensaje = '¡Datos de acceso incorrectos!';
            }
        });
	};
});

controllers.controller('barraUsuarioCTRL', function($scope, $cookieStore){
    //barra superior con nombre y otros datos del usuario
    $scope.barra_usuario = $cookieStore.get('usuario');
});

controllers.controller('registro_rapidoCTRL', function($scope, $http, $location, $cookieStore, $route){

    $scope.seccion = "Registro Rápido";
    $http.get('json/fecha.php').then(function(result) {
        $scope.fecha = result.data.info;
    });

    $scope.formulario = {};

    $scope.registrar = function(){
        $http({
            url: 'json/registro_rapido.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function(result){
            if(result.data.mensaje=='OK'){
                $route.reload();
            }
        });
    };

    $scope.cerrarSesion = function(){
        $cookieStore.remove("usuario");
        $location.path('/ingreso');
    };
    if (typeof $scope.usuario === 'undefined') {
        $location.path('/ingreso');
    };
});

controllers.controller('menu_lateralCTRL', function($scope, $http, $cookieStore){

    $scope.menu_lateral = [];
    $scope.usuario = $cookieStore.get("usuario");

    $scope.hoy = moment().locale('es');
    $scope.semana = moment().week();
    $scope.anio = moment().year();

    if ( $scope.usuario.tipo_usuario == 'Paciente' ){
        $scope.menu_lateral.push(
            {
                nombre:'Mi Perfil',
                opciones:
                    [
                        {nombre:'Datos Personales',vinculo:'#/miperfil/datos'},
                        {nombre:'Cambiar Contraseña',vinculo:'#/miperfil/clave'},
                        {nombre:'Mi Historial de Atención',vinculo:'#/miperfil/historial'}
                    ]
            },
            {
                nombre:'Mis Citas',
                opciones:
                [
                    {nombre:'Crear Cita',vinculo:'#/miscitas/crear'},
                    {nombre:'Consultar Mis Citas',vinculo:'#/miscitas/consultar'}
                ]
            }
        );
    }else if ( $scope.usuario.tipo_usuario == 'Profesional' ){

        $scope.menu_lateral.push(
            {
                nombre:'Agenda de Pacientes',
                opciones:
                [
                    {nombre:'Agenda',vinculo:'#/profesional/agenda/'+$scope.semana+'/'+$scope.anio},
                    {nombre:'Asignar Cita',vinculo:'#/profesional/crearcita'} //////****************************************
                ]
            },
            {
                nombre:'Pacientes',
                opciones:
                [
                    {nombre:'Crear Paciente',vinculo:'#/crearpaciente'},
                    {nombre:'Consultar Paciente',vinculo:'#/consultarpaciente'}
                ]
            },{
                nombre:'Hospitalización',
                opciones:
                [
                    {nombre:'Crear',vinculo:'#/seguimiento'},
                    {nombre:'Ver',vinculo:'#/seguimiento'}
                ]
            },{
                nombre:'Seguimiento Telefónico',
                opciones:
                [
                    {nombre:'Crear',vinculo:'#/seguimiento'},
                    {nombre:'Ver',vinculo:'#/seguimiento'}
                ]
            }

        );
    }else if ( $scope.usuario.tipo_usuario == 'Recepción' ){

        $scope.menu_lateral.push(
            {
                nombre:'Agenda de Pacientes',
                opciones:
                [
                    {nombre:'Agenda',vinculo:'#/recepcion/agenda/'+$scope.semana+'/'+$scope.anio},//////****************************************
                    {nombre:'Asignar Cita',vinculo:'#/recepcion/crearcita'}//////****************************************
                ]
            },
            {
                nombre:'Pacientes',
                opciones:
                [
                    {nombre:'Crear Paciente',vinculo:'#/crearpaciente'},//////****************************************
                    {nombre:'Consultar Paciente',vinculo:'#/consultarpaciente'}
                ]
            }
        );
    }
});

controllers.controller('agendaCTRL', function($scope, $http, $location, $cookieStore, $route, $routeParams){

    $scope.pagina = Number($routeParams.pagina);
    $scope.semana = Number($routeParams.semana);
    $scope.anio = Number($routeParams.anio);
    $scope.filtro_profesional = {};

    $scope.bl_profesional = 1;

    $scope.usuario = $cookieStore.get('usuario');
    if ( $scope.usuario.tipo_usuario == 'Profesional' ) {
        $scope.seccion = "Profesional";
        $scope.filtro_profesional.id = $scope.usuario.id;
    }else if ( $scope.usuario.tipo_usuario == 'Recepción' ) {
        $scope.seccion = "Recepción";
        $scope.filtro_profesional.id = $scope.usuario.id;
    };


    $http.get('json/fecha.php').then(function(result) {
        $scope.fecha = result.data.info;
    });

    $http.get('json/agenda.php?semana='+$scope.semana+'&anio='+$scope.anio).then(function(result) {
        $scope.dias_semana = result.data.dias_semana;
        $scope.profesionales = result.data.profesionales;
    });

    $scope.cambiarSemana = function(_incremento){
        if ( _incremento == -1 ){
            //console.log("-");
            if ( $scope.semana === 0 ){
                $scope.semana = 52;
                $scope.anio -= 1;
            }else{
                $scope.semana--;
            }
        }else if ( _incremento == 1 ){
            //console.log("+");
            if ( $scope.semana == 52 ){
                $scope.semana = 0;
                $scope.anio += 1;
            }else{
                $scope.semana++;
            }
        }

        if ( _incremento === 0 ){
            $route.reload();
        }else {
            $location.path('/recepcion/agenda/'+$scope.semana+'/'+$scope.anio);
        }
    };

    $scope.sinFiltro = function(){
        $scope.filtro_profesional = '';
    };

    $scope.irHistoriaClinica = function(_cita, _especialidad){
        if ( _especialidad == 'Medicina General' ){
            $location.path('/medicina/'+_cita);
        }else if ( _especialidad == 'Nutrición' ){
            $location.path('/nutricion/'+_cita);
        }else if ( _especialidad == 'Psicología' ){
            $location.path('/psicologia/'+_cita);
        }
    };

    $scope.enSala = function(_cita){
        console.log(_cita);
        //alert(_cita.estado);
    };

    $scope.atendido = function(_cita){
        console.log(_cita);
    };

    $scope.inasistencia = function(_cita){
        console.log(_cita);
    };

    $scope.cerrarSesion = function(){
        $cookieStore.remove("usuario");
        $location.path('/ingreso');
    };
    if (typeof $scope.usuario === 'undefined') {
        $location.path('/ingreso');
    };
});

/////MEDICINA
controllers.controller('medicinaCTRL', function($scope, $http, $location, $cookieStore, $route, $routeParams){

    $scope.vista = false;

    $scope.diagnostico = {};

    $scope.formulario = {};

    $scope.finalidades = [];
    $scope.causas = [];
    $scope.tipos_evento = [];

    $scope.seccion = "Profesional";
    $http.get('json/fecha.php').then(function(result) {
        $scope.fecha = result.data.info;
        $scope.fecha_hoy = result.data.info;
    });

    $http.get('json/hora.php').then(function(result) {
        $scope.hora = result.data.hora_hoy;
    });

    //datos básicos del doctor
    $scope.usuario = $cookieStore.get('usuario');

    //datos básicos del paciente y de la cita
    $scope.formulario.cita = $routeParams.cita;
    $http({
        url: 'json/medicina.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function(result){

        $scope.cargarDatosIniciales = function(){
            $scope.tipos_id = result.data.tipos_identificacion;
            $scope.departamentos = result.data.departamentos;
            $scope.ciudades = result.data.ciudades;
            $scope.fecha = {};
            $scope.fecha.dias = result.data.fecha.dias;
            $scope.fecha.meses = result.data.fecha.meses;
            $scope.fecha.anios = result.data.fecha.anios;
            $scope.estados_civiles = result.data.estados_civiles;
            $scope.niveles_escolaridad = result.data.niveles_escolaridad;
            $scope.generos = result.data.generos;
            $scope.epss = result.data.epss;
            $scope.tipos_vinculacion = result.data.tipos_vinculacion;
            $scope.paquetes = result.data.paquetes;
            $scope.afiliaciones = result.data.afiliacion_estados;

            $scope.paciente = result.data.paciente;
            $scope.formulario.p_nombre = $scope.paciente.primer_nombre;
            $scope.formulario.p_apellido = $scope.paciente.primer_apellido;
            $scope.formulario.s_nombre = $scope.paciente.segundo_nombre;
            $scope.formulario.s_apellido = $scope.paciente.segundo_apellido;
            $scope.formulario.tipo_id = $scope.paciente.rd_tipo_identificacion;
            $scope.formulario.identificacion = $scope.paciente.numero_identificacion;
            
            $scope.formulario.fecha_nac = {};
            $scope.formulario.fecha_nac.anio = $scope.paciente.da_nacimiento.anio;
            $scope.formulario.fecha_nac.dia = $scope.paciente.da_nacimiento.dia;
            $scope.formulario.fecha_nac.mes = $scope.paciente.da_nacimiento.mes;
            
            $scope.formulario.edad_actual = $scope.paciente.edad_actual;
            $scope.formulario.estado_civil = $scope.paciente.sl_estado_civil;
            $scope.formulario.sl_departamento = $scope.paciente.sl_departamento;
            $scope.formulario.sl_municipio = $scope.paciente.sl_municipio;
            $scope.formulario.ocupacion = $scope.paciente.ocupacion;
            $scope.formulario.escolaridad = $scope.paciente.sl_escolaridad;
            $scope.formulario.genero = $scope.paciente.rd_genero;
            $scope.formulario.direccion = $scope.paciente.direccion;
            $scope.formulario.telefono = $scope.paciente.telefono;
            $scope.formulario.celular = $scope.paciente.celular;
            $scope.formulario.acudiente = $scope.paciente.acudiente;
            $scope.formulario.acu_parentesco = $scope.paciente.acudiente_parentesco;
            $scope.formulario.cel_acudiente = $scope.paciente.acudiente_celular;
            $scope.formulario.eps = $scope.paciente.sl_eps;
            $scope.formulario.paquete = $scope.paciente.servicios;
            $scope.formulario.vinculacion = $scope.paciente.sl_tipo_vinculacion;
            $scope.formulario.afiliacion = $scope.paciente.sl_estado_afiliacion;

            //motivos
            $scope.finalidades = result.data.finalidades;
            $scope.causas_ext = result.data.causas_ext;
            $scope.eventos = result.data.eventos;

            //examen fisico
            $scope.examenfisico = {};
            $scope.examenfisico.peso = 0;
            $scope.examenfisico.talla = 0;
            $scope.examenfisico.imc = 0;
            $scope.examenfisico.imc_clasificacion = 0;
            $scope.est_generales = result.data.est_generales;
            $scope.estados_resp = result.data.estados_resp;
            $scope.estados_hidratacion = result.data.estados_hidratacion;
            $scope.glasgows = result.data.glasgows;
            $scope.estados_conciencia = result.data.estados_conciencia;
            $scope.imc_clasificaciones = result.data.imc_clasificaciones;

            //diagnósticos
            $scope.tipos_diagnostico = result.data.tipos_diagnostico;
            $scope.tipos_contingencia = result.data.causas_ext;

            //medicamentos
            $scope.medicamentos = result.data.medicamentos;

            $scope.motivo = {};
            $scope.motivo.id_cita = $routeParams.cita;
        };
        $scope.cargarDatosIniciales();

        //motivos pagina 0
        $scope.mensaje_motivo = "";
        $scope.enviarMotivo = function(){
            $scope.mensaje_motivo = "";
            $scope.motivo.pagina = 0;
            $http({
                url: 'json/medicina.php',
                method: 'POST',
                data: $scope.motivo,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function(result){
                $scope.mensaje_motivo = result.data.estado;
            });
        };

        //antecetendes pagina 1
        $scope.antecedentes = {};
        $scope.mensaje_antecedentes = '';
        $scope.enviarAntecedentes = function(){
            $scope.mensaje_antecedentes = '';
            $scope.antecedentes.id_cita = $routeParams.cita;
            $scope.antecedentes.pagina = 1;
            $http({
                url: 'json/medicina.php',
                method: 'POST',
                data: $scope.antecedentes,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function(result){
                $scope.mensaje_antecedentes = result.data.estado;
            });
        };

        //examen fisico pagina 2
        $scope.examenfisico = {};
        $scope.mensaje_examenfisico = '';
        $scope.enviarExamenFisico = function(){
            $scope.mensaje_examenfisico = '';
            $scope.examenfisico.id_cita = $routeParams.cita;
            $scope.examenfisico.pagina = 2;
            $http({
                url: 'json/medicina.php',
                method: 'POST',
                data: $scope.examenfisico,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function(result){
                $scope.mensaje_examenfisico = result.data.estado;
            });
        };
        $scope.enviarDiagnosticos = function(){
            $scope.mensaje = "";
            $scope.mensaje_diagnosticos = '';
            $scope.cita_diagnosticos = {};
            $scope.cita_diagnosticos.accion = "crear";
            $scope.cita_diagnosticos.id_cita = $routeParams.cita;
            $scope.cita_diagnosticos.pagina = 3;
            $scope.cita_diagnosticos.diagnosticos_cita = $scope.diagnosticos_cita;
            $http({
                url: 'json/medicina.php',
                method: 'POST',
                data: $scope.cita_diagnosticos,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function(result){
                $scope.mensaje = result.data.mensaje;
                $scope.mensaje_diagnosticos = result.data.estado;
            });
        };

        $scope.calculoIMC = function(){
            $scope.examenfisico.imc = $scope.examenfisico.peso/($scope.examenfisico.talla/100*$scope.examenfisico.talla/100);

            if ( $scope.examenfisico.imc < 16 ){
                $scope.examenfisico.imc_clasificacion = 1;
            }else if ( $scope.examenfisico.imc >= 16 && $scope.examenfisico.imc <= 16.9 ){
                $scope.examenfisico.imc_clasificacion = 2;
            }else if ( $scope.examenfisico.imc >= 17 && $scope.examenfisico.imc <= 17.5 ){
                $scope.examenfisico.imc_clasificacion = 3;
            }else if ( $scope.examenfisico.imc >= 17.6 && $scope.examenfisico.imc <= 17.9 ){
                $scope.examenfisico.imc_clasificacion = 4;
            }else if ( $scope.examenfisico.imc >= 18 && $scope.examenfisico.imc <= 24.9 ){
                $scope.examenfisico.imc_clasificacion = 5;
            }else if ( $scope.examenfisico.imc >= 25 && $scope.examenfisico.imc <= 26.9 ){
                $scope.examenfisico.imc_clasificacion = 6;
            }else if ( $scope.examenfisico.imc >= 27 && $scope.examenfisico.imc <= 29.9 ){
                $scope.examenfisico.imc_clasificacion = 7;
            }else if ( $scope.examenfisico.imc >= 30 && $scope.examenfisico.imc <= 39.9 ){
                $scope.examenfisico.imc_clasificacion = 8;
            }else if ( $scope.examenfisico.imc > 40 ){
                $scope.examenfisico.imc_clasificacion = 9;
            }else{
                $scope.examenfisico.imc_clasificacion = 0;
            }
        };

        $scope.examenfisico.observaciones_ef = '';
        //completar al hacer clic en los check
        $scope.examenFisicoCheck = function(_campo){
            console.log(_campo);
            if (_campo == 'cabeza' && $scope.examenfisico.ch_cabeza ) $scope.examenfisico.observaciones_ef += "Cabeza: Mucosas húmedas, conjuntivas normocrómicas, escleras anictéricas.";
            if (_campo == 'ojos' && $scope.examenfisico.ch_ojos ) $scope.examenfisico.observaciones_ef += "\nOjos: ";
            if (_campo == 'oidos' && $scope.examenfisico.ch_oido ) $scope.examenfisico.observaciones_ef += "\nOidos: ";
            if (_campo == 'nariz' && $scope.examenfisico.ch_nariz ) $scope.examenfisico.observaciones_ef += "\nNariz: ";
            if (_campo == 'boca' && $scope.examenfisico.ch_boca ) $scope.examenfisico.observaciones_ef += "\nBoca: ";
            if (_campo == 'faringe' && $scope.examenfisico.ch_faringe ) $scope.examenfisico.observaciones_ef += "\nFaringe: ";
            if (_campo == 'laringe' && $scope.examenfisico.ch_laringe ) $scope.examenfisico.observaciones_ef += "\nLaringe: ";
            if (_campo == 'cuello' && $scope.examenfisico.ch_cuello ) $scope.examenfisico.observaciones_ef += "\nCuello: No se nota ingurgitación yugular, no adenopatías.";
            if (_campo == 'torax' && $scope.examenfisico.ch_torax ) $scope.examenfisico.observaciones_ef += "\nTorax: Simétrico, Ruidos cardíacos rítmicos, no se notan soplos, respiratorios no agregados.";
            if (_campo == 'senos' && $scope.examenfisico.ch_senos ) $scope.examenfisico.observaciones_ef += "\nSenos: ";
            if (_campo == 'corazon' && $scope.examenfisico.ch_corazon ) $scope.examenfisico.observaciones_ef += "\nCorazón: ";
            if (_campo == 'pulmon' && $scope.examenfisico.ch_pulmon ) $scope.examenfisico.observaciones_ef += "\nPulmón: ";
            if (_campo == 'abdomen' && $scope.examenfisico.ch_abdomen ) $scope.examenfisico.observaciones_ef += "\nAbdomen: No dolor, no masas, no signos de irritación peritoneal.";
            if (_campo == 'genitales' && $scope.examenfisico.ch_genitales ) $scope.examenfisico.observaciones_ef += "\nGenitales: Normal. No adenopatías inguinales.";
            if (_campo == 'ginecologico' && $scope.examenfisico.ch_ginecologico ) $scope.examenfisico.observaciones_ef += "\nGinecológico: ";
            if (_campo == 'rectal' && $scope.examenfisico.ch_rectal ) $scope.examenfisico.observaciones_ef += "\nRectal: ";
            if (_campo == 'miembros_sup' && $scope.examenfisico.ch_miembros_sup ) $scope.examenfisico.observaciones_ef += "\nMiembros Superiores: Pulsos simétricos de adecuada amplitud, no edemas.";
            if (_campo == 'miembros_inf' && $scope.examenfisico.ch_miembros_inf ) $scope.examenfisico.observaciones_ef += "\nMiembros Inferiores: Pulsos simétricos de adecuada amplitud, no edemas.";
            if (_campo == 'columna' && $scope.examenfisico.ch_columna ) $scope.examenfisico.observaciones_ef += "\nColumna: ";
            if (_campo == 'reflejos' && $scope.examenfisico.ch_reflejos ) $scope.examenfisico.observaciones_ef += "\nReflejos: ";
            if (_campo == 'marcha' && $scope.examenfisico.ch_marcha ) $scope.examenfisico.observaciones_ef += "\nMarcha: ";
            if (_campo == 'postura' && $scope.examenfisico.ch_postura ) $scope.examenfisico.observaciones_ef += "\nPostura: ";
            if (_campo == 'piel_faneras' && $scope.examenfisico.ch_piel_faneras ) $scope.examenfisico.observaciones_ef += "\nPiel Faneras: Adecuada humectación. No cambios en la coloración. No lesiones.";
            if (_campo == 'ganglios' && $scope.examenfisico.ch_ganglios ) $scope.examenfisico.observaciones_ef += "\nGanglios: ";
            if (_campo == 'pulsos' && $scope.examenfisico.ch_pulsos ) $scope.examenfisico.observaciones_ef += "\nPulsos: ";
            if (_campo == 'mental' && $scope.examenfisico.ch_mental ) $scope.examenfisico.observaciones_ef += "\nMental: ";
            if (_campo == 'craneanos' && $scope.examenfisico.ch_craneanos ) $scope.examenfisico.observaciones_ef += "\nCraneanos: ";
            if (_campo == 'pruebas' && $scope.examenfisico.ch_pruebas ) $scope.examenfisico.observaciones_ef += "\nPruebas: ";
            if (_campo == 'general' && $scope.examenfisico.ch_asp_general ) $scope.examenfisico.observaciones_ef += "\nAsp General: ";
            if (_campo == 'motor' && $scope.examenfisico.ch_motor ) $scope.examenfisico.observaciones_ef += "\nMotor: ";
            if (_campo == 'neurologico' && $scope.examenfisico.ch_neurologico ) $scope.examenfisico.observaciones_ef += "\nNeurológico:  Alerta orientado en persona espacio y tiempo, no signos de focalización motora, sensibilidad conservada.  no signos meníngeos. Pares craneanos conservados.";
        };

        $scope.cies = {};
        $scope.bl_buscado = false;
        $scope.buscarCies = function(_palabra){
            $http.get('json/medicina.php?listados=1&palabra='+_palabra).then(function(result) {
                $scope.bl_buscado = true;
                $scope.cies = result.data.cies;
                $scope.diagnostico.nombre = _palabra;
            });
        };

        //diagnostico
        $scope.diagnosticos_cita = [];
        $scope.agregarDiagnostico = function(){
            var diagnosticoTemp = {};
            diagnosticoTemp.ppal = false;
            diagnosticoTemp.codigo = $scope.diagnostico.nombre.codigo;
            diagnosticoTemp.diagnostico = $scope.diagnostico.nombre.descripcion;
            diagnosticoTemp.tipo = $scope.diagnostico.tipo;
            diagnosticoTemp.contingencia = $scope.diagnostico.tipo_contingencia;
            $scope.diagnosticos_cita.push(diagnosticoTemp);
            $scope.limpiarDiagnostico();
        };
        $scope.diagnosticoPrincipal = function(){

            for (var i = 0; i < $scope.diagnosticos_cita.length; i++) {
                if ( i == this.$index ) $scope.diagnosticos_cita[i].ppal = true;
                else $scope.diagnosticos_cita[i].ppal = false;
            }

            $scope.formulario_ = {};
            $scope.formulario_.accion = "principal";
            $scope.formulario_.codigo = $scope.diagnosticos_cita[this.$index].codigo;
            $scope.formulario_.ppal = $scope.diagnosticos_cita[this.$index].ppal;
            $scope.formulario_.id_cita = $routeParams.cita;
            $scope.formulario_.pagina = 3;
            $http({
                url: 'json/medicina.php',
                method: 'POST',
                data: $scope.formulario_,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function(result){
            });
        };
        $scope.eliminarDiagnostico = function(_index){
            $scope.formulario_ = {};
            $scope.formulario_.accion = "eliminar";
            $scope.formulario_.codigo = $scope.diagnosticos_cita[_index].codigo;
            $scope.formulario_.id_cita = $routeParams.cita;
            $scope.formulario_.pagina = 3;
            $http({
                url: 'json/medicina.php',
                method: 'POST',
                data: $scope.formulario_,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function(result){
                //eliminando un diagnostico del listado
                $scope.diagnosticos_cita.splice(_index,1);
            });
        };
        $scope.limpiarDiagnostico = function(){
            $scope.cies = {};
            $scope.bl_buscado = false;
            $scope.diagnostico = {};
        };
        $scope.guardarDiagnosticos = function(){
            $scope.mensaje_diagnosticos = '';
            $scope.formulario = {};
            $scope.formulario.diagnosticos_cita = $scope.diagnosticos_cita;
            $scope.formulario.id_cita = $routeParams.cita;
            $scope.formulario.pagina = 3;
            $http({
                url: 'json/medicina.php',
                method: 'POST',
                data: $scope.formulario,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function(result){
            });
        };

        //evolucion
        $scope.evolucion = {};
        $scope.evolucion_invalida = true;
        $scope.evoluciones_cita = [];
        $scope.verificarEvolucion = function(){
            if ( $scope.evolucion !== "" ){
                $scope.evolucion_invalida = false;
            }
        };
        $scope.crearEvolucion = function(){
            var evolucionTemp = {};
            evolucionTemp.descripcion = $scope.evolucion.descripcion;
            evolucionTemp.pagina = 4;
            evolucionTemp.id_cita = $routeParams.cita;
            evolucionTemp.accion = 'crear';
            $http({
                url: 'json/medicina.php',
                method: 'POST',
                data: evolucionTemp,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function(result){
                evolucionTemp.numero = result.data.numero;
                $scope.evoluciones_cita.push(evolucionTemp);
                $scope.limpiarEvolucion();
                $scope.evolucion_invalida = true;
                $scope.mensaje = result.data.mensaje;
                $scope.mensaje_borrar = result.data.mensaje_borrar;
            });
        };
        $scope.eliminarEvolucion = function(_index){
            //$scope.diagnosticos_cita[this.$index].codigo
            var evolucionTemp = {};
            evolucionTemp.pagina = 4;
            evolucionTemp.id_cita = $routeParams.cita;
            evolucionTemp.accion = 'eliminar';
            evolucionTemp.id_evolucion = $scope.evoluciones_cita[this.$index].numero;
            $http({
                url: 'json/medicina.php',
                method: 'POST',
                data: evolucionTemp,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function(result){
                if ( result.data.status == "OK" ){
                    $scope.evoluciones_cita.splice(_index, 1);
                    $scope.mensaje_borrar = result.data.mensaje_borrar;
                    $scope.mensaje = '';
                }
            });
        };
        $scope.limpiarEvolucion = function(){
            //
            $scope.evolucion = {};
        };
        //Limpiar mensajes
        $scope.reset = function(){
            $scope.mensaje = '';
            $scope.mensaje_borrar = '';
        }

        //medicamentos
        $scope.filtro_pos = 2;
        $scope.medicamentos_formula = [];
        $scope.busqueda = {};
        $scope.agregarMedicamento = function(){
            $scope.formulario = {};
            $scope.formulario.id_cita = $routeParams.cita;
            $scope.formulario.pagina = 5;
            $scope.formulario.accion = "agregar_medicamento";
            $scope.formulario.medicamento = $scope.busqueda.medicamento;
            $http({
                url: 'json/medicina.php',
                method: 'POST',
                data: $scope.formulario,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function(result){
                $scope.limpiarMedicamento();
                $scope.mensaje = result.data.mensaje;
            });
            $scope.medicamentos_formula.push($scope.busqueda.medicamento);
        };
        $scope.limpiarMedicamento = function(){
            $scope.busqueda = {};
        };

        //ordenes
        $scope.cups = {};
        $scope.bl_buscado = false;
        $scope.orden = {};
        $scope.ordenes = [];
        $scope.buscarCUP = function(_palabra){
            $http.get('json/medicina.php?listados=2&palabra='+_palabra).then(function(result) {
                $scope.bl_buscado = true;
                $scope.cups = result.data.cups;
            });
        };
        $scope.agregarOrden = function(){
            $scope.formulario = {};
            $scope.formulario.id_cita = $routeParams.cita;
            $scope.formulario.pagina = 6;
            $scope.formulario.accion = "agregar_orden";
            $scope.formulario.orden = $scope.orden.seleccionada;
            $scope.formulario.orden.cantidad = $scope.orden.cantidad;
            $http({
                url: 'json/medicina.php',
                method: 'POST',
                data: $scope.formulario,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function(result){
                $scope.ordenes.push($scope.orden.seleccionada);
                $scope.orden = {};
                $scope.bl_buscado = false;
                $scope.mensaje = result.data.mensaje;
                $scope.status = result.data.status;
            });
            

        };
    });

    $scope.siguiente = function(){
        console.log("siguiente");
        $('tabset tab').each( function( index, value ) {
          alert( index + ": " + value );
      });
        //$('.nav-tabs > .active').next('li').find('a').trigger('click');
    };
    $scope.cerrarSesion = function(){
        $cookieStore.remove("usuario");
        $location.path('/ingreso');
    };
    if (typeof $scope.usuario === 'undefined') {
        $location.path('/ingreso');
    };
});

controllers.controller('medicinaVerCTRL', function($scope, $http, $location, $cookieStore, $routeParams){

    $scope.seccion = "Ver Medicina";
    $http.get('json/fecha.php').then(function(result) {
        $scope.fecha = result.data.info;
        $scope.fecha_hoy = result.data.info;
    });

    $scope.vista = true;

    $scope.diagnostico = {};

    $scope.formulario = {};

    $scope.finalidades = [];
    $scope.causas = [];
    $scope.tipos_evento = [];

    $scope.seccion = "Profesional";
    $http.get('json/fecha.php').then(function(result) {
        $scope.fecha = result.data.info;
    });

    //datos básicos del doctor
    $scope.usuario = $cookieStore.get('usuario');

    //datos básicos del paciente y de la cita
    $scope.formulario.cita = $routeParams.cita;
    $http({
        url: 'json/medicina.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function(result){
        $scope.tipos_id = result.data.tipos_identificacion;
        $scope.departamentos = result.data.departamentos;
        $scope.ciudades = result.data.ciudades;
        $scope.fecha = {};
        $scope.fecha.dias = result.data.fecha.dias;
        $scope.fecha.meses = result.data.fecha.meses;
        $scope.fecha.anios = result.data.fecha.anios;
        $scope.estados_civiles = result.data.estados_civiles;
        $scope.niveles_escolaridad = result.data.niveles_escolaridad;
        $scope.generos = result.data.generos;
        $scope.epss = result.data.epss;
        $scope.tipos_vinculacion = result.data.tipos_vinculacion;
        $scope.paquetes = result.data.paquetes;
        $scope.afiliaciones = result.data.afiliacion_estados;

        $scope.paciente = result.data.paciente;
        $scope.formulario.p_nombre = $scope.paciente.primer_nombre;
        $scope.formulario.p_apellido = $scope.paciente.primer_apellido;
        $scope.formulario.s_nombre = $scope.paciente.segundo_nombre;
        $scope.formulario.s_apellido = $scope.paciente.segundo_apellido;
        $scope.formulario.tipo_id = $scope.paciente.rd_tipo_identificacion;
        $scope.formulario.identificacion = $scope.paciente.numero_identificacion;
        $scope.formulario.fecha_nac = {};
        $scope.formulario.fecha_nac.dia = $scope.paciente.da_nacimiento.dia;
        $scope.formulario.fecha_nac.mes = $scope.paciente.da_nacimiento.mes;
        $scope.formulario.fecha_nac.anio = $scope.paciente.da_nacimiento.anio;
        $scope.formulario.edad_actual = $scope.paciente.edad_actual;
        $scope.formulario.estado_civil = $scope.paciente.sl_estado_civil;
        $scope.formulario.sl_departamento = $scope.paciente.sl_departamento;
        $scope.formulario.sl_municipio = $scope.paciente.sl_municipio;
        $scope.formulario.ocupacion = $scope.paciente.ocupacion;
        $scope.formulario.escolaridad = $scope.paciente.sl_escolaridad;
        $scope.formulario.genero = $scope.paciente.rd_genero;
        $scope.formulario.direccion = $scope.paciente.direccion;
        $scope.formulario.telefono = $scope.paciente.telefono;
        $scope.formulario.celular = $scope.paciente.celular;
        $scope.formulario.acudiente = $scope.paciente.acudiente;
        $scope.formulario.acu_parentesco = $scope.paciente.acudiente_parentesco;
        $scope.formulario.cel_acudiente = $scope.paciente.acudiente_celular;
        $scope.formulario.eps = $scope.paciente.sl_eps;
        $scope.formulario.paquete = $scope.paciente.servicios;
        $scope.formulario.vinculacion = $scope.paciente.sl_tipo_vinculacion;
        $scope.formulario.afiliacion = $scope.paciente.sl_estado_afiliacion;

        //motivos
        $scope.finalidades = result.data.finalidades;
        $scope.causas_ext = result.data.causas_ext;
        $scope.eventos = result.data.eventos;

        //examen fisico
        $scope.examenfisico = {};
        $scope.examenfisico.peso = 0;
        $scope.examenfisico.talla = 0;
        $scope.examenfisico.imc = 0;
        $scope.examenfisico.imc_clasificacion = 0;
        $scope.est_generales = result.data.est_generales;
        $scope.estados_resp = result.data.estados_resp;
        $scope.estados_hidratacion = result.data.estados_hidratacion;
        $scope.glasgows = result.data.glasgows;
        $scope.estados_conciencia = result.data.estados_conciencia;
        $scope.imc_clasificaciones = result.data.imc_clasificaciones;

        //diagnósticos
        $scope.tipos_diagnostico = result.data.tipos_diagnostico;
        $scope.tipos_contingencia = result.data.causas_ext;

        //medicamentos
        $scope.medicamentos = result.data.medicamentos;
    });

    //recuperando datos cita

    $scope.formulario = {};
    $scope.formulario.accion = "ver";
    $scope.formulario.cita = $routeParams.cita;
    $scope.motivo = {};
    $http({
        url: 'json/medicina.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function(result){
        //motivo
        $scope.motivo = result.data.motivo;
        $scope.antecedentes = result.data.antecedentes;
        $scope.examenfisico = result.data.examenfisico;
        //evoluciones
        $scope.evoluciones_cita = result.data.evoluciones_cita;
        //diagnosticos
        $scope.diagnosticos = result.data.diagnosticos;
        //medicamentos
        $scope.formulas = result.data.formulas;
        //ordenes
        $scope.ordenes = result.data.ordenes;
    });

    $scope.cerrarSesion = function(){
        $cookieStore.remove("usuario");
        $location.path('/ingreso');
    };
    if (typeof $scope.usuario === 'undefined') {
        $location.path('/ingreso');
    };
});

controllers.controller('seguimientoCTRL', function($scope, $http, $location, $cookieStore, $route){

    $scope.seccion = "Seguimiento";
    $http.get('json/fecha.php').then(function(result) {
        $scope.fecha = result.data.info;
    });

    $scope.usuario = $cookieStore.get('usuario');

    $scope.formulario = {motivo:''};
    $scope.inactivo = true;
    $scope.obligatorio = '';

    $scope.buscarPaciente = function(){
        $http.get('json/seguimiento.php?identificacion='+$scope.identificacion).then(function(result) {
            $scope.paciente = result.data.paciente;
            if ( $scope.paciente === null ) $scope.mensaje = "¡El usuario no se encuentra registrado en el sistema!";
            else {
                $scope.mensaje = "";
                $scope.formulario.id = $scope.paciente.id;
                $scope.paciente.nombre = $scope.paciente.primer_nombre+' '+$scope.paciente.segundo_nombre+' '+$scope.paciente.primer_apellido+' '+$scope.paciente.segundo_apellido;
            }
        });
    };

    $scope.registrar = function(){
        $http({
            url: 'json/seguimiento.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function(result){
            if(result.data.estado=='OK'){
                alert(result.data.mensaje);
                $route.reload();
            }else{
                alert(result.data.mensaje);
            }
        });
    };

    //validacion campos formulario
    $scope.$watch('formulario.motivo', function() {
        if ( $scope.formulario.motivo.length === 0 ){
            $scope.inactivo = true;
            $scope.obligatorio = 'has-warning';
        }else{
            $scope.inactivo = false;
            $scope.obligatorio = '';
        }
    }, true);

    $scope.cerrarSesion = function(){
        $cookieStore.remove("usuario");
        $location.path('/ingreso');
    };
    if (typeof $scope.usuario === 'undefined') {
        $location.path('/ingreso');
    };
});

controllers.controller('crearpacienteCTRL', function($scope, $http, $cookieStore, $route, $location){


    $http.get('json/fecha.php').then(function(result) {
        $scope.fecha = result.data.info;
    });

    $scope.usuario = $cookieStore.get('usuario');

    $scope.formulario = {};
    $scope.formulario.tipo_identificacion = "-1";
    $scope.formulario.departamento = "-1";
    $scope.formulario.ciudad = "-1";
    $scope.formulario.anio = "-1";
    $scope.formulario.mes = "-1";
    $scope.formulario.dia = "-1";
    $scope.formulario.estadocivil = "-1";
    $scope.formulario.entidad = "-1";
    $scope.formulario.tipovinculacion = "0";
    $scope.formulario.escolaridad = "0";
    $scope.formulario.paquete = "-1";
    $scope.formulario.afiliacion = "0";

    $scope.inactivo = true;

    //variable para mostrar o esconder el formulario de paciente
    $scope.paciente_creado = false;
    $scope.paciente_no_creado = false;
    $scope.paciente_mensaje = "";
    $scope.mostrar_paciente_mensaje = false;
    $scope.estado_paciente_mensaje = '';


    $http.get('json/paciente.php').then(function(result) {
        $scope.departamentos = result.data.departamentos;
        $scope.ciudades = result.data.ciudades;
        $scope.tipos_identificacion = result.data.tipos_identificacion;
        $scope.anios = result.data.fecha.anios;
        $scope.meses = result.data.fecha.meses;
        $scope.dias = result.data.fecha.dias;
        $scope.estados_civiles = result.data.estados_civiles;
        $scope.epss = result.data.epss;
        $scope.tipos_vinculacion = result.data.tipos_vinculacion;
        $scope.niveles_escolaridad = result.data.niveles_escolaridad;
        $scope.paquetes = result.data.paquetes;
        $scope.estados_afiliacion = result.data.afiliacion_estados;
    });

    $scope.crear = function(){
        $scope.formulario.accion = "crear";
        $http({
            url: 'json/paciente.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function(result){
            if ( result.data.estado == 'OK'){
                $scope.paciente_creado = true;
                $scope.paciente_mensaje = result.data.mensaje;
                $cookieStore.put('identificacion_paciente_nuevo', $scope.formulario.identificacion);
                $cookieStore.put('paciente_nuevo', true);
                $scope.paciente_no_creado = false;
            }else{
                $scope.paciente_no_creado = true;
                $scope.paciente_mensaje = result.data.mensaje;
            }
        });
    };

    $scope.citaPaciente = function(){
        $location.path('/recepcion/crearcita');
    };

    $scope.otroPaciente = function(){
        $route.reload();
    };

    //validacion campos formulario
    $scope.$watch('formulario.celacudiente', function() {
        if ( $scope.formulario.celacudiente.length === 0 ){
            $scope.inactivo = true;
        }else{
            $scope.inactivo = false;
        }
    }, true);

    $scope.cerrarSesion = function(){
        $cookieStore.remove("usuario");
        $location.path('/ingreso');
    };
    if (typeof $scope.usuario === 'undefined') {
        $location.path('/ingreso');
    };
});

controllers.controller('consultarpacienteCTRL', function($scope, $http, $location, $cookieStore){

    $scope.seccion = "Consultar Paciente";
    $http.get('json/fecha.php').then(function(result) {
        $scope.fecha = result.data.info;
    });

    $scope.usuario = $cookieStore.get('usuario');

    $scope.formulario = {};

    $scope.buscarPaciente = function(){
        //EMPAQUETAR TIPO DE ACCION E INFORMACIÓN NECESARIA
        $scope.formulario.accion = "resumen_paciente";
        $scope.formulario.identificacion = $scope.identificacion;
        $scope.mensaje = '';

        $http({
            url: 'json/paciente.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function(result){
            $scope.informacion_paciente = {};
            if ( result.data.estado == "ERROR" ){
                $scope.mensaje = result.data.mensaje;
            }else{
                $scope.mensaje = '';
                $scope.informacion_paciente = result.data.informacion_paciente;
                $scope.especialidades = result.data.especialidades;
            }
        });
    };
    //// Funcion creada por DAVID para redirigir a la historia clinica diligenciada
    $scope.verHC = function (_especialidad, _id_atendida){

        if ( _especialidad == 'Medicina General' ) _especialidad = 'medicina';
        else if ( _especialidad == 'Psicología') _especialidad = 'psicologia';
        else if ( _especialidad == 'Nutrición') _especialidad = 'nutricion';

        $location.path('/ver-'+_especialidad+'/'+_id_atendida);
    };

    $scope.printHC = function (_especialidad, _id_atendida){

        if ( _especialidad == 'Medicina General' ) _especialidad = 'medicina';
        else if ( _especialidad == 'Psicología' ) _especialidad = 'psicologia';
        else if ( _especialidad == 'Nutrición' ) _especialidad = 'nutricion';
        
        //alert(_especialidad+_id_atendida);
        //$location.path('/impr-'+_especialidad+'/'+_id_atendida);
        //$location.path('json/fpdf');
    };

    $scope.cerrarSesion = function(){
        $cookieStore.remove("usuario");
        $location.path('/ingreso');
    };
    if (typeof $scope.usuario === 'undefined') {
        $location.path('/ingreso');
    };
});

controllers.controller('miperfilCTRL', function($scope, $http, $location, $cookieStore){

    $http.get('json/fecha.php').then(function(result) {
        $scope.fecha = result.data.info;
    });

    var path = $location.path(); console.log(path);
    var seccion = path.split("/");
    $scope.seccion = seccion[2];

    if ( $scope.seccion == "datos" ){
        $scope.seccion = "Datos Personales";
    } else if ( $scope.seccion == "clave" ){
        $scope.seccion = "Mi Contraseña";
    }

    $scope.usuario = $cookieStore.get('usuario');

    $scope.formulario = {};
    $scope.formulario.accion = "paciente";
    $scope.formulario.seccion = $scope.seccion;
    $scope.formulario.usuario = $scope.usuario;

    /*$scope.formulario.clave1 = '';
    $scope.formulario.clave2 = '';
    $scope.formulario.clave_actual = '';*/

    //datos básicos del paciente y de la cita
    $http({
        url: 'json/paciente.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function(result){
        $scope.paciente = result.data.paciente;
    });

    //Validación de contraseñas
    $scope.validarClaves = function () {
        if ($scope.formulario.clave1 == $scope.formulario.clave2 && $scope.formulario.clave1 !== '' && $scope.formulario.clave2 !== '' && $scope.formulario.clave1.length > 7 && $scope.formulario.clave2.length > 7) {
            $scope.estilo_claves = {'background-color': '#dff0d8','border-color':'#3c763d'};
        } else {
            $scope.estilo_claves = {'background-color': 'rgba(242, 203, 203, 0.38)'};
        }
    };
    
    
    $scope.guardarClave = function(){
        if ($scope.paciente.clave == $scope.formulario.clave_actual && $scope.formulario.clave1 == $scope.formulario.clave2) {
            alert("guardarClave");
            $scope.mensaje = "";
            $scope.estado = "";
            /*$scope.formulario = {};
            $scope.formulario.accion = "cambiar_clave";
            $scope.formulario.clave1 = $scope.clave1;*/
            

            $http({
                url: 'json/paciente.php',
                method: 'POST',
                data: $scope.formulario,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function(result){
                $scope.mensaje = result.data.mensaje;
                $scope.estado = result.data.estado;
            });
        };

    }

    $scope.cerrarSesion = function(){
        $cookieStore.remove("usuario");
        $location.path('/ingreso');
    };
    if (typeof $scope.usuario === 'undefined') {
        $location.path('/ingreso');
    };
});

controllers.controller('crearcitaCTRL', function($scope, $http, $cookieStore, $location){

    $scope.seccion = "Crear Cita";
    $http.get('json/fecha.php').then(function(result) {
        $scope.fecha = result.data.info;
    });
    //barra usuario
    //$scope.usuario = $cookieStore.get('usuario');
    $scope.barra_usuario = $cookieStore.get('usuario');

    $scope.datepickerOptions = {
        format: 'yyyy/mm/dd',
        language: 'es',
        autoclose: true,
        weekStart: 0
    };

    $scope.agendada = false;

    $scope.formulario = {};

    $scope.buscarPaciente = function(){
        //EMPAQUETAR TIPO DE ACCION E INFORMACIÓN NECESARIA
        $scope.formulario.accion = "basica_paciente";
        $http({
            url: 'json/paciente.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function(result){

            if ( result.data.estado == "ERROR" ){
                $scope.mensaje = result.data.mensaje;
                $scope.informacion_paciente = [];
            }else{
                $scope.mensaje = '';
                $scope.informacion_paciente = result.data.info;
                $scope.usuario = {};
                $scope.usuario.id = result.data.info.id;
                $scope.primer_nombre = result.data.info.primer_nombre;
                $scope.segundo_nombre = result.data.info.segundo_nombre;
                $scope.primer_apellido = result.data.info.primer_apellido;
                $scope.segundo_apellido = result.data.info.segundo_apellido;
                $scope.ocupacion = result.data.info.ocupacion;
                $scope.edad_actual = result.data.info.edad_actual;
                //almacenar la respuesta del php y ... mostrarla en la vista
                //$scope.paciente = result.data.paciente;
            //
            }
        });
    };

    if ( $cookieStore.get('paciente_nuevo') == 1 ){
        $scope.formulario.identificacion = $cookieStore.get('identificacion_paciente_nuevo');
        $scope.buscarPaciente();
        //$cookieStore.put('paciente_nuevo', 0);
    }else{
        //datos básicos del paciente y de la cita
    }
    $scope.formulario = {};
    $scope.formulario.accion = 'iniciales';
    $http({
        url: 'json/citas.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function(result){
        $scope.especialidades = result.data.especialidades;
        $scope.profesionales = result.data.profesionales;
    });

    if ( $location.path() == '/miscitas/crear' ){
        $scope.titulo_seccion = 'Mis Citas';
        $scope.usuario = $cookieStore.get('usuario');
    } else if ( $location.path() == '/recepcion/crearcita' ){
        $scope.titulo_seccion = 'Asignar Cita';
    } else if ( $location.path() == '/profesional/crearcita'){
        $scope.titulo_seccion = 'Asignar Cita Profesional';
    }

    $scope.disponibles = function(){
        $scope.formulario.accion = 'disponibles';
        $http({
            url: 'json/citas.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function(result){
            $scope.citas = result.data.citas;
        });
    };

    $scope.agendar = function(){
        $scope.formulario.accion = 'agendar';
        $scope.formulario.usuario = $scope.usuario;
        $http({
            url: 'json/citas.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function(result){
            if ( result.data.estado == 'OK' ){
                $scope.agendada = true;
                $scope.mensaje = result.data.mensaje;
            }
        });
    };

    $scope.reiniciar = function(){
        $scope.formulario = {};
        $scope.formulario.accion = 'iniciales';
        $scope.agendada = false;
        $scope.mensaje = '';
    };

    $scope.$watch('formulario.fecha', function() {
        //var fecha = new Date($scope.formulario.fecha);
        $scope.citas = {};
        $scope.disponibles();
    }, true);

    $scope.cerrarSesion = function(){
        $cookieStore.remove("usuario");
        $location.path('/ingreso');
    };
    if (typeof $scope.usuario === 'undefined') {
        $location.path('/ingreso');
    };
});

controllers.controller('miscitasCTRL', function($scope, $http, $cookieStore, $location){

    $scope.usuario = $cookieStore.get('usuario');

    $scope.seccion = "Mis Citas";
    $http.get('json/fecha.php').then(function(result) {
        $scope.fecha = result.data.info;
    });
    //datos básicos del paciente y de la cita
    $scope.formulario = {};
    $scope.formulario.accion = 'agendadas';
    $scope.formulario.id_paciente = $scope.usuario.id;
    $http({
        url: 'json/citas.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function(result){
        $scope.proxima_cita = result.data.proxima_cita;
        $scope.citas = result.data.citas;
    });

    $scope.cerrarSesion = function(){
        $cookieStore.remove("usuario");
        $location.path('/ingreso');
    };
    if (typeof $scope.usuario === 'undefined') {
        $location.path('/ingreso');
    };
});

controllers.controller('dateCTRL', ['$scope', function($scope){
    $scope.open = function($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened = true;
    };
}]);
