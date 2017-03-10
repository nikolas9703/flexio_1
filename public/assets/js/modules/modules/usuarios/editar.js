$(function() {

	//Set error placement
	$.validator.setDefaults({
	    errorPlacement: function(error, element){
			var elementName = $(element).attr("name"),
			groupDefaultAgencia = $.map(this.groups, function(fields, name) {
				if(fields.match(/agencia/g)){
					var fieldsArr;
					if (fields.indexOf(elementName) >= 0){
						fieldsArr = fields.split(" ");
						return fieldsArr[fieldsArr.length - 1];
					} else {
						return null;
					}
				} else {
					return null;
				}
			})[0];

			groupAgencia = $.map(this.messages, function(indx, field) {
				if(field.match(/agencia/g)){
					var fieldsArr;
					if (field.indexOf(elementName) >= 0){
						fieldsArr = field.split(" ");
						return fieldsArr[fieldsArr.length - 1];
					} else {
						return null;
					}
				} else {
					return null;
				}
			})[0];

			//Place error depend on field
	    	if(groupAgencia || groupDefaultAgencia){
	    		$('.agencia-error').empty().append( $(error).addClass('pull-left') );
	    	}else{
	    		$(element).after(error);
	    	}
	    }
	});

	//Add require from group method
	//URL: http://codepen.io/tjdunklee/pen/KbdIC/
	$.validator.addMethod("require_from_group", function(value, element, options) {
		var validator = this;
		var selector = options[1];
		var validOrNot = $(selector, element.form).filter(function() {
			return validator.elementValue(this);
		}).length >= options[0];

		//Verificar si esta seleccionado checkbox de "Todas las Agencias"
		if($('#asignarTodasAgencias').is(':checked') == true){
			return false;
		}

		if(!$(element).data('being_validated')) {
			var fields = $(selector, element.form);
			fields.data('being_validated', true);
			fields.valid();
			fields.data('being_validated', false);
		}
		return validOrNot;
	},  $.validator.format("Por favor, llene los campos marcados como requeridos."));

	//Inicializar jQuery Validate
	$('#editUsuarioForm').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '',
		//debug: true,
		groups: {
		    defaultAgencia: "id_agencia_0 id_departamento_0"
		},
		invalidHandler: function(event, validator) {
			//var errors = validator.numberOfInvalids();
			$.each( validator.errorMap, function(index,obj){
				//---------------------------------------
				// Abrir el acordeon segun la seccion 
				// donde se encuentre error.
				//---------------------------------------
				//Seccion Informacion Personal
				if(index.match(/nombre/g) || index.match(/apellido/g) || index.match(/email/g) || index.match(/id_rol/g)){
					$('#seccionInforacionPersonal').is(':hidden') == true ? $('#seccionInforacionPersonal').collapse('show') : '';
				}
				//Seccion Agencias
				else if(index.match(/agencia/g)){
					$('#seccionAgencias').is(':hidden') == true ? $('#seccionAgencias').collapse('show') : '';
				}
				//Seccion Informacion de Acceso
				else if(index.match(/usuario/g) || index.match(/pasword/g) || index.match(/confirm_password/g)){
					$('#seccionInformacionAcceso').is(':hidden') == true ? $('#seccionInformacionAcceso').collapse('show') : '';
				}
			});
		}
	});

	//Reglas de Validacion
	$('#nombre').rules("add",{ required: true, messages: { required: 'Introduzca Nombre.' } });
	$('#apellido').rules("add",{ required: true, messages: { required:'Introduzca Apellido' } });
	$('#email').rules("add",{ required: true, email:true, messages: { required:'Introduzca Email', email:'Por favor, introduzca una direccion de email valida.' } });
	$('#id_rol').rules("add",{ required: true, messages: { required:'Seleccione Rol' } });
	$('#usuario').rules("add",{ required: true, messages: { required:'Introduzca usuario' } });
	$('#confirm_password').rules("add",{ 
		required: function(element) {
			return $('#password').val() != "" ? true : false;
    	}, 
		equalTo: "#password", 
		messages: { required:'No coincide con la contrase&ntilde;a introducida.' } 
	});

	$('#id_agencia_0').rules("add",{
		required: function(element) {
			return $('#asignarTodasAgencias').is(':checked') == false ? true : false;
    	},
		messages: { required: 'Por favor, llene los campos marcados como requeridos.' }
    });
    $('#id_departamento_0').rules("add",{
		required: function(element) {
			return $('#asignarTodasAgencias').is(':checked') == false ? true : false;
    	},
		messages: { required: 'Por favor, llene los campos marcados como requeridos.' }
    });

    //Al cargar la pagina
    //Verificar si el checkbox esta seleccionado por default
    if($("#asignarTodasAgencias").is(':checked') == true){
		seleccionarTodasAgencias()
    }

	//Evento: Seleccionar todas las agencias.
	$("#editUsuarioForm").on("click", "#asignarTodasAgencias", function(e){
		if($(this).is(':checked') == true){	
			seleccionarTodasAgencias();
		}
		else
		{
			//Resetear seleccion de todas las agencias
			$('#todasAgencias').find('option').removeAttr('selected');

			//Habilitar boton de "Agregar Agencias"
			$('#agregarAgencia').removeClass("disabled");

			//Habilitar campo "Agencia"
			$('#id_agencia_0').removeAttr('disabled');
		}
	});


	//Inicializar plugin chosen, en los campos departamentos 
	//que tienen valores
	if($('select[id*="id_departamento"]').filter(function(){return $(this).find('option:checked').length > 0 ? true : false;}).length > 0){
		$('select[id*="id_departamento"]')
		.filter(function(){return $(this).find('option:checked').length > 0 ? true : false;})
		.chosen({
			width: '100%'
		}).trigger('chosen:updated');
	}else{
		$('select[id*="id_departamento"]').chosen({
			width: '100%'
		}).trigger('chosen:updated');
	}

	//Inicializar plugin chosen, en los campos 
	//celulas que tienen valores
	if($('select[id*="id_celula"]').filter(function(){return $(this).find('option:checked').length > 0 ? true : false;}).length > 0){
		$('select[id*="id_celula"]')
		.filter(function(){return $(this).find('option:checked').length > 0 ? true : false;})
		.chosen({
			width: '100%'
		}).trigger('chosen:updated');
	}else{
		$('select[id*="id_celula"]').chosen({
			width: '100%'
		}).trigger('chosen:updated');
	}

	//Eliminar Departamento uno a uno.
	$("#editUsuarioForm").on('change', 'select[id*="id_departamento"]', function(e, params) {
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		if(params.deselected)
		{
			var id_agencia = $(this).closest('tr').find('select[id*="id_agencia"]').find('option:selected').val();
			var id_usuario = $('#id_usuario').val();

			$.ajax({
				url: phost() + 'usuarios/ajax-delete-departamento',
				data: {
					id_usuario: id_usuario,
					id_agencia: id_agencia,
					id_departamento: params.deselected
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

				}else{

				}
			});
		}
	});

	//Eliminar Celula uno a uno.
	$("#editUsuarioForm").on('change', 'select[id*="id_celula"]', function(e, params) {
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		if(params.deselected)
		{
			var id_agencia = $(this).closest('tr').find('select[id*="id_agencia"]').find('option:selected').val();
			var id_usuario = $('#id_usuario').val();

			$.ajax({
				url: phost() + 'usuarios/ajax-delete-celula',
				data: {
					id_usuario: id_usuario,
					id_agencia: id_agencia,
					id_celula: params.deselected
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

				}else{

				}
			});
		}
	});

	//Fix for jquery chosen
	// Add select/deselect all toggle to optgroups in chosen
	$("#editUsuarioForm").on('click', '.group-result', function() {
	    // Get unselected items in this group
	    var unselected = $(this).nextUntil('.group-result').not('.result-selected');
	    if(unselected.length) {
	        // Select all items in this group
	        unselected.trigger('mouseup');
	    } else {
	        $(this).nextUntil('.group-result').each(function() {
	            // Deselect all items in this group
	            $('a.search-choice-close[data-option-array-index="' + $(this).data('option-array-index') + '"]').trigger('click');
	        });
	    }
	});

	//Boton Guardar
	$('#saveFormBtn').on('click', submitFormBtnHlr);
});


