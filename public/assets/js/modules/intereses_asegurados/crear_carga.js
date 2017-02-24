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

    $(".valor_mercancia").inputmask('currency',{
        prefix: "",
        autoUnmask : true,
        removeMaskOnSubmit: true
    });  
    
});


function register_user_no_carga()
    {
        $.ajax({
            type: "POST",
            data: {
                no_liquidacion: $('#no_liquidacion').val(),
                uuid_carga: $('.uuid_carga').val(),
                erptkn: tkn
            },
            url: phost() + 'intereses_asegurados/ajax-check-carga',
            success: function(data)
            {                  
                console.log(data); 
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
    $('.estado_carga').attr('disabled', 'disabled');
    var counter = 2;
    $('#del_file_carga').hide();
    $('#add_file_carga').click(function(){
            
        $('#file_tools_carga').before('<div class="file_upload_carga row" id="fcarga'+counter+'"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"><br><br></div>');
        $('#del_file_carga').fadeIn(0);
    counter++;
    });
    $('#del_file_carga').click(function(){
        if(counter==3){
            $('#del_file_carga').hide();
        }   
        counter--;
        $('#fcarga'+counter).remove();
    });  

    //imprimir formulario de vehiculo
    $('#imprimirLnk').click(function(){
        var id_carga=$('.uuid_carga').val();
        console.log(id_carga);
        window.location.href = '../imprimirFormulario/'+id_carga+'?tipo=2';  
    });

	//Documentos Modal
    $('#subirDocumentoLnk').click(function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            //Inicializar opciones del Modal
            $('#documentosModal').modal({
                    backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                    show: false
            });
			
            $('#documentosModal').modal('show');
    }); 

    if (permiso_cambio_estado === 0) {
        $('.estado_carga').attr('disabled', true);
    }

    if (vista==='editar') {
        if (desde==="intereses_asegurados") {
            $(".docentregados").hide();
        }else if(desde==="solicitudes"){
            $(".docentregados").show();
        }        
    }

    if(vista==='editar' && permiso_editar === 0){
        //Verificar si tiene permisos para editar
        if(typeof permiso_editar !== 'undefined')
        {
            $(".guardarCarga").prop('disabled', true);
            $('#no_liquidacion').attr('disabled', true);
            $('#fecha_despacho').attr('disabled', true);
            $('#fecha_arribo').attr('disabled', true);
            $('#detalle').attr('disabled', true);
            $('#valor').attr('disabled', true);
            $('.tipo_empaque').attr('disabled', true);
            $('.condicion_envio').attr('disabled', true);
            $('.medio_transporte').attr('disabled', true);
            $('.acreedor_carga').attr('disabled', true);
            $('#acreedor_carga_opcional').attr('disabled', true);
            $('.tipo_obligacion').attr('disabled', true);
            $('#tipo_obligacion_opcional').attr('disabled', true);
            $('#observaciones_carga').attr('disabled', true);
            $('#nombre_documento').attr('disabled', true);
            $('.filedoc').attr('disabled', true); 
            $('.estado_carga').attr('disabled', true); 


        }
    }

    var contador = 2;
      //$('#del_file_vehiculo').hide();
      $('#add_file').click(function(){
        
       $('#file_tools').before('<div class="file_upload" id="f'+contador+'"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"></div>');
       $('#del_file').fadeIn(0);
       contador++;
    });
      $('#del_file').click(function(){
           /*if(counter==3){
            $('#del_file_vehiculo').hide();
        } */  
        contador--;
        $('#f'+contador).remove();
    });
});

//Popular formulario
new Vue({
  el: '#proyecto_actividad',
  ready:function(data){
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