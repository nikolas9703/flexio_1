$(function() {

	//Inicializar jQuery Validate
	$('#politicasContrasena').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '',
		 
	});

	//Reglas de Validacion
	$('#minima_cantidad_letras').rules(
			"add",{ 	
				required: true,
				number: true,
				min: 4,
				max: 15,
  				messages: { 
  					required: 'Requerido.' ,
  					number:'Debe ser un número.',   
  					min: 'Debe ser mínimo 4.',   
  					max: 'Debe ser máximo 15.'
				} 
			});
 	 
	//Boton Guardar
	$('#guardarContrasenaFormBtn').on('click', submitFormBtnHlr);
});

 
