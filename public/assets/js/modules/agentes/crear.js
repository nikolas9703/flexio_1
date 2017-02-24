bluapp.controller("AgenteFormularioController", function($scope, $http){
    var objFrom = {
        agenteForm: $('#formNuevoAgente'),
    };

    angular.element('#formNuevoAgente').validate({
        submitHandler: function(form) {

            angular.element('#campo[guardar]').attr('disabled', true);
            $.post(phost() + 'agentes/existsIdentificacion', $('#formNuevoAgente').serialize(), function(data){
                //console.log(data);
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
    if(letra === 'pasaporte'){
        $('.PAS').show();
        $('.noPAS').hide();        
        $('.RUC').hide();        
    }
    else if(letra === 'juridico'){
        $('.PAS').hide();        
        $('.noPAS').hide();        
        $('.RUC').show();        
    }    
    else if(letra === 'natural'){
        $('.PAS').hide();
        $('.RUC').hide();
        $('.noPAS').show();

        if(letra == "N" || letra == "PE" || letra == "E"){
            $(".provincia").val('').prop("disabled", true);
        }else{
            $(".provincia").prop("disabled", false);
        }
    }else{
        $(".PAS").hide();
        $(".noPAS").hide();
        $(".RUC").hide();
    }
});

var margin=0;
var sumcob=0;
function agregarfila (evt,tabla) {
    var $tr = $('#'+tabla).find("tbody tr:last").clone();
    //var $tr = $('#'+tabla).find("tbody tr:last").clone();
    
    
    $tr.attr('style', '');
    $tr.find("input:text").val("");
    $tr.find("input:hidden").val("");
    $tr.find("input,select").attr("name", function () {
        var name = this.name;
        return name;
    }).attr("id", function () {
        if (tabla=="tabla_ramos_parti") {
            var id = this.id;
            sumcob++;
            idco=id;
            }              
        return id;
    });                        
    $('#'+tabla).find("tbody tr:last").after($tr);  
    $tr.find("#porcentaje_participacion").inputmask('float',{min:0.01, max:100.00});  
    
    if (sumcob===0) {
        $(evt).parent().parent().find("#agregarbtn").attr('style', 'margin-top: -173px; margin-left: 100%;');
        $(evt).parent().parent().find("#eliminarbtn").attr('style', 'margin-top: -20px; display:none;');
    }else{
        $(evt).parent().parent().find("#agregarbtn").attr('style', 'margin-top: -173px; margin-left: 100%;');
        $(evt).parent().parent().find("#eliminarbtn").attr('style', 'margin-top: -20px; display: block;');
        $("#tabla_ramos_parti tbody tr:last").find("#eliminarbtn").attr('style', 'margin-top: -20px; display:block;');
        $("#tabla_ramos_parti tbody tr").each(function (index) 
        {
            
            $(this).find("#eliminarbtn").attr('style', 'margin-top: -20px; display:block;');
        });
    }
}

function eliminarfila (evt) {
    
    sumcob-=2;
    if (sumcob===0) {
         $("#tabla_ramos_parti tbody tr").each(function (index) 
        {
            
            $(this).find("#eliminarbtn").attr('style', 'margin-top: -20px; display:none;');
        });
    }
    $(evt).parent().parent().parent().remove();
    }
    

$(document).ready(function () {

    $.validator.addMethod(
        "regex",
        function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Campo es Alfabetico."
    );

    $('input[name="campo[nombre]"').rules(
        "add",{ required: true, 
        regex:'^[a-zA-Záéíóúñ ]+$',
        //message: "Campo es Alfanumerico"
    });

    $( 'input[name="campo[guardar]"' ).click(function() {
        console.log("entrooo");
        margin=1;        
        $("#tabla_ramos_parti tbody tr").find("#agregarbtn").attr('style', 'margin-top: -20px; display: none');
        $("#tabla_ramos_parti tbody tr").find("#eliminarbtn").attr('style', 'margin-top: -20px;');
        $("#tabla_ramos_parti tbody tr:last").find("#agregarbtn").attr('style', 'margin-top: -20px;');
        $("#tabla_ramos_parti tbody tr:last").find("#eliminarbtn").attr('style', 'margin-top: -20px; display:none');
        console.log("margin="+margin);
    });

    if(sumcob === 0){
            $("#tabla_ramos_parti tbody tr").find("#eliminarbtn").attr('style', 'margin-top: -20px; display:none;');
        }

});