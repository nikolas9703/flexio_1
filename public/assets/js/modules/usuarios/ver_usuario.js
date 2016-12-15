$(".img-circle").css("width", "90");
$(".img-circle").css("height", "90");
 $(function() {
  	 
	 //cancelar
	 
	 
	var id_usuario = $('input[id="campo[id_usuario]"]').val();
	
	 $('#cancelar').click(function() {
		    window.location.href = phost() + 'usuarios/ver-perfil/'+id_usuario;
		    return false;
		});
	 var expr_regular = $("#expr_regular").val();
  	 
	 var min = $('#longitud_minima').val();
  	 $.validator.addMethod("validandoPassword", function(value, element) {
  		 var expreg = eval(expr_regular);
  		 	return this.optional(element) || expreg.test(value); //TEST
   	 }, "Error");
  	 
  	 
  	$('input[id="campo[password]"]').keypress(function (e) {
    		$('input[id="campo[password]"]').rules(
  				"add",
  				{ 
  					validandoPassword: (expr_regular!='') ? true:false,
  					validandoPasswordAntiguos:  true,
  					minlength: min,
  					messages: { 
   						minlength: "Minimo se desea "+min+" caracteres",
  						validandoPassword: "La contraseña no cumple con los requisitos",
  						validandoPasswordAntiguos: "La contraseña ya se ha usado en las ultimas 10 veces"
  					} 
  				});
   	 
 	  		$.validator.addMethod("validandoPasswordAntiguos",function(value,element){
	  	  		var result = true;
	  	  	    $.ajax({
	  	   	      url: phost() + 'usuarios/ajax-validando-contrasenas',
	  	  	      type: 'GET',
	  	  	      async: false,
	  	  	      data: {contrasena:value},
	  	  	      success: function(data) {
	  	   	    	 if(data == 'true') 
	  	   	    		result = false;
	  	   	    	 else
	  	   	    		 result = true;
	  	    	      }
	  	  	    });
	  	  	   
	  	  	   return result; 
	  	  	},"Error");
     });
  	 
  	//Inicializar jQuery Validate
	 $('#editarUsuario').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '',
	 
	});  

 	$('input[name="campo[imagen_archivo]').rules("add",{ url: false });
 	
 	//$('#email').rules("add",{ required: true, email:true, messages: { required:'Introduzca Email', email:'Por favor, introduzca una direccion de email valida.' } });
 	$('input[id="campo[email]"]').rules("add",{ required: true, email:true, messages: { required:'Introduzca Email', email:'Por favor, introduzca una direccion de email valida.' } });
 	//$('#confirm_password').rules("add",{ 
 	
 	$('input[id="campo[confirm_password]"]').rules("add",{ 
		required: function(element) {
			return $('input[id="campo[password]"]').val() != "" ? true : false;
    	}, 
		equalTo: $('input[id="campo[password]"]'), 
 		messages: { required:'Este campos es requerido', equalTo:'No coincide con la contrase&ntilde;a introducida.',  } 
	});
 	
  	//Boton Guardar
	  $('#guardarFormBtn').on('click', guardarFormBtnHlr);
	
});
  

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

 
