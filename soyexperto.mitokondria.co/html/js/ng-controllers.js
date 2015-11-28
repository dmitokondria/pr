var controllers = angular.module('soyexperto.controllers', []);

controllers.controller('indexCTRL', function ($http, $location, $scope, $cookieStore, $modal) {

    //mostrar video o no
    /*if ($cookieStore.get('video')) {
    } else {
        $modal.open({
            templateUrl: 'views/MD_video.html',
            controller: 'MD_videoCTRL',
            resolve: {
            }
        });
    }*/

    //reseteando frase búsqueda
    $cookieStore.remove('criterio');

    /* http://jsfiddle.net/CXnKD/2/ */
    $http.get('json/busquedas.php').then(function (result) {
        $scope.p = {};
        $scope.busqueda = {};
        $scope.p.opciones = result.data.busquedas;

        // Auto complete preload saved value
        //$scope.busqueda.opciones = $scope.p.opciones[0];
        $scope.buscar = function () {

            if ( $scope.busqueda.opciones !== "" ){
                $scope.criterio = {'accion': 'buscar', 'nueva': 0, 'frase': '', 'id': -1};
                //Cómo se si es una nueva o se eligió del listado
                if ($scope.busqueda.opciones.id) {
                    $scope.criterio.id = $scope.busqueda.opciones.id;
                    $scope.criterio.frase = $scope.busqueda.opciones.frase.toLowerCase();
                } else {
                    $scope.criterio.nueva = 1;
                    $scope.criterio.frase = $scope.busqueda.opciones.toLowerCase();
                }
                $cookieStore.put('criterio', $scope.criterio);
                $location.path("/resultados/10/1");
            }
        };
    });

    //después de registrarse como nuevo
    $scope.usuario = $cookieStore.get('usuario');
    if ($scope.usuario) {
        $scope.formulario = {};
        $scope.formulario.accion = 'datos_iniciales';
        $scope.formulario.user = $scope.usuario;

        $http({
            url: 'json/perfil.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            $scope.formulario = result.data.usuario;
            $cookieStore.put('avatar', $scope.formulario.imagen);
            $cookieStore.put('valoracion', result.data.usuario.valoracion);
            $scope.tipos_telefono = result.data.tipos_telefono;
        });
    } else {

    }
});

controllers.controller('MD_videoCTRL', function ($http, $scope, $modalInstance, $cookieStore, $route) {

    $cookieStore.put('video', true);

    $scope.cancelar = function () {
        $modalInstance.dismiss();
    };
});

controllers.controller('menuCTRL', function ($http, $scope, $location, $modal, $cookieStore, sessionService) {
    $scope.perfil = false;

    $scope.$on('$routeChangeSuccess', function () {

        $scope.usuario_session = "";
        $http.get('json/sesion_revisar.php').then(function (result) {

            $scope.usuario_sesion = result.data;
            $cookieStore.put('usuario_sesion', $scope.usuario_sesion);

            $scope.usuario_cookie = $cookieStore.get("usuario");

            if ( $scope.usuario_sesion == 'authentified' ){

                var url = $location.path();
                if (url.indexOf("perfil") == '1') {
                    var url_split = url.split('/');
                    $scope.usuario = url_split[2];
                    $scope.usuario = $scope.usuario.charAt(0).toUpperCase() + $scope.usuario.slice(1);
                    $scope.perfil = true;
                }

                $scope.usuario = $scope.usuario_cookie.nombre;
                $scope.menu = 'views/menu_usuario.html';
            }else{
                $cookieStore.put('usuario_sesion', 'timeout');
                $scope.menu = 'views/menu_general.html';
                $scope.login = function () {
                    $modal.open({
                        templateUrl: 'views/MD_login.html',
                        controller: 'MD_loginCTRL',
                        resolve: {
                        }
                    });
                };
            }

            $scope.cerrarSesion = function () {
                $scope.perfil = false;
                $cookieStore.put('usuario', 'cerrar_sesion');
                $scope.usuario_cookie = false;
                sessionService.destroy('uid');
                $location.path("/");
            };

            $scope.verPerfil = function () {
                $scope.usuario_cookie = $cookieStore.get("usuario");
                $location.path('/perfil/' + $scope.usuario_cookie.usuario);
            };
        });
    });
});

controllers.controller('MD_sesion_expiradaCTRL', function ($http, $scope, $location, $modalInstance){
    $scope.cancelar = function () {
        $modalInstance.dismiss();
        $location.path('/index');
    };
});

