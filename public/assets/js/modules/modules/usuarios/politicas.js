$(function() {
	var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

	elems.forEach(function(html) {
	  var switchery = new Switchery(html);
	  
	});
	//var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
	//Inicializar jQuery Validate
 
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
	  
	  
	  $.validator.addMethod("validandoConfavanzado",function(value,element){
		  
		  
		   
		   var conf_ava = $('#configuracion_avanzada').is(':checked');
		   
		   if(conf_ava == true){
			   var lonMinima  	= $('#long_minima_contrasena').val();
	 		  	
	 	 		var letras 		= $('#minima_cantidad_letras').val();
		 		var numeros 	=  $('#minima_cantidad_numeros').val();
		 		var caracteres 	=  $('#minima_cantidad_caracteres').val();
		 		
		 		var total = parseInt(letras) + parseInt(numeros) + parseInt(caracteres);
		 		
		 		if(lonMinima < total){
		  			var res2 =  false;
		 		}else{
		 			var res2 =  true;
		 		}
		   		return res2;
		   }else{
			   return true;
		   }
  		  	
	 	},"El Minimo debe ser mayor que la suma"); 	 
	
	
	  
	 	$('#politicasUsuario').validate({
			focusInvalid: true,
			ignore: '',
			wrapper: '',
			rules:{
				
			}
		});  
 
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
   
  
	 	
	 	
	 	
	 	
	 	
	 	$('#politicasContrasena').validate({
			focusInvalid: true,
			ignore: '',
			wrapper: '',
			rules:{
				
			}
		});  	
	 	
	 	$('#long_minima_contrasena').rules(
 	 			  
				"add",{ 	
					required: true,
					number: true,
					min: 4,
					max: 30,
					validandoConfavanzado:true,
	  				messages: { 
	  					required: 'Este Campo es Requerido.' ,
	  					number:'Debe ser un número.',   
	  					min: 'Debe ser mínimo 4.',   
	  					max: 'Debe ser máximo 15.',
	  					validandoConfavanzado: 'La longitud mínima debe ser igual o más grande que la sumatoria de configuraciones avanzadas.',
	  					
	   				} 
			}); 
 
	 	
	 	$('#expira_despues_dias').rules(
	 			  
				"add",{ 	
					required: true,
					number: true,
 	  				messages: { 
	  					required: 'Este Campo es Requerido.' ,
	  					number:'Debe ser un número.'   
 	  					
	   				} 
			}); 
 
	$('.botones').on('click', guardarFormBtnHlr);
 
 		 
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




function guardarFormBtnHlr(e)
{
 	e.preventDefault();
	e.returnValue=false;
	e.stopPropagation();
	 
	//Desabilitar boton
	//$('#guardarFormBtn').off('click', guardarFormBtn).prop('disabled', 'disabled');
	
	if( this.id == 'usuarioFormBtn' )
 	{
		if( $('#politicasUsuario').validate().form() == true ){
 			$('#politicasUsuario').submit();
		}	
		 
	}
	else
	{
		 console.log( $('#configuracion_avanzada').val()   );
		if( $('#politicasContrasena').validate().form() == true ){
			console.log("Hola mUndo");
 			$('#politicasContrasena').submit();
		}	
		 
	}  
}
 
