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

////PSICOLOGIA
controllers.controller('psicologiaCTRL', function($scope, $http, $location, $cookieStore, $route, $routeParams){

    $scope.vista = false;

    $scope.diagnostico = {};

    $scope.seccion = "Profesional";
    $http.get('json/fecha.php').then(function(result) {
        $scope.fecha = result.data.info;
        $scope.fecha_hoy = result.data.info;
    });

    //datos básicos del doctor
    $scope.usuario = $cookieStore.get('usuario');

    //datos básicos del paciente y de la cita
    $scope.formulario = {};
    $scope.formulario.cita = $routeParams.cita;
    $http({
        url: 'json/psicologia.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function(result){
        $scope.datosInicialesPestañas = function(){
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

            $scope.formulario = result.data.paciente;
            $scope.formulario.cita = $routeParams.cita;
        //diagnósticos
            $scope.tipos_diagnostico = result.data.tipos_diagnostico;
            $scope.tipos_contingencia = result.data.causas_ext;
        };
        $scope.datosInicialesPestañas();

        //funcion para enviar y guardar los datos de las pestañas
        $scope.accion = function(_nombreAccion){
            //armar paquete
            $scope.mensaje = "";
            $scope.paquete = {};
            $scope.paquete.accion = _nombreAccion;
            $scope.paquete.formulario = $scope.formulario;
            //enviar paquete
            $http({
                url: 'json/psicologia.php',
                method: 'POST',
                data: $scope.paquete,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function(result){
                $scope.mensaje = result.data.mensaje;
            });
        };

    });
    
    ///////diagnostico

    $scope.cies = {};
    $scope.bl_buscado = false;
    $scope.buscarCies = function(_palabra){
        $http.get('json/medicina.php?listados=1&palabra='+_palabra).then(function(result) {
            $scope.bl_buscado = true;
            $scope.cies = result.data.cies;
            $scope.diagnostico.nombre = _palabra;
        });
    };

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
            //console.log(result.data.estado);
            $scope.mensaje = result.data.mensaje;
            $scope.mensaje_diagnosticos = 'ok';
            //$scope.mensaje_examenfisico = result.data.estado;
        });
    };

    ////////FIN DIAGNOSTICOS

    //// Borra el mensaje "Datos almacenados exitosamente." según se navegue entre tabs de la HC.
    $scope.reset = function(){
        $scope.mensaje = '';
    }

    $scope.cerrarSesion = function(){
        $cookieStore.remove("usuario");
        $location.path('/ingreso');
    };
    if (typeof $scope.usuario === 'undefined') {
        $location.path('/ingreso');
    };
});
///No tocar el VER
controllers.controller('psicologiaVerCTRL', function($scope, $http, $cookieStore, $routeParams){

    $scope.vista = true;

    $scope.diagnostico = {};

    $scope.seccion = "Profesional";
    $http.get('json/fecha.php').then(function(result) {
        $scope.fecha = result.data.info;
        $scope.fecha_hoy = result.data.info;
    });

    $scope.usuario = $cookieStore.get('usuario');

    //datos básicos del paciente y de la cita
    $scope.formulario = {};
    $scope.formulario.cita = $routeParams.cita;
    $http({
        url: 'json/psicologia.php',
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
        $scope.formulario.primer_nombre = $scope.paciente.primer_nombre;
        $scope.formulario.primer_apellido = $scope.paciente.primer_apellido;
        $scope.formulario.segundo_nombre = $scope.paciente.segundo_nombre;
        $scope.formulario.segundo_apellido = $scope.paciente.segundo_apellido;
        $scope.formulario.rd_tipo_identificacion = $scope.paciente.rd_tipo_identificacion;
        $scope.formulario.numero_identificacion = $scope.paciente.numero_identificacion;
        $scope.formulario.da_nacimiento = {};
        $scope.formulario.da_nacimiento.dia = $scope.paciente.da_nacimiento.dia;
        $scope.formulario.da_nacimiento.mes = $scope.paciente.da_nacimiento.mes;
        $scope.formulario.da_nacimiento.anio = $scope.paciente.da_nacimiento.anio;
        $scope.formulario.edad_actual = $scope.paciente.edad_actual;
        $scope.formulario.sl_estado_civil = $scope.paciente.sl_estado_civil;
        $scope.formulario.sl_departamento = $scope.paciente.sl_departamento;
        $scope.formulario.sl_municipio = $scope.paciente.sl_municipio;
        $scope.formulario.ocupacion = $scope.paciente.ocupacion;
        $scope.formulario.sl_escolaridad = $scope.paciente.sl_escolaridad;
        $scope.formulario.rd_genero = $scope.paciente.rd_genero;
        $scope.formulario.direccion = $scope.paciente.direccion;
        $scope.formulario.telefono = $scope.paciente.telefono;
        $scope.formulario.celular = $scope.paciente.celular;
        $scope.formulario.acudiente = $scope.paciente.acudiente;
        $scope.formulario.acudiente_parentesco = $scope.paciente.acudiente_parentesco;
        $scope.formulario.acudiente_celular = $scope.paciente.acudiente_celular;
        $scope.formulario.sl_eps = $scope.paciente.sl_eps;
        $scope.formulario.servicios = $scope.paciente.servicios;
        $scope.formulario.sl_tipo_vinculacion = $scope.paciente.sl_tipo_vinculacion;
        $scope.formulario.sl_estado_afiliacion = $scope.paciente.sl_estado_afiliacion;

    });

 ////// DAVID HIZO ESTO
    $scope.formulario = {};
    $scope.formulario.accion = "ver";
    $scope.formulario.cita = $routeParams.cita;
    $http({
        url: 'json/psicologia.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function(result){
        //$scope.basicos = result.data.basicos;
        $scope.formulario.ajuste = result.data.ajuste;
        $scope.formulario.analisis_prof = result.data.analisis_prof;
        $scope.formulario.rd_ansiedad = result.data.ansiedad;
        $scope.formulario.rd_tristeza = result.data.tristeza;
        $scope.formulario.rd_irritable = result.data.irritable;
        $scope.formulario.rd_dolor = result.data.dolor;
        $scope.formulario.acompanante = result.data.acompanante;
        $scope.formulario.motivo = result.data.motivo;
        $scope.formulario.observaciones = result.data.observaciones;
        $scope.formulario.recomendaciones = result.data.recomendaciones;

        $scope.diagnostico_cita = result.data.diagnostico_cita;
    });

    $scope.cerrarSesion = function(){
        $cookieStore.remove("usuario");
        $location.path('/ingreso');
    };
    if (typeof $scope.usuario === 'undefined') {
        $location.path('/ingreso');
    };
});

