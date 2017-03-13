/**
 * @category   Js Módulo
 * @package    Propiedades
 * @author     Pensanomica Team
 * @author     ...
 * @copyright  Mayo, Junio, 2015
 * @license    ...
 * @version    SVN:  
 */

$(function(){
 	console.log(tipo_incremento);
           if(tipo_incremento  == '16' ){//Incremento Monetario
                    var padre = document.getElementById("valor_incremento0").parentNode;
                    padre.style.display = (padre.style.display == 'none') ? 'table' : 'table'; 

                     var span = padre.getElementsByTagName('span')[0];
                     span.remove();
                     var input = padre.getElementsByTagName('input')[0];
                      padre.insertBefore(span,input);//arriba
                     span.innerHTML="$";
                 }else if(tipo_incremento  == '15' ){//Incremento Porcentual
                      var padre = document.getElementById("valor_incremento0").parentNode;
                     padre.style.display = (padre.style.display == 'none') ? 'table' : 'table'; 

                     var span = padre.getElementsByTagName('span')[0];
                     span.remove();
                     var input = padre.getElementsByTagName('input')[0];
                     padre.appendChild(span);//abajo
                    span.innerHTML="%";
                 }
                 //OnChange al momento de tocar Tipo de incremento
            $('form#editarPropiedad').on('change', 'select[name="valor1[0][tipo_incremento]"]', function(e){
                var tipo_incremento = $("option:selected", $(this)).text();
                
                var padre = document.getElementById("valor_incremento0").parentNode;
               padre.style.display = (padre.style.display == 'none') ? 'table' : 'table'; 
                
                var span = padre.getElementsByTagName('span')[0];
                span.remove();
                var input = padre.getElementsByTagName('input')[0];
                
                if(tipo_incremento  == 'Incremento Monetario' ){
                      padre.insertBefore(span,input);//arriba
                     span.innerHTML="$";
                 }else if(tipo_incremento  == 'Incremento Porcentual' ){
                     padre.appendChild(span);//abajo
                    span.innerHTML="%";
                 }
            });
            
	/* Habilita y Deshabiltado tipo de Transaccion dependiendo del proyecto de la propiedad */
	 if(transaccion_nombre  == 'Alquiler'  || transaccion_nombre  == 'Venta' ){
		 $('#editarPropiedad ').find('select[name*="campo[uuid_tipo_transaccion]"]').prop('disabled','disabled');
	 }
 	 else{
		 $('#editarPropiedad ').find('select[name*="campo[uuid_tipo_transaccion]"]').prop('disabled', false);
	 }
	 
	
	
 	 //Funcion que envie los valores aunque esten deshabiltados
	 $('form').bind('submit', function() {
	        $(this).find(':input').removeAttr('disabled');
	 }); 
	//Vaores Automaticos al seleccionar un proyecto
    $("form#editarPropiedad").on("change", 'select[name*="proyecto[id_proyecto]"] ', function(e){ 
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		if( $(this).val() ==''){
			$('#valorTable').css('display', 'none');
                        $('#valor1Table').css('display', 'none');
	 		$('#ventaTable').css('display', 'none');
	 		$('#comisionTable').css('display', 'none');
	 		$('#editarPropiedad ').find('input[name*="campo[ch_comision_compartida]"]').prop('checked',false);
	 		$('#editarPropiedad ').find('input[name*="campo[ch_comision_compartida]"]').prop('disabled','disabled');
	 		
	 		$('#editarPropiedad').find('select[name*="campo[uuid_tipo_transaccion]"]').attr("disabled", false);
 	 		$('#editarPropiedad').find('select[name*="campo[uuid_tipo_transaccion]"]').children().removeAttr("selected");
		}
		else
			popular_tipo_transaccion($(this).val());
  	});
    
	//Por defecto esta tabla debe estar abierta
	$('#valorTable').css('display', 'block');  
        $('#valor1Table').css('display', 'block'); 
	$('#ventaTable').css('display', 'block');  
 
	//Valores que deben ser readonly
  	$('input[id="monto_alquiler0"]').attr("readonly", "readonly");
  	$('input[id="monto_venta0"]').attr("readonly", "readonly");
  	$('input[id="monto_venta0"]').attr("readonly", "readonly");
  	$('.monto_alquiler').attr("readonly", "readonly");
 	$('.monto_venta').attr("readonly", "readonly");
 	
	//-------------------------------------------------------------------------------//
 	//--------------Validacion del Fomulatio antes del submit-------------------------
	//-------------------------------------------------------------------------------//
	
 	// Funcion: Validadacion del Formulario 
	$('#editarPropiedad').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '',
	});
 	 
	
	//Validando la sumatoria del porcentaje de las comisiones
	 $('input[id*="porcentaje_comision0"').rules(
				"add",{ 
						sumandoPorcentaje:  true,
						messages: { 
							sumandoPorcentaje: "La distrubución de comisión no puede ser mayor al 100%"
						},
	});  
	
	 //Regla de Validacion de la Sumatoria de Comisiones
	$.validator.addMethod("sumandoPorcentaje",function(value,element){
		var validate = true; 
		if( $('input[id*="campo[ch_comision_compartida]').is(':checked') == true ){
			//Aqui debe estar la suma del porcentaje
          var val = 0;
          $(':input[class^="form-control porc_comision"]').each(function() {//Hago una suma suma de todos los valorea
                 val += Number($(this).val()); //Sumatoria total de valores
          });
          
          if(val > 100){
          	validate = false;
          }
		}
		console.log(validate);
		return validate;
	});
	
	//-------------------------------------------------------------------------------//
 	//--------------     Terminacion de Validacion             ----------------------
	//-------------------------------------------------------------------------------// 
 	//Funcion solo para editar, x default que ventanas debe salir
	 var tipo_transaccion = $("option:selected", 'select[name="campo[uuid_tipo_transaccion]"').text();
	 if(tipo_transaccion  == 'Alquiler' ){
		 $('#valorTable').css('display', 'block');
                 $('#valor1Table').css('display', 'block');
		 $('#ventaTable').css('display', 'none'); 
	 }
	 else  if(tipo_transaccion  == 'Venta' ){
		$('#valorTable').css('display', 'none');
                $('#valor1Table').css('display', 'none');
		$('#ventaTable').css('display', 'block');
 	 }
	 else  if(tipo_transaccion  == 'Ambos' ){
		$('#valorTable').css('display', 'block');
                $('#valor1Table').css('display', 'block');
		$('#ventaTable').css('display', 'block'); 
	 }
	 else{
		$('#valorTable').css('display', 'none');
                $('#valor1Table').css('display', 'none');
		$('#ventaTable').css('display', 'none');
	 }

	 
	//OnChange al momento de tocar Tipo de Transacción 
	  $('form#editarPropiedad').on('change', 'select[name="campo[uuid_tipo_transaccion]"]', function(e){
		
		 var tipo_transaccion = $("option:selected", $(this)).text();
		 if(tipo_transaccion  == 'Alquiler' ){
				$('#valorTable').css('display', 'block');
                                $('#valor1Table').css('display', 'block');
	 			$('#ventaTable').css('display', 'none'); 
			var monto_repartir_alquiler = $('input[name="valor[0][monto_alquiler]"]').val();
			if(monto_repartir_alquiler > 0){
				//Se Recalcula todos los alquileres
	 			$(':input[class^="form-control porc_comision"]').each(function() {//Hago Nuevos Calculos
	 				   var porcentaje_actual = $(this).val();
 	 				   var idName =  $(this).attr("id");
	 				   var index = idName.replace('porcentaje_comision', '');
 	 				   var comision_alquiler_filax = (porcentaje_actual/100)*Number(monto_repartir_alquiler);
	     			$('#agente_comision_alquiler'+index).val( roundNumber(comision_alquiler_filax,2));
  		        });
				
			}
			//Se pone en 0.00 todas las ventas
			$(':input[class^="form-control porc_comision"]').each(function() {//Hago Nuevos Calculos
				  
	 				var idName =  $(this).attr("id");
	 				var index = idName.replace('porcentaje_comision', '');
	 				$('#agente_comision_venta'+index).val('0.00');
	        });
		 }
		 else if(tipo_transaccion  == 'Venta' ){
			$('#valorTable').css('display', 'none');
                        $('#valor1Table').css('display', 'none');
	  		$('#ventaTable').css('display', 'block');
	  		
			var monto_repartir_venta = $('input[name="venta[0][monto_venta]"]').val();
			if(monto_repartir_venta > 0){
				//Se Recalcula todos las Ventas
	 			$(':input[class^="form-control porc_comision"]').each(function() {//Hago Nuevos Calculos
	 				   var porcentaje_actual = $(this).val();
 	 				   var idName =  $(this).attr("id");
	 				   var index = idName.replace('porcentaje_comision', '');
 	 				   var comision_alquiler_filax = (porcentaje_actual/100)*Number(monto_repartir_venta);
	     			$('#agente_comision_venta'+index).val( roundNumber(comision_alquiler_filax,2));
  		        });
			}
			
			//Se pone en 0.00 todas las alquileres
			$(':input[class^="form-control porc_comision"]').each(function() {//Hago Nuevos Calculos
				  
				var idName =  $(this).attr("id");
				var index = idName.replace('porcentaje_comision', '');
				$('#agente_comision_alquiler'+index).val('0.00');
			});
		 }
		 else if(tipo_transaccion  == 'Ambos' ){
			$('#valorTable').css('display', 'block');
                        $('#valor1Table').css('display', 'block');
	 		$('#ventaTable').css('display', 'block');
			var monto_repartir_alquiler = $('input[name="valor[0][monto_alquiler]"]').val();
			var monto_repartir_venta = $('input[name="venta[0][monto_venta]"]').val();
			
			 
			if(monto_repartir_alquiler > 0  ){
				//Se Recalcula todos los alquileres
				
	 			$(':input[class^="form-control porc_comision"]').each(function() {//Hago Nuevos Calculos
	 				 	var porcentaje_actual = $(this).val();
  	 				   var idName =  $(this).attr("id");
	 				   var index = idName.replace('porcentaje_comision', '');
 	 				   var comision_alquiler_filax = (porcentaje_actual/100)*Number(monto_repartir_alquiler);
	     			$('#agente_comision_alquiler'+index).val( roundNumber(comision_alquiler_filax,2));
  		        });
			}
			if(monto_repartir_venta > 0  ){
				//Se Recalcula todos las Ventas
	 			$(':input[class^="form-control porc_comision"]').each(function() {//Hago Nuevos Calculos
	 				 var porcentaje_actual = $(this).val();
  	 				   var idName =  $(this).attr("id");
	 				   var index = idName.replace('porcentaje_comision', '');
 	 				   var comision_alquiler_filax = (porcentaje_actual/100)*Number(monto_repartir_venta);
	     			$('#agente_comision_venta'+index).val( roundNumber(comision_alquiler_filax,2));
  		        });
			}
			
		 }
		 else{
			$('#valorTable').css('display', 'none');
                        $('#valor1Table').css('display', 'none');
	 		$('#ventaTable').css('display', 'none');
	 		//$('#comisionTable').css('display', 'none');
	 		
	 		//$('#editarPropiedad ').find('input[name*="campo[ch_comision_compartida]"]').attr('disabled',true);
 	 		//$('#editarPropiedad ').find('input[name*="campo[ch_comision_compartida]"]').prop('checked',false);
  		 }
	  });
	
	
	
	
	//Calculo para los Monto de Venta
	var comision_venta = 0;
	 
	$('input[name="venta[0][valor_venta]"], input[name="venta[0][comision_propiedad_venta]"]').keyup(function() {
       
        
        var valor 	 = $('input[name="venta[0][valor_venta]"]').val();
        var comision = $('input[name="venta[0][comision_propiedad_venta]"]').val();
 
         if (valor.indexOf("__") >= 0 || valor.indexOf("_") >= 0){
            var valor = valor.replace("__", "00");
            var valor = valor.replace("_", "0");
        }
          if (comision.indexOf("__") >= 0 || comision.indexOf("_") >= 0){
             var comision = comision.replace("__", "00");
             var comision = comision.replace("_", "0");
         }
          comision_venta = (valor/100)*(comision);
         
        $('input[name="venta[0][monto_venta]"]').val(  roundNumber(comision_venta,2) );
        
        var tipo_transaccion = $("option:selected", 'select[name="campo[uuid_tipo_transaccion]"').text();
        
		if( (Number($('input[name*="venta[0][monto_venta]').val())  && (tipo_transaccion  == 'Venta' || tipo_transaccion  == 'Ambos'))> 0 
		    	|| (Number($('input[name*="valor[0][monto_alquiler]"]').val()) && (tipo_transaccion  == 'Alquiler' || tipo_transaccion  == 'Ambos')) ){ 
		    	 $('#editarPropiedad ').find('input[name*="campo[ch_comision_compartida]"]').attr('disabled',false);
		}	 	
        else{
         		$('#editarPropiedad ').find('input[name*="campo[ch_comision_compartida]"]').attr('disabled','disabled');
         }
         
         var comision_compartida = $('input[name*="campo[ch_comision_compartida]').is(':checked');  
          //En caso de estar seleccionado la primera fila de la comision debe hacer los calculos respectivos ;(
        if(comision_compartida == true){
 
        	var monto_repartir_venta = $('input[name="venta[0][monto_venta]"]').val();
 	    	$('.monto_venta').each(function(){
	    		
	    		 var idName =  $(this).attr("id");
	  			 var index = idName.replace('agente_comision_venta', '');
 	  			  
	  			 var comision_venta_filax = ( $('input[name="comision['+index+'][porcentaje_comision]"]').val()/100)*Number(monto_repartir_venta);
 	  			 $(this).val( roundNumber(comision_venta_filax,2));
	    	 });	
        	 
        }
         
   	});
	
	//Calculo para el monto de Alquiler
	var comision_alquiler = 0;
	 
	$('input[name="valor[0][valor_alquiler]"], input[name="valor[0][meses_alquiler]"]').keyup(function() {
        var valor = $('input[name="valor[0][valor_alquiler]"]').val();
        var meses = $('input[name="valor[0][meses_alquiler]"]').val();
        
      
        if (valor.indexOf("__") >= 0 || valor.indexOf("_") >= 0){
            var valor = valor.replace("__", "00");
            var valor = valor.replace("_", "0");
        }
        if (meses.indexOf("__") >= 0 || meses.indexOf("_") >= 0){
            var meses = meses.replace("__", "00");
            var meses = meses.replace("_", "0");
        }
        
        comision_alquiler =valor*meses;
        $('input[name="valor[0][monto_alquiler]"]').val(  roundNumber(comision_alquiler,2) );
        
        
        var tipo_transaccion = $("option:selected", 'select[name="campo[uuid_tipo_transaccion]"').text();
   	    
         if( (Number($('input[name*="venta[0][monto_venta]').val())  && (tipo_transaccion  == 'Venta' || tipo_transaccion  == 'Ambos'))> 0 
        	|| (Number($('input[name*="valor[0][monto_alquiler]"]').val()) && (tipo_transaccion  == 'Alquiler' || tipo_transaccion  == 'Ambos')) ){ 
        	 $('#editarPropiedad ').find('input[name*="campo[ch_comision_compartida]"]').attr('disabled',false);
        }
        else{
        	$('#editarPropiedad ').find('input[name*="campo[ch_comision_compartida]"]').attr('disabled','disabled');
         	
		}	
        var comision_compartida = $('input[name*="campo[ch_comision_compartida]').is(':checked');  
        
        //En caso de estar seleccionado la primera fila de la comision debe hacer los calculos respectivos ;(
        if(comision_compartida == true){
        	var monto_repartir_alquiler = $('input[name="valor[0][monto_alquiler]"]').val();
    	    $('.monto_alquiler').each(function(){
  	    		 
  	    		 var idName =  $(this).attr("id");
  	  			 var index = idName.replace('agente_comision_alquiler', '');
  	  			 
  	  			  
  	  			 var comision_alquiler_filax = ( $('input[name="comision['+index+'][porcentaje_comision]"]').val()/100)*Number(monto_repartir_alquiler);
  	  			 $(this).val( roundNumber(comision_alquiler_filax,2));
   	    	 });	
        }
   	});

 	
	 $('.porc_comision').keyup(function() {
		 var idName =  $(this).attr("id");
			var index = idName.replace('porcentaje_comision', '');
			
			var porcentaje = $(this).val();
			
			// var porcentaje_comision_fila1 = $(this).val();
	 		  
	 		   if (porcentaje.indexOf("__") >= 0 || porcentaje.indexOf("_") >= 0){
	 	            var porcentaje = porcentaje.replace("__", "00");
	 	            var porcentaje = porcentaje.replace("_", "0");
	 	        }
			
			
			
			var tipo_transaccion = $("option:selected", 'select[name="campo[uuid_tipo_transaccion]"').text();
 	     
        	if(tipo_transaccion  == 'Alquiler' ){
        	    	
        	    	var monto_repartir_alquiler = $('input[name="valor[0][monto_alquiler]"]').val();
        	    	var comision_alquiler_filax = (porcentaje/100)*Number(monto_repartir_alquiler);
        	    	
        	    	$('#agente_comision_alquiler'+index).val( roundNumber(comision_alquiler_filax,2));
        	    	$('#agente_comision_venta'+index).val('0.00');
        	    	 
      	}else if(tipo_transaccion  == 'Venta' ){
      			var monto_repartir_venta = $('input[name="venta[0][monto_venta]"]').val();
      			var comision_venta_filax = (porcentaje/100)*Number(monto_repartir_venta);
      			
      			$('#agente_comision_alquiler'+index).val('0.00');
      			$('#agente_comision_venta'+index).val( roundNumber(comision_venta_filax,2));
      	}
      	else if(tipo_transaccion  == 'Ambos' ){

      			//Alquiler
      			var monto_repartir_alquiler = $('input[name="valor[0][monto_alquiler]"]').val();
      			var comision_alquiler_filax = (porcentaje/100)*Number(monto_repartir_alquiler);
        	    	$('#agente_comision_alquiler'+index).val( roundNumber(comision_alquiler_filax,2));
      			
      			//Venta
      			var monto_repartir_venta = $('input[name="venta[0][monto_venta]"]').val();
      			var comision_venta_filax = (porcentaje/100)*Number(monto_repartir_venta);
      			$('#agente_comision_venta'+index).val( roundNumber(comision_venta_filax,2));
      	}  

	});

	
	 
	 /**
		 * 
		 * Listado de Porcentajes de Comision dependiendo del agente
		 * 
	 	 */

	 $('form#editarPropiedad').on('change', 'select[class="form-control id_agente_comision"]', function(e){

		    var idName =  $(this).attr("name");
		    var string = idName.replace('][id_agente_comision]','');
		    var index = string.replace('comision[', '');
		    
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			var uuid_agente = this.value;
	   	 	$.ajax({
				url: phost() + 'agentes/ajax-seleccionar-porcentaje',
	 			data: {
	 				uuid_agente: uuid_agente,
		            erptkn: tkn
				},
				type: "POST",
				dataType: "json",
				cache: false,
			}).done(function(json) {
	 			  //Check Session
		        if( $.isEmptyObject(json.session) == false){
		            window.location = phost() + "login?expired";
		        }
		        $('#monto_comision'+index).val("0");
   		        if( $.isEmptyObject(json.results[0]) == false ){
	   		        	//console.log(json.results[0][0]['porcentaje_participacion']);
   		        	var porcentaje = json.results[0][0]['porcentaje_participacion']; 
  	          	    $('#porcentaje_comision'+index).val(porcentaje); 
  	          	    
	  	          	    var tipo_transaccion = $("option:selected", 'select[name="campo[uuid_tipo_transaccion]"').text();
            	     
	           	    if(tipo_transaccion  == 'Alquiler' ){
	           	    	
	           	    	var monto_repartir_alquiler = $('input[name="valor[0][monto_alquiler]"]').val();
	           	    	var comision_alquiler_filax = (porcentaje/100)*Number(monto_repartir_alquiler);
	           	    	
	           	    	$('#agente_comision_alquiler'+index).val( roundNumber(comision_alquiler_filax,2));
	           	    	$('#agente_comision_venta'+index).val('0.00');
	           	    	 
	         		}else if(tipo_transaccion  == 'Venta' ){
	         			var monto_repartir_venta = $('input[name="venta[0][monto_venta]"]').val();
	         			var comision_venta_filax = (porcentaje/100)*Number(monto_repartir_venta);
	         			
	         			$('#agente_comision_alquiler'+index).val('0.00');
	         			$('#agente_comision_venta'+index).val( roundNumber(comision_venta_filax,2));
	         		}
	         		else if(tipo_transaccion  == 'Ambos' ){

	         			//Alquiler
	         			var monto_repartir_alquiler = $('input[name="valor[0][monto_alquiler]"]').val();
	         			var comision_alquiler_filax = (porcentaje/100)*Number(monto_repartir_alquiler);
	           	    	$('#agente_comision_alquiler'+index).val( roundNumber(comision_alquiler_filax,2));
	         			
	         			//Venta
	         			var monto_repartir_venta = $('input[name="venta[0][monto_venta]"]').val();
	         			var comision_venta_filax = (porcentaje/100)*Number(monto_repartir_venta);
		         			$('#agente_comision_venta'+index).val( roundNumber(comision_venta_filax,2));
	         		}  
  	  	        } 
   		        else{
   		        	$('#agente_comision_alquiler'+index).val('0.00');
   		        	$('#agente_comision_venta'+index).val('0.00');
   		        }
		        
		       
			});	 
		});
	 
 	//Por defecto esta tabla debe esatar cerrada
 	$('#comisionTable').css('display', 'none');  
	
	var  comision_compartida = $('input[id*="campo[ch_comision_compartida]').is(':checked');
	
	//En caso de estar activado el chechbox, abrir la tabla
	if(comision_compartida == true){ 
  		$('#comisionTable').css('display', 'block');  
	}
	
	//Inicilzación del Mapa en el formulario 
	$('.google_map').locationpicker({
		location: {latitude: latitud, longitude: longitud},	
		radius: 0,
		zoom: 15,
		inputBinding: {
			latitudeInput: $('#us3-lat'),
			longitudeInput: $('#us3-lon'),
			radiusInput: $('#us3-radius'),
			locationNameInput: $('#us3-address')        
		},
		enableAutocomplete: true,
			onchanged: function(currentLocation, radius, isMarkerDropped) {
		}
	});
	
	//Esta acción abre y cierra la tabla de comisiones compartidas
 	$('#editarPropiedad').on('change', 'input[id*="campo[ch_comision_compartida]"]', function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		//Abrir tabla
		if($(this).is(':checked') == true){
			$('#comisionTable').css('display', 'block');  
 		}else{
			$('#comisionTable').css('display', 'none');  
 		}
	});

 	/** Funcion que elimina las comisiones en la tabla Dinamica  **/
  	$('#editarPropiedad #comisionTable').on('click', '.eliminarBtn', function(e){
  		
 		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
  		
		var indice_fila 		= $(this).attr('data-index');
		var table 				= $(this).closest('table');
		var row 				= $(this).closest('tr');
  		var id_comision			= $(this).attr("data-id");
 		var agrupador_campos 	= $(row).attr("id").replace(/[0-9]/g, '');
		
		
		//Resaltar la fila que se esta
		//seleccionando para eliminar.
		$(row).addClass('highlight');
  		
  		var mensaje_confirmacion = '¿Esta seguro que desea eliminar esta comisión.?';
 
			//Ventana de Confirmacion
		$('#optionsModal').find('.modal-title').empty().append('Confirme');
		$('#optionsModal').find('.modal-body').empty().append(mensaje_confirmacion);
		$('#optionsModal').find('.modal-footer')
			.empty()
			.append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
			.append(
				$('<button class="btn btn-w-m btn-danger" type="button" />').append('Eliminar').click(function(e){
					e.preventDefault();
					e.returnValue=false;
					e.stopPropagation();
 					
			  		var url = phost() +  'propiedades/ajax-eliminar-comision';
			  		
			  		$.ajax({
						url: url,
						data: {
							id_comision: id_comision,
			                 erptkn: tkn
						},
						type: "POST",
						dataType: "json",
						cache: false,
					}).done(function(json) {
						 
						//Check Session
						if( $.isEmptyObject(json.session) == false){
							window.location = phost() + "login?expired";
						}
						
						//If json object is empty.
						if($.isEmptyObject(json.results[0]) == true){
							return false;
						}
 						//Si la respuesta fue true
						//remover el telefono del input
						if(json.results[0]['respuesta'] == true){
							
							//Antes de remover la fila
							//Primero verificar la cantidad
							//de filas que quedan
							var total_filas = $(table).find('tbody').find('tr').length;
							
 							
							if(total_filas > 1 && indice_fila == 0){
 								//si quedan varias filas
								//remover la fila actual
								$(row).remove();
							
							}else{
		
								//si es la unica fila y su indice
								//es el undice (0) osea el primero
								//solo limpiar los campos. 
								if(indice_fila == 0){
									//Limpiar Campos
									setTimeout(function(){
										$(row).find('select option').removeAttr('selected');
										$(row).find('input').prop('value', '');
										$(row).find('input').removeAttr('data-id');
									}, 500);
		
								}else{
									$(row).remove();
								}
							}
							
							//
							// Formatear indices de las filas de la Tabla
							//
							//Actualizar los incides
							//Al eliminar una fila.
							$.each( $(table).find('tbody').find('tr[id*="'+ agrupador_campos +'"]'), function(i, obj1){
								var nindex = i;
								//var cntx = i + 2;
								$(this).prop("id", agrupador_campos + nindex);
		
								$.each( $(this).find('td'), function(j, obj2){
									
									if($(this).find('input').attr('name')){
										var name = $(this).find('input').attr('name');
											name = name.replace(/([\d])/, nindex);
		
										var id = $(this).find('input').attr('id');
											id = id.replace(/(\d)/, nindex);
		
										$(this).find('input').attr("name", name).attr("id", id);
									}
									if($(this).find('select').attr('name')){
										var name = $(this).find('select').attr('name');
											name = name.replace(/([\d])/, nindex);
		
										var id = $(this).find('select').attr('id');
											id = id.replace(/(\d)/, nindex);
		
										$(this).find('select').attr("name", name).attr("id", id);
									}
									if($(this).find('div[id*="_chosen"]')){
										if( $(this).find('div[id*="_chosen"]').attr('id') != undefined )
										{
											var id = $(this).find('div[id*="chosen"]').attr('id');
												id = id.replace(/(\d)/, nindex);
		
											$(this).find('div[id*="chosen"]').attr("id", id);
										}
									}
									if($(this).find('a')){
										$(this).find('a').attr("data-index", nindex);
									}
								});
							});
						}
						
	 						//Mostrar Mensaje
							$class_mensaje = json.results[0]['respuesta'] == true ? 'alert-success' : 'alert-danger';
							mensaje_alerta(json.results[0]['mensaje'], $class_mensaje);
	
							$('#optionsModal').modal('hide'); 
						});
				})
			);
		$('#optionsModal').modal('show');
 		 
 	}); 
 	
  	
  //Redireccion para ver detalles de la propiedad
	$('#moduloOpciones ul').on("click", "#detallesPropiedadBtn", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var url =  phost() + 'propiedades/detalles-propiedad/'+uuid_propiedad; 
		$(location).attr('href',url);
   	});
 	 
 	//Verificar si tiene permisos 
	//de editar el fomrulario
 	 if(permiso_editar_propiedad == "false"){
	  	$("#editarPropiedad").find('select, input, button, textarea').prop("disabled", "disabled");
	 }
     //Inicialización para el switcher
    $(switchery.switcher).css({"display":"block"}).after('<input type="text" class="form-control" style="visibility: hidden; position: relative; top:-35px; width:0px; height:0px">');
	$('.switchery').css('margin-left', '10px');

    //Auto rellenar con ceros el monto si no tiene decimales
    $('input[name="campo[metraje]"], input[name="comision[0][porcentaje_comision]"],input[name="valor[0][meses_alquiler]"], input[name="valor[0][valor_alquiler]"], input[name="venta[0][valor_venta]"] , input[name="venta[0][comision_propiedad_venta]"]').on('focusout',function(){

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
    
    $('input[name="campo[valor_m2]"],input[name="campo[metraje]"]').keyup(function() {
        
        var cuota_mantenimiento_m2= $('#editarPropiedad ').find('input[id*="campo[valor_m2]"]').val();
        var metraje= $('#editarPropiedad ').find('input[id*="campo[metraje]"]').val();
        var valor=metraje*cuota_mantenimiento_m2;
        
        if (valor - Math.floor(valor) == 0) {
            valor=valor+".00";
        }
        $('#editarPropiedad ').find('input[id*="valor_venta0"]').val(valor);
    });
$('.fecha').combodate({
		minYear: 1915,
	    maxYear: 2020,
	}); 
});



