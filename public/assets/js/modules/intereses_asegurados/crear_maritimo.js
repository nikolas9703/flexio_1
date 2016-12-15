var maritimoAsegurados = (function(){
	var objFrom = {
        maritimoForm: $('#casco_maritimo'),
    };

    $('#casco_maritimo').validate({
        submitHandler: function(form) {
            angular.element('#campo[guardar]').attr('disabled', true);
            form.submit();
        }
    });

    var inicializar = function(){
        objFrom.maritimoForm.validate({
            ignore: '',
            wrapper: '',
        });
    };
    return{
		init: function(){
			
		},
	};
})();

maritimoAsegurados.init();

$(function(){

	//$('input[name="campo[porcentaje_participacion]"]').val("0.00");

	//jQuery Validate
	$('#casco_maritimo').validate({
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
 
	 $('input[name="campo[valor]').rules(
			 "add",{ required: false, 
				 	number:true, 
				 	messages: { 
 					number:'Por favor, introduzca un valor válido.' 
				 } 
			 });
	 $('input[name="campo[pasajeros]').rules(
			 "add",{ required: false, 
				 	number:true, 
				 	messages: { 
 					number:'Por favor, introduzca un número válido.' 
				 } 
			 });            
});

$( "#serie_maritimo" ).keypress(function() {
  setTimeout(function () {  
  register_user_maritimo();
  }, 300);
});


function register_user_maritimo()
        {
            $.ajax({
                type: "POST",
                data: {
                    serie: $('#serie_maritimo').val(),
                    erptkn: tkn
                },
                url: phost() + 'intereses_asegurados/ajax-check-maritimo',
                success: function(data)
                {                   
                    if(data === 'USER_EXISTS')
                    {
                        toastr.warning('No se puede guardar, registro duplicado');
                        $('.guardarMaritimo').attr('disabled', true);
                    }
                    else{
                        $('.guardarMaritimo').attr('disabled', false);
                    }
                }
            })              
        }
$(document).ready(function(){
	var counter = 2;
	$('#del_file_maritimo').hide();
	$('#add_file_maritimo').click(function(){
            
		$('#file_tools_maritimo').before('<div class="file_upload" id="f'+counter+'"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"></div>');
		$('#del_file_maritimo').fadeIn(0);
	counter++;
	});
	$('#del_file_maritimo').click(function(){
		if(counter==3){
			$('#del_file_maritimo').hide();
		}   
		counter--;
		$('#f'+counter).remove();
	});
	$("#valor_maritimo").inputmask('decimal', {min:1, max:9999999999}).css("text-align", "left");
    $(".porcentaje_acreedor_maritimo").inputmask('integer',{min:1, max:100}).css("text-align", "left");
});

//Popular formulario
new Vue({
  el: '#casco_maritimo',
  ready:function(){
    if(vista==='ver' && formulario_seleccionado === 'casco_maritimo'){
    if(typeof intereses_asegurados_id_casco_maritimo !== 'undefined'){    
    $('.uuid_maritimo').val(intereses_asegurados_id_casco_maritimo);
    }
    $('#serie_maritimo').val(data.serie);
    $('#nombre_embarcacion').val(data.nombre_embarcacion);
    $('.tipo_maritimo').find('option[value=' + data.tipo + ']').prop('selected', true);
    $('#marca_maritimo').val(data.marca);
    $('#valor_maritimo').val(data.valor);
    $('#pasajeros_maritimo').val(data.pasajeros);
    $('.acreedor_maritimo').find('option[value=' + data.acreedor + ']').prop('selected', 'selected');
    $('.porcentaje_acreedor_maritimo').val(data.porcentaje_acreedor);    
    $('#observaciones_maritimo').val(data.observaciones);
    $('.estado_maritimo').find('option[value=' + data.estado + ']').prop('selected', 'selected');

    }
  },
})