/**
 * @category   Js Módulo
 * @package    Proyectos
 * @author     Pensanomica Team
 * @author     ...
 * @copyright  2015
 * @license    ...
 * @version    SVN:  
 */
$(function() {
	//Funcion que envie los valores aunque esten deshabiltados
	 $('form').bind('submit', function() {
	        $(this).find(':input').removeAttr('disabled');
	 }); 
	//Vaores Automaticos al seleccionar un proyecto
     $("form#crearPropiedad").on("change", 'select[name*="proyecto[id_proyecto]"] ', function(e){ 
 		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
 		if( $(this).val() ==''){
 			$('#valorTable').css('display', 'none');
                        $('#valor1Table').css('display', 'none');
 	 		$('#ventaTable').css('display', 'none');
 	 		
 	 		$('#crearPropiedad').find('select[name*="campo[uuid_tipo_transaccion]"]').attr("disabled", false);
  	 		$('#crearPropiedad').find('select[name*="campo[uuid_tipo_transaccion]"]').children().removeAttr("selected");
 		}
 		else
 			popular_tipo_transaccion($(this).val());
   	});
	
	
	
    //Por defecto asignado el proyecto, Cuando viene del modulo de proyectos
	$('#crearPropiedad ').find('select[name*="proyecto[id_proyecto]"] option[value="'+ uuid_proyecto +'"]').prop('selected', 'selected');
	/*if(categoria_pertenece != 'admin'){
		$('#crearPropiedad ').find('select[name*="campo[uuid_categoria]"] option[value="'+ categoria_pertenece +'"]').prop('selected', 'selected');
		$('#crearPropiedad ').find('select[name*="campo[uuid_categoria]"]').attr("disabled", "disabled");
 	}*/
    //Por defecto seleccionar el tipo de transacción del proyecto
    $('#crearPropiedad ').find('select[id*="uuid_tipo_transaccion"] option[value="'+ uuid_tipo_transaccion_var +'"]').prop('selected', 'selected');

    //Checkbox
    $('#crearPropiedad ').find('input[name*="campo[ch_comision_compartida]"]').attr('disabled','disabled');
    $('#crearPropiedad ').find('input[name*="comision[0][porcentaje_comision]"]').val('0.00');
    $('#crearPropiedad ').find('input[name*="comision[0][porcentaje_comision]"]').attr('disabled','disabled');
     //Por defecto usar el promotor del proyecto
    $('#crearPropiedad ').find('input[id*="campo[promotor]"]').val(promotor_var);
    
    $('#crearPropiedad ').find('input[id*="campo[cuota_mantenimiento_m2]"]').val(cuota_mantenimiento_m2_var);

     setTimeout(function(){
        $(".chosen-select").chosen({
            width: '100%'
        }).trigger('chosen:updated');
    }, 500);
    
 	//Por defecto asignado a debe ser la persona logeada
 	$('#uuid_usuario option[value="'+ uuid_usuario +'"]').prop('selected', uuid_usuario);
	
	//Por defecto esta tabla debe estar cerrada
	$('#comisionTable').css('display', 'none');
	
	//Por defecto este input debe estar bloqueado
	$('input[name="valor[0][monto]"]').attr("readonly", "readonly");
	
	//Por defecto esta tabla debe estar cerrada
	$('#valorTable').css('display', 'none');  
        $('#valor1Table').css('display', 'none'); 
	$('#ventaTable').css('display', 'none');  
	
 	 //----------------------------------------------------------------------------------------------
         
 	
  	//Aqui empieza los calculos automaticos para Monto Comision Alquiler/Venta
 	//Readolny para monto de comisiones
 	$('input[id="monto_alquiler0"]').attr("readonly", "readonly");
 	$('input[id="monto_venta0"]').attr("readonly", "readonly");
 	
 	//Funcion que calcular el nonto de comnision del alquiler, ya sea al cambiar: Valor del alquiler o los meses de alquiler
 	//Calculo para el monto
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
        
        comision_alquiler = valor*meses;
        $('input[name="valor[0][monto_alquiler]"]').val(  roundNumber(comision_alquiler,2) );
        
        
        var tipo_transaccion = $("option:selected", 'select[name="campo[uuid_tipo_transaccion]"').text();
   	    
         if( (Number($('input[name*="venta[0][monto_venta]').val())  && (tipo_transaccion  == 'Venta' || tipo_transaccion  == 'Ambos'))> 0 
        	|| (Number($('input[name*="valor[0][monto_alquiler]"]').val()) && (tipo_transaccion  == 'Alquiler' || tipo_transaccion  == 'Ambos')) ){ 
        	 $('#crearPropiedad ').find('input[name*="campo[ch_comision_compartida]"]').attr('disabled',false);
        }
        else{
        	$('#crearPropiedad ').find('input[name*="campo[ch_comision_compartida]"]').attr('disabled','disabled');
         	
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
		    	 $('#crearPropiedad ').find('input[name*="campo[ch_comision_compartida]"]').attr('disabled',false);
		}	 	
        else{
         		$('#crearPropiedad ').find('input[name*="campo[ch_comision_compartida]"]').attr('disabled','disabled');
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
 	
 	
 	//-------------------------------------------------------------------------------//
 	//--------------Validacion del Fomulatio antes del submit-------------------------
	//-------------------------------------------------------------------------------//
	//Validacion del Formulario
	$('#crearPropiedad').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '' 
		
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
	
	//Dependiendo si tiene Comision  se activa
 	$('#crearPropiedad').on('change', 'input[id*="campo[ch_comision_compartida]"]', function(e){
 		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
 		//Abrir tabla
		if($(this).is(':checked') == true ){
			$('#comisionTable').css('display', 'block');
			$('input[id="agente_comision_alquiler0"]').attr("readonly", "readonly");
			$('input[id="agente_comision_venta0"]').attr("readonly", "readonly");
   		}else{
			$('#comisionTable').css('display', 'none'); 
			
 		}
	});
        //--------------------------------------------------------------------------------------------------------
        /*$('#crearPropiedad').on('change', 'input[id*="ch_fecha_incremento0"]', function(e){
 		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
	});*/
 	
 	//Incialización para el Mapa, por defecto, Calle 50
 	$('.google_map').locationpicker({
 			location: {latitude: 8.983175444197041, longitude: -79.49915617449949},	
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
 	
 	/**
	 * 
	 * Listado de Porcentajes de Comision dependiendo del agente
	 * 
 	 */

	 $('form#crearPropiedad').on('change', 'select[name="comision[0][id_agente_comision]"]', function(e){
 
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
	        $('#porcentaje_comision0').val("0.00");
	        //If json object is not empty.
	        if( $.isEmptyObject(json.results[0]) == false ){
	        	
	        	var porcentaje_participacion = json.results[0][0]['porcentaje_participacion'];
	        	/*console.log(porcentaje_participacion);
	        	 if (porcentaje_participacion.indexOf("__") >= 0 || porcentaje_participacion.indexOf("_") >= 0){
	                 var porcentaje_participacion = porcentaje_participacion.replace("__", "00");
	                 var porcentaje_participacion = porcentaje_participacion.replace("_", "0");
	             }*/
	                
	               
          	   $('#porcentaje_comision0').val(porcentaje_participacion);
          	    
          	  $('#crearPropiedad ').find('input[name*="comision[0][porcentaje_comision]"]').attr('disabled',false);
           	    
          	    var tipo_transaccion = $("option:selected", 'select[name="campo[uuid_tipo_transaccion]"').text();
           	    
          	    if(tipo_transaccion  == 'Alquiler' ){
          	    	
          	    	var monto_repartir_alquiler = $('input[name="valor[0][monto_alquiler]"]').val();
          	    	var comision_alquiler_fila1 = (porcentaje_participacion/100)*Number(monto_repartir_alquiler);
          	    	
            	    	$('input[name="comision[0][agente_comision_alquiler]"]').val( roundNumber(comision_alquiler_fila1,2));
          	    	    $('input[name="comision[0][agente_comision_venta]"]').val('0.00');
          	    	 
        		}else if(tipo_transaccion  == 'Venta' ){
        			var monto_repartir_venta = $('input[name="venta[0][monto_venta]"]').val();
        			var comision_venta_fila1 = (porcentaje_participacion/100)*Number(monto_repartir_venta);
        			
         			$('input[name="comision[0][agente_comision_alquiler]"]').val('0.00');
        			$('input[name="comision[0][agente_comision_venta]"]').val( roundNumber(comision_venta_fila1,2));
        		}
        		else if(tipo_transaccion  == 'Ambos' ){

        			//Alquiler
        			var monto_repartir_alquiler = $('input[name="valor[0][monto_alquiler]"]').val();
        			var comision_alquiler_fila1 = (porcentaje_participacion/100)*Number(monto_repartir_alquiler);
        			$('input[name="comision[0][agente_comision_alquiler]"]').val( roundNumber(comision_alquiler_fila1,2));
        			
        			//Venta
        			var monto_repartir_venta = $('input[name="venta[0][monto_venta]"]').val();
        			var comision_venta_fila1 = (porcentaje_participacion/100)*Number(monto_repartir_venta);
        			$('input[name="comision[0][agente_comision_venta]"]').val( roundNumber(comision_venta_fila1,2)); 
        		}
	        }
	        else{
	        	$('input[name="comision[0][agente_comision_venta]"]').val('0.00');
	        	$('input[name="comision[0][agente_comision_alquiler]"]').val('0.00');
	        }
		});	 
	});

 	   $('input[name="comision[0][porcentaje_comision]"]').keyup(function() {
 		   
 		  var porcentaje_comision_fila1 = $(this).val();
 		  
 		   if (porcentaje_comision_fila1.indexOf("__") >= 0 || porcentaje_comision_fila1.indexOf("_") >= 0){
 	            var porcentaje_comision_fila1 = porcentaje_comision_fila1.replace("__", "00");
 	            var porcentaje_comision_fila1 = porcentaje_comision_fila1.replace("_", "0");
 	        }
 		  
 		   console.log(porcentaje_comision_fila1);
 		    
 			var tipo_transaccion = $("option:selected", 'select[name="campo[uuid_tipo_transaccion]"').text();
			 
        	 if(tipo_transaccion  == 'Alquiler' ){
       	    	
	       	    	var monto_repartir_alquiler = $('input[name="valor[0][monto_alquiler]"]').val();
	     			var comision_alquiler_fila1 = (porcentaje_comision_fila1/100)*Number(monto_repartir_alquiler);
	     			$('input[name="comision[0][agente_comision_alquiler]"]').val( roundNumber(comision_alquiler_fila1,2));
	     			$('input[name="comision[0][agente_comision_venta]"]').val('0.00');
     			
       	    	 
     		}else if(tipo_transaccion  == 'Venta' ){
     			var monto_repartir_venta = $('input[name="venta[0][monto_venta]"]').val();
     			var comision_venta_fila1 = (porcentaje_comision_fila1/100)*Number(monto_repartir_venta);
     			$('input[name="comision[0][agente_comision_venta]"]').val( roundNumber(comision_venta_fila1,2)); 
     			$('input[name="comision[0][agente_comision_alquiler]"]').val('0.00');
       		}
     		else if(tipo_transaccion  == 'Ambos' ){

     			//Alquiler
     			var monto_repartir_alquiler = $('input[name="valor[0][monto_alquiler]"]').val();
     			var comision_alquiler_fila1 = (porcentaje_comision_fila1/100)*Number(monto_repartir_alquiler);
     			$('input[name="comision[0][agente_comision_alquiler]"]').val( roundNumber(comision_alquiler_fila1,2));
     			
     			//Venta
     			var monto_repartir_venta = $('input[name="venta[0][monto_venta]"]').val();
     			var comision_venta_fila1 = (porcentaje_comision_fila1/100)*Number(monto_repartir_venta);
     			$('input[name="comision[0][agente_comision_venta]"]').val( roundNumber(comision_venta_fila1,2)); 
     		} 

	 	});
            //OnChange al momento de tocar Tipo de incremento
            var padre = document.getElementById("valor_incremento0").parentNode;
           padre.style.display = (padre.style.display == 'none') ? 'block' : 'none'; 
            $('form#crearPropiedad').on('change', 'select[name="valor1[0][tipo_incremento]"]', function(e){
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
 
 	  //OnChange al momento de tocar Tipo de Transacción 
 	  $('form#crearPropiedad').on('change', 'select[name="campo[uuid_tipo_transaccion]"]', function(e){
 		
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
 		 }
 	  });
     //Auto rellenar con ceros el monto si no tiene decimales
 	//select[class*="form-control"]  input[class*="form-control porc_comision"],
    $('input[name="campo[metraje]"],input[name="comision[0][porcentaje_comision]"], input[name="valor[0][meses_alquiler]"], input[name="valor[0][valor_alquiler]"], input[name="venta[0][valor_venta]"] , input[name="venta[0][comision_propiedad_venta]"]').on('focusout',function(){

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
        var cuota_mantenimiento_m2= $('#crearPropiedad ').find('input[id*="campo[valor_m2]"]').val();
        var metraje= $('#crearPropiedad ').find('input[id*="campo[metraje]"]').val();
        var valor=metraje*cuota_mantenimiento_m2;
        
        if (valor - Math.floor(valor) == 0) {
            valor=valor+".00";
        }
        $('#crearPropiedad ').find('input[id*="valor_venta0"]').val(valor);
    });
    
    if(permiso_comision_compartida){
        $('#crearPropiedad ').find('input[id*="campo[ch_comision_compartida]"]').removeProp("disabled");
    }
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
                $('#crearPropiedad ').find('input[name*="campo[promotor]"]').val(promotor);
      		
                var cuota_mantenimiento_m2 = json.results[0][0]['cuota_mantenimiento_m2'];
                $('#crearPropiedad ').find('textarea[id*="campo[direccion]"]').val(json.results[0][0]['ubicacion']);
                $('#crearPropiedad ').find('input[id*="campo[cuota_mantenimiento_m2]"]').val(cuota_mantenimiento_m2);
      		
      		 
     		if(transaccion == 'Alquiler'){
        		$('#crearPropiedad').find('select[name*="campo[uuid_tipo_transaccion]"]').attr("disabled", "disabled");
      			$('#crearPropiedad').find('#uuid_tipo_transaccion option[value="'+ tipo_transaccion +'"]').prop('selected', tipo_transaccion);
      			
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
      			$('#crearPropiedad').find('select[name*="campo[uuid_tipo_transaccion]"]').attr("disabled", "disabled");
      			$('#crearPropiedad').find('#uuid_tipo_transaccion option[value="'+ tipo_transaccion +'"]').prop('selected', tipo_transaccion);
      			
      			
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
     	 		
     	 		$('#crearPropiedad').find('select[name*="campo[uuid_tipo_transaccion]"]').attr("disabled", false);
      	 		$('#crearPropiedad').find('select[name*="campo[uuid_tipo_transaccion]"]').children().removeAttr("selected");
      		}
     		
           
       }else{

          // $('#uuid_sociedad').empty().append('<option value="">Seleccione</option>').prop('disabled', 'disabled');
       }

   });
}
 