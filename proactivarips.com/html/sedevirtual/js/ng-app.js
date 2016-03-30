var app = angular.module('sedevirtual', ['ngRoute', 'ngCookies', 'ui.bootstrap', 'sedevirtual.controllers', 'sedevirtual.directives']);

app.config(function($routeProvider) {
    $routeProvider
    	.when('/ingreso', {controller: 'ingresoCTRL', templateUrl: 'views/ingreso.html'})
        .when('/registro_rapido', {controller: 'registro_rapidoCTRL', templateUrl: 'views/registro_rapido.html'})
        .when('/recepcion/agenda/:semana/:anio', {controller: 'agendaCTRL', templateUrl: 'views/agenda.html'})
        .when('/recepcion/crearcita', {controller: 'crearcitaCTRL', templateUrl: 'views/crearcita.html'})
        //Agregada por David para corregir errores de los nombres en barra_usuario
        .when('/profesional/crearcita',{controller: 'crearcitaCTRL', templateUrl: 'views/crearcita.html'})
        //
        .when('/profesional/agenda/:semana/:anio', {controller: 'agendaCTRL', templateUrl: 'views/agenda.html'})
    	//
        .when('/medicina/:cita', {controller: 'medicinaCTRL', templateUrl: 'views/medicina.html'})
    	.when('/ver-medicina/:cita', {controller: 'medicinaVerCTRL', templateUrl: 'views/medicina.html'})
        .when('/psicologia/:cita', {controller: 'psicologiaCTRL', templateUrl: 'views/psicologia.html'})
        .when('/ver-psicologia/:cita', {controller: 'psicologiaVerCTRL', templateUrl: 'views/psicologia.html'})

        //.when('/impr-psicologia/:cita', {controller: 'impr_psicologiaCTRL', templateUrl: 'json/fpdf/helloworld.php'})
        .when('/json/fpdf', {controller: 'impr_psicologiaCTRL', templateUrl: 'json/fpdf/helloworld.php'})

        .when('/nutricion/:cita', {controller: 'nutricionCTRL', templateUrl: 'views/nutricion.html'})
        .when('/ver-nutricion/:cita', {controller: 'nutricionVerCTRL', templateUrl: 'views/nutricion.html'})
    	//
    	.when('/seguimiento', {controller: 'seguimientoCTRL', templateUrl: 'views/seguimiento.html'})
        .when('/crearpaciente', {controller: 'crearpacienteCTRL', templateUrl: 'views/crearpaciente.html'})
        .when('/consultarpaciente', {controller: 'consultarpacienteCTRL', templateUrl: 'views/consultarpaciente.html'})
        //
        .when('/miperfil/datos', {controller: 'miperfilCTRL', templateUrl: 'views/miperfil.html'})
        .when('/miperfil/clave', {controller: 'miperfilCTRL', templateUrl: 'views/miperfil.html'})
        .when('/miperfil/historial', {controller: 'miperfilCTRL', templateUrl: 'views/miperfil.html'})
        //
        .when('/miscitas/crear', {controller: 'crearcitaCTRL', templateUrl: 'views/crearcita.html'})
        .when('/miscitas/consultar', {controller: 'miscitasCTRL', templateUrl: 'views/miscitas.html'})
    	.otherwise({redirectTo: '/ingreso'})
});

/*app.run(function($rootScope, $location, $cookieStore) {
    $rootScope.$on('$routeChangeStart', function(_event, _page) {
        if($cookieStore.get('usuario')){
        }else{
            $location.path('/');
        }
    });
});*/