//** Esta funcion popula el select de nombre comercial **//
function popular_tipo_transaccion(uuid_proyecto)
{
	
 if(uuid_proyecto==""){
     return false;
 }

 $.ajax({
     url: phost() + 'proyectos/ajax-seleccionar-tipo-transaccion',
     data: {
  	   uuid_proyecto: uuid_proyecto,
         erptkn: tkn
     },
     type: "POST",
     dataType: "json",
     cache: false,
 }).done(function(json) {
	 $('#crearPropiedad ').find('input[name*="campo[promotor]"]').val('');
     //Check Session
     if( $.isEmptyObject(json.session) == false){
         window.location = phost() + "login?expired";
     }
   
     //If json object is not empty.
     if( $.isEmptyObject(json.results[0]) == false ){
    	 var promotor = json.results[0][0]['promotor']; 
  	   	 var tipo_transaccion = json.results[0][0]['uuid_tipo_transaccion']; 
    	 var transaccion =  $("#uuid_tipo_transaccion option[value='"+tipo_transaccion+"']").text();
   		 $('#editarPropiedad ').find('input[name*="campo[promotor]"]').val(promotor);
   		 
                 var cuota_mantenimiento_m2 = json.results[0][0]['cuota_mantenimiento_m2'];
                $('#editarPropiedad ').find('textarea[id*="campo[direccion]"]').val(json.results[0][0]['ubicacion']);
                $('#editarPropiedad ').find('input[id*="campo[cuota_mantenimiento_m2]"]').val(cuota_mantenimiento_m2);
   		if(transaccion == 'Alquiler'){
      		$('#editarPropiedad').find('select[name*="campo[uuid_tipo_transaccion]"]').attr("disabled", "disabled");
    			$('#editarPropiedad').find('#uuid_tipo_transaccion option[value="'+ tipo_transaccion +'"]').prop('selected', tipo_transaccion);
    			
    			$('#valorTable').css('display', 'block'); 
                        $('#valor1Table').css('display', 'block');
   			$('#ventaTable').css('display', 'none'); 
   			var monto_repartir_alquiler = $('input[name="valor[0][monto_alquiler]"]').val();
   			if(monto_repartir_alquiler > 0){
   				//Se Recalcula todos los alquileres
   	 			$(':input[class^="form-control porc_comision"]').each(function() {//Hago Nuevos Calculos
   	 				   var porcentaje_actual = $(this).val();
    	 				   var idName =  $(this).attr("id");
   	 				   var index = idName.replace('porcentaje_comision', '');
    	 				   var comision_alquiler_filax = (porcentaje_actual/100)*Number(monto_repartir_alquiler);
   	     			$('#agente_comision_alquiler'+index).val( roundNumber(comision_alquiler_filax,2));
     		        });
   				
   			}
   			//Se pone en 0.00 todas las ventas
   			$(':input[class^="form-control porc_comision"]').each(function() {//Hago Nuevos Calculos
   				  
  	 				var idName =  $(this).attr("id");
  	 				var index = idName.replace('porcentaje_comision', '');
  	 				$('#agente_comision_venta'+index).val('0.00');
  	        });
   		}
   		else if(transaccion == 'Venta'){
    			$('#editarPropiedad').find('select[name*="campo[uuid_tipo_transaccion]"]').attr("disabled", "disabled");
    			$('#editarPropiedad').find('#uuid_tipo_transaccion option[value="'+ tipo_transaccion +'"]').prop('selected', tipo_transaccion);
    			
    			
    			$('#valorTable').css('display', 'none');
                        $('#valor1Table').css('display', 'none');
    			$('#ventaTable').css('display', 'block'); 
   			var monto_repartir_venta = $('input[name="venta[0][monto_venta]"]').val();
   			if(monto_repartir_venta > 0){
   				//Se Recalcula todos las Ventas
   	 			$(':input[class^="form-control porc_comision"]').each(function() {//Hago Nuevos Calculos
   	 				   var porcentaje_actual = $(this).val();
    	 				   var idName =  $(this).attr("id");
   	 				   var index = idName.replace('porcentaje_comision', '');
    	 				   var comision_alquiler_filax = (porcentaje_actual/100)*Number(monto_repartir_venta);
   	     			$('#agente_comision_venta'+index).val( roundNumber(comision_alquiler_filax,2));
     		        });
   			}
   			
   			//Se pone en 0.00 todas las alquileres
   			$(':input[class^="form-control porc_comision"]').each(function() {//Hago Nuevos Calculos
  				  
   				var idName =  $(this).attr("id");
   				var index = idName.replace('porcentaje_comision', '');
   				$('#agente_comision_alquiler'+index).val('0.00');
   			});
    			
    			
      			
   		}
   		//En caso de Ambos debe por fuerza seleccionar otras opciones
   		 else{
    	 		$('#valorTable').css('display', 'none');
                        $('#valor1Table').css('display', 'none');
    	 		$('#ventaTable').css('display', 'none');
    	 		$('#comisionTable').css('display', 'none');
    	 		$('#editarPropiedad ').find('input[name*="campo[ch_comision_compartida]"]').prop('checked',false);
    	 		$('#editarPropiedad ').find('input[name*="campo[ch_comision_compartida]"]').prop('disabled','disabled');
    	 		
    	 		$('#editarPropiedad').find('select[name*="campo[uuid_tipo_transaccion]"]').attr("disabled", false);
    	 		$('#editarPropiedad').find('select[name*="campo[uuid_tipo_transaccion]"]').children().removeAttr("selected");
    		}
   		
         
     }else{

        // $('#uuid_sociedad').empty().append('<option value="">Seleccione</option>').prop('disabled', 'disabled');
     }

 });
}
	
	