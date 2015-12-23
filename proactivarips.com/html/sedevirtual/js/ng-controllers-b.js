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
                    {nombre:'Asignar Cita',vinculo:'#/recepcion/crearcita'} //////****************************************
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

    $scope.seccion = "Profesional";
    $http.get('json/fecha.php').then(function(result) {
        $scope.fecha = result.data.info;
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

    $scope.cerrarSesion = function(){
        $cookieStore.remove("user");
        $location.path('/ingreso');
    };
});
///No tocar el VER
controllers.controller('psicologiaVerCTRL', function($scope, $http, $cookieStore, $routeParams){

    $scope.vista = true;

    $scope.seccion = "Profesional";
    $http.get('json/fecha.php').then(function(result) {
        $scope.fecha = result.data.info;
    });

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

        /*$scope.paciente = result.data.paciente;
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
        $scope.formulario.edad = $scope.paciente.edad;
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
        $scope.formulario.afiliacion = $scope.paciente.sl_estado_afiliacion;*/

    });

 ////// DAVID INTENTÓ HACER ESTO
    $scope.mensaje = {};
    $scope.mensaje.accion = "ver";
    $scope.mensaje.cita = $routeParams.cita;
    $http({
        url: 'json/psicologia.php',
        method: 'POST',
        data: $scope.mensaje,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function(result){
        $scope.basicos = result.data.basicos;
        $scope.emocionalidad = result.data.emocionalidad;
        $scope.recomendaciones = result.data.recomendaciones;
    });

    $scope.cerrarSesion = function(){
        $cookieStore.remove("user");
        $location.path('/ingreso');
    };
});

////NUTRICION
controllers.controller('nutricionCTRL', function($scope, $http, $location, $cookieStore, $route, $routeParams){

    /*if(typeof $scope.user === "undefined") {
        $location.path('/ingreso');
    }*/

    $scope.seccion = "Profesional";
    $http.get('json/fecha.php').then(function(result) {
        $scope.fecha = result.data.info;
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

    $scope.cerrarSesion = function(){
        $cookieStore.remove("user");
        $location.path('/ingreso');
    };
});
///No tocar el VER
controllers.controller('nutricionVerCTRL', function($scope, $http, $location, $cookieStore, $route, $routeParams){});
////////