controllers.controller('registroCTRL', function ($http, $scope, $location, $routeParams, $modal, $cookieStore) {

    //$scope.usuario_libre = 'has-success';
    $scope.formulario = {};
    /*$scope.formulario.tipo_identificacion = -1;
     $scope.formulario.pais = -1;
     $scope.formulario.ciudad = -1;
     $scope.formulario.telefono = {};
     $scope.formulario.telefono.tipo = -1;*/
    $scope.formulario.clave1 = '';
    $scope.formulario.clave2 = '';

    $scope.mensaje = {};
    $scope.mensaje.formulario = "";
    $scope.mensaje.usuario = "";
    $scope.mensaje.descripcion = "";
    $scope.formulario.referido_por_nuevo = 0;
    $scope.clase_referido_por = "ng-valid";

    $scope.referidos_por_posibles = {};

    //si viene de comentar servicio pero no se ha registrado
    $scope.url_servicio_visitado = $cookieStore.get('url_servicio_visitado');
    if (typeof $routeParams.dominio !== 'undefined' && $routeParams.dominio != 'registro'){
        $scope.formulario.referido_por = $routeParams.dominio;
        $scope.formulario.referido_por_nuevo = 1;
        $scope.existe_referido_por = true;
    }else if (typeof $routeParams.referente !== 'undefined' && typeof $routeParams.referido !== 'undefined') {//Si llega referente y referido, traigo el listado de referentes para que el usuario elija
        $scope.formulario.accion = 'referentes';
        $scope.formulario.referente = $routeParams.referente;
        $scope.formulario.referido = $routeParams.referido;
        $scope.formulario.email = $routeParams.referido;
        $scope.formulario.referido_por = $routeParams.referente;
        $scope.existe_referido_por = true;
        $http({
            url: 'json/registro.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            $scope.referidos_por_posibles = result.data.referentes;
            $scope.info_referente = result.data.info_referente;
            $scope.formulario.referente = $scope.referentes[$scope.posicionUsuario($scope.referentes, result.data.info_referente.usuario)];
            $scope.paises = result.data.paises;
            $scope.ciudades = result.data.ciudades;
            $scope.tipos_identificacion = result.data.tipos_identificacion;
            $scope.tipos_telefono = result.data.tipos_telefono;
        });
    } else {
        //DATOS INICIALES
        $http({
            url: 'json/registro.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            $scope.paises = result.data.paises;
            $scope.ciudades = result.data.ciudades;
            $scope.tipos_identificacion = result.data.tipos_identificacion;
            $scope.tipos_telefono = result.data.tipos_telefono;
            $scope.contrato = result.data.contrato;
        });
    }

    //ENVIANDO DATOS A PHP
    $scope.registrar = function () {
        $scope.formulario.accion = 'registro';
        $http({
            url: 'json/registro.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            if (result.data.status == 'OK') {
                $cookieStore.put('usuario', {id: result.data.user.id, nombre: result.data.user.nombre, usuario: result.data.user.usuario});
                if ($scope.url_servicio_visitado) {
                    $location.path($scope.url_servicio_visitado);
                } else {
                    $location.path('/');
                }

            } else if (result.data.status == 'error_usuario') {
                $scope.mensaje.usuario = "error";
                $scope.mensaje.descripcion = result.data.mensaje;
            } else if (result.data.status == 'error_cedula') {
                $scope.mensaje.formulario = "error";
                $scope.mensaje.descripcion = result.data.mensaje;
            } else if (result.data.status == 'Error') {
                $scope.mensaje.formulario = "error";
                $scope.mensaje.descripcion = result.data.message;
            }
        });
    };

    //TERMINOS Y CONDICIONES
    $scope.terminos = function () {
        $modal.open({
            templateUrl: 'views/MD_terminos.html',
            controller: 'MD_terminosCTRL',
            resolve: {
            }
        });
    };

    $scope.posicionUsuario = function (_vector, _usuario) {
        for (var posicion in _vector) {
            if (_vector[posicion].usuario == _usuario) {
                return posicion;
            }
        }
    };

    $scope.referenteAutomatico = function(){
        $scope.formulario.accion = "automatico";
        $http({
            url: 'json/registro.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            $scope.formulario.referido_por = result.data.dominio;
            $scope.formulario.referido_por_nuevo = 0;
            $scope.clase_referido_por = "ng-valid";
            $scope.existe_referido_por = true;
        });
    };

    $scope.es_real = true;
    $scope.existe_bd = false;
    $scope.validarCorreo = function (_mail) {
        $scope.formulario.email = _mail;
        $scope.verificando_mail = true;
        $http({
            url: 'json/verificar_correo.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            $scope.es_real = result.data.es_real;
            $scope.existe_bd = result.data.existe_bd;
            if ($scope.es_real === false || $scope.existe_bd === true) {
                $scope.fondoInput = {'background-color': 'rgba(242, 203, 203, 0.38)', 'border-color': '#b94a48'};
            } else {
                $scope.fondoInput = {};
            }
            $scope.verificando_mail = false;
        });
    };

    //correo y usuario
    $scope.validarClaves = function () {
        //contraseñas iguales
        if ($scope.formulario.clave1 == $scope.formulario.clave2 && $scope.formulario.clave1 !== '' && $scope.formulario.clave2 !== '' && $scope.formulario.clave1.length > 7 && $scope.formulario.clave2.length > 7) {
            $scope.estilo_claves = {'background-color': 'rgba(206, 235, 206, 0.58)'};
        } else {
            $scope.estilo_claves = {'background-color': 'rgba(242, 203, 203, 0.38)'};
        }
    };
});

controllers.controller('MD_terminosCTRL', function ($http, $scope, $location, $modalInstance) {

    $scope.formulario = {};
    $scope.formulario.accion = "contrato";
    $scope.formulario.id_contrato = 1;

    $http({
        url: 'json/registro.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function (result) {
        $scope.contrato = result.data.contrato;
    });

    $scope.cerrarAlerta = function (index) {
        $scope.alertas.splice(index, 1);
    };
    $scope.cancelar = function () {
        $modalInstance.dismiss();
    };
});

