$(function() {
	var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

	elems.forEach(function(html) {
	  var switchery = new Switchery(html);
	});
	//var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
	//Inicializar jQuery Validate
 /*
 $('#politicasUsuario').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '',
		rules:{
			
		}
	}); */
 $('form').each(function () {
	    $(this).validate();
	});
 
 
 	 $.validator.addMethod("dependMinimo",function(value,element){
 		
 		var longMinimo  = $('#long_minima_usuario').val();
 		var longMaximo = $('#long_maxima_usuario').val();
  		if(parseInt(longMinimo) < parseInt(longMaximo) ){
  			var res =  true;
 		}else{
 			var res =  false;
 		}
  		return res;
 	},"El mínimo debe ser menor que el máximo.1");
 	 $.validator.addMethod("dependMaximo",function(value,element){
 		var longMinimo  = $('#long_minima_usuario').val();
 		var longMaximo =  $('#long_maxima_usuario').val();
 		
    	if(parseInt(longMaximo) > parseInt(longMinimo) ){
  			var res2 =  true;
 		}else{
 			var res2 =  false;
 		}
   		return res2;
 	},"El máximo debe ser mayor que el minimo."); 	 
 	
 	
	//Reglas de Validacion
	$('#long_minima_usuario').rules(
			"add",{ 	
				required: true,
				number: true,
				min: 4,
				max: 15,
				dependMinimo:  true,
  				messages: { 
  					required: 'Este Campo es Requerido.' ,
  					number:'Debe ser un entero.',   
  					min: 'Longitud Mínima no debe ser menos de 4 caracteres.',   
  					max: 'Longitud Mínima no debe ser más de 15 caracteres',   
  					dependMinimo: 'El mínimo debe ser menor que el máximo.'
  					} 
	});
	
 	$('#long_maxima_usuario').rules(
			"add",{ 
				required: true,
				number: true,
				min: 5,
				max: 40,
				dependMaximo:  true,
				messages: 	{ 
					required: 'Este Campo es Requerido.' ,
					number:'Debe ser un entero.',  
					min: 'Longitud Máxima no debe ser menos de 5 caracteres.',   
					max: 'Longitud Máxima no debe ser más de 40 caracteres.',
					dependMaximo: 'El máximo debe ser mayor que el minimo.'
				} 
	});
 
 
	//Boton Guardar
	 $('#saveFormBtn').on('click', submitFormBtnHlr);
	
 
		 
	/*	$('#politicasContrasena').validate({
			focusInvalid: true,
			ignore: '',
			wrapper: '',
			 
		}); */
 		$('#long_minima_contrasena').rules(
			"add",{ 	
				required: true,
				number: true,
				min: 4,
				max: 30,
  				messages: { 
  					required: 'Requerido.' ,
  					number:'Debe ser un número.',   
  					min: 'Debe ser mínimo 4.',   
  					max: 'Debe ser máximo 15.',   
   					} 
		}); 
	
 		$('#guardarContrasenaFormBtn').on('click', submitFormBtnHlr);
});


$('#notificacion_usuarios_expiracion').change(function() {
	
  	if( $(this).is(':checked')  == true){
		 
 		$("#contr_notificar_antes_dias").prop('disabled', false);
 
	}   
	else{
		//Habilitar
		$("#contr_notificar_antes_dias").prop('disabled', true);
 		 
	}	
});

$('#c_usuario').val('');
$('#configuracion_avanzada').change(function() {
	
  	if( $(this).is(':checked')  == true){
		 
 		$("#minima_cantidad_letras").prop('disabled', false);
		$("#minima_cantidad_numeros").prop('disabled', false);
		$("#minima_cantidad_caracteres").prop('disabled', false);
 		
		var min = parseInt($('#minima_cantidad_letras').val());
		var num = parseInt($('#minima_cantidad_numeros').val());
		var car = parseInt($('#minima_cantidad_caracteres').val());
		var total = min + num + car;
		$("#cantidad_minima").val(total);
		
		
       // $('#textbox1').val('dsd');
	}   
	else{
		//Habilitar
		$("#minima_cantidad_letras").prop('disabled', true);
		$("#minima_cantidad_numeros").prop('disabled', true);
		$("#minima_cantidad_caracteres").prop('disabled', true); 
		
	
	}	
});