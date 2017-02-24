$(function(){
	localStorage.setItem("ml-selected","Aseguradoras");
    var objFrom = {
        ajustadoresForm: $('#formAseguradoraCrear'),
    };
	//$('input[name="campo[porcentaje_participacion]"]').val("0.00");

	//jQuery Validate
	$('#formAseguradoraCrear').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '',
		submitHandler: function(form) {		
			//Habilitar campos ocultos
			//$('input:hidden, select:hidden, textarea').removeAttr('disabled');
			$('input[type="submit"]').prop('disabled','true');
			//Enviar el formulario
			form.submit();                        
		}
	});
	
	$('input[name="campo[folio]"').rules(
			 "add",{ required: false, 
				 	regex:'^[a-zA-Z0-9áéíóúñ ]+$',
	});
	$('input[name="campo[tomo]"').rules(
			 "add",{ required: false, 
				 	regex:'^[a-zA-Z0-9áéíóúñ ]+$',
	});
	$('input[name="campo[asiento]"').rules(
			 "add",{ required: false, 
				 	regex:'^[a-zA-Z0-9áéíóúñ ]+$',
	});

	//$('select[name="campo[estado]"').attr('disabled', 'disabled');
	 
});

$.validator.addMethod(
        "regex",
        function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Campo es alfanumerico."
);

 $('#agregarContactoBtn').on("click",function() {
     var id_aseguradora=$('input[name="campo[uuid_aseguradora]').val();
	 //var base_url = window.location.origin;
	 //alert(base_url);
	 window.location.href = '../agregarcontacto/'+id_aseguradora+'?opt=2';	 
	 //$(location).attr('href','aseguradoras/agregarcontacto');  
 });

 $('#agregarPlanBtn').on("click",function() {
     var id_aseguradora=$('input[name="campo[uuid]').val();
	 //var base_url = window.location.origin;
	 //alert(base_url);
	 window.location.href = phost()+'catalogos/crear/planes?a='+id_aseguradora+'';	 
	 //$(location).attr('href','aseguradoras/agregarcontacto');  
 });

	//$('#exportarBtn').attr('class','botonexportardetalles');

  $("#formulariocontacto").hide();