bluapp.controller("AseguradoraController", function($scope, $http){
    var objFrom = {
        agenteForm: $('#editarAseguradora'),
    };

    $scope.inicializar = function(){
        objFrom.agenteForm.validate({
            ignore: '',
            wrapper: '',
        });
    };
    $scope.inicializar();
    
if(permiso_editar === 'true'){
  $('input[name="campo[guardarFormBtn]').css('display', 'block'); 
}else{
  $('input[name="campo[guardarFormBtn]').css('display', 'none');   
}    
    
});