////.......OK +++++ vinculo servicio (dominio/titulo_servicio)
controllers.controller('resultadosCTRL', function ($http, $scope, $route, $cookieStore, $routeParams, $location) {

    $scope.criterio = $cookieStore.get('criterio');

    /* http://jsfiddle.net/CXnKD/2/ */
    $http.get('json/busquedas.php').then(function (result) {
        $scope.p = {};
        $scope.busqueda = {};
        $scope.busqueda.opciones = $scope.criterio.frase;
        $scope.p.opciones = result.data.busquedas;

        // Auto complete preload saved value
        //$scope.busqueda.opciones = $scope.p.opciones[0];
    });

    $scope.formulario = {};
    $scope.formulario.criterio = $scope.criterio;
    $scope.formulario.items = $routeParams.items;
    $scope.formulario.pagina = $routeParams.pagina;

    $http({
        url: 'json/resultados.php', /*+++++ dominio para el servicio */
        data: $scope.formulario,
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).success(function (result) {
        $scope.resultados = result.resultados;
        $scope.totalResultados = result.resultados_total;
        $scope.items = $routeParams.items;
        $scope.pagina = $routeParams.pagina;
        //$log.log('Se eliminó en ' + $scope.modelo + ' el id:' + _entidad.id);
        //$route.reload();
    });

    $scope.cambioPagina = function (_pagina) {
        //alert("/resultados/" + _pagina + "/" + $routeParams.items);
        //$location.path("/resultados/"+_pagina+"/"+$routeParams.items);
    };

    $scope.navegar = function () {
        $location.path("/resultados/" + $scope.items + "/" + $scope.pagina);
    };

    $scope.buscar = function () {
        if ( $scope.busqueda.opciones !== "" ){
            $scope.criterio = {'accion': 'buscar', 'nueva': 0, 'frase': '', 'id': -1};
            //Cómo se si es una nueva o se eligió del listado
            if ($scope.busqueda.opciones.id) {
                $scope.criterio.id = $scope.busqueda.opciones.id;
                $scope.criterio.frase = $scope.busqueda.opciones.frase.toLowerCase();
            } else {
                $scope.criterio.nueva = 1;
                $scope.criterio.frase = $scope.busqueda.opciones.toLowerCase();
            }
            $cookieStore.put('criterio', $scope.criterio);
            $route.reload();
        }
    };

    $scope.verServicio = function (_servicio) {
        $cookieStore.put('servicio', _servicio);
        $location.path(_servicio.dominio + '/' + _servicio.titulo);
    };
});

////.......OK +++++ se cambia el vínculo a (dominio/titulo_servicio), si no tiene un servicio en cookie, debe buscarlo en el php
controllers.controller('usuarioServicioCTRL', function ($http, $scope, $location, $routeParams, $cookieStore, $modal) {

    $scope.dias_semana = {1: 'Lunes', 2: 'Martes', 3: 'Miércoles', 4: 'Jueves', 5: 'Viernes', 6: 'Sábado', 7: 'Domingo'};

    $scope.usuario = $cookieStore.get("usuario");

    if ($scope.usuario && $scope.usuario.dominio != $routeParams.dominio) {
        $scope.logeado = true;
    } else {
        $scope.logeado = false;
    }

    $scope.formulario = {};
    $scope.formulario.accion = "visita";
    $scope.formulario.dominio = $routeParams.dominio;

    $scope.servicio_cookie = $cookieStore.get("servicio");
    /*if ( $scope.servicio_cookie ) $scope.formulario.id_servicio = $scope.servicio_cookie.id;
     else*/ $scope.formulario.titulo_servicio = $routeParams.servicio;

    $http({
        url: 'json/servicio.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function (result) {
        $scope.servicio = result.data.servicio;
        if ( $scope.servicio.horarios[0].horas == '01:00 am - 12:59 am' ) $scope.servicio.horarios[0].horas = '24 horas';
        $scope.calificacion = result.data.calificacion;
        $scope.calificacion_valor = result.data.calificacion_valor;
        $scope.comentarios = result.data.comentarios;
    });

    $scope.comentar = function () {
        $cookieStore.put('url_servicio_visitado', $location.path());
        if ($scope.logeado) {
            $modal.open({
                templateUrl: 'views/MD_comentar.html',
                controller: 'MD_comentarCTRL',
                resolve: {
                    parametros: function () {
                        return {id_servicio: $scope.servicio.id, id_autor: $cookieStore.get("usuario").id};
                    }
                }
            });
        } else {
            $scope.login();
        }
    };

    $scope.login = function () {
        $modal.open({
            templateUrl: 'views/MD_login.html',
            controller: 'MD_loginCTRL',
            resolve: {
            }
        });
    };
});

controllers.controller('MD_comentarCTRL', function ($http, $scope, $modalInstance, $cookieStore, $location, $route, parametros) {

    $scope.parametros = parametros;

    $scope.usuario = $cookieStore.get("usuario");
    $scope.formulario = {};

    $scope.enviarComentario = function () {
        $scope.formulario.accion = "crear";
        $scope.formulario.id_autor = $scope.usuario.id;
        $scope.formulario.id_servicio = $scope.parametros.id_servicio;
        $http({
            url: 'json/comentario.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            if (result.data.estado == 'Ok') {
                $modalInstance.dismiss();
                $route.reload();
            }
        });
    };

    $scope.cerrarAlerta = function (index) {
        $scope.alertas.splice(index, 1);
    };
    $scope.cancelar = function () {
        $modalInstance.dismiss();
    };
});

controllers.controller('MD_loginCTRL', function ($http, $scope, $modalInstance, $cookieStore, $location, $route, sessionService) {

    $cookieStore.put('usuario', false);

    $scope.enviar = function () {
        $scope.formulario.accion = 'ingresar';
        $http({
            url: 'json/usuario.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            $scope.message = result.data.message;
            if (result.data.status == 'OK') {
                $modalInstance.dismiss();
                sessionService.set('uid', result.data.uid);
                $cookieStore.put('usuario', {id: result.data.id, nombre: result.data.nombre, usuario: result.data.usuario, dominio: result.data.dominio});
                $route.reload();
            }
        });
    };
    $scope.cerrarAlerta = function (index) {
        $scope.alertas.splice(index, 1);
    };
    $scope.cancelar = function () {
        $modalInstance.dismiss();
    };
    $scope.registro_rapido = function () {
        $modalInstance.dismiss();
        $location.path('/registro');
    };
});

