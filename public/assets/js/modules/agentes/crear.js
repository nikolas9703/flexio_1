bluapp.controller("AgenteFormularioController", function($scope, $http){
    var objFrom = {
        agenteForm: $('#formNuevoAgente'),
    };

    angular.element('#formNuevoAgente').validate({
        submitHandler: function(form) {
            angular.element('#campo[guardar]').attr('disabled', true);
            $.post(phost() + 'agentes/existsIdentificacion', $('#formNuevoAgente').serialize(), function(data){
                var respuesta = $.parseJSON(data);
                if(respuesta.existe){
                    $("#mensaje_info").empty().html('<div id="success-alert" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>¡Error!</strong> Identificacion ya existe.</div>');
                }else{
                    form.submit();
                }
                
            });
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
