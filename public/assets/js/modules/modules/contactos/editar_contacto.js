/**
 * Esta variable es usada para guardar
 * el campo desde el que se activo el
 * modal de clientes.
 */
var campo_seleccionadmo = '';

/**
 * Esta variable es usada para guardar
 * el uuid_cliente del cliente seleccionado.
 */
var uuid_cliente = '';

/**
 * Esta variable es usada para guardar
 * el campo_nombre_comercial seleccionado.
 */
var campo_nombre_comercial = '';

$(function(){
    
	//Asignar el usuario logueado al contacto por defecto
	if(uuid_usuario !== 'undefined')
	$('#id_asignado option[value="'+ uuid_usuario +'"]').prop('selected', uuid_usuario);
	
	//Fix para campos chosen en tabla dinamica
	$('select.chosen-select').chosen({
        width: '100%',
    }).trigger('chosen:updated').on('chosen:showing_dropdown', function(evt, params) {
        $(this).closest('div.table-responsive').css("overflow", "visible");
    }).on('chosen:hiding_dropdown', function(evt, params) {
    	$(this).closest('div.table-responsive').css({'overflow-x':'auto !important'});
    });
	
	//Configurar modal, para que no se cierre
	//haciendo click en cualquier lado.
	$('#optionsModal').modal({
		backdrop: 'static',
		show: false
	});
	
	// --------------------
	// Funciones Tabla dinamica
	// --------------------
	//Al seleccionar cliente, popular nombre comerial si es juridico.
	$('#editarContacto').find('#clientesTable').on('change', 'select[name*="uuid_cliente"]', function(e){
		e.preventDefault();
	    e.returnValue=false;
	    e.stopPropagation();
		
	    uuid_cliente = $(this).find('option:selected').val();
	    campo_nombre_comercial = $(this).closest('tr').find('select[name*="nombre_comercial"]');
	    
	    //Popular select de nombre comerciales
	    editarcon_popular_cliente_nombre_comercial();
	});
	
	//Al presionar el boton de binoculares
	//mostrar el modal de clientes.
	$('#editarContacto').find('#clientesTable').on('click', 'button[id*="Btn"]', function(e){
		e.preventDefault();
	    e.returnValue=false;
	    e.stopPropagation();
	    
	    campo_seleccionado = $(this).closest('.input-group').find('select[name*="uuid_cliente"]');
	    
	    $('#busquedaClienteModal').modal('toggle');
	});
	
	//Pasar la seleccion del modal al chosen
    $("#clientesGrid").on("click", ".viewOptions", function(){
        
    	$('#editarContacto').find(campo_seleccionado).find('option[value="'+ $(this).attr("data-cliente") +'"]').prop('selected', 'selected');
        
    	//cerrar modal
    	$('#busquedaClienteModal').modal('hide');
    	
    	//establecer el cliente asignado.
    	uuid_cliente = $(this).attr("data-cliente");
    	
    	campo_nombre_comercial = $(campo_seleccionado).closest('tr').find('select[name*="nombre_comercial"]');
    	
        //Actualizar chosen
        setTimeout(function(){
            $(".chosen-select").chosen({
                width: '100%'
            }).trigger('chosen:updated');
        }, 600);

        //Popular select de nombre comerciales
        setTimeout(function(){
        	editarcon_popular_cliente_nombre_comercial();
        }, 500);
    });
    
    //Al presionar el checkbox de nombre comercial
	//habilitar el campo o desabilitarlo en caso que le haga uncheck.
	$('#editarContacto').find('#clientesTable').on('click', 'input[type="checkbox"][id*="Check"]', function(e){

	    var object = this;
	    
		if(this.checked){ 
	    	//remover atributo disabled
	    	$(this).closest('tr').find('select[name*="nombre_comercial"]').removeAttr('disabled');
	    	
		}else{
			
			//Limpiar campo 'nombre comecial'
	    	$(this).closest('tr').find('select[name*="nombre_comercial"]').empty();
			
			//agregarle atributo disabled
	    	$(this).closest('tr').find('select[name*="nombre_comercial"]').attr('disabled', 'disabled');
		}

	    //Actualizar chosen plugin
	    setTimeout(function(){
	    	$(object).closest('tr').find(".chosen-select").chosen({
                width: '100%'
            }).trigger('chosen:updated');
        }, 600);
	});
	
	 //Agregar un input hidden al cargar el plugin switchery para acomodar el formulario.
    $(switchery.switcher).css({"display":"block"}).after('<input type="text" class="form-control" style="visibility: hidden; position: relative; top:-35px; width:0px; height:0px">');
    
	//Datos de Contacto
	//Boton Mas Informacion
	$('#editarContacto').on('change', 'input[id*="campo[mas_informacion]"]', function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		//Abrir panel, si no esta visible
		if($(this).is(':checked') == true){
			$('.ApellidoMaterno, .ApellidoCasada').removeClass('hide');
		}else{
			$('.ApellidoMaterno, .ApellidoCasada').addClass('hide');
		}
	});
	
	// Redimensioanr Grid al cambiar tamaño de la ventanas.
	$('#busquedaClienteModal').on('shown.bs.modal', function (e) {
	    $(".ui-jqgrid").each(function(){
	        var w = parseInt( $(this).parent().width()) - 6;
	        var tmpId = $(this).attr("id");
	        var gId = tmpId.replace("gbox_","");
	        $("#"+gId).setGridWidth(w);
	    });
	});

	//Validar que los correos introducidos, tengan un formato de correo valido.
	$('input[id*="campo[email]"]').on('beforeItemAdd', function(event) {
	    // event.item: contains the item
	    // event.cancel: set to true to prevent the item getting added
	    var valido = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test( event.item );

	    //Verificar si el correo introducido
	    //es una direccion de correo valida.
	    if(valido){
	        event.cancel = false;
	    }else{
	        event.cancel = true;
	    }
	});
	
	//jQuery Validate
	$('#editarContacto').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '',
	});
	
	//Verificar si tiene permisos 
	//de editar el fomrulario
	if(permiso_editar_contacto == "false"){
		$("#editarContacto").find('select, input, button, textarea').prop("disabled", "disabled");
	}
	
	
	//----------------------------------
	// Al cargar la pagina
	// Verificar la tabla de clientes.
	// Llenar y mostrar bien los nombres
	// comerciales.
	$.each( $('#editarContacto').find('#clientesTable').find('select[name*="uuid_cliente"]'), function(i,obj){
		var uuid_cliente = $(this).find('option:selected').val();
		var field = this;
		var nombre_comercial = [];
		
		$.ajax({
			url: phost() + 'clientes/ajax-seleccionar-cliente',
			data: {
				uuid_cliente: uuid_cliente,
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
			if($.isEmptyObject(json.results) == true){
				return false;
			}
			
			if(json.results[0]["tipo_cliente"] == "natural"){
				
				//Limpiar campo 'nombre comecial'
				$(field).closest('tr').find('select[name*="nombre_comercial"]').empty();
				
				//agregarle atributo disabled
				$(field).closest('tr').find('select[name*="nombre_comercial"]').attr('disabled', 'disabled');
			
			    //Actualizar chosen plugin
			    setTimeout(function(){
			    	$(field).closest('tr').find(".chosen-select").chosen({
		                width: '100%'
		            }).trigger('chosen:updated');
		        }, 500);
				
			}else{
				
				//Si el cliente es JURIDICO
				var sociedades_seleccionadas = $(field).closest('tr').find('select[name*="nombre_comercial"]').find('option:selected');
				var campo_nombre_comercial = $(field).closest('tr').find('select[name*="nombre_comercial"]');
				
				//Introducir los nombres comeciales
				//en el arreglo.
				$.each(sociedades_seleccionadas, function(i,sociedad){
					nombre_comercial.push(this.value);
				});
	
				$.ajax({
					url: phost() + 'oportunidades/ajax-seleccionar-cliente-sociedades',
					data: {
						uuid_cliente: uuid_cliente,
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
	
					//If json object is not empty.
					if( $.isEmptyObject(json.results[0]) == false ){
						
						//Si hay nombres comerciales (sociedades) seleccionados
						if( sociedades_seleccionadas.length > 0 ){
							
							$('#editarContacto').find(campo_nombre_comercial).empty().removeAttr('disabled');
							$.each(json.results[0], function(i, result){
								var selected = $.inArray(result['uuid_sociedad'], nombre_comercial) > -1 ? 'selected="selected"' : "";
								$('#editarContacto').find(campo_nombre_comercial).append('<option value="'+ result['uuid_sociedad'] +'" '+ selected +'>'+ result['nombre_comercial'] +'</option>');
							});
							
							//Ponerle atributo cheked al checkbox de nombre comercial
							$(field).closest('tr').find('input[type="checkbox"][id*="Check"]').trigger('click');
							
						}else{
							
							$('#editarContacto').find(campo_nombre_comercial).empty().removeAttr('disabled');
							$.each(json.results[0], function(i, result){
								$('#editarContacto').find(campo_nombre_comercial).append('<option value="'+ result['uuid_sociedad'] +'">'+ result['nombre_comercial'] +'</option>');
							});
						}
						
					}else{
						
						$('#editarContacto').find(campo_nombre_comercial).empty().prop('disabled', 'disabled');
					}
				});

			}
		});
	});
	
	//Eliminar individualmente cada nombre comercial
	//relacionadmo a un cliente.
	setTimeout(function(){
	$('#editarContacto').find('#clientesTable').on('change', 'select[name*="nombre_comercial"]', function(e, params) {
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var uuid_contacto = $('#editarContacto').find('input[id="campo[guardar]"][type="button"]').attr('data-contacto');
		
		if(params['deselected'])
		{
			$.ajax({
				url: phost() + 'contactos/ajax-eliminar-cliente-nombre-comercial',
				data: {
					uuid_contacto: uuid_contacto,
					uuid_sociedad: params['deselected'],
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
				if($.isEmptyObject(json.results) == true){
					return false;
				}
				
				//Mostrar Mensaje
				$class_mensaje = json.results[0]['respuesta'] == true ? 'alert-success' : 'alert-danger';
				mensaje_alerta(json.results[0]['mensaje'], $class_mensaje);
			});
		}
	});
	}, 1800);
	
	//Tabla de Clientes
	//Boton de Eliminar Cliente
	//Eliminar fila completa de cliente
	$('#editarContacto #clientesTable').on('click', '.eliminarBtn', function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var indice_fila = $(this).attr('data-index');
		var table = $(this).closest('table');
		var row = $(this).closest('tr');
		var uuid_cliente = $(this).attr('data-id');
		var agrupador_campos = $(row).attr("id").replace(/[0-9]/g, '');
		
		//Resaltar la fila que se esta
		//seleccionando para eliminar.
		$(row).addClass('highlight');
		
		//Ventana de Confirmacion
		$('#optionsModal').find('.modal-title').empty().append('Confirme');
		$('#optionsModal').find('.modal-body').empty().append('&iquest;Esta seguro que desea eliminar este cliente?');
		$('#optionsModal').find('.modal-footer')
		.empty()
		.append(
			$('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal" />').append('Cancelar').click(function(e){
				$(row).removeClass('highlight');
			})
		).append(
					
			$('<button class="btn btn-w-m btn-danger" type="button" />').append('Eliminar').click(function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();
	
			   	//Eliminar Usuarios
				$.ajax({
					url: phost() + 'contactos/ajax-eliminar-cliente',
					data: {
						uuid_cliente: uuid_cliente,
						uuid_contacto: uuid_contacto,
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
					//remover la soceidad seleccionada de la tabla
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
					
					//Cerrar Ventana
					$('#optionsModal').modal('hide');
				});
			})	
		);
		
		$('#optionsModal').on('hidden.bs.modal', function (e) {
			$('#sociedadesTable tbody').find('tr').removeClass('highlight');
		});
		
		$('#optionsModal').modal('show');
	});
	
	
	setTimeout(function(){
		
		//Guardar tipo input button, para cuando se 
		//cargar formulario desde otro modulo.
		if($('#editarContacto').find('input[id="campo[guardar]"][type="button"]').attr('id') != undefined)
		{
			$('#editarContacto').on('click', 'input[id="campo[guardar]"][type="button"]', function(e){
				e.preventDefault();
				
				$('#editarContacto').find('input:disabled, select:disabled').removeAttr("disabled");
				
				var uuid_contacto = $(this).attr('data-contacto');
				
				//Submitting multipart/form-data using jQuery and Ajax
				var formData = new FormData($('form#editarContacto')[0]);

				$.ajax({
					url: phost() + 'contactos/ver-contacto/'+ uuid_contacto,
					data: formData,
					type: 'POST',
					async: false,
					cache: false,
					contentType: false,
					processData: false,
					dataType: "json",
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
					var mensaje = json.results[0] == true ? 'Se ha actualizado satisfactoriamente el contacto.' : 'Hubo un error tratando de actualizar el contacto.';
					
					//Mostrar Mensaje
					mensaje_alerta(mensaje, class_mensaje);
					
					//Hacer scroll del formulario hacia arriba 
					$('html, body').animate({ scrollTop: 0 }, 'slow');
				});
			});
		}
		
	}, 1000);
	
});


//Popular nombre comerciales del cliente seleccionado.
function editarcon_popular_cliente_nombre_comercial(uuidcliente){

	if(uuidcliente != ""){
		uuid_cliente = uuidcliente;
	}else if(uuid_cliente==""){
		return false;
	}
	
	$.ajax({
		url: phost() + 'oportunidades/ajax-seleccionar-cliente-sociedades',
		data: {
			uuid_cliente: uuid_cliente,
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

		//If json object is not empty.
		if( $.isEmptyObject(json.results[0]) == false ){
			
			$('#editarContacto').find(campo_nombre_comercial).empty().removeAttr('disabled');
			$.each(json.results[0], function(i, result){
				$('#editarContacto').find(campo_nombre_comercial).append('<option value="'+ result['uuid_sociedad'] +'">'+ result['nombre_comercial'] +'</option>');
			});
		}else{
			
			$('#editarContacto').find(campo_nombre_comercial).empty().prop('disabled', 'disabled');
		}
		
		//Actualizar campos chosen
		$('select.chosen-select').chosen({
	        width: '100%',
	    });
	});
}



