$(function(){
	
	var id_form = '';
	var id_cat = '';
	var nombre_valor = '';
	var id_grid = '';
	
	//Catalogo Etapa Oportunidades
	moduloConfiguracion.initGridCatalogo("#oportunidadEtapaVenta", "oportunidades", "id_etapa_venta");
	
	//-------------------------
	// Boton de opciones
	//-------------------------
	$('div[id*="gbox_"]').on("click", ".viewOptions", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		id_cat = $(this).attr("data-id-cat");
		id_form = id_grid + 'Form';
		//id_grid = $(this).closest('div[id*="gbox_"]').attr('id').replace('gbox_', '');

		var rowINFO = $("#"+ id_grid).getRowData(id_cat);
	    var options = rowINFO["options"];
	    	nombre_valor = rowINFO["Valor"];
	    
	    //Init boton de opciones
		$('#optionsModal').find('.modal-title').empty().append('Opciones: '+ nombre_valor);
		$('#optionsModal').find('.modal-body').empty().append(options);
		$('#optionsModal').find('.modal-footer').empty();
		$('#optionsModal').modal('show');
	});
	
	//Editar Valor de Catalogo
	$("#optionsModal").on("click", ".editarCatlogoBtn", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		$("#"+ id_form).find('input[name="valor"]').val(nombre_valor);
		$("#"+ id_form).find('input[name="id_cat"]').val(id_cat);
		
		$('#optionsModal').modal('hide');
	});
	
	//Eliminar Valñor de Catalogo
	$("#optionsModal").on("click", ".borrarCatlogoBtn", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var row = $(this).closest('tr');
		var modulo = $(this).attr('data-modulo');
  		var id_cat = $(this).attr('data-id-cat');
  		var campo = $(this).attr('data-campo');
  		
  		//Resaltar la fila que se esta
		//seleccionando para eliminar.
		$(row).addClass('highlight');
		
  		var mensaje_confirmacion = '&#191;Esta seguro que desea eliminar valor del catalogo?';
 
  		//console.log(mensaje_confirmacion);
  		//console.log(row);
  	
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
					
					var parametros = {modulo:modulo, campo:campo, id_cat:id_cat};
					
					//Guardar valor de catalogo
					var eliminarCatalogoForm = moduloConfiguracion.eliminarCatalogo(parametros);
					
					eliminarCatalogoForm.done(function(json){
						
						json = $.parseJSON(json);
						
						//Check Session
						if( $.isEmptyObject(json.session) == false){
							window.location = phost() + "login?expired";
						}

						//If json object is empty.
						if($.isEmptyObject(json.results[0]) == true){
							return false;
						}
						
						class_mensaje = json.results[0]['respuesta'] == true ? 'alert-success' : 'alert-danger';
						mensaje_alerta(json.results[0]['mensaje'], class_mensaje);
		
						//Recargar jQgrid
						$("#"+ id_grid).trigger('reloadGrid');
						
						//Cerrar Ventana
						$('#optionsModal').modal('hide');
					});
				})
			);
	});
	
	//Al abrir accordeon y mostrar contenido
	$('#accordeonCatalogos').on('shown.bs.collapse', function (e) {
		e.target // newly activated tab
		e.relatedTarget // previous active tab
		
		id_grid = $('form[id$="Form"]:visible').find('div[id*="gbox_"]').attr('id').replace('gbox_', '');
		
		//Ajustar dimension del jqgrid
		$('form[id$="Form"]:visible').find(".ui-jqgrid").each(function(){
			var w = parseInt( $(this).parent().width()) - 6;
			var tmpId = $(this).attr("id");
			var gId = tmpId.replace("gbox_","");
			$("#"+gId).setGridWidth(w);
		});
		
		//Guardar valor del catalogo
		$('form[id$="Form"]:visible').on('click', '.guardarCatalogoBtn', function(){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			var valor = $('form[id$="Form"]:visible').find('input[name="valor"]').val();
			var modulo = $(this).attr("data-modulo");
			var campo = $(this).attr("data-campo");
			
			var parametros = {valor:valor, modulo:modulo, campo:campo, id_cat:id_cat};
			
			//Validar campo
			if($('form[id$="Form"]:visible').validate().form() == true)
			{
				//Guardar valor de catalogo
				var formularioCatalogo = moduloConfiguracion.guardarEditarCatalogo(parametros);
				
				formularioCatalogo.done(function(json){
					
					json = $.parseJSON(json);
					
					//Check Session
					if( $.isEmptyObject(json.session) == false){
						window.location = phost() + "login?expired";
					}

					//If json object is empty.
					if($.isEmptyObject(json.results[0]) == true){
						return false;
					}
					
					class_mensaje = json.results[0]['respuesta'] == true ? 'alert-success' : 'alert-danger';
					mensaje_alerta(json.results[0]['mensaje'], class_mensaje);
					
					//Limpiar Formulario
					limpiarFormulario();
					
					//Recargar jQgrid
					$("#"+ id_grid).trigger('reloadGrid');
				});
			}
		});
		
		//Inicializar Validate
		$('form[id$="Form"]:visible').validate({
		    focusInvalid: true,
		    debug: false,
		    ignore: '',
		    wrapper: '',
		});
		
		//Cancelar guardar catalogo
		$('.cancelarCatalogoBtn:visible').on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			//Limpiar Formulario
			limpiarFormulario();
		});
		
		//Buscar valor en catalogo
		$('.buscarValorCatalogoCampo:visible').on("keypress", function(e){
			if (e.which == 13) {
				
				var valor = $(this).val();
				
				if(valor==""){
					return false;
				}
				
				//Recargar jQgrid
				$("#"+ id_grid).setGridParam({
					postData: {
						busqueda: valor,
						erptkn: tkn
					}
				}).trigger('reloadGrid');
			    return false;
			}
		});
		$('.buscarValorCatalogoBtn:visible').on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
				
			var valor = $('.buscarValorCatalogoCampo:visible').val();
			
			if(valor==""){
				return false;
			}
				
			//Recargar jQgrid
			$("#"+ id_grid).setGridParam({
				postData: {
					busqueda: valor,
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		});
		
		//Limpiar Busqueda
		$('.limpiarValorCatalogoBtn:visible').on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			//Limpiar campo
			$('.buscarValorCatalogoCampo:visible').val('');
			
			//Recargar jQgrid
			$("#"+ id_grid).setGridParam({
				postData: {
					busqueda: '',
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		    return false;
		});
		
	});
	
	function limpiarFormulario(){
		id_cat = '';
		$("#"+ id_form).find('input[name="valor"]:visible').val('');
		$("#"+ id_form).find('input[name="id_cat"]').val('');
	}
});