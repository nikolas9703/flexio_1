$(function(){
	
	var flag_permiso_administrador = 'false'; 
	if(typeof permiso_administrador !== 'undefined'){ //Entra solo en el modulo de clientes
		flag_permiso_administrador = permiso_administrador;
	}
	
	//Verificra si existe permiso_asignado
	var flag_permiso_asignado = 1; 
	if(typeof permiso_asignado !== 'undefined'){ //Entra solo en el modulo de clientes
		flag_permiso_asignado = permiso_asignado;
	}
    if(uuid_cliente!="" && uuid_cliente!=undefined){
        $('form#crearCaso').find('select[name*="campo[uuid_cliente]"] option[value="'+ uuid_cliente +'"]').prop('selected', 'selected');
        setTimeout(function(){
            $(".chosen-select").chosen({
                width: '100%'
            }).trigger('chosen:updated');
        }, 500);
    }
    
  //jQuery Validate
	$('form#crearCaso').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '',
		submitHandler: function(form) {
 			form.submit();
	}
	});
	 
 
	setTimeout(function(){
		
		$('form#crearCaso').find('select[name="campo[id_asignado]"]').val(uuid_usuario);
			
 		if($('form#crearCaso').find('input[id="campo[guardar]"][type="button"]').attr('id') != undefined)
		{
			$('form#crearCaso').on('click', 'input[id="campo[guardar]"][type="button"]', function(e){
				e.preventDefault();
				
 				$('form#crearCaso').find('input:disabled, select:disabled').removeAttr("disabled");
 				 if ($("form#crearCaso").valid() != false) {
 			 
				$.ajax({
					url: phost() + 'casos/crear-caso/0',
					data: $('form#crearCaso').serialize(),
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
					var mensaje = json.results[0] == true ? 'Se ha creado el caso satisfactoriamente.' : 'Hubo un error tratando de crear el caso.';

					//Mostrar Mensaje
					mensaje_alerta(mensaje, class_mensaje);
					
 					//Hacer scroll del formulario hacia arriba 
					$('html, body').animate({ scrollTop: 0 }, 'slow');
 					//Limpiar formulario
					$('form#crearCaso').find('input[name="campo[asunto]"]').val('');
 					$('form#crearCaso').find('#id_estado option:eq(0), #id_tipo  option:eq(0)').prop("selected", "selected");
  					$("form#crearCaso").find('textarea[id="campo[descripcion]"], textarea[id="campo[resolucion]"], textarea[id="campo[comentarios]"]').val('');
  					
  					if(json.results[0] == true)
					{
 						//Recargar Grid
						 $("#casosGrid").setGridParam({
							url: phost() + 'casos/ajax-listar-casos',
							datatype: "json",
							postData: {
								uuid_cliente: uuid_cliente_casos,
 								erptkn: tkn
							}
						}).trigger('reloadGrid'); 
					}
					
  				});
				
				
				}
			});
			
			 
		}
	}, 1000);
	
	
	
	
 	//Por defecto asignado a debe ser la persona logeada
 	//$('#id_asignado option[value="'+ uuid_usuario +'"]').prop('selected', uuid_usuario); //Corregir esta mal

    var now = moment();

    //Inicializando datetimepicker en campo fecha/hora
    $('input[name*="campo[fecha"]').val(now.format('YYYY-MM-DD HH:mm:ss'));

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
     	$('form#crearCaso').find('select[name*="campo[uuid_cliente]"] option[value="'+ $(this).attr("data-cliente") +'"]').prop('selected', 'selected');
    	 setTimeout(function(){
             $(".chosen-select").chosen({
                 width: '100%'
             }).trigger('chosen:updated');
         }, 500);
 
        $('#busquedaClienteModal').modal('hide');
    });
    
     if( flag_permiso_administrador == 'true'){
    	//console.log("Abierto");
     } else {
    	if( flag_permiso_asignado == 1 ){
        	//console.log("Abierto");
        } else {
        	$("form#crearCaso").find('input[name="campo[guardar]"]').attr('value', 'Sin Permiso');
    		$("form#crearCaso").find('select, input, button, textarea').prop("disabled", "disabled");
        }	
    }
    
    
    
});
