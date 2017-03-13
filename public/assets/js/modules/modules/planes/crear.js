bluapp.controller("AseguradoraController", function($scope, $http){
    var objFrom = {
        agenteForm: $('#crearAseguradora'),
    };

    $scope.inicializar = function(){
        objFrom.agenteForm.validate({
            ignore: '',
            wrapper: '',
        });
    };

    $scope.inicializar();
});