var directives = angular.module('sedevirtual.directives', []);

directives.directive('ngBindHtmlUnsafe', ['$sce', function($sce){
    return {
        scope: {
            ngBindHtmlUnsafe: '=',
        },
        template: "<div ng-bind-html='trustedHtml'></div>",
        link: function($scope, iElm, iAttrs, controller) {
            $scope.updateView = function() {
                $scope.trustedHtml = $sce.trustAsHtml($scope.ngBindHtmlUnsafe);
            };

            $scope.$watch('ngBindHtmlUnsafe', function(newVal, oldVal) {
                $scope.updateView(newVal);
            });
        }
    };
}]);

directives.directive('fecha', [function() {
    return {
        restrict: 'A',
        scope: { model: '=ngModel' },

        link: function(scope, element, attrs) {
            element.datetimepicker({
                format: 'yyyy-mm-dd HH:ii:ss',
                autoclose: true,
                todayBtn: true,
                pickerPosition: 'bottom-left'
            }).on('changeDate', function(event){
                var fecha = moment(event.date).locale('es');
                scope.model = fecha.add(5,'hours').format('YYYY-MM-DD HH:mm:ss');
                scope.$apply();
                console.log('enviar nueva fecha');
            });
        }
    };
}]);

directives.directive('myEnter', [function() {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if(event.which === 13) {
                scope.$apply(function (){
                    scope.$eval(attrs.myEnter);
                });

                event.preventDefault();
            }
        });
    };
}]);

    