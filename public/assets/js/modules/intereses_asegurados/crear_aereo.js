var aereoAsegurados = (function(){
	var objFrom = {
        aereoForm: $('#casco_aereo'),
    };

    $('#casco_aereo').validate({
        submitHandler: function(form) {
            angular.element('#campo[guardar]').attr('disabled', true);
            form.submit();
        }
    });

    var inicializar = function(){
        objFrom.aereoForm.validate({
            ignore: '',
            wrapper: '',
        });
    };
    return{
		init: function(){
			
		},
	};
})();

aereoAsegurados.init();

$(function(){

	//$('input[name="campo[porcentaje_participacion]"]').val("0.00");

	//jQuery Validate
	$('#casco_aereo').validate({
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
        $('input[name="campo[tripulacion]').rules(
			 "add",{ required: false, 
				 	number:true, 
				 	messages: { 
 					number:'Por favor, introduzca un número válido.' 
				 } 
			 });        
});

$( "#serie_aereo" ).keypress(function() {
  setTimeout(function () {  
  register_user_aereo();
  }, 300);
});


function register_user_aereo()
        {
            $.ajax({
                type: "POST",
                data: {
                    serie: $('#serie_aereo').val(),
                    erptkn: tkn
                },
                url: phost() + 'intereses_asegurados/ajax-check-aereo',
                success: function(data)
                {                   
                    if(data === 'USER_EXISTS')
                    {
                        toastr.warning('No se puede guardar, registro duplicado');
                        $('.guardarAereo').attr('disabled', true);
                    }
                    else{
                        $('.guardarAereo').attr('disabled', false);
                    }
                }
            })              
        }
$(document).ready(function(){
	var counter = 2;
	$('#del_file_aereo').hide();
	$('#add_file_aereo').click(function(){
            
		$('#file_tools_aereo').before('<div class="file_upload" id="f'+counter+'"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"></div>');
		$('#del_file_aereo').fadeIn(0);
	counter++;
	});
	$('#del_file_aereo').click(function(){
		if(counter==3){
			$('#del_file_aereo').hide();
		}   
		counter--;
		$('#f'+counter).remove();
	});
	$("#tripulacion_aereo").inputmask('integer', {min:1, max:1000}).css("text-align", "left");
});

//Popular formulario
new Vue({
  el: '#casco_aereo',
  ready:function(){
    if(vista==='ver' && formulario_seleccionado === 'casco_aereo'){
    if(typeof intereses_asegurados_id_casco_aereo !== 'undefined'){    
    $('.uuid_aereo').val(intereses_asegurados_id_casco_aereo);
    }
    $('#serie_aereo').val(data.serie);   
    $('#marca_aereo').val(data.marca);
    $('#modelo_aereo').val(data.marca);
    $('#matricula_aereo').val(data.matricula);
    $('#valor_aereo').val(data.valor);
    $('#pasajeros_aereo').val(data.pasajeros);   
    $('#tripulacion_aereo').val(data.tripulacion);    
    $('#observaciones_aereo').val(data.observaciones);
    $('.estado_aereo').find('option[value=' + data.estado + ']').prop('selected', 'selected');

    //Verificar si tiene permisos para editar
    if(typeof permiso_editar !== 'undefined')
    {
            if(permiso_editar == 'true'){
                    setTimeout(function(){
                            $(".guardarAereo").prop('disabled', false);
                           
                    }, 1000);
            }
    }
    }
  },
})