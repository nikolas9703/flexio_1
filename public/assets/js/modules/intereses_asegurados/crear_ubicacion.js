var vistaUbicacion = (function(){
	var objFrom = {
        ubicacionForm: $('#formUbicacion'),
    };

    $('#formUbicacion').validate({
        submitHandler: function(form) {
            angular.element('#campo[guardar]').attr('disabled', true);
            form.submit();
        }
    });

    var inicializar = function(){
        objFrom.ubicacionForm.validate({
            ignore: '',
            wrapper: '',
        });
    };
    return{
        init: function(){},
    };
})();

vistaUbicacion.init();

$(function(){
    $('#formUbicacion').validate({
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
                
});
$( "#direccion_ubicacion" ).keyup(function() {
    if(typeof data !== 'undefined'){
        if(data.direccion !=$('#direccion_ubicacion').val()){
            setTimeout(function () {  
            register_user_ubicacion();
            }, 300);
        }
    }else{
        setTimeout(function () {  
            register_user_ubicacion();
        }, 300);
    }
    
  
});
function register_user_ubicacion()
        {
            $.ajax({
                type: "POST",
                data: {
                    ubicacion: $('#direccion_ubicacion').val(),
                    erptkn: tkn
                },
                url: phost() + 'intereses_asegurados/ajax-check-ubicacion',
                success: function(data)
                {                   
                    if(data === 'USER_EXISTS')
                    {
                        toastr.warning('No se puede guardar, registro duplicado');
                        $('.guardarUbicacion').attr('disabled', true);
                    }
                    else{
                        $('.guardarUbicacion').attr('disabled', false);
                    }
                }
            })              
        }
$(document).ready(function(){
	var counter = 2;
	$('#del_file_ubicacion').hide();
	$('#add_file_ubicacion').click(function(){
            
		$('#file_tools_ubicacion').before('<div class="file_upload" id="f'+counter+'"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"></div>');
		$('#del_file_ubicacion').fadeIn(0);
	counter++;
	});
	$('#del_file_ubicacion').click(function(){
		if(counter==3){
			$('#del_file_ubicacion').hide();
		}   
		counter--;
		$('#f'+counter).remove();
	});
	$("#edif_mejoras").inputmask('decimal', {min:1, max:9999999999}).css("text-align", "left");
        $("#contenido").inputmask('decimal', {min:1, max:9999999999}).css("text-align", "left");
        $("#maquinaria").inputmask('decimal', {min:1, max:9999999999}).css("text-align", "left");
        $("#inventario").inputmask('decimal', {min:1, max:9999999999}).css("text-align", "left");
        $(".porcentaje_acreedor_ubicacion").inputmask('integer',{min:1, max:100}).css("text-align", "left");
});
    $('.acreedor_ubicacion').on('change', function(){ 
       if($(this).val() == "otro"){
        $('#acreedor_ubicacion_opcional').val('');
        $('#acreedor_ubicacion_opcional').removeAttr('disabled');
        }else{
        $('#acreedor_ubicacion_opcional').val('');    
        $('#acreedor_ubicacion_opcional').attr('disabled', true);
        }
    });
//Popular formulario
new Vue({
  el: '#formUbicacion',
  ready:function(){
    if(vista==='ver' && formulario_seleccionado === 'ubicacion'){
        if(typeof intereses_asegurados_id_ubicacion !== 'undefined'){    
            $('.uuid_ubicacion').val(intereses_asegurados_id_ubicacion);
        }
        
        $('#nombre_ubicacion').val(data.nombre);
        $('#direccion_ubicacion').val(data.direccion);
        $('#edif_mejoras').val(data.edif_mejoras);
        $('#contenido').val(data.contenido);
        $('#maquinaria').val(data.maquinaria);
        $('#inventario').val(data.inventario);
        if ($.isNumeric(data.acreedor)){
            $('.acreedor_ubicacion').find('option[value=' + data.acreedor + ']').prop('selected', 'selected');
        }else{
            $('.acreedor_ubicacion').find('option[value=otro]').prop('selected', 'selected');
            $('#acreedor_ubicacion_opcional').val(data.acreedor_opcional).prop('disabled', false);
        }
        $('.porcentaje_acreedor_ubicacion').val(data.porcentaje_acreedor);   
        $('#observaciones_ubicacion').val(data.observaciones);
        $('.estado_articulo').find('option[value=' + data.estado + ']').prop('selected', 'selected');

    }
    //Verificar si tiene permisos para editar
    if(typeof permiso_editar !== 'undefined'){
        if(permiso_editar == 'true'){
            setTimeout(function(){
                $(".guardarUbicacion").prop('disabled', false);

            }, 1000);
        }
    }
  },
})