controllers.controller('perfilCTRL', function ($http, $scope, $cookieStore, $routeParams, $modal) {

    $scope.usuario = $routeParams.usuario;
    $scope.user = $cookieStore.get('usuario');
    $scope.formulario = {};
    $scope.formulario.accion = 'datos_iniciales';
    $scope.formulario.user = $scope.user;
    $scope.formulario.telefonos = [{telefono: '', tipo: 0}];
    $scope.tipos_telefono = {};
    //$scope.tipos_telefono = [{id: '0', nombre: 'Seleccione una opción'}, {id: '1', nombre: 'fijo'}, {id: '2', nombre: 'celular'}];

    $scope.mensaje = {};
    $scope.mensaje.formulario = "";
    $scope.mensaje.usuario = "";
    $scope.mensaje.descripcion = "";

    $http({
        url: 'json/perfil.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function (result) {
        $scope.formulario = result.data.usuario;
        $scope.formulario.dominio = result.data.usuario.dominio;
        $scope.formulario.referido_por_nuevo = 0;
        $cookieStore.put('avatar', $scope.formulario.imagen);
        $cookieStore.put('valoracion', result.data.usuario.valoracion);
        $scope.tipos_telefono = result.data.tipos_telefono;
        $scope.tipos_documento = result.data.tipos_documento;
        $scope.referidos_por_posibles = result.data.referidos_por_posibles;
        if ( result.data.usuario.referido_por === null ) {
            $scope.existe_referido_por = false;
        }else {
            $scope.existe_referido_por = true;
            $scope.formulario.referido_por = result.data.usuario.referido_por;
        }
        if ( result.data.usuario.identificacion === null ) {
            $scope.existe_cedula = false;
        }else {
            $scope.existe_cedula = true;
            $scope.formulario.identificacion = result.data.usuario.identificacion;
            $scope.formulario.tipo_identificacion = result.data.usuario.tipo_identificacion;
        }
        //existe plan para dominio
        $scope.no_existe_plan = 1;
        if (result.data.usuario.id_plan !== null){
            $scope.no_existe_plan = 0;
        }

        $scope.formulario.dominio_nuevo = 0;
        $scope.formulario.identificacion_nuevo = 0;
    });

    //$scope.busqueda.opciones = $scope.p.opciones[0];
    $scope.seleccionar = function () {
        //Cómo se si es una nueva o se eligió del listado
        if ($scope.formulario.referido_por.id) {
            //$scope.criterio.id = $scope.busqueda.opciones.id;
            //$scope.criterio.frase = $scope.busqueda.opciones.frase.toLowerCase();
        } else {
            //$scope.criterio.nueva = 1;
            //$scope.criterio.frase = $scope.busqueda.opciones.toLowerCase();
        }
        $cookieStore.put('criterio', $scope.criterio);

        $location.path("/resultados/10/1");
    };

    $scope.clase_dominio = 'ng-valid';
    $scope.validarDominio = function (_dominio) {
        $scope.formulario.dominio_nuevo = 1;
        $scope.formulario_dominio = {};
        $scope.formulario_dominio.accion = "dominio_libre";
        $scope.formulario_dominio.id = $scope.user.id;
        $scope.formulario_dominio.dominio = _dominio;
        $http({
            url: 'json/perfil.php',
            method: 'POST',
            data: $scope.formulario_dominio,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            if (result.data.status == "Ok"){
                $scope.clase_dominio = "ng-valid";
                $scope.mensaje.type = "success";
            }else{
                $scope.clase_dominio = "ng-invalid";
                $scope.formulario.dominio_nuevo = 0;
                $scope.mensaje.type = "alert";
            }
            $scope.mensaje.descripcion = result.data.message;
        });
    };

    $scope.mensaje = {};
    $scope.clase_existe_cedula = "ng-valid";
    /*$scope.mensaje.type = "danger";
    $scope.mensaje.descripcion = "....";*/
    $scope.editarCampos = function () {
        $scope.formulario.accion = "editar_campos";
        //if ( $scope.formulario.referido_por_nuevo = ""; )
        $http({
            url: 'json/perfil.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            $scope.mensaje = {};
            if (result.data.status == "Error" && result.data.message == 'El correo de referido ingresado no existe en nuestro sistema'){
                $scope.mensaje.type = "danger";
                $scope.clase_referido_por = "ng-invalid";
            }else if (result.data.status == "Ok" && result.data.message == 'El referido ha sido actualizado correctamente'){
                $scope.mensaje.type = "success";
                $scope.clase_referido_por = "ng-valid";
                $scope.existe_referido_por = true;
            }else if (result.data.status == "Ok" && result.data.message == 'La información de identificación ha sida actualizada correctamente'){
                $scope.mensaje.type = "success";
                $scope.clase_existe_cedula = "ng-valid";
                $scope.existe_cedula = true;
            }else if (result.data.status == "Error" && result.data.message == 'La información suministrada es incompleta' || result.data.status == "Error" && result.data.message == 'La información suministrada es incorrecta'){
                $scope.mensaje.type = "danger";
                $scope.existe_cedula = false;
            }else if ( result.data.status == "Ok" && result.data.message == 'Dominio actualizado correctamente!' ){
                $scope.mensaje.type = "success";
            }else if ( result.data.status == "Error" && result.data.message == 'información básica usuario no se pudo guardar!' ){
                $scope.mensaje.type = "danger";
            }else{
                $scope.mensaje.type = "success";
            }
            $scope.mensaje.descripcion = result.data.message;
            //if ( $scope.formulario.identificacion !== '' ) $scope.existe_cedula = true;
        });
    };

    $scope.cambiar_clave = function () {
        $modal.open({
            templateUrl: 'views/MD_cambiar_clave.html',
            controller: 'MD_cambiar_claveCTRL',
            resolve: {
            }
        });
    };

    $scope.avatar = function () {
        $modal.open({
            templateUrl: 'views/MD_avatar.html',
            controller: 'MD_avatarCTRL',
            resolve: {
            }
        });
    };

    /////+++++++++
    $scope.cargarRut = function () {
        $modal.open({
            templateUrl: 'views/MD_cargarRut.html',
            controller: 'MD_avatarCTRL',
            resolve: {
            }
        });
    };

    $scope.agregarTelefono = function () {
        $scope.formulario.telefonos.push({id: '', numero: '', tipo: 0, nuevo: true, visible: true});
    };

    $scope.agregarEmail = function () {
        $scope.anadir = 1;
        $scope.formulario.emails.push({id: '', email: '', visible: true, nuevoMail: true});
    };
});

controllers.controller('MD_avatarCTRL', function ($http, $scope, $cookieStore, $modalInstance) {
    $scope.cerrarAlerta = function (index) {
        $scope.alertas.splice(index, 1);
    };
    $scope.cancelar = function () {
        $modalInstance.dismiss();
    };
});

controllers.controller('MD_editarPerfilCTRL', function ($http, $scope, $cookieStore, $modalInstance) {
    $scope.cerrarAlerta = function (index) {
        $scope.alertas.splice(index, 1);
    };
    $scope.cancelar = function () {
        $modalInstance.dismiss();
    };
});

controllers.controller('MD_cambiar_claveCTRL', function ($http, $scope, $cookieStore, $modalInstance) {
    $scope.cambiarClave = function () {
        $scope.formulario.accion = 'cambiar_clave';
        $scope.formulario.usuario = $cookieStore.get('usuario');
        $http({
            url: 'json/usuario.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            $scope.message = result.data.message;
            if (result.data.status == 'Ok') {
                $modalInstance.dismiss();
                $route.reload();
            }
        });
    };


    $scope.cerrarAlerta = function (index) {
        $scope.alertas.splice(index, 1);
    };
    $scope.cancelar = function () {
        $modalInstance.dismiss();
    };
});

