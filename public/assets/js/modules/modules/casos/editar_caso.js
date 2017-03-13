$(function(){
 	var modulo_caso = window.location.pathname.match(/casos/g) ? true : false;

	var flag_permiso_administrador = 'false'; 
	if(typeof permiso_administrador !== 'undefined'){ //Entra solo en el modulo de clientes
		flag_permiso_administrador = permiso_administrador;
	}
	
	//Verificra si existe permiso_asignado
	var flag_permiso_asignado = 0; 
	if(typeof permiso_asignado !== 'undefined'){
		flag_permiso_asignado = permiso_asignado;
	}
	
	//Datos para el Formulario Principal: Crear
	$('#editarCaso').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: ''
	});

    var now = moment();


    //Abrir modals de clientes y contactos
    $("#uuid_clienteBtn").on("click", function(){
        $('#busquedaClienteModal').modal('toggle');
    });

    //Boton para mostrar ventana de busqueda de Clientes
    $("#crearActividad").on("click", "#uuid_clienteBtn", function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
    });

    //Al abrir el modal de Busqueda de Clientes
    //Redimensionar el jqgrid
    $('#busquedaClienteModal').on('shown.bs.modal', function (e) {
        $(".ui-jqgrid").each(function(){
            var w = parseInt( $(this).parent().width()) - 6;
            var tmpId = $(this).attr("id");
            var gId = tmpId.replace("gbox_","");
            $("#"+gId).setGridWidth(w);
        });
    });

    //Pasar la Seleccion del modal al chosen
    $("#clientesGrid").on("click", ".viewOptions", function(){
     	$('form#editarCaso').find('select[name*="campo[uuid_cliente]"] option[value="'+ $(this).attr("data-cliente") +'"]').prop('selected', 'selected');
    	 setTimeout(function(){
             $(".chosen-select").chosen({
                 width: '100%'
             }).trigger('chosen:updated');
         }, 500);
 
        $('#busquedaClienteModal').modal('hide');
    });

    $('form#editarCaso').on("click", 'input[id="campo[guardar]"][type="button"]', function(e){
     	e.preventDefault();
    	e.returnValue=false;
    	e.stopPropagation();
    	
     
    	 
     	$('form#editarCaso').find('input:disabled, select:disabled').removeAttr("disabled");
    	
    	var uuid_caso = $(this).attr('data-caso');

    	$.ajax({
    		url: phost() + 'casos/ver-caso/'+ uuid_caso,
    		data: $('form#editarCaso').serialize(),
    		type: "POST",
    		dataType: "json",
    		cache: false,
    	}).done(function(json) {

    		//Check Session
    		if( $.isEmptyObject(json.session) == false){
    			window.location = phost() + "login?expired";
    		}
    		
    		//If json object is empty.
    		if($.isEmptyObject(json.results) == true){
    			return false;
    		}
     	     
    		var class_mensaje = json.results[0] == true ? 'alert-success' : 'alert-danger';
			var mensaje = json.results[0] == true ? 'Se ha actualizado satisfactoriamente el caso.' : 'Hubo un error tratando de actualizar el caso.';
			
			//Mostrar Mensaje
			mensaje_alerta(mensaje, class_mensaje);
			
			//Hacer scroll del formulario hacia arriba 
			$('html, body').animate({ scrollTop: 0 }, 'slow');
			
			//Recargar el Grid de Oportunidades
			$("#casosGrid").setGridParam({
				url: phost() + 'casos/ajax-listar-casos',
				datatype: "json",
				postData: {
					uuid_cliente: uuid_cliente,
 					erptkn: tkn
				}
			}).trigger('reloadGrid');
 
       	});
    	
    });
   // if(modulo_caso == false){ //Solo que funcione en Clientes
    	if(permiso_editar_caso == "false"){
        	$("form#editarCaso").find('input[name="campo[guardar]"]').attr('value', 'Sin Permiso');
    		$("form#editarCaso").find('select, input, button, textarea').prop("disabled", "disabled");
        }else{
        	 if( flag_permiso_administrador == 'true'){
        	    	//console.log("Abierto");
        	 } 
        	 else {
        		 if( flag_permiso_asignado == 1 ){
        	        	//console.log("Abierto");
        	        } else {
        	        	$("form#editarCaso").find('input[name="campo[guardar]"]').attr('value', 'Sin Permiso');
        	    		$("form#editarCaso").find('select, input, button, textarea').prop("disabled", "disabled");
        	        }
        	 }
        }
  /*  }else{ //En caso de modulos de casos
    	if(permiso_editar_caso == "false"){
        	$("form#editarCaso").find('input[name="campo[guardar]"]').attr('value', 'Sin Permiso');
    		$("form#editarCaso").find('select, input, button, textarea').prop("disabled", "disabled");
        }
    }*/
    
  
});
