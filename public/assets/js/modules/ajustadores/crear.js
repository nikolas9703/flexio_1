bluapp.controller("AjustadoresFormularioController", function($scope, $http){
    var objFrom = {
        ajustadoresForm: $('#formAjustadores'),
    };

    angular.element('#formAjustadores').validate({
        submitHandler: function(form) {
            angular.element('#campo[guardar]').attr('disabled', true);
            form.submit();
        }
    });

    $scope.inicializar = function(){
        objFrom.ajustadoresForm.validate({
            ignore: '',
            wrapper: '',
        });
    };

    $scope.inicializar();
});

$(".PAS").hide();
$(".noPAS").hide();
$(".RUC").hide();

$('.identificacion').on("change", function(){
    var letra = $(this).val();
    if(letra === '45'){
        $('.PAS').hide();
        $('.noPAS').hide();        
        $('.RUC').show();        
    }
    else{
        $('.PAS').hide();        
        $('.noPAS').show();        
        $('.RUC').hide();        
    }
});

$('.letra').on("change", function(){
    var letra = $(this).val();   
    if(letra == "N" || letra == "PE" || letra == "E" || letra == '0' || letra == 'PI'){
    $(".provincia").val('').prop("disabled", false);
    }else{
    $(".provincia").prop("disabled", true);
    $('.PAS').show();
    $('.RUC').hide();
    $('.noPAS').hide();
    }    
});
if(vista == 'ver'){
   $('.identificacion').find("option[value=" + ajustadores.identificacion + "]").attr('selected','selected');   
   $('.identificacion').trigger('change');
   $('#ajustador_id').val(ajustadores.id);
   setTimeout(function () {
   $('.id').val(ajustadores.id);    
   $('.nombre').val(ajustadores.nombre);
   //juridico
   if(ajustadores.identificacion == '45'){
   var result = ajustadores.ruc.split('-');   
   $('.tomo_ruc').val(result[0]);
   $('.folio_ruc').val(result[1]);
   $('.asiento_ruc').val(result[2]);
   $('.digito_ruc').val(result[3]);
   }
   if(ajustadores.identificacion == '46'){
   var result = ajustadores.ruc.split('-');   
   $('.provincia').val(result[0]);
   $('.letra').val(result[1]);
   $('.tomo_cedula').val(result[2]);
   $('.asiento_cedula').val(result[3]);       
   $('.pasaporte').val(result);
   $('.letra').trigger('change');
   }
   $('.telefono').val(ajustadores.telefono);
   $('.correo').val(ajustadores.email);
   $('.direccion').val(ajustadores.direccion);
   }, 500);
   vista =! '1' ? $('.enviar').attr('disabled', true) : '';
   $('#agregarContacto').on("click",function() {
     $('#form_contacto').css('display', '');  
     $('#datosdelAjustadores-5').css('display', 'none');  
   });
   $('.volver').on("click",function() {
     $('#form_contacto').css('display', 'none');  
     $('#datosdelAjustadores-5').css('display', '');
     $('#form_contacto').find("#contacto_id").remove();
     $('#contacto_id').val("");
     $('#nombreContacto').val("");
     $('#apellidoContacto').val("");
     $('#cargoContacto').val("");
     $('#telefonoContacto').val("");
     $('#celularContacto').val("");
     $('#emailContacto').val("");   
     
     
   });
   setTimeout(function () {
   if(agregar_contacto == 1){
   $('#form_contacto').css('display', '');  
   $('#datosdelAjustadores-5').css('display', 'none');
   }
   },400);
}
$('.enviarContacto').on("click",function() {
  guardarContacto();  
});
//guardar contacto
function guardarContacto(){
        var contacto_id = $('#contacto_id').val();
        var ajustador_id = $('#ajustador_id').val();
        var nombreContacto = $('#nombreContacto').val();
        var apellidoContacto = $('#apellidoContacto').val();
        var cargoContacto = $('#cargoContacto').val();
        var telefonoContacto = $('#telefonoContacto').val();
        var celularContacto = $('#celularContacto').val();
        var emailContacto = $('#emailContacto').val();        
        var parametros = {
                erptkn: tkn,
                "ajustador_id" : typeof ajustador_id != 'undefined' ? ajustador_id : '',
                "contacto_id" : typeof contacto_id != 'undefined' ? contacto_id : '',
                "nombre" : typeof nombreContacto != 'undefined' ? nombreContacto : '',
                "apellido" : typeof apellidoContacto != 'undefined' ? apellidoContacto : '',
                "cargo" : typeof cargoContacto != 'undefined' ? cargoContacto : '',
                "telefono" : typeof telefonoContacto != 'undefined' ? telefonoContacto : '',
                "celular" : typeof celularContacto != 'undefined' ? celularContacto : '',
                "email" : typeof emailContacto != 'undefined' ? emailContacto : ''
        };        
        $.ajax({
                data:  parametros,
                url:   phost() + 'ajustadores/ajax-guardar-contacto',
                type:  'post',
                beforeSend: function () {
                        $("#resultado").html("Procesando, espere por favor...");
                },
                success:  function (response) {
                        $("#resultado").html(response);
                        $("#contactosGrid").setGridParam({
                                url: phost() + 'ajustadores/ajax-listar-contacto',
                                datatype: "json",
                                postData: {
                                erptkn: tkn,
                                ajustador_id: ajustadores.id
                            }
                        }).trigger('reloadGrid');
                        $('#form_contacto').css('display', 'none');  
                        $('#datosdelAjustadores-5').css('display', '');
                        $('#form_contacto').find("#contacto_id").remove();
                        $('#formulario_contacto').find('input[type="text"]').prop("value", "");
                        $('#celularContacto').val('');
                        $('#emailContacto').val('');
                        $('#telefonoContacto').val('');
                }
        });
}