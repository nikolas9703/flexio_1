$(function() {

	//Inicializar jQuery Validate
	$('#createUsuarioForm').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '',
		//debug: true,
		groups: {
		    defaultAgencia: "id_agencia id_departamento"
		},
		invalidHandler: function(event, validator) {
			//var errors = validator.numberOfInvalids();
			$.each( validator.errorMap, function(index,obj){
				//---------------------------------------
				// Abrir el acordeon segun la seccion 
				// donde se encuentre error.
				//---------------------------------------
				//Seccion Informacion Personal
				if(index.match(/nombre/g) || index.match(/apellido/g) || index.match(/email/g) || index.match(/id_rol/g)){
					$('#seccionInforacionPersonal').is(':hidden') == true ? $('#seccionInforacionPersonal').collapse('show') : '';
				}
				//Seccion Informacion de Acceso
				else if(index.match(/usuario/g) || index.match(/pasword/g) || index.match(/confirm_password/g)){
					$('#seccionInformacionAcceso').is(':hidden') == true ? $('#seccionInformacionAcceso').collapse('show') : '';
				}
			});
		}
	});

	//Reglas de Validacion
	$('#nombre').rules("add",{ required: true, messages: { required: 'Introduzca Nombre.' } });
	$('#apellido').rules("add",{ required: true, messages: { required:'Introduzca Apellido' } });
	$('#email').rules("add",{ required: true, email:true, messages: { required:'Introduzca Email', email:'Por favor, introduzca una direccion de email valida.' } });
	$('#id_rol').rules("add",{ required: true, messages: { required:'Seleccione Rol' } });
	$('#usuario').rules("add",{ required: true, messages: { required:'Introduzca usuario' } });
	$('#password').rules("add",{ required: true, messages: { required:'Introduzca Contrase&ntilde;a' } });
	$('#confirm_password').rules("add",{ required: true, equalTo: "#password", messages: { required:'No coincide con la contrase&ntilde;a introducida.' } });

	//Boton Guardar
	$('#saveFormBtn').on('click', submitFormBtnHlr);
});


function submitFormBtnHlr(e)
{
	e.preventDefault();
	e.returnValue=false;
	e.stopPropagation();
	
	//Desabilitar boton
	$('#saveFormBtn').off('click', submitFormBtnHlr).prop('disabled', 'disabled');
	
	if( $('#createUsuarioForm').validate().form() == true )
	{
		//Habilitar campos, para poder capturarlos
		$('input:disabled').attr("disabled", "");
		$('input').removeAttr("readonly");
		
		//Enviar Formulario
		$('form#createUsuarioForm').submit();
	}
	else
	{
		//Habilitar boton
		$('#saveFormBtn').on('click', submitFormBtnHlr).removeAttr('disabled');
	}
}
