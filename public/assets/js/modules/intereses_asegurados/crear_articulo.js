var vistaArticulo = (function(){
	var objFrom = {
        articuloForm: $('#formArticulo'),
    };

    $('#formArticulo').validate({
        submitHandler: function(form) {
            angular.element('#campo[guardar]').attr('disabled', true);
            form.submit();
        }
    });

    var inicializar = function(){
        objFrom.articuloForm.validate({
            ignore: '',
            wrapper: '',
        });
    };
    return{
        init: function(){},
    };
})();

vistaArticulo.init();

$(function(){
    $('#formArticulo').validate({
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
    $('input[name="campo[anio]"').rules("add",{ 
        required: false, 
	number:true, 
	messages: { 
            number:'Por favor, introduzca un año válido.' 
	} 
    });            
});

$(document).ready(function(){
	var counter = 2;
	$('#del_file_articulo').hide();
	$('#add_file_articulo').click(function(){
            
		$('#file_tools_articulo').before('<div class="file_upload" id="f'+counter+'"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"></div>');
		$('#del_file_articulo').fadeIn(0);
	counter++;
	});
	$('#del_file_articulo').click(function(){
		if(counter==3){
			$('#del_file_articulo').hide();
		}   
		counter--;
		$('#f'+counter).remove();
	});
	$("#valor_articulo").inputmask('decimal', {min:1, max:9999999999}).css("text-align", "left");
});

//Popular formulario
new Vue({
  el: '#formArticulo',
  ready:function(){
    if(vista==='ver' && formulario_seleccionado === 'articulo'){
        if(typeof intereses_asegurados_id_articulo !== 'undefined'){    
            $('.uuid_articulo').val(intereses_asegurados_id_articulo);
        }
        
        $('#nombre_articulo').val(data.nombre);
        $('#clase_equipo').val(data.clase_equipo);
        $('#marca_articulo').val(data.marca);
        $('#modelo_articulo').val(data.modelo);
        $('#anio_articulo').val(data.anio);
        $('#numero_serie').val(data.numero_serie);
        $('.condicion_articulo').find('option[value=' + data.id_condicion + ']').prop('selected', 'selected');
        $('#valor_articulo').val(data.valor);   
        $('#observaciones_articulo').val(data.observaciones);
        $('.estado_articulo').find('option[value=' + data.estado + ']').prop('selected', 'selected');

    }
    //Verificar si tiene permisos para editar
    if(typeof permiso_editar !== 'undefined'){
        if(permiso_editar == 'true'){
            setTimeout(function(){
                $(".guardarArticulo").prop('disabled', false);

            }, 1000);
        }
    }
  },
})