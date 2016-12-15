
bluapp.controller("AgenteFormularioController", function($scope, $http){
    var objFrom = {
        agenteForm: $('#formVerAgente'),
    };

    angular.element('#formVerAgente').validate({
        submitHandler: function(form) {
            angular.element('#campo[guardar]').attr('disabled', true);
            form.submit();
        }
    });

    $scope.inicializar = function(){
        objFrom.agenteForm.validate({
            ignore: '',
            wrapper: '',
        });
    };

    $scope.inicializar();
});

$(document).on("ready", function(){
    $('.tipo_identificacion').trigger("change");
    
    if(permiso_editar_agente == "true"){
        $('input').prop('disabled', false);
        $('select').prop('disabled', false);
        $('.letra').trigger("change");
    }else{
        $('input').prop('disabled', true);
        $('select').prop('disabled', true);
        $('.botones').hide();
    }
});

$(".PAS").hide();
$(".noPAS").hide();
$(".RUC").hide();

$('.tipo_identificacion').on("change", function(){
    var letra = $(this).val();
    if(letra === 'PAS'){
        $('.PAS').show();
        $('.noPAS').hide();        
        $('.RUC').hide();        
    }
    else if(letra === 'RUC'){
        $('.PAS').hide();        
        $('.noPAS').hide();        
        $('.RUC').show();        
    }    
    else{
        $('.PAS').hide();
        $('.RUC').hide();
        $('.noPAS').show();

        if(letra == "N" || letra == "PE" || letra == "E"){
            $(".provincia").val('').prop("disabled", true);
        }else{
            $(".provincia").prop("disabled", false);
        }
    }
});