$(function() {
	
	 var expr_regular = $("#expr_regular").val();
  	 $.validator.addMethod("validandoPassword", function(value, element) {
  		 var expreg = eval(expr_regular);
  		 	return this.optional(element) || expreg.test(value); //TEST
   		}, "Error");

 	//Inicializar jQuery Validate
	$('#changePassword').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '',
	 
	}); 
	
	//Reglas de Validacion
 	var min = $('#longitud_minima').val();
 	
 	$('#password1').rules(
			"add",
			{ 
				validandoPassword: (expr_regular!='') ? true:false,
				minlength: min,
				messages: { 
					minlength: "Minimo se desea "+min+" caracteres",
					validandoPassword: "La contraseña no cumple con los requisitos"
				} 
	}); 
 	
 	$('#password2').rules("add",{ 
		required: function(element) {
			return $('#password1').val() != "" ? true : false;
    	}, 
		equalTo: "#password1", 
 		messages: { required:'Este campos es requerido', equalTo:'No coincide con la contrase&ntilde;a introducida.',  } 
	});
});