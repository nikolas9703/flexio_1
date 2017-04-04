var campo_usuarios = '';

$(function(){
	
	$('#notificadosTable').css('display', 'block');  
	
	//Fix para campos chosen en tabla dinamica
 	$('select.chosen-select').chosen({
        width: '100%',
    }).trigger('chosen:updated').on('chosen:showing_dropdown', function(evt, params) {
        $(this).closest('div.table-responsive').css("overflow", "visible");
    }).on('chosen:hiding_dropdown', function(evt, params) {
    	$(this).closest('div.table-responsive').css({'overflow-x':'auto !important'});
    });
 	
	 
  	$('#editarNotificacion').find('#notificadosTable').on('change', 'select[name*="rol"]', function(e){
  		
 		e.preventDefault();
	    e.returnValue=false;
	    e.stopPropagation();
		
	    id_rol = $(this).find('option:selected').val();
	    campo_usuarios = $(this).closest('tr').find('select[name*="id_usuario"]');
	    
	    popular_usuarios(id_rol);
	});
  	
  	/** Funcion que elimina las comisiones en la tabla Dinamica  **/
  	$('#editarNotificacion #notificadosTable').on('click', '.eliminarBtn', function(e){
  		
 		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
  		
		var indice_fila 		= $(this).attr('data-index');
		var table 				= $(this).closest('table');
		var row 				= $(this).closest('tr');
  		var uuid_notificacion_rol			= $(this).attr("data-id");
 		var agrupador_campos 	= $(row).attr("id").replace(/[0-9]/g, '');
		
 		//Resaltar la fila que se esta
		//seleccionando para eliminar.
		$(row).addClass('highlight');
  		
  		var mensaje_confirmacion = '¿Esta seguro que desea eliminar este rol?';
 
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
 					
			  		var url = phost() +  'notificaciones/ajax-eliminar-rol-usuario';
			  		
			  		$.ajax({
						url: url,
						data: {
							uuid_notificacion_rol: uuid_notificacion_rol,
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
 	
  	
});
//Popular nombre comerciales del cliente seleccionado.
function popular_usuarios(id_rol){

	if(id_rol != ""){
		id_rol = id_rol;
	}else if(id_rol==""){
		return false;
	}
 	$.ajax({
		url: phost() + 'usuarios/ajax-listar-usuarios',
		data: {
			id_rol: id_rol,
			erptkn: tkn,
			rows: 1000,
            sord: 'ASC',
            sidx: 'usr.nombre'
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
		if( json.records > 0){
			 $('#editarNotificacion').find(campo_usuarios).empty().removeAttr('disabled');
 			 // $('#editarNotificacion').find(campo_usuarios).empty();
 			  $.each( json['rows'], function( i, result ) {
   				 $('#editarNotificacion').find(campo_usuarios).append('<option value="'+ result['uuid_usuario'] +'">'+ result['cell']['0'] +' '+result['cell']['1']+'</option>');
   				
			});
		}else{
			 
			$('#editarNotificacion').find(campo_usuarios).empty().prop('disabled', 'disabled');
		}
		   $('select.chosen-select').chosen({
  				width: '100%'
  		}).trigger('chosen:updated');
		
	});
 
}