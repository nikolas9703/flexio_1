$(function(){

	//$('input[name="campo[porcentaje_participacion]"]').val("0.00");

	//jQuery Validate
	$('#articulo').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '',
		submitHandler: function(form) {
			//Habilitar campos ocultos
			$('input:hidden, select:hidden, textarea, select').removeAttr('disabled');
			$('select[name="campo[estado]"').removeAttr('disabled');
			$('select[name="campo[estado]"').attr('disabled', false);
		
			//Enviar el formulario
			form.submit();                        
		}
	});
 

	$('input[name="campo[clase_equipo]').rules(
	 "add",{ required: false, 
		 	 rgx:'^[a-zA-Z0-9áéíóúñÁÉÍÓÚÑ ]+$',
	});

	$('input[name="campo[marca]"]').rules(
		"add",{ requires: false,
				regex:'^[a-zA-ZáéíóúñÁÉÍÓÚÑ ]+$',
	});

	/*$('input[name="campo[modelo]"]').rules(
		"add",{ requires: false,
				rgx:'^[a-zA-Z0-9áéíóúñÁÉÍÓÚÑ ]+$',
	});*/

	$('input[name="campo[anio]"]').rules(
		"add",{ requires: false,
				number: true,
				messages: {
					number:'Campo Númerico.',
				}
	});

	$('input[name="campo[numero_serie]"]').rules(
		"add",{ requires: false,
				rgx:'^[a-zA-Z0-9áéíóúñÁÉÍÓÚÑ ]+$',
	});
	
});

$.validator.addMethod(
        "regex",
        function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Campo alfanumerico."
);
$.validator.addMethod(
        "rgx",
        function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Campo alfabetico."
);


$(document).ready(function(){
    //$('.estado_articulo').attr('disabled', 'disabled');
	var counter = 2;
    $('#del_file_articulo').hide();
    $('#add_file_articulo').click(function(){
            
        $('#file_tools_articulo').before('<div class="file_upload_articulo row" id="farticulo'+counter+'"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"><br><br></div>');
        $('#del_file_articulo').fadeIn(0);
    counter++;
    });
    $('#del_file_articulo').click(function(){
        if(counter==3){
            $('#del_file_articulo').hide();
        }   
        counter--;
        $('#farticulo'+counter).remove();
    });  

	$(".valor_articulo").inputmask('currency',{
      prefix: "",
      autoUnmask : true,
      removeMaskOnSubmit: true
    }); 

	//subir documentos del vehiculo
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
});
