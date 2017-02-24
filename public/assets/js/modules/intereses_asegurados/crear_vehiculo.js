$(function(){

	//$('input[name="campo[porcentaje_participacion]"]').val("0.00");

	//jQuery Validate
	$('#vehiculo').validate({
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

	$('input[name="campo[ano]').rules(
		"add",{ required: false, 
			number:true, 
			messages: { 
				number:'Campo Númerico.' 
			} 
		});
	$('input[name="campo[unidad]').rules(
		"add",{ required: false, 
			number:true, 
			messages: { 
				number:'Campo Númerico.' 
			} 
		});
	$('input[name="campo[capacidad]').rules(
		"add",{ required: false, 
			number:true, 
			messages: { 
				number:'Campo Númerico.' 
			} 
		});
	$('input[name="campo[valor_extras]').rules(
		"add",{ required: false, 
			number:true, 
			messages: { 
				number:'Campo Númerico.' 
			} 
		});

	$('input[name="campo[marca]"').rules(
		"add",{ required: false, 
			rgx:'^[a-zA-ZáéíóúñÁÉÍÓÚÑ ]+$',
		}); 

	$('.modelo_vehiculo').rules(
		"add",{ required: false, 
			regex:'^[a-zA-Z0-9áéíóúñÁÉÍÓÚÑ ]+$',
		});	

	$('input[name="campo[placa]"').rules(
		"add",{ required: false, 
			regex:'^[a-zA-Z0-9áéíóúñÁÉÍÓÚÑ ]+$',
		});	

	$('input[name="campo[motor]"').rules(
		"add",{ required: true, 
			regex:'^[a-zA-Z0-9áéíóúñÁÉÍÓÚÑ ]+$',
		});	

	$('input[name="campo[color]"').rules(
		"add",{ required: false, 
			rgx:'^[a-zA-ZáéíóúñÁÉÍÓÚÑ ]+$',
		});

	$('input[name="campo[operador]"').rules(
		"add",{ required: false, 
			rgx:'^[a-zA-ZáéíóúñÁÉÍÓÚÑ ]+$',
		});

	$('input[name="campo[extras]"').rules(
		"add",{ required: false, 
			regex:'^[a-zA-Z0-9áéíóúñÁÉÍÓÚÑ ]+$',
		});	

		//$('select[name="campo[estado]"').attr('disabled', true);
		
		$( "#motor" ).keypress(function() {
			setTimeout(function () {

				if($("#motor").val()!=="") { 
					register_user();
				}
			}, 300);
		});

		$("#motor").blur(function() {
			setTimeout(function () {
				if($( "#motor").val()!=="") {
					register_user();
				}
			}, 300);
		});
		
		$("#motor").keydown(function(){
			if (event.ctrlKey==true) {
				return false;
			}
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


function register_user()
{
	$.ajax({
		type: "POST",
		data: {
			chasis: $('#motor').val(),
			uuid: $('#uuid_vehiculo').val(),
			erptkn: tkn
		},
		url: phost() + 'intereses_asegurados/ajax-check-vehiculo',
		success: function(data)
		{                   
			if(data === 'USER_EXISTS')
			{
				toastr.warning('Este N°. Motor ya existe');
				$('.guardarVehiculo').attr('disabled', true);
				return false;
			}
			else{
				$('.guardarVehiculo').attr('disabled', false);
				return true;
			}
		}
	});              
}
$(document).ready(function(){
	var counter = 2;
	$('#del_file_vehiculo').hide();
	$('#add_file_vehiculo').click(function(){

		$('#file_tools_vehiculo').before('<div class="file_upload_vehiculo row" id="fvehiculo'+counter+'"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"><br><br></div>');
		$('#del_file_vehiculo').fadeIn(0);
		counter++;
	});
	$('#del_file_vehiculo').click(function(){
		if(counter==3){
			$('#del_file_vehiculo').hide();
		}   
		counter--;
		$('#fvehiculo'+counter).remove();
	});  
	$(".porcentaje_vehiculo").inputmask('integer',{min:1, max:100}).css("text-align", "left"); 
	
	//$('.guardarVehiculo').attr('disabled', true);
	
	$(".guardarVehiculo").click(function() {

		if ($('#motor').val()!==""){
			$.ajax({
				type: "POST",
				data: {
					chasis: $('#motor').val(),
					uuid: $('#uuid_vehiculo').val(),
					erptkn: tkn
				},
				url: phost() + 'intereses_asegurados/ajax-check-vehiculo',
				success: function(data)
				{                   
					if(data === 'USER_EXISTS')
					{
						toastr.warning('Este N°. Motor ya existe');
						$('.guardarVehiculo').attr('disabled', true);
						return false;
					}
					else{
						$('.guardarVehiculo').attr('disabled', false);
						return true;
					}
				}
			}); 
		}
	});
	
	//imprimir formulario de vehiculo
	$('#imprimirLnk').click(function(){
		var id_vehiculo=$('#uuid_vehiculo').val();
		console.log(id_vehiculo);
		window.location.href = '../imprimirFormulario/'+id_vehiculo+'?tipo=8';	
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