function submitFormBtnHlr(e)
{
	e.preventDefault();
	e.returnValue=false;
	e.stopPropagation();
	
	//Desabilitar boton
	$('#saveFormBtn').off('click', submitFormBtnHlr).prop('disabled', 'disabled');
	
	if( $('#editUsuarioForm').validate().form() == true )
	{
		//Habilitar campos, para poder capturarlos
		$('input:disabled').attr("disabled", "");
		$('input').removeAttr("readonly");
		
		//Enviar Formulario
		$('form#editUsuarioForm').submit();
		//console.log('Mierda! envio el formulario.');
	}
	else
	{
		//Habilitar boton
		$('#saveFormBtn').on('click', submitFormBtnHlr).removeAttr('disabled');
	}
}

function seleccionarTodasAgencias()
{
	//Seleccionar todas las agencias
	$('#todasAgencias').find('option[value!=""]').prop('selected', 'selected');

	//Remover las agencias agregadas dinamicamente
	$('.tablaAgencias').find('tr[id*="agencia"]').not('.default').remove();

	//Desabilitar boton de "Agregar Agencias"
	$('#agregarAgencia').addClass("disabled");

	//Desabilitar campo "Agencia" y resetear valor
	$('#id_agencia_0').prop('disabled', 'disabled');
	$('#id_agencia_0 option:eq(0)').prop('selected', 'selected');

	//Desabilitar campo "Departamento" y resetear valor
	$('#id_departamento_0').prop('disabled', 'disabled');
	$('#id_departamento_0 option').removeAttr('selected');
	$('#id_departamento_0').trigger('chosen:updated');

	//Desabilitar campo "Celula" y resetear valor
	$('#id_celula_0').prop('disabled', 'disabled');
	$('#id_celula_0 option').removeAttr('selected');
	$('#id_celula_0').trigger('chosen:updated');
	//Ocultar div que contiene campo celulas
	$('.celula-field').addClass('hide');
}

