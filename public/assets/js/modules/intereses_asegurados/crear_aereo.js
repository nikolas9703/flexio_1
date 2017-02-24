var aereoAsegurados = (function(){
	var objFrom = {
        aereoForm: $('#formcasco_aereo'),
    };

    $('#formcasco_aereo').validate({
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
	$('#formcasco_aereo').validate({
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

    
        
     $('input[name="campo[valor_aereo]').rules(
             "add",{ required: false, 
                    number:true, 
                    messages: { 
                    number:'Por favor, introduzca un valor válido.' 
                 } 
             });

     $.validator.addMethod(
        "rgx3",
        function (value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Campo es alfanumerico."
    );
	 
     $('input[name="campo[marca_aereo]').rules(
			"add", {required: false,
            rgx3: '^[a-zA-Z0-9áéíóúñÁÉÍÓÚ ]+$'
	});
     $('input[name="campo[modelo_aereo]').rules(
            "add", {required: false,
            rgx3: '^[a-zA-Z0-9áéíóúñÁÉÍÓÚ ]+$'
    });
     $('input[name="campo[matricula_aereo]').rules(
            "add", {required: false,
            rgx3: '^[a-zA-Z0-9áéíóúñÁÉÍÓÚ ]+$'
    });

	 /*$('input[name="campo[pasajeros_aereo]').rules(
			 "add",{ required: false, 
				 	number:true, 
				 	messages: { 
 					number:'Por favor, introduzca un número válido.' 
				 } 
			 });
        $('input[name="campo[tripulacion_aereo]').rules(
			 "add",{ required: false, 
				 	number:true, 
				 	messages: { 
 					number:'Por favor, introduzca un número válido.' 
				 } 
			 });  */ 


    $("#valor_aereo").inputmask('currency',{
        prefix: "",
        autoUnmask : true,
        removeMaskOnSubmit: true
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
                    uuid_aereo: $('.uuid_aereo').val(),
                    erptkn: tkn
                },
                url: phost() + 'intereses_asegurados/ajax-check-aereo',
                success: function(data)
                {                   
                    console.log(data);
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
            
        $('#file_tools_aereo').before('<div class="file_upload_aereo row" id="faereo'+counter+'"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"><br><br></div>');
        $('#del_file_aereo').fadeIn(0);
    counter++;
    });
    $('#del_file_aereo').click(function(){
        if(counter==3){
            $('#del_file_aereo').hide();
        }   
        counter--;
        $('#faereo'+counter).remove();
    });  
	$("#tripulacion_aereo").inputmask('integer', {min:1, max:1000}).css("text-align", "left");

    //imprimir formulario de vehiculo
    $('#imprimirLnk').click(function(){
        var id_aereo=$('.uuid_aereo').val();
        window.location.href = '../imprimirFormulario/'+id_aereo+'?tipo=3';  
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


    if (vista==='editar') {
        if (desde==="intereses_asegurados") {
            $(".docentregados_aereo").hide();
        }else if(desde==="solicitudes"){
            $(".docentregados_aereo").show();
        }        
    }

    if(vista==='editar' && permiso_editar === 0){
        //Verificar si tiene permisos para editar
        if(typeof permiso_editar !== 'undefined')
        {
            $(".guardarAereo").prop('disabled', true);
            $('#serie_aereo').attr('disabled', true);
            $('#marca_aereo').attr('disabled', true);
            $('#modelo_aereo').attr('disabled', true);
            $('#matricula_aereo').attr('disabled', true);
            $('#valor_aereo').attr('disabled', true);
            $('#pasajeros_aereo').attr('disabled', true);
            $('#tripulacion_aereo').attr('disabled', true);
            $('#observaciones_aereo').attr('disabled', true);
            $('.estado_aereo').attr('disabled', true); 
        }
    }

});

//Popular formulario
new Vue({
  el: '#formcasco_aereo',
  ready:function(){
    if(vista==='ver' && formulario_seleccionado === 'formcasco_aereo'){
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