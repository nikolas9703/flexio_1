var cargaAsegurados = (function(){
    var objFrom = {
        cargaForm: $('#formCarga'),
    };

    $('#formCarga').validate({
        submitHandler: function(form) {
            angular.element('#campo[guardar]').attr('disabled', true);
            form.submit();
        }
    });

    var inicializar = function(){
        objFrom.cargaForm.validate({
            ignore: '',
            wrapper: '',
        });
    };
    return{
        init: function(){
            
        },
    };
})();

cargaAsegurados.init();

$(function(){

    //$('input[name="campo[porcentaje_participacion]"]').val("0.00");

    //jQuery Validate
    $('#formCarga').validate({
        focusInvalid: true,
        ignore: '',
        wrapper: '',
        submitHandler: function(form) {     
            //Habilitar campos ocultos
            $('input:hidden, select:hidden, textarea').removeAttr('disabled');

            //Enviar el formulario
            form.submit();                        
        }
    });


    $.validator.addMethod(
        "rgx",
        function (value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Campo es alfanumerico."
    );
    $.validator.addMethod(
        "regex",
        function (value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Campo es alfabetico."
    );
 
    $('input[name="campo[no_liquidacion]').rules(
        "add", {required: true,
        rgx: '^[a-zA-Z0-9áéíóúñÁÉÍÓÚ ]+$'
    });
    
});


function register_user_no_carga()
    {
        $.ajax({
            type: "POST",
            data: {
                no_liquidacion: $('#no_liquidacion').val(),
                erptkn: tkn
            },
            url: phost() + 'intereses_asegurados/ajax-check-carga',
            success: function(data)
            {                  
                //console.log(data); 
                if(data === 'USER_EXISTS')
                {
                    toastr.warning('No se puede guardar, registro duplicado');
                    $('.guardarCarga').attr('disabled', true);
                }
                else{
                    $('.guardarCarga').attr('disabled', false);
                }
            }
        })              
    } 

    $( "#no_liquidacion" ).keypress(function() {
        console.log("cambio");
          setTimeout(function () {  
              register_user_no_carga();
          }, 100);
      });




$(document).ready(function(){
    var counter = 2;
    $('#del_file_carga').hide();
    $('#add_file_carga').click(function(){
            
        $('#file_tools_carga').before('<div class="file_upload_carga" id="f'+counter+'"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"></div>');
        $('#del_file_carga').fadeIn(0);
    counter++;
    });
    $('#del_file_carga').click(function(){
        if(counter==3){
            $('#del_file_carga').hide();
        }   
        counter--;
        $('#f'+counter).remove();
    });  




});

//Popular formulario
new Vue({
  el: '#proyecto_actividad',
  ready:function(){
    if(vista==='ver' && formulario_seleccionado === 'carga'){
    if(typeof intereses_asegurados_id_carga !== 'undefined'){    
    $('.uuid_carga').val(intereses_asegurados_id_carga);
    }
    $('#no_liquidacion').val(data.no_liquidacion);   
    $('#fecha_despacho').val(data.fecha_despacho);
    $('#fecha_arribo').val(data.fecha_arribo);
    $('#representante_legal_proyecto').val(data.representante_legal);
    $('#detalle_mercancia').val(data.detalle);
    $('#valor_mercancia').val(data.valor);
    if ($.isNumeric(data.acreedor)){
        $('.acreedor_carga').find('option[value=' + data.acreedor + ']').prop('selected', 'selected');
    }else{
        $('.acreedor_carga').find('option[value=otro]').prop('selected', 'selected');
        $('#acreedor_carga_opcional').val(data.acreedor).prop('disabled', false);
    }
    if ($.isNumeric(data.tipo_obligacion)){
        $('.tipo_obligacion').find('option[value=' + data.tipo_obligacion + ']').prop('selected', 'selected');
    }else{
        $('.tipo_obligacion').find('option[value=otro]').prop('selected', 'selected');
        $('#tipo_obligacion_opcional').val(data.tipo_obligacion).prop('disabled', false);
    }
    $('.tipo_empaque').find('option[value=' + data.tipo_empaque + ']').prop('selected', 'selected');
    $('.condicion_carga').find('option[value=' + data.condicion_envio + ']').prop('selected', 'selected');
    $('.medio_transporte').find('option[value=' + data.medio_transporte + ']').prop('selected', 'selected');
    $('#origen_carga').val(data.origen);   
    $('#destino_carga').val(data.destino);
    $('#observaciones_carga').val(data.observaciones);
    $('.estado_carga').find('option[value=' + data.estado + ']').prop('selected', 'selected');

    //Verificar si tiene permisos para editar
    if(typeof permiso_editar !== 'undefined')
    {
            if(permiso_editar == 'true'){
                    setTimeout(function(){
                            $(".guardarCarga").prop('disabled', false);
                           
                    }, 1000);
            }
    }
    }
  },
})

// select
$('.acreedor_carga').on('change', function(){ 
   if($(this).val() == "otro"){
    $('#acreedor_carga_opcional').val('');
    $('#acreedor_carga_opcional').removeAttr('disabled');
    }else{
    $('#acreedor_carga_opcional').val('');    
    $('#acreedor_carga_opcional').attr('disabled', true);
    }
});
$('.tipo_obligacion').on('change', function(){ 
   if($(this).val() == "otro"){
    $('#tipo_obligacion_opcional').val('');
    $('#tipo_obligacion_opcional').removeAttr('disabled');
    }else{
    $('#tipo_obligacion_opcional').val('');    
    $('#tipo_obligacion_opcional').attr('disabled', true);    
    }
});