/**
 * Agencia Default
 * ------------------------------
 * Buscar departamentos/celulas que pertenecen a la agencia seleccionada.
 */
(function(){

	$("#editUsuarioForm").on("change", "#id_agencia_0", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var id_agencia = this.options[this.selectedIndex].value;

		if(id_agencia==""){
			
			//Desabilitar campos
			$('#id_departamento_0, #id_celula_0').empty().append('<option value="">Seleccione</option>').prop('disabled', 'disabled');
			$('.celula-field').addClass('hide');

			return false;
		}

		//Buscar departamentos
		$.ajax({
			url: phost() + 'usuarios/ajax-get-agencia-departamentos',
			data: {
				id_agencia: id_agencia
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
				
				$('#id_departamento_0').empty().removeAttr('disabled').append('<optgroup label="Seleccionar Todos" />');
				$.each(json.results[0], function(i, result){
					$('#id_departamento_0').find('optgroup').append('<option value="'+ result['id'] +'">'+ result['nombre_departamento'] +'</option>');
				});
				$('#id_departamento_0').removeAttr('disabled');

				//Init chosen plugin
				$('#id_departamento_0').chosen({
					width: '100%'
				}).trigger('chosen:updated');

			}else{
				
				$('#id_departamento_0').empty().append('<option value="">Seleccione</option>').prop('disabled', 'disabled');
			}
		});

		//Buscar celulas
		$.ajax({
			url: phost() + 'usuarios/ajax-get-agencia-celulas',
			data: {
				id_agencia: id_agencia
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
				
				//Mostrar div que contiene campo celulas
				$('.celula-field').removeClass('hide');
				
				//LLenar dropdown de celulas
				$('#id_celula_0').empty().empty().append('<optgroup label="Seleccionar Todos" />');
				$.each(json.results[0], function(i, result){
					$('#id_celula_0').find('optgroup').append('<option value="'+ result['id'] +'">'+ result['nombre_celula'] +'</option>');
				});
				$('#id_celula_0').removeAttr('disabled');

				//Init chosen plugin
				$('#id_celula_0').chosen({
					width: '100%'
				}).trigger('chosen:updated');

			}else{
				
				//Limpiar dropdown de celulas
				$('#id_celula_0').empty().append('<option value="">Seleccione</option>').prop('disabled', 'disabled');
				
				//Ocultar div que contiene campo celulas
				$('.celula-field').addClass('hide');
			}
		});
	});

})();

/**
 * Agencias Creada dinamicamente
 * ------------------------------
 * Buscar departamentos/celulas que pertenecen a la agencia seleccionada.
 */
