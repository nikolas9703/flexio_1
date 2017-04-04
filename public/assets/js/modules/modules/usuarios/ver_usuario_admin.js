  $(function() {
 
	//Inicializar jQuery Validate
	$('#editarUsuarioAdmin').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '',
	 
	}); 
	
	//input[id="campo[password]"]   //FORMA CORRECTA
	
	//Reglas de Validacion
	$('input[name="campo[imagen_archivo]').rules("add",{ url: false });
	
	//$('#password').rules("add",{   messages: { required:'Introduzca Apellido' } }); 
 	$('input[id="campo[password]"]').rules("add",{   messages: { required:'Introduzca Apellido' } }); 
	$('input[id="campo[email]"]').rules("add",{ required: true, email:true, messages: { required:'Introduzca Email', email:'Por favor, introduzca una direccion de email valida.' } });
	
 	$('input[id="campo[confirm_password]"]').rules("add",{ 
		required: function(element) {
			return $('input[id="campo[password]"]').val() != "" ? true : false;
    	}, 
		equalTo: 'input[id="campo[password]"]', 
 		messages: { required:'Este campos es requerido', equalTo:'No coincide con la contrase&ntilde;a introducida.',  } 
	});

 
	//Boton Guardar
	$('#guardarFormBtn').on('click', guardarFormBtnHlr);
	
});
 function validatePass(campo) {
	    var RegExPattern = /(?!^[0-9]*$)(?!^[a-zA-Z]*$)^([a-zA-Z0-9]{8,10})$/;
	    var errorMessage = 'Password Incorrecta.';
	    if ((campo.value.match(RegExPattern)) && (campo.value!='')) {
	        alert('Password Correcta'); 
	    } else {
	        alert(errorMessage);
	        campo.focus();
	    } 
	}

function guardarFormBtnHlr(e)
{
 	 
	e.preventDefault();
	e.returnValue=false;
	e.stopPropagation();
	 
	//Desabilitar boton
	$('#guardarFormBtn').off('click', guardarFormBtn).prop('disabled', 'disabled');
	
	if( $('#editarUsuario').validate().form() == true )
	{
		//Habilitar campos, para poder capturarlos
		$('input:disabled').attr("disabled", "");
		$('input').removeAttr("readonly");
		
		//Enviar Formulario
		$('form#editarUsuario').submit();
		 
	}
	else
	{
		//Habilitar boton
		$('#guardarFormBtn').on('click', guardarFormBtn).removeAttr('disabled');
	}  
}
 
$(".img-circle").css("width", "90");
$(".img-circle").css("height", "90");
 