////NUTRICION
controllers.controller('nutricionCTRL', function($scope, $http, $location, $cookieStore, $route, $routeParams){

    $scope.diagnostico = {};

    $scope.seccion = "Profesional";
    $http.get('json/fecha.php').then(function(result) {
        $scope.fecha = result.data.info;
        $scope.fecha_hoy = result.data.info;
    });

    //datos básicos del doctor
    $scope.usuario = $cookieStore.get('usuario');

    //datos básicos del paciente y de la cita
    $scope.formulario = {};
    $scope.formulario.cita = $routeParams.cita;
    $http({
        url: 'json/nutricion.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function(result){
        $scope.datosInicialesPestañas = function(){
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

            $scope.formulario = result.data.paciente;
            $scope.formulario.cita = $routeParams.cita;

            //diagnósticos
            $scope.tipos_diagnostico = result.data.tipos_diagnostico;
            $scope.tipos_contingencia = result.data.causas_ext;
            //IMC
            $scope.imc_clasificaciones = result.data.imc_clasificaciones;
        }
        $scope.datosInicialesPestañas();

    //funcion para enviar y guardar los datos de las pestañas
    $scope.accion = function(_nombreAccion){
        //Armar el paquete para el envío de datos
        $scope.mensaje = "";
        $scope.paquete = {};
        $scope.paquete.accion = _nombreAccion;
        $scope.paquete.formulario = $scope.formulario;
        //Enviar paquete
        $http({
                url: 'json/nutricion.php',
                method: 'POST',
                data: $scope.paquete,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function(result){
                $scope.mensaje = result.data.mensaje;
        });
    }
    });
    
    /////// Examen Físico

    $scope.calculoIMC = function(){
        $scope.formulario.imc = $scope.formulario.peso/($scope.formulario.talla/100*$scope.formulario.talla/100);

        if ($scope.formulario.imc < 16) {
            $scope.formulario.imc_clasificacion = 1;
        }else if ($scope.formulario.imc >= 16 && $scope.formulario.imc <= 16.9) {
            $scope.formulario.imc_clasificacion = 2;
        }else if ( $scope.formulario.imc >= 17 && $scope.formulario.imc <= 17.5 ){
                $scope.formulario.imc_clasificacion = 3;
        }else if ( $scope.formulario.imc >= 17.6 && $scope.formulario.imc <= 17.9 ){
            $scope.formulario.imc_clasificacion = 4;
        }else if ( $scope.formulario.imc >= 18 && $scope.formulario.imc <= 24.9 ){
            $scope.formulario.imc_clasificacion = 5;
        }else if ( $scope.formulario.imc >= 25 && $scope.formulario.imc <= 26.9 ){
            $scope.formulario.imc_clasificacion = 6;
        }else if ( $scope.formulario.imc >= 27 && $scope.formulario.imc <= 29.9 ){
            $scope.formulario.imc_clasificacion = 7;
        }else if ( $scope.formulario.imc >= 30 && $scope.formulario.imc <= 39.9 ){
            $scope.formulario.imc_clasificacion = 8;
        }else if ( $scope.formulario.imc > 40 ){
            $scope.formulario.imc_clasificacion = 9;
        }else{
            $scope.formulario.imc_clasificacion = 0;
        }
    };



    ///////diagnostico

    $scope.cies = {};
    $scope.bl_buscado = false;
    $scope.buscarCies = function(_palabra){
        $http.get('json/medicina.php?listados=1&palabra='+_palabra).then(function(result) {
            $scope.bl_buscado = true;
            $scope.cies = result.data.cies;
            $scope.diagnostico.nombre = _palabra;
        });
    };

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
            $scope.mensaje_diagnosticos = 'ok';
        });
    };

    ////////FIN DIAGNOSTICOS

    //// Borra el mensaje "Datos almacenados exitosamente." según se navegue entre tabs de la HC.
    $scope.reset = function(){
        $scope.mensaje = '';
    }

    $scope.cerrarSesion = function(){
        $cookieStore.remove("usuario");
        $location.path('/ingreso');
    };
    if (typeof $scope.usuario === 'undefined') {
        $location.path('/ingreso');
    };
});
///No tocar el VER
controllers.controller('nutricionVerCTRL', function($scope, $http, $location, $cookieStore, $route, $routeParams){

    $scope.vista = true;
    $scope.diagnostico = {};

    $http.get('json/fecha.php').then(function(result) {
        $scope.fecha = result.data.info;
        $scope.fecha_hoy = result.data.info;
    });
});
////////