(function(){

	//Event: Al seleccionar agencia
	$('.tablaAgencias').on("change", 'tr[id*="agencia"] select[id*="id_agencia_"]', function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var id_agencia = this.options[this.selectedIndex].value;
		var index = this.id.replace( /^\D+/g, '');
		var index_departamento = 'id_departamento_'+ index;
		var index_celula = 'id_celula_'+ index;

		if(id_agencia==""){
			
			//Desabilitar campos
			$('#'+ index_departamento +', #'+ index_celula).empty().append('<option value="">Seleccione</option>').prop('disabled', 'disabled');
			$('#'+ index_departamento).chosen({
				width: '100%'
			}).trigger('chosen:updated');
			$('.celula-field-'+ index).addClass('hide');

			return false;
		}

		//Buscar departamentos
		$.ajax({
			url: phost() + 'usuarios/ajax-get-agencia-departamentos',
			data: {
				id_agencia: id_agencia
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
				
				$('#'+ index_departamento).empty().append('<optgroup label="Seleccionar Todos" />');
				$.each(json.results[0], function(i, result){
					$('#'+ index_departamento).find('optgroup').append('<option value="'+ result['id'] +'">'+ result['nombre_departamento'] +'</option>');
				});
				$('#'+ index_departamento).removeAttr('disabled');

				//Init chosen plugin
				$('#'+ index_departamento).chosen({
					width: '100%'
				}).trigger('chosen:updated');

			}else{
				
				$('#'+ index_departamento).empty().append('<option value="">Seleccione</option>').prop('disabled', 'disabled');
				$('#'+ index_departamento).chosen({
					width: '100%'
				}).trigger('chosen:updated');
			}
		});

		//Buscar celulas
		$.ajax({
			url: phost() + 'usuarios/ajax-get-agencia-celulas',
			data: {
				id_agencia: id_agencia
			},
			type: "POST",
			dataType: "json",
			cache: false,
		}).done(function(json) {

			//Check Session
			if( $.isEmptyObject(json.session) == false){
				window.location = phost() + "/login?expired";
			}

			//If json object is not empty.
			if( $.isEmptyObject(json.results[0]) == false ){
				
				//Mostrar div que contiene campo celulas
				$('.celula-field-'+ index).removeClass('hide');
				
				//LLenar dropdown de celulas
				$('#'+ index_celula).empty().empty().append('<optgroup label="Seleccionar Todos" />');
				$.each(json.results[0], function(i, result){
					$('#'+ index_celula).find('optgroup').append('<option value="'+ result['id'] +'">'+ result['nombre_celula'] +'</option>');
				});
				$('#'+ index_celula).removeAttr('disabled');

				//Init chosen plugin
				$('#'+ index_celula).chosen({
					width: '100%'
				}).trigger('chosen:updated');

			}else{
				
				//Limpiar dropdown de celulas
				$('#'+ index_celula).empty().append('<option value="">Seleccione</option>').prop('disabled', 'disabled');
				
				//Ocultar div que contiene campo celulas
				$('.celula-field-'+ index).addClass('hide');
			}
		});
	});

	
	//Boton de Eliminar Agencia
	//Despliega Dialogo de confirmacion
	$('.tablaAgencias').on("click", 'tr[id*="agencia"] a[class*="deleteAgencia"]', function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var index 			= $(this).attr('data-index');
		var id_agencia 		= $('#id_agencia_'+ index).find('option:selected').val();
		var nombre_agencia 	= $('#id_agencia_'+ index).find('option:selected').text();
		$('#idAgencia').prop("value", id_agencia);
		$('#indexAgencia').prop("value", index);

		//Confirmar que desea eliminar el departamento
		$('#confirmModal').find('.modal-title').empty().append('Confrirme Accion');
		$('#confirmModal').find('.modal-body').empty().append('¿Esta seguro que desea eliminar la agencia: '+ nombre_agencia +' ?');
		$('#confirmModal').find('.modal-footer')
			.empty()
			.append('<button id="closeModal" class="btn btn-w-m btn-default" type="button">Cancelar</button>')
			.append('<button id="deleteAgencia" class="btn btn-w-m btn-danger" type="button">Eliminar</button>');
		$('#confirmModal').modal('show');
	});
	
	//Boton eliminar agencia
	//dentro dentro de dialogo de confirmacion
	$("#confirmModal").on("click", "#deleteAgencia", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var id_agencia = $('#idAgencia').val();
		var id_usuario = $('#id_usuario').val();
		var indexAgencia = $('#indexAgencia').val();
		
		$.ajax({
			url: phost() + 'usuarios/ajax-delete-agencia',
			data: {
				id_usuario: id_usuario,
				id_agencia: id_agencia
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

				if(json.results[0]['deleted'] == true)
				{
					if(indexAgencia == 0){
						
						//Reset Fields
						setTimeout(function(){
							$('tr#agencia0').find('select option').removeAttr('selected');
							$('tr#agencia0').find('#id_agencia_0 option:eq(0)').prop('selected', 'selected');
							$('tr#agencia0').find('select#id_departamento_0').empty().prop('disabled','disabled').chosen({
								width: '100%'
							}).trigger('chosen:updated');
						}, 500);

					}else{
						$('tr#agencia'+ indexAgencia).remove();
					}
					
					//ON REMOVE THE ROW
					//Update rows and fields index
					$.each( $('.tablaAgencias').find('tbody').find('tr[id*="agencia"]'), function(i,obj1){
						var nindex = i;
						var cntx = i + 2;
						$(this).prop("id", "agencia"+ nindex);

						$.each( $(this).find('td'), function(j,obj2){
							
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

					//remover valor de idAgencia, indexAgencia
					$('#idAgencia, #indexAgencia').prop("value", "");

					//cerrar dialogo
					$('#confirmModal').modal('hide');
				}
				else
				{
					//Hubo error tratando de eliminar
					$('#confirmModal').find('.modal-title').empty().append('Error');
					$('#confirmModal').find('.modal-body').empty().append('Se produjo un error tratando de eliminar la agencia.');
					$('#confirmModal').find('.modal-footer')
						.empty()
						.append('<button class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cerrar</button>');
					$('#confirmModal').modal('show');
				}

			}else{
				$('#confirmModal').modal('hide');
			}
		});
	});

	//Cerrar dialogo de confirmacion
	$("#confirmModal").on("click", "#closeModal", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		//remover valor de idAgencia, indexAgencia
		$('#idAgencia, #indexAgencia').prop("value", "");

		//Cerrar dialogo
		$('#confirmModal').modal('hide');
	});
	

})();


