$(function(){

 /* Validar formato de email que contenga dominio */
  $.validator.addMethod("emailFormat", function(value, element) {
	  var pattern = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/;
	  var regex = new RegExp(pattern);
	  return this.optional(element) || regex.test(value);
  }, "Por favor, escribe una direccion de correo valida.");

	$.validator.addClassRules({
		validEmailFormat: {
	        required: false,
	        emailFormat: true
	    }
	});

	//jQuery Validate
	$('#crearClientePotencial').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '',
	});

	//Verificar si tiene permisos
	//de editar el fomrulario
	if(permiso_editar_cliente == "false"){
		$("#editarClientePotencial").find('select, input, button, textarea').prop("disabled", "disabled");
	}
 });
