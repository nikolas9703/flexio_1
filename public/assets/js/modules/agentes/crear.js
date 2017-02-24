 
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
                    var x="";
					var totalesagentesramos=[];
					totalesagentesramos=$('select[name="ramos[]"]').val();
					
					console.log(totalesagentesramos);
					if(totalesagentesramos!="" && totalesagentesramos!=null)
					{
						totalesagentesramos.each(function(){                        
                        $.each( $(this).val(), function(key, value){
                            console.log(key);
                            console.log(value);
                            x=x+value+",";               
                        });
                        x=x+"-";         
						});
						$("#camporamo").val(""+x+"");
					}
                    
                    console.log($("#camporamo").val());
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

    }else{
        $(".PAS").hide();
        $(".noPAS").hide();
        $(".RUC").hide();
    }
});





function desabilitaramos (){
    var num = [];
    $('select[name="ramos[]"]').each(function(){
        if ($(this).val()!="") {
            num.push($(this).val());
        }        
    });

    console.log(num);

    $('select[name="ramos[]"]').each(function(){
        var valor = $(this).val();
        $("option", this).each(function(){
            $(this).removeAttr("disabled");
            if ($.inArray($(this).attr('value'), num)>=0) {
                if (valor != $(this).attr('value')) {
                    $(this).attr("disabled", "disabled");
                }                
            }
        });        
    });
} 

function desabilitaramos2 (){
    var num = [];
    $('select[name="ramos[]"]').each(function(){
        if ($(this).val() != "" && $(this).val() != null) {
            $.each( $(this).val(), function(key, value){
                num.push(value);                
            });
        }     
    });

    $('select[name="ramos[]"]').each(function(){
        var valor = $(this).val();
        $("option", this).each(function(){
            $(this).removeAttr("disabled");
            if ($.inArray($(this).attr('value'), num)>=0) {   
                //console.log($(this).attr('value')); 
                if ($.inArray($(this).attr('value'), valor)<0) {
                    $(this).attr("disabled", "disabled");
                    var y = $(this).attr('data-index');
                }
                /*if ($.inArray($(this).attr('value'), valor)>=0) {
                //if (valor != $(this).attr('value')) {
                    $(this).attr("disabled", "disabled");
                }   */             
            }
        });        
    });
} 


var clones = $("table tr.bodyramo:last").clone(true);
$("select.ramotabla").chosen({width: "100%"});

var margin=0;
var sumcob=0;
function agregarfila (evt,tabla) {
    
    var ParentRow = $("table tr.bodyramo").last();
    clones.clone(true).insertAfter(ParentRow);

    //var $tr = $('#'+tabla).find("tbody tr:first").clone();
    //var $tr = $('#'+tabla).find("tbody tr:last").clone();    
    
    $tr = $('#'+tabla).find("tbody tr:last");

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
    //$('#'+tabla).find("tbody tr:last").after(clones);  
    
    $tr.find("#porcentaje_participacion").inputmask('float',{min:0.01, max:100.00});  
    
    if (sumcob===0) {
        $(evt).parent().parent().find("#agregarbtn").attr('style', 'margin-top: -170px; margin-left: 100%;');
        $(evt).parent().parent().find("#eliminarbtn").attr('style', 'margin-top: -20px; display:none;');
    }else{
        $(evt).parent().parent().find("#agregarbtn").attr('style', 'margin-top: -170px; margin-left: 100%;');
        $(evt).parent().parent().find("#eliminarbtn").attr('style', 'margin-top: -20px; ');
        $("#tabla_ramos_parti tbody tr:last").find("#eliminarbtn").attr('style', 'margin-top: -20px; ');
        $("#tabla_ramos_parti tbody tr").each(function (index){            
            $(this).find("#eliminarbtn").attr('style', 'margin-top: -20px;');
        });
    }
    desabilitaramos2();
    $('.chosen-select-width').trigger("chosen:updated");    
    $('tr.bodyramo:last select.ramotabla').chosen();
    //$(".chosen-container").css("margin-top", "-20px");
    $(".chosen-container").css("margin-top", "0px");
    $('tr.bodyramo:first .chosen-container').css("margin-top", "0px");
    
}

function eliminarfila (evt) {    
    sumcob-=2;
    if (sumcob===0) {
        $("#tabla_ramos_parti tbody tr").each(function (index){           
            $(this).find("#eliminarbtn").attr('style', 'margin-top: -20px; display:none;');
        });
    }
    $(evt).parent().parent().parent().remove();
    
    desabilitaramos2();
    $('.chosen-select-width').trigger("chosen:updated");    
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
        
    $('select[name="campo[estado]"').attr('disabled', 'disabled');

    $('.letra').on("change", function(){
        if( $(".letra").val() != ""){
            if( $(".provincia").val() ==""){
                $(".provincia").val('').prop("disabled", true);
                $(".provincia").removeAttr("data-rule-required");
            }            
        }else{
            $(".provincia").prop("disabled", false);
            $(".provincia").attr("data-rule-required", "true");
        }
    });

    $(".chosen-container").css("margin-top", "-20px");

});