controllers.controller('perfilEspecializadaCTRL', function ($http, $scope, $cookieStore, $routeParams, $modal, $route, $location) {
    $scope.usuario = $routeParams.usuario;
    $scope.formulario = {};
    $scope.formulario.user = $cookieStore.get('usuario');
    $scope.imagen = $cookieStore.get('avatar');
    $scope.valoracion = $cookieStore.get('valoracion');

    $http({
        url: 'json/servicios.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function (result) {
        $scope.servicios = result.data.servicios;
        $scope.plan = result.data.plan;
        $scope.num_servicios = $scope.servicios.length;
    });

    $scope.eliminar = function (_id) {
        $scope.formulario.accion = 'eliminar';
        $scope.formulario.id_servicio = _id;
        $http({
            url: 'json/servicio.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            $route.reload();
        });
    };

    $scope.nuevo = function () {
        $modal.open({
            templateUrl: 'views/MD_servicio.html',
            controller: 'MD_servicioCTRL',
            resolve: {
                parametros: function () {
                    return {id_servicio: 0, max_etiquetas: $scope.plan.max_etiquetas};
                }
            }
        });
    };

    $scope.editar = function (_id) {
        $modal.open({
            templateUrl: 'views/MD_servicio.html',
            controller: 'MD_servicioCTRL',
            resolve: {
                parametros: function () {
                    return {id_servicio: _id, max_etiquetas: $scope.plan.max_etiquetas};
                }
            }
        });
    };

    $scope.ver = function (_titulo_servicio) {
        $location.path($scope.formulario.user.dominio + '/' + _titulo_servicio);
    };

    $scope.avatar = function () {
        $modal.open({
            templateUrl: 'views/MD_avatar.html',
            controller: 'MD_avatarCTRL',
            resolve: {
            }
        });
    };
});

controllers.controller('MD_servicioCTRL', function ($http, $scope, $modalInstance, $cookieStore, $route, parametros) {

    $scope.dias = [{id: 1, nombre: 'Lunes'}, {id: 2, nombre: 'Martes'}, {id: 3, nombre: 'Miércoles'}, {id: 4, nombre: 'Jueves'}, {id: 5, nombre: 'Viernes'}, {id: 6, nombre: 'Sábado'}, {id: 7, nombre: 'Domingo'}];
    $scope.formulario = {};
    $scope.formulario.parametros = parametros;
    $scope.formulario.etiquetas = {};
    $scope.num_etiquetas = 0;
    $scope.max_etiquetas = parametros.max_etiquetas;
    $scope.formulario.experiencias = [{donde: '', inicio: '', fin: '', descripcion: ''}];
    $scope.formulario.ubicaciones = [{pais: '', ciudad: '', barrio: '', codigozip: '', descripcion: ''}];
    $scope.formulario.horarios = [{dias: '', inicio: '', fin: ''}];
    $scope.user = $cookieStore.get('usuario');
    $scope.formulario.user = $scope.user;
    $scope.formulario.accion = "valores_iniciales";
    $scope.formulario.id_usuario = $scope.user.id;
    $scope.veinticuatro = false;

    $http({
        url: 'json/servicio.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function (result){
        //http://vitalets.github.io/checklist-model/
        $scope.telefonos = result.data.telefonos;
        $scope.emails = result.data.emails;
        $scope.paises = result.data.paises;
        $scope.ciudades = result.data.ciudades;
        $scope.caracteristicas = result.data.caracteristicas;
        $scope.etiquetas = result.data.etiquetas;
        if ( parametros.id_servicio !== 0 ){
            $scope.formulario.titulo = result.data.servicio.titulo;
            $scope.formulario.descripcion = result.data.servicio.descripcion;
            $scope.formulario.web = result.data.servicio.web;
            $scope.formulario.experiencias = result.data.servicio.experiencias;
            $scope.formulario.telefonos = result.data.servicio.telefonos;
            $scope.formulario.emails = result.data.servicio.emails;
            $scope.formulario.ubicaciones = result.data.servicio.ubicaciones;
            $scope.formulario.horarios = result.data.servicio.horarios;
            if ( $scope.formulario.experiencias[0].fin == '2999-12-31' ) $scope.formulario.experiencias[0].actual = true;
            if ( $scope.formulario.horarios[0].inicio == '2015-10-25T06:00:49.242Z' && $scope.formulario.horarios[0].fin == '2015-10-25T05:59:49.259Z' ){
                $scope.formulario.horarios[0].veinticuatro = true;
                $scope.veinticuatro = true;
            }
            $scope.formulario.caracteristicas = result.data.servicio.caracteristicas;
            $scope.formulario.etiquetas = result.data.servicio.etiquetas;
        }
    });

    $scope.cerrarAlerta = function (index) {
        $scope.alertas.splice(index, 1);
    };
    $scope.cancelar = function () {
        $modalInstance.dismiss();
    };
    $scope.agregarExperiencia = function () {
        $scope.formulario.experiencias.push({donde: '', inicio: '', fin: '', descripcion: ''});
    };
    $scope.agregarUbicacion = function () {
        $scope.formulario.ubicaciones.push({pais: '', ciudad: '', barrio: '', codigozip: '', direccion: ''});
    };
    $scope.agregarHorario = function () {
        $scope.formulario.horarios.push({dias: '', inicio: '', fin: ''});
    };
    $scope.crearServicio = function () {
        $scope.formulario.accion = "crear";
        $http({
            url: 'json/servicio.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            $modalInstance.dismiss();
            $route.reload();
        });
    };
    $scope.validarNumEtiquetas = function(){
        $scope.num_etiquetas = $scope.formulario.etiquetas.length;
        if ( $scope.num_etiquetas < $scope.max_etiquetas ){
        }else if ( $scope.num_etiquetas == $scope.max_etiquetas ){
        }else if( $scope.num_etiquetas > $scope.max_etiquetas ){
            $scope.formulario.etiquetas.pop();
            $scope.mensaje = "Recuerda que únicamente las primeras "+$scope.max_etiquetas+" etiquetas serán tenidas en cuenta por tu plan";
        }
    };
    $scope.editarServicio = function(){
        $scope.formulario.accion = "editar";
        $http({
            url: 'json/servicio.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            $modalInstance.dismiss();
            $route.reload();
        });
    };
});

