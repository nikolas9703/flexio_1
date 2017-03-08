$(function(){
	//jQuery Validate
	$('#formEditarAgente').validate({
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
	//Validando la sumatoria del porcentaje de las comisiones
	 $('input[name="campo[porcentaje_participacion]"]').rules(
				"add",{ 
					  required: true,
	                  range: [0, 100],
	                  messages: {
	                      range: "% No debe pasar de 100"
	                 }      
	}); 
	 $('input[name="campo[correo]').rules(
			 "add",{ required: false, 
				 	email:true, 
				 	messages: { 
 					email:'Por favor, introduzca una dirección de email válida.' 
				 } 
			 });
	//Verificar si tiene permisos 
	//de editar el fomrulario
	if(permiso_editar_agente == "false"){
		$("#formEditarAgente").find('select, input, button').prop("disabled", "disabled");
	}
	 $('input[name="campo[porcentaje_participacion]"]').on('focusout',function(){

	    	//console.log($(this).val());
	    	
	    	if (!$(this).val() .trim()) {
	    		res = '0.00';
	    	}
	 		else{
				 var res = $(this).val().replace("__", "00");
			     var res = res.replace("_", "0");
			}
	       
	        $(this).val(res);

	    });

	 
	 
});