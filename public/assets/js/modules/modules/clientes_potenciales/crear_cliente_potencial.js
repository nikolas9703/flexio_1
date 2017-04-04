$(function(){
	//var self = this;
	var $form = $('#crearClientePotencialForm');
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
	$('#crearClientePotencialForm').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '',
		errorPlacement: function(error, element) {
			if( $(element).attr('id') == "id_toma_contacto"){
				element.closest('div').append(  $(error).css({"margin-top":-10}) );
			}else{
				$(element).after(error);
			}
		}
	});

	$(".select2").select2();

});