controllers.controller('perfilColmenaCTRL', function ($http, $scope, $location, $cookieStore, $routeParams, $modal) {

    $scope.usuario = $cookieStore.get("usuario");

    $scope.formulario = {};
    $scope.formulario.accion = "valores_iniciales";
    $scope.formulario.usuario = $scope.usuario;
    $scope.valoracion = $cookieStore.get('valoracion');

    $http({
        url: 'json/carrera.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function (result) {
        $scope.plan = result.data.plan;
        $scope.usuarios_alarma = result.data.usuario_alarma;
        $scope.niveles = result.data.max_nivel;
    });

    $scope.go = function (_seccion) {
        _path = 'perfil/'+$routeParams.usuario+'/'+_seccion;
      $location.path(_path);
    };

    $scope.usuario = $routeParams.usuario;
    $scope.imagen = $cookieStore.get('avatar');

    // Build the chart
    $scope.barras = {
        chart: {
            zoomType: 'xy'
        },
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        xAxis: [{
                categories: ['Jan', 'Feb', 'Mar', 'Apr'],
                crosshair: true
            }],
        yAxis: [{// Primary yAxis
                labels: {
                    format: '{value}°C',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                },
                title: {
                    text: 'Temperature',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                }
            }, {// Secondary yAxis
                title: {
                    text: 'Rainfall',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                labels: {
                    format: '{value} mm',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                opposite: true
            }],
        tooltip: {
            shared: true
        },
        legend: {
            layout: 'vertical',
            align: 'left',
            x: 120,
            verticalAlign: 'top',
            y: 100,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },
        series: [{
                name: 'Rainfall',
                type: 'column',
                yAxis: 1,
                data: [0, 0],
                tooltip: {
                    valueSuffix: ' mm'
                }

            }, {
                name: 'Temperature',
                type: 'spline',
                data: [0, 0],
                tooltip: {
                    valueSuffix: '°C'
                }
            }]
    };

    $scope.torta = {
        title: {
            text: 'Referidos'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        series: [{
                type: 'pie',
                name: '',
                data: [
                    ['No Abonados', 100.0],
                    {
                        name: 'Abonados',
                        y: 0.0,
                        sliced: true,
                        selected: true
                    }
                ]
            }]
    };

    $scope.lineas = {
        chart: {
            type: 'spline'
        },
        title: {
            text: 'Referidos por Día'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: {// don't display the dummy year
                month: '%e. %b',
                year: '%b'
            },
            title: {
                text: 'Date'
            }
        },
        yAxis: {
            title: {
                text: 'Snow depth (m)'
            },
            min: 0
        },
        tooltip: {
            headerFormat: '<b>{series.name}</b><br>',
            pointFormat: '{point.x:%e. %b}: {point.y:.2f} m'
        },
        plotOptions: {
            spline: {
                marker: {
                    enabled: true
                }
            }
        },
        series: [{
                name: 'Winter 2007-2008',
                // Define the data points. All series have a dummy year
                // of 1970/71 in order to be compared on the same x axis. Note
                // that in JavaScript, months start at 0 for January, 1 for February etc.
                data: [
                    [Date.UTC(2015, 1, 27), 0],
                    [Date.UTC(2015, 2, 10), 0],
                    [Date.UTC(2015, 3, 18), 0]
                ]
            }]
    };

    $scope.llamado = 0;

    $scope.alarmadosNivel = function (_nivel) {
        if (!$scope.llamado) {
            $scope.formulario.accion = "hijos_nivel";
            $scope.formulario.nivel = _nivel;
            $http({
                url: 'json/carrera.php',
                method: 'POST',
                data: $scope.formulario,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function (result) {
                //$scope.info_nivel = result.data;
            });
            $scope.llamado = 1;
        }
    };

    $scope.hijosNivel = function(_nivel){
        $scope.formulario.accion = "hijos_nivel";
        $scope.formulario.nivel = _nivel;
        $http({
            url: 'json/carrera.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            $scope.hijos = result.data.hijos_nivel;
        });
    };

    $scope.avatar = function () {
        $modal.open({
            templateUrl: 'views/MD_avatar.html',
            controller: 'MD_avatarCTRL',
            resolve: {
            }
        });
    };
});

controllers.controller('perfilFinanzasCTRL', function ($http, $scope, $cookieStore, $routeParams, $modal) {
    $scope.usuario = $routeParams.usuario;
    $scope.imagen = $cookieStore.get('avatar');
    $scope.valoracion = $cookieStore.get('valoracion');

    $scope.formulario = {};
    $scope.formulario.usuario = $cookieStore.get('usuario');

    $http({
        url: 'json/finanzas.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function (result) {
        $scope.plan = result.data.plan;
        $scope.monto = result.data.monto;
    });

    $scope.abonar = function () {
        $modal.open({
            templateUrl: 'views/MD_abonar.html',
            controller: 'MD_abonarCTRL',
            resolve: {
            }
        });
    };

    $scope.retirar = function () {
        $modal.open({
            templateUrl: 'views/MD_retirar.html',
            controller: 'MD_retirarCTRL',
            resolve: {
            }
        });
    };

    $scope.transferir = function () {
        $modal.open({
            templateUrl: 'views/MD_transferir.html',
            controller: 'MD_transferirCTRL',
            resolve: {
            }
        });
    };

    $scope.estados = function () {
        $modal.open({
            templateUrl: 'views/MD_estados.html',
            controller: 'MD_estadosCTRL',
            resolve: {
            }
        });
    };

    $scope.debito = function () {
        $modal.open({
            templateUrl: 'views/MD_debito.html',
            controller: 'MD_debitoCTRL',
            resolve: {
            }
        });
    };

    $scope.adquirirPlan = function () {
        $modal.open({
            templateUrl: 'views/MD_adquirir_plan.html',
            controller: 'MD_adquirir_planCTRL',
            resolve: {
            }
        });
    };

    /*$scope.pagar = function(){
     $scope.formulario.monto = $scope.monto;
     $http({
     url: 'json/pagar.php',
     method: 'POST',
     data: $scope.formulario,
     headers: {'Content-Type': 'application/x-www-form-urlencoded'}
     }).then(function(result){
     });
     };*/

    // Build the chart
    $scope.barras = {
        chart: {
            zoomType: 'xy'
        },
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        xAxis: [{
                categories: ['Jan', 'Feb', 'Mar', 'Apr'],
                crosshair: true
            }],
        yAxis: [{// Primary yAxis
                labels: {
                    format: '{value}°C',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                },
                title: {
                    text: 'Temperature',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                }
            }, {// Secondary yAxis
                title: {
                    text: 'Rainfall',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                labels: {
                    format: '{value} mm',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                opposite: true
            }],
        tooltip: {
            shared: true
        },
        legend: {
            layout: 'vertical',
            align: 'left',
            x: 120,
            verticalAlign: 'top',
            y: 100,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },
        series: [{
                name: 'Rainfall',
                type: 'column',
                yAxis: 1,
                data: [0, 0],
                tooltip: {
                    valueSuffix: ' mm'
                }

            }, {
                name: 'Temperature',
                type: 'spline',
                data: [1, 1],
                tooltip: {
                    valueSuffix: '°C'
                }
            }]
    };

    $scope.torta = {
        title: {
            text: 'Referidos'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        series: [{
                type: 'pie',
                name: '',
                data: [
                    ['No Abonados', 100.0],
                    {
                        name: 'Abonados',
                        y: 0.0,
                        sliced: true,
                        selected: true
                    }
                ]
            }]
    };

    $scope.lineas = {
        chart: {
            type: 'spline'
        },
        title: {
            text: 'Referidos por Día'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: {// don't display the dummy year
                month: '%e. %b',
                year: '%b'
            },
            title: {
                text: 'Date'
            }
        },
        yAxis: {
            title: {
                text: 'Snow depth (m)'
            },
            min: 0
        },
        tooltip: {
            headerFormat: '<b>{series.name}</b><br>',
            pointFormat: '{point.x:%e. %b}: {point.y:.2f} m'
        },
        plotOptions: {
            spline: {
                marker: {
                    enabled: true
                }
            }
        },
        series: [{
                name: '??',
                // Define the data points. All series have a dummy year
                // of 1970/71 in order to be compared on the same x axis. Note
                // that in JavaScript, months start at 0 for January, 1 for February etc.
                data: [
                    [Date.UTC(2015, 1, 27), 0],
                    [Date.UTC(2015, 2, 10), 0]
                ]
            }]
    };

    $scope.avatar = function () {
        $modal.open({
            templateUrl: 'views/MD_avatar.html',
            controller: 'MD_avatarCTRL',
            resolve: {
            }
        });
    };
});

controllers.controller('MD_estadosCTRL', function ($http, $scope, $cookieStore, $modalInstance) {

    $scope.usuario = $cookieStore.get('usuario');

    $scope.formulario = {};
    $scope.formulario.accion = 'valores_iniciales';
    $scope.formulario.usuario = $scope.usuario;
    /*$scope.formulario.bl_acreditada = 1;
     $scope.formulario.items = 20;
     $scope.formulario.pagina = 1;*/

    $http({
        url: 'json/estados.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function (result) {
        $scope.formulario = {};
        $scope.formulario.acreditados = 1;
        $scope.fecha_extracto = result.data.fecha_extracto;
        $scope.usuario_label = result.data.usuario;
        $scope.meses_movimientos = result.data.mesesMovimientos;
    });

    $scope.listar_extracto = function () {
            $scope.formulario.pagina = 1;
        $scope.formulario.items = 5;
        $scope.formulario.accion = 'movimientos_usuario';
        $scope.formulario.usuario = $scope.usuario;
        $scope.formulario.cambio = 1;
        $http({
            url: 'json/estados.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            $scope.movimientos = result.data.movimientos;
            $scope.total = result.data.total_movimientos;
        });
    };

    $scope.extractoSiguientePagina = function () {
        $scope.formulario.accion = 'movimientos_usuario';
        $scope.formulario.usuario = $scope.usuario;
        $scope.formulario.cambio = 2;
        $http({
            url: 'json/estados.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            $scope.movimientos = result.data.movimientos;
        });
    };

    $scope.cerrarAlerta = function (index) {
        $scope.alertas.splice(index, 1);
    };
    $scope.cancelar = function () {
        $modalInstance.dismiss();
    };
});

controllers.controller('MD_adquirir_planCTRL', function ($http, $scope, $cookieStore, $routeParams, $modalInstance, $route, $window, $location) {

    $scope.usuario = $cookieStore.get('usuario');

    $http.get('json/planes.php').then(function (result) {
        $scope.planes = result.data.planes;
        $scope.plan_actual = {id:$scope.planes[0].id, monto:$scope.planes[0].monto};
    });

    $scope.seleccionPlan = function(_id, _monto){
        $scope.plan_actual.id = _id;
        $scope.plan_actual.monto = _monto;
    };

    $scope.pagar = function () {
        $scope.formulario = {};
        $scope.formulario.monto = $scope.plan_actual.monto;
        $scope.formulario.id_plan = $scope.plan_actual.id;
        $scope.formulario.usuario = $scope.usuario;

        $http({
            url: 'json/pagar.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            $modalInstance.dismiss();
            $route.reload();
        });
    };

    $scope.nueva_ventana = function(_plan_id){
        var host = 'http://'+$location.host();
        $window.open(host+'/files/planes/fi_name_recurso.'+_plan_id+'.pdf', 'C-Sharpcorner', 'width=500,height=400');
    };

    $scope.cerrarAlerta = function (index) {
        $scope.alertas.splice(index, 1);
    };
    $scope.cancelar = function () {
        $modalInstance.dismiss();
    };
});

controllers.controller('perfilReferirCTRL', function ($http, $scope, $cookieStore, $routeParams, $modal) {
    $scope.usuario = $routeParams.usuario;
    $scope.imagen = $cookieStore.get('avatar');
    $scope.valoracion = $cookieStore.get('valoracion');

    $scope.formulario = {};
    $scope.formulario.correos = {};
    $scope.formulario.correos =[{id: '', correo: ''}];
    $scope.formulario.usuario = $cookieStore.get('usuario');
    $scope.formulario.url = "#!/registro/" + $scope.formulario.usuario.dominio + "/";

    $scope.referir_mail = function () {
        $scope.formulario.accion="invitar";
        $http({
            url: 'json/referir_mail.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            //$scope.formulario = result.data.usuario;
            //$cookieStore.put('avatar', $scope.formulario.imagen);
        });
    };

    $scope.agregarEmail = function () {
        $scope.anadir = 1;
        $scope.formulario.correos.push({id: '', correo: ''});
    };
    $scope.limpiarEmail = function () {
        $scope.anadir = 0;
        $scope.formulario.correos =[{id: '', correo: ''}];
    };

    $scope.avatar = function () {
        $modal.open({
            templateUrl: 'views/MD_avatar.html',
            controller: 'MD_avatarCTRL',
            resolve: {
            }
        });
    };
});

controllers.controller('MD_abonarCTRL', function ($http, $scope, $cookieStore, $modalInstance, $route) {

    $scope.usuario = $cookieStore.get('usuario');
    $scope.formulario = {};

    $scope.formulario.accion = 'valores_iniciales';
    $scope.formulario.usuario = $scope.usuario;

    $http({
        url: 'json/abonar.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function (result) {
        $scope.saldo = result.data.saldo;
    });

    $scope.mensaje = {};

    $scope.abonar = function () {
        $scope.formulario.accion = 'abonar';
        $http({
            url: 'json/abonar.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            if ( result.data.status == 'Ok' ){
                $scope.mensaje.type = 'success';
                $scope.mensaje.descripcion = result.data.message;
                $modalInstance.dismiss();
                $route.reload();
            }else{
                $scope.mensaje.type = 'danger';
                $scope.mensaje.descripcion = result.data.message;
            }
        });
    };

    $scope.cerrarAlerta = function (index) {
        $scope.alertas.splice(index, 1);
    };
    $scope.cancelar = function () {
        $modalInstance.dismiss();
    };
});

controllers.controller('MD_retirarCTRL', function ($http, $scope, $cookieStore, $modalInstance, $route) {

    $scope.usuario = $cookieStore.get('usuario');

    $scope.formulario = {};
    $scope.formulario.accion = 'valores_iniciales';
    $scope.formulario.usuario = $scope.usuario;
    $scope.mensaje = {};
    /*$scope.formulario.bl_acreditada = 1;
     $scope.formulario.items = 20;
     $scope.formulario.pagina = 1;*/

    $http({
        url: 'json/retirar.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function (result) {
        $scope.saldo = result.data.saldo;
        $scope.bancos = result.data.banco;
        $scope.tipos_cuenta = result.data.tipo_cuenta;
        $scope.tipos_identificacion = result.data.tipo_identificacion;

        $scope.formulario.banco = {};
        $scope.formulario.cuenta = {};
        $scope.formulario.banco.id = -1;
        $scope.formulario.cuenta.tipo_cuenta = -1;
        $scope.formulario.cuenta.tipo_identificacion = -1;
    });

    $scope.retirar = function () {
        $scope.formulario.accion = "retiro";
        $http({
            url: 'json/retirar.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            if ( result.data.status == 'Ok' ){
                $scope.mensaje.type = 'success';
                $scope.mensaje.descripcion = result.data.message;
                $modalInstance.dismiss();
                $route.reload();
            }else{
                $scope.mensaje.type = 'danger';
                $scope.mensaje.descripcion = result.data.message;
            }
        });
    };

    $scope.cerrarAlerta = function (index) {
        $scope.alertas.splice(index, 1);
    };

    $scope.cancelar = function () {
        $modalInstance.dismiss();
    };
});

controllers.controller('MD_transferirCTRL', function ($http, $scope, $cookieStore, $modalInstance, $route) {

    $scope.usuario = $cookieStore.get('usuario');
    $scope.formulario = {};

    $scope.formulario.accion = 'valores_iniciales';
    $scope.formulario.usuario = $scope.usuario;

    $scope.mensaje = {};

    $http({
        url: 'json/transferir.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function (result) {
        $scope.saldo = result.data.saldo;
    });

    $scope.transferir = function () {
        $scope.formulario.accion = "tranferencia";
        $http({
            url: 'json/transferir.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            if ( result.data.status == 'Ok' ){
                $scope.mensaje.type = 'success';
                $scope.mensaje.descripcion = result.data.message;
                $modalInstance.dismiss();
                $route.reload();
            }else{
                $scope.mensaje.type = 'danger';
                $scope.mensaje.descripcion = result.data.message;
            }
        });
    };

    $scope.cerrarAlerta = function (index) {
        $scope.alertas.splice(index, 1);
    };
    $scope.cancelar = function () {
        $modalInstance.dismiss();
    };
});

controllers.controller('MD_debitoCTRL', function ($http, $scope, $cookieStore, $modalInstance, $route) {

    $scope.usuario = $cookieStore.get('usuario');
    $scope.formulario = {};

    $scope.formulario.accion = 'valores_iniciales';
    $scope.formulario.usuario = $scope.usuario;

    $scope.mensaje = {};

    $http({
        url: 'json/debito.php',
        method: 'POST',
        data: $scope.formulario,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function (result) {
        $scope.plan = result.data.plan;
        $scope.titulo = result.data.titulo;
        $scope.boton = result.data.boton;
    });


    $scope.cambiar = function () {
        $scope.formulario.accion = "cambiar_debito";
        $scope.formulario.id_plan = $scope.plan.id_usuario_plan;
        $scope.formulario.debito_automatico = $scope.plan.debito_automatico;
        $http({
            url: 'json/debito.php',
            method: 'POST',
            data: $scope.formulario,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function (result) {
            $modalInstance.dismiss();
            $route.reload();
        });
    };

    $scope.cerrarAlerta = function (index) {
        $scope.alertas.splice(index, 1);
    };
    $scope.cancelar = function () {
        $modalInstance.dismiss();
    };
});

controllers.controller('dateCTRL', function ($scope, $timeout) {
    $scope.open = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened = true;
    };
});

controllers.controller('TimepickerDemoCtrl', function ($scope, $log) {

    $scope.mytime = new Date();

    $scope.hstep = 1;
    $scope.mstep = 15;

    $scope.options = {
        hstep: [1, 2, 3],
        mstep: [1, 5, 10, 15, 25, 30]
    };

    $scope.ismeridian = true;
    $scope.toggleMode = function () {
        $scope.ismeridian = !$scope.ismeridian;
    };

    $scope.update = function () {
        var d = new Date();
        d.setHours(0);
        d.setMinutes(0);
        $scope.mytime = d;
    };

    $scope.changed = function () {
        $log.log('Time changed to: ' + $scope.mytime);
    };

    $scope.clear = function () {
        $scope.mytime = null;
    };
});

controllers.controller('andresCTRL', function ($scope) {
});