/**
 * Agregar otra agencia
 */
(function(){
	$("#editUsuarioForm").on('click', '#agregarAgencia', function(e){
		e.preventDefault();
		
		var total = $('.tablaAgencias').find('tbody').find('tr').length;
		var index = total;
		var cntr = total + 1;
		
		var html = $('<tr id="agencia'+ index +'" />')
		.append( 
			$('<td />').append(
				$('<select name="agencia['+ index +'][id]" id="id_agencia_'+ index +'" class="form-control agencia-group">').append( $('#id_agencia_0').html() )
			) 
		).append(
			$('<td />').append(
				$('<select name="agencia['+ index +'][id_departamento][]" id="id_departamento_'+ index +'" class="form-control agencia-group" size="1" multiple="multiple" disabled="disabled" data-placeholder="Seleccione" />')
			)
		).append( 
			$('<td />').append(
				$('<div class="celula-field-'+ index +' hide" />').append(
					$('<select name="agencia['+ index +'][id_celula][]" id="id_celula_'+ index +'" class="form-control" size="1" multiple="multiple" disabled="disabled" data-placeholder="Seleccione" />')
				)
			)
		).append(
			$('<td />').append(
				$('<a href="#" class="btn btn-danger btn-block" data-index="'+ index +'"><i class="fa fa-trash"></i> <span class="hidden-sm">Eliminar</span></a>').click(function(e){
					e.preventDefault();
					var objindex = $(this).attr('data-index');
					
					//Remove Rules
					if($('#id_agencia_'+ index).attr('id') != undefined){
						$('#id_agencia_'+ index).rules("remove");
					}
					if($('#id_departamento_'+ index).attr('id') != undefined){
						$('#id_departamento_'+ index).rules("remove");
					}

					//Remove Row
					$('tr#agencia'+ objindex).remove();
					
					//ON REMOVE THE ROW
					//Update rows and fields index
					$.each( $('.tablaAgencias').find('tbody').find('tr[id*="agencia"]'), function(i,obj1){
						var nindex = i;
						var cntx = i + 2;
						$(this).prop("id", "agencia"+ nindex);

						$.each( $(this).find('td'), function(j,obj2){
							
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

							if(nindex != 0){
								if($('#id_agencia_'+ nindex).attr('id') != undefined){
									$('#id_agencia_'+ nindex).rules("add",{ require_from_group: [2, ".agencia-group"], messages: {  } });
								}
								if($('#id_departamento_'+ nindex).attr('id') != undefined){
									$('#id_departamento_'+ nindex).rules("add",{ require_from_group: [2, ".agencia-group"], messages: {  } });
								}
							}
						});
					});
					
				})
			)
		);


		$('.tablaAgencias').find('tbody').append(html);

		//Reset seleccion de agencia
		setTimeout(function(){ 
			$('#id_agencia_'+ index).find('option:eq(0)').prop('selected', 'selected');
		}, 50);

		//Validate Required Fields
		setTimeout(function(){ 
			$('#id_agencia_'+ index).rules("add",{ require_from_group: [2, ".agencia-group"], messages: {  } });
			$('#id_departamento_'+ index).rules("add",{ require_from_group: [2, ".agencia-group"], messages: {  } });
		},500);

	});
})();
