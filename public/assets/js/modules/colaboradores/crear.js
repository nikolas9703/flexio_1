//Modulo
var colaboradores = (function(){
    
      //Formato moneda
      $(".salario_mensual").inputmask('currency',{
      prefix: "",
      autoUnmask : true,
      removeMaskOnSubmit: true
    });

	var scope = this;
	var url = 'colaboradores/ajax-listar-cargos';
	var formulario = '#crearColaboradorForm, #datosEspecificosForm, #formulario82';
	var formulario82 = '#formulario82';
	var opcionesModal = $('#opcionesModal');
	var acordeon = $('#accordion-colaborador');
	
	var campo_departamento = $(formulario).find('#departamento_id');
	var campo_cargo = $(formulario).find('#cargo_id');
	var campo_salario_mensual = $(formulario).find('input[id*="salario_mensual"]');
	var campo_rata_hora = $(formulario).find('input[id*="rata_hora"]');
	var campo_fecha_inicio_labores = $(formulario).find('input[id*="campo[fecha_inicio_labores]"]');
	
	var campos = function(){
		
		$(formulario).find('input[name="campo[edad]"]').attr('readonly', 'readonly');
		
		//Evento Acordeon
		acordeon.on('show.bs.collapse', function () {
			//redimensionar grids
			setTimeout(function(){
				$(".ui-jqgrid").each(function(){
					var w = parseInt( $(this).parent().width()) - 6;
					var tmpId = $(this).attr("id");
					var gId = tmpId.replace("gbox_","");
					$("#"+gId).setGridWidth(w);
				});
			}, 50);
		});
		
		//Verificar si existe variable "seccion_accordion"
		//Para abrir un acordeon por default
		//al cargar la pagina
		if(typeof seccion_accordion != 'undefined'){
			$('#'+ seccion_accordion).collapse('show');
		}
		
		//Evento: al cambiar fecha nacimiento
		$(formulario).on('change', 'input[name*="campo[fecha_nacimiento]"]', function(e){
			var edad =  getAge(this.value);
			$('input[name="campo[edad]"]').val(edad);
	 	});
		
		//Init Combodate plugin
		//Fecha de Nacimiento
		$(formulario).find('.fecha').combodate({
			minYear: 1915,
		    maxYear: moment().format('YYYY')
		});
		
		//Init Bootstrap Calendar Plugin
		$(formulario).find('.fecha-salida, .fecha-inicio-labores, .fecha-consulta-medica, #fecha_devengado_desde0, #fecha_devengado_hasta0, .fecha-formulario-82').daterangepicker({
	    	singleDatePicker: true,
	    	autoUpdateInput: false,
	    	format: 'MM-DD-YYYY',
	        showDropdowns: true,
	        opens: "left",
	        locale: {
	        	applyLabel: 'Seleccionar',
	            cancelLabel: 'Cancelar',
	        	daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
	            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
	            firstDay: 1
	        }
	    }).on('apply.daterangepicker', function(ev, picker) {
	    	$(this).val(picker.startDate.format('DD/MM/YYYY'));
	    });
		
		//Inicializar opciones del Modal
		opcionesModal.modal({
			backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
			show: false
		});
		
		//Verificar cedula a mostrar
		if(typeof colaborador_id != "undefined"){
			setTimeout(function(){
				verificar_cedula();
			}, 600);
		}
		
		$(formulario).on('change', 'select[id*="provincia_id"]', function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			//armar cedula
			armar_cedula(this);
		});
		
		$(formulario).on('change', 'select[id*="letra_id"]', function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			var seleccionado = $(this).find('option:selected').text();
			
			//Verificar ubicacion del campo (tabla o div)
			//para saber de donde seleccionar los campos
			if($(this).closest('td').length > 0){
				
				var campo_tomo = $(this).closest('tr').find('input[id*="tomo"]');
				var campo_asiento = $(this).closest('tr').find('input[id*="asiento"]');
				var campo_pasaporte = $(this).closest('tr').find('input[id*="no_pasaporte"]');
				var campo_provincia = $(this).closest('tr').find('select[id*="provincia_id"]');
			}
			else if($(this).closest('div.form-group').length > 0){
				
				var campo_tomo = $(formulario).find('input[id="campo[tomo]"]');
				var campo_asiento = $(formulario).find('input[id="campo[asiento]"]');
				var campo_pasaporte = $(formulario).find('input[id="campo[no_pasaporte]"]');
				var campo_provincia = $(formulario).find('#provincia_id');
			}
			
			if(seleccionado == "PAS"){
				
				//Desabilitar Provincia
				$(campo_provincia).prop('disabled', 'disabled').find('option:eq(0)').prop('selected', 'selected');
				
				//Verificar ubicacion del campo (tabla o div)
				//para saber si ocultar div o td
				if($(this).closest('td').length > 0){
					
					//Ocultar campos (tomo y asiento)
					$(campo_tomo).val('').prop('disabled', 'disabled');
					$(campo_asiento).val('').prop('disabled', 'disabled');
					
					//Mostrar campo pasaporte y header
					$(campo_pasaporte).removeAttr('disabled');
				}
				else if($(this).closest('div.form-group').length > 0){
					
					//Ocultar campos (tomo y asiento)
					$(campo_tomo).val('').closest('div').addClass('hide');
					$(campo_asiento).val('').closest('div').addClass('hide');
					
					//Mostrar campo pasaporte
					$(campo_pasaporte).closest('div').removeClass('hide');
				}
			}else{
				
				if(["N", "E", "PE"].indexOf(seleccionado) >= 0){
					$(campo_provincia).prop('disabled', 'disabled').find('option:eq(0)').prop('selected', 'selected');
				}else{
					$(campo_provincia).removeAttr('disabled');
				}
				
				//Verificar ubicacion del campo (tabla o div)
				//para saber si ocultar div o td
				if($(this).closest('td').length > 0){
					
					///Mostrar campos (tomo y asiento)
					$(campo_tomo).removeAttr('disabled');
					$(campo_asiento).removeAttr('disabled');
					
					//Ocultar campo pasaporte
					$(campo_pasaporte).val('').prop('disabled', 'disabled');
				}
				else if($(this).closest('div.form-group').length > 0){
					//Mostrar campos (tomo y asiento)
					$(campo_tomo).closest('div').removeClass('hide');
					$(campo_asiento).closest('div').removeClass('hide');

					//Ocultar campo pasaporte
					$(campo_pasaporte).val('').closest('div').addClass('hide');
					$(this).closest('table').find('thead').find('th.no_pasaporte').addClass('hide');
				}
			}
			
			//actualizar campos chosen
			actualizar_chosen();
			
			//armar cedula
			armar_cedula(this);
		});
		
		$(formulario).on('focusout', 'input[name*="tomo"], input[name*="asiento"], input[name*="no_pasaporte"]', function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			//armar cedula
			armar_cedula(this);
		});
		
		//Init Tabla Dinamica Plugin
		$(formulario).find('.agregarFamiliaBtn, .agregarDependientesBtn, .agregarEstudiosBtn, .agregarBenPrinBtn, .agregarBenConBtn, .agregarBenParBtn, .agregarDeducBtn').tablaDinamica({
			 
			afterAddRow: function(row){
				//actualizar campos chosen
				actualizar_chosen();
				
				// jQuery Input Mask plugin
				$(row).find(':input[data-inputmask]').filter(function(){
					return $(this).val() == '';
				}).inputmask();
			},
			afterDeleteRow: function(){
				
				//recalcular total deducciones
				calcular_total_deducciones();
			},
			onClickDeleteBtn: function(tabla_id, row){
 				//plugin scope
				var scope = this;
				
				//Resaltar la fila a eliminar
				$(row).addClass('alert-warning');
				
				//Evento: al cerrar modal
				opcionesModal.on('hidden.bs.modal', function (e) {
					//Quitar clase de warning de la fila
					$(row).removeClass('alert-warning');
				});
				//374.96 + 79.20 = 454.16
				//227.08
				
				//Modal
				opcionesModal.find('.modal-title').empty().append('Confirme');
			    opcionesModal.find('.modal-body').empty().append('&#191;Esta seguro que desea eliminar?');
			    opcionesModal.find('.modal-footer').empty()
				.append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
				.append(
					$('<button class="btn btn-w-m btn-danger" type="button" />').append('Eliminar').click(function(e){
						e.preventDefault();
						e.returnValue=false;
						e.stopPropagation();
						
						var response = {};
						var id = $(row).find('input:hidden[name*="[id]"]').val();

						switch(tabla_id) {
						    case 'beneficiario_principalTable':
						    case 'beneficiario_contingenteTable':
						    case 'beneficiario_parienteTable':
						    	response = ajax('colaboradores/ajax-eliminar-beneficiario', {id: id});
						        break;
						    case 'dependientesTable':
						    	response = ajax('colaboradores/ajax-eliminar-dependiente', {id: id});
						        break;
                                                    case 'familiaTable':
                                                    response = ajax('colaboradores/ajax-eliminar-familia', {id: id});
                                                    break;
						    case 'estudiosTable':
						    	response = ajax('colaboradores/ajax-eliminar-estudio', {id: id});
						        break;
						    case 'deduccionesTable':
						    	response = ajax('colaboradores/ajax-eliminar-deduccion', {id: id});
						        break;
						    default:
						    	 break;
						};
						
						//Respuesta del Ajax
						response.done(function(json){

				            //Check Session
							if( $.isEmptyObject(json.session) == false){
								window.location = phost() + "login?expired";
							}
				            
							//If json object is empty.
							if($.isEmptyObject(json) == true){
								return false;
							}
							
							//Mostrar Mensaje
							if(json.response == "" || json.response == undefined || json.response == 0){
								toastr.error(json.mensaje);
							}else{
								toastr.success(json.mensaje);
							}
							
							//Eliminar fila de la tabla
							scope.deleterow(row);
							
							//Ocultar Modal
							opcionesModal.modal('hide');
							
				        }).fail(function(xhr, textStatus, errorThrown) {
							//mensaje error
							toastr.error('Hubo un error al tratar de eliminar.');
						})
					})
				);
			    opcionesModal.modal('show');
			}
		});
		
		//Sumar montos de la columna de deducciones
		//Formulario 82 - Deducciones
		$(formulario).on('keyup', 'input[id*="deduccion"]', function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			calcular_total_deducciones();
	 	});

		//Inicializar Validate en Formularios
		$.each(formulario.split(','), function(i, form){
			$(form).validate({
				focusInvalid: true,
				ignore: '',
				wrapper: '',
				submitHandler: function(form) {
				   
					//Habilitar campos desabilitados
					$(form).find('input:disabled, select:disabled, textarea, select, input').removeAttr('disabled');
					
					//submit
				    form.submit();
				}
			});
		});
		
		//Al seleccionar Centro Contable
		/*$(formulario).on("change", '#centro_contable_id', function(e){ //ESTA FUNCION ME HACIA EL CHANGE AL SELECCIONAR UN CENTRO CONTABLE
      //alert('Alert centro_contable_id');
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			var centro_id = $(this).find('option:selected').val();
			var campo_cargo = $(formulario).find('#cargo_id');
			var campo_departamento_id = $(formulario).find('#departamento_id');

			if(centro_id==""){
				limpiar_seleccion_dropdown(campo_departamento_id);
				limpiar_seleccion_dropdown(campo_cargo);
				limpiar_campos_salario();
				return false;
			}

			//Mensaje de Loading
			//$('.departamento-loader').remove();
			//$(campo_departamento_id).closest('div').append('<div class="departamento-loader"><small class="text-success">Buscando &Aacute;reas de Negocio... <i class="fa fa-circle-o-notch fa-spin"></i></small></div>');

			//Buscar listado de Areas de Negocios
			//Asociadas al Centro contable seleccionado.
			popular_departamento(centro_id);
		});*/

		//Al seleccionar Departamento
		/*$(formulario).on("change", '#departamento_id', function(e){  //ES EL CHANGE QUE SE EJECUTA CUANDO SELECCIONAS UN AREA DE NEGOCIO
      alert('Dentro de departamento_id');
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			var campo_departamento = this;
			var campo_cargo = $(formulario).find('#cargo_id');
			var departamento_id = $(campo_departamento).find('option:selected').val(); 
			
			if(departamento_id==""){
				limpiar_seleccion_dropdown(campo_cargo);
				limpiar_campos_salario();
				return false;
			}
			
			//Mensaje de Loading
			$(campo_cargo).closest('div').append('<div class="cargo-loader"><small class="text-success">Buscando Cargos... <i class="fa fa-circle-o-notch fa-spin"></i></small></div>');
			
			//Consultar el listado de Cargos
			//Segun departamento seleccionado.
			popular_cargo(departamento_id);
		});*/

		//Al seleccionar Cargo
		$(formulario).on("change", '#cargo_id', function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			var cargo_id = $(this).find('option:selected').val(); 
			
			var campo_cargo = this;
			var campo_salario_mensual = $(formulario).find('input[id*="salario_mensual"]');
			var campo_rata_hora = $(formulario).find('input[id*="rata_hora"]');
			var rata = roundNumber(parseFloat($(campo_cargo).find('option:selected').attr('data-rata')),2);
			var tipo_rata = $(campo_cargo).find('option:selected').attr('data-tipo-rata');
			
			if(cargo_id==""){
				limpiar_campos_salario();
				return false;
			}
			
			//tipo de rata
			$(formulario).find('input[id*="tipo_salario"]').prop("value", tipo_rata);
			
			//resetear campos
			$(campo_salario_mensual, campo_rata_hora).prop("disabled", "disabled").val('');
			
			//verificar tipo rata
			//y habilitar campo para edicion
			if(tipo_rata == 'Mensual'){
				//Introducir rata mensual
				$(formulario).find(campo_salario_mensual).removeAttr('disabled').prop("value", rata);
				
			}else{
				//Introducir rata por hora
				$(formulario).find(campo_rata_hora).removeAttr('disabled').prop("value", rata);
			}
			
			actualizar_chosen();
		});
		
		//Verificar si existe variable uuid de edicion
		if(typeof colaborador_uuid != 'undefined')
    {
      //************** Inicializo en false los requeridos por cheque
      $('#banco_id').attr('data-rule-required',false);
      $('#tipo_cuenta_id').attr('data-rule-required',false);
      $('.no_cuenta').attr('data-rule-required', false);
      //Remueve el Span Required de los labels
      $('#banco_id').closest('div').find('span').remove();
      $('#tipo_cuenta_id').closest('div').find('span').remove();
      $('.no_cuenta').closest('div').find('span').remove();
      //************************ fin Inicializacion *********************
			//Si existe, habilitar boton submit de formulario de Datos Especificos/ Deducciones
			$(formulario).find('input[id="campo[guardarDatosEspecificosFormBtn]"]').removeAttr('disabled');
      //******************* SI ES UNA OPCION DISTINTA A CHEQUE QUITALE EL REQUIRED *****************************
      $('#forma_pago_id').change(function()
      {
        var forma_de_pago = $('#forma_pago_id option:selected').text();
        //console.log(forma_de_pago);
        if(forma_de_pago == "Cheque")
        {
          //Remueve el Required de los imputs
          $('#banco_id').attr('data-rule-required',true);
          $('#tipo_cuenta_id').attr('data-rule-required',true);
          $('.no_cuenta').attr('data-rule-required', true);
          //Remueve el Span Required de los labels
          $('#banco_id').closest('div').find('label').append('<span style="color:red">*</span>');
          $('#tipo_cuenta_id').closest('div').find('label').append('<span style="color:red">*</span>');
          $('.no_cuenta').closest('div').find('label').append('<span style="color:red">*</span>');
        }
        else
        {
          //************** Inicializo en false los requeridos por cheque
          $('#banco_id').attr('data-rule-required',false);
          $('#tipo_cuenta_id').attr('data-rule-required',false);
          $('.no_cuenta').attr('data-rule-required', false);
          //Remueve el Span Required de los labels
          $('#banco_id').closest('div').find('span').remove();
          $('#tipo_cuenta_id').closest('div').find('span').remove();
          $('.no_cuenta').closest('div').find('span').remove();
          //************************ fin Inicializacion *********************
        }
      });
      //************************* FIN DE VALIDACION **************************************************************
			$(formulario).find('input[id="campo[guardarFormulario82Btn]"]').removeAttr('disabled');

			
			//Verificar si tiene permisos para editar
			if(typeof permiso_editar !== 'undefined')
			{
				if(permiso_editar == 'false'){
					setTimeout(function(){
						$(formulario).removeAttr('action').find('input, select, textarea, button').removeAttr('readonly').prop('disabled', 'disabled');
						actualizar_chosen();
					}, 1000);
				}
			}
			
			//hacer trigger  change en campo de fecha, para mostrarla
			setTimeout(function(){
				$('input[name="campo[fecha_nacimiento]"]').trigger('change');
	        }, 1500);
			
			//calcular total de deducciones
			calcular_total_deducciones();
		}
		else
		{
			//Si no existe variable colaborador_uuid, establecer fecha en 0
			$(formulario).find('input[name="campo[edad]"]').val("0");
		}
		
		//abril panel de datos profesionales
		setTimeout(function(){
			$('.ibox-title:contains("Datos Profesionales")').find('.collapse-link').trigger('click');
		}, 1000);
		
		//----------------
		// Formulario 82 (Deducciones)
		//----------------
		//Remover boton de abrir/cerrar de formulario 82
		$(formulario82).find('.ibox-title').find('.ibox-tools').remove();
		
		//mostrar cedula para formnulario 82
		$(formulario82).find('input[id="campo[cedula]"]').prop("type", "text").addClass("form-control");
		
		//Desabilitar los campos de datos generales
		$(formulario82).find('input[id="campo[nombre]"], input[id="campo[segundo_nombre]"], input[name="campo[apellido]"], input[name="campo[apellido_materno]"], input[id="campo[cedula]"], input[id="campo[telefono_residencial]"], input[id="campo[direccion]"], #estado_civil_id').prop('disabled', 'disabled');
		
		
		//Verificar si existe variable salario_mensual
		if(typeof selected_centro_contable_id !== 'undefined'){
			
			if(typeof selected_departamento_id == 'undefined'){
				var selected_departamento_id = '';
			}
			
			popular_departamento(selected_centro_contable_id, selected_departamento_id);
		}
		
		//Verificar si existe variable salario_mensual
		setTimeout(function(){
			if(typeof selected_departamento_id !== 'undefined'){
				
				//Seleccionar Departamento
				$(campo_departamento).find('option[value="'+ s_departamento_id +'"]').prop('selected', 'selected');

				//Popular Cargos
				popular_cargo(selected_departamento_id, cargo_id);
				
				actualizar_chosen();
			}	
		}, 1000);

		//Verificar si existe variable salario_mensual
		if(typeof salario_mensual !== 'undefined'){
			
			//Si existe, habilitar campo
			$(campo_salario_mensual).removeAttr('disabled');
		}
		
		//Verificar si existe variable salario_mensual
		if(typeof rata_hora !== 'undefined'){
			
			//Si existe, habilitar campo
			$(campo_rata_hora).removeAttr('disabled');
		}
		
		//Verificar si existe variable fecha_inicio_labores
		if(typeof fecha_inicio_labores !== 'undefined'){
			
			//Si existe, popular campo
			setTimeout(function(){
				$(campo_fecha_inicio_labores).removeAttr("value");
				$(campo_fecha_inicio_labores).attr("value", fecha_inicio_labores);
				
				//refresh datepicker
				$(formulario).find('.fecha-inicio-labores').daterangepicker({
			    	singleDatePicker: true,
			    	autoUpdateInput: false,
			    	format: 'MM-DD-YYYY',
			        showDropdowns: true,
			        opens: "left",
			        locale: {
			        	applyLabel: 'Seleccionar',
			            cancelLabel: 'Cancelar',
			        	daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
			            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
			            firstDay: 1
			        }
			    }).on('apply.daterangepicker', function(ev, picker) {
			    	$(this).val(picker.startDate.format('DD/MM/YYYY'));
			    });
				
			}, 500);
		}
		
		//actualiazr campos chosen para formulario
		actualizar_chosen();
	};
	
	//Armar Cedula
	var armar_cedula = function(field){
		
		//Verificar ubicacion del campo (tabla o div)
		if($(field).closest('td').length > 0){
			
			var campo_tomo = $(field).closest('tr').find('input[id*="tomo"]');
			var campo_asiento = $(field).closest('tr').find('input[id*="asiento"]');
			var campo_pasaporte = $(field).closest('tr').find('input[id*="no_pasaporte"]');
			var campo_provincia = $(field).closest('tr').find('select[id*="provincia_id"]');
			var campo_letra = $(field).closest('tr').find('select[id*="letra_id"]');
			var campo_cedula = $(field).closest('tr').find('input[name*="cedula"]');
		}
		else if($(field).closest('div.form-group').length > 0){
			
			var campo_tomo = $(formulario).find('input[id="campo[tomo]"]');
			var campo_asiento = $(formulario).find('input[id="campo[asiento]"]');
			var campo_pasaporte = $(formulario).find('input[id="campo[no_pasaporte]"]');
			var campo_provincia = $(formulario).find('#provincia_id');
			var campo_letra = $(formulario).find('#letra_id');
			var campo_cedula = $(formulario).find('input[name="campo[cedula]"]');
		}
		
		var provincia = Number($(campo_provincia).find('option:selected').text().replace(/[^0-9\.]+/g,""));
		var letra = $(campo_letra).find('option:selected').text();
		
		var tomo = $(campo_tomo).val();
			tomo = tomo != '' ? '-'+ tomo : '';
		
		var asiento = $(campo_asiento).val();
			asiento = asiento != '' ? '-'+ asiento : '';
		
		var pasaporte = $(campo_pasaporte).val();
			pasaporte = pasaporte != '' ? '-'+ pasaporte : '';

		var cedula = '';
		if(letra == "PAS"){

			cedula = letra + pasaporte;
		}else{
			
			cedula = (["N", "E", "PE"].indexOf(letra) >= 0) ? letra + tomo + asiento : provincia + tomo + asiento;
		}
		
		//Popular campo oculto de cedula
		$(campo_cedula).val(cedula);
	};
	
	//Verificar todos los campos de cedula
	//para mostrar/ocultar segun campos segun
	//la letra seleccionada.
	var verificar_cedula = function(){
		
		var letra = $(formulario).find('#letra_id option:selected').text();
		
		$.each($('select[id*="letra_id"]'), function(indice, letra){

			var field 		= this;	
			var letra_id 	= $(letra).find('option:selected').val();
			var letra 		= $(letra).find('option:selected').text();
			
			//avanzar siguiente iteracion
			if(letra_id == ""){
				return true;
			}

			//Verificar ubicacion del campo (tabla o div)
			//para saber de donde seleccionar los campos
			if($.isEmptyObject($(field).closest('td')) == false){
				
				var campo_tomo = $(field).closest('tr').find('input[id*="tomo"]');
				var campo_asiento = $(field).closest('tr').find('input[id*="asiento"]');
				var campo_pasaporte = $(field).closest('tr').find('input[id*="no_pasaporte"]');
				var campo_provincia = $(field).closest('tr').find('select[id*="provincia_id"]');
				var campo_letra = $(field).closest('tr').find('select[id*="letra_id"]');
			}
			else if($.isEmptyObject($(field).closest('div.form-group')) == false){
				
				var campo_tomo = $(formulario).find('input[id="campo[tomo]"]');
				var campo_asiento = $(formulario).find('input[id="campo[asiento]"]');
				var campo_pasaporte = $(formulario).find('input[id="campo[no_pasaporte]"]');
				var campo_provincia = $(formulario).find('#provincia_id');
			}

			if(letra == "PAS"){
				
				//Desabilitar Provincia
				$(campo_provincia).prop('disabled', 'disabled').find('option:eq(0)').prop('selected', 'selected');
				
				//Verificar ubicacion del campo (tabla o div)
				//para saber si ocultar div o td
				if($.isEmptyObject($(this).closest('td')) == false){
					
					//Ocultar campos (tomo y asiento)
					$(campo_tomo).val('').prop('disabled', 'disabled');
					$(campo_asiento).val('').prop('disabled', 'disabled');
					
					//Mostrar campo pasaporte y header
					$(campo_pasaporte).removeAttr('disabled');
				}
				else if($.isEmptyObject($(this).closest('div.form-group')) == false){
					
					//Ocultar campos (tomo y asiento)
					$(campo_tomo).val('').closest('div').addClass('hide');
					$(campo_asiento).val('').closest('div').addClass('hide');
					
					//Mostrar campo pasaporte
					$(campo_pasaporte).closest('div').removeClass('hide');
				}
			}else{
				
				if(["N", "E", "PE"].indexOf(letra) >= 0){
					$(campo_provincia).prop('disabled', 'disabled').find('option:eq(0)').prop('selected', 'selected');
				}else{
					$(campo_provincia).removeAttr('disabled');
				}
				
				//Verificar ubicacion del campo (tabla o div)
				//para saber si ocultar div o td
				if($.isEmptyObject($(this).closest('td')) == false){
					
					///Mostrar campos (tomo y asiento)
					$(campo_tomo).removeAttr('disabled');
					$(campo_asiento).removeAttr('disabled');
					
					//Ocultar campo pasaporte
					$(campo_pasaporte).val('').prop('disabled', 'disabled');
				}
				else if($.isEmptyObject($(this).closest('div.form-group')) == false){
					//Mostrar campos (tomo y asiento)
					$(campo_tomo).closest('div').removeClass('hide');
					$(campo_asiento).closest('div').removeClass('hide');

					//Ocultar campo pasaporte
					$(campo_pasaporte).val('').closest('div').addClass('hide');
					$(this).closest('table').find('thead').find('th.no_pasaporte').addClass('hide');
				}
			}
			
		});
	};
	
	//Limpiar seleccion de campo: dropdown
	var limpiar_seleccion_dropdown = function(campo){
		$(formulario).find(campo).attr("disabled", "disabled").empty().append('<option value="">Seleccione</option>').find('option:eq(0)').attr("selected", "selected");
		
		setTimeout(function(){
			actualizar_chosen();
		}, 300);
	};
	
	//Limpiar campos de salarios
	var limpiar_campos_salario = function(){
		var campo_salario_mensual = $(formulario).find('input[id*="salario_mensual"], input[id*="rata_hora"], input[id*="tipo_salario"]').prop("disabled", "disabled").val('');
		actualizar_chosen();
	};
	
	//Actualizar campos chosen
	var actualizar_chosen = function(){
		$(formulario).find('.chosen-select').chosen({
			width: '100%',
        }).trigger('chosen:updated').on('chosen:showing_dropdown', function(evt, params) {
        	$(this).closest('div.table-responsive').css("overflow", "visible");
        }).on('chosen:hiding_dropdown', function(evt, params) {
        	$(this).closest('div.table-responsive').css({'overflow-x':'auto !important'});
        });
	};
	
	/**
	 * Selecciona departamentos asociados a
	 * un Centro Contable
	 */
	var lista_departamentos = function(parametros){
		return ajax('colaboradores/ajax-lista-departamentos-asociado-centros', parametros);
	};
	
	/**
	 * Popular dropdown departamento/area de negocio
	 * segun centro contable
	 * seleccionado.
	 */
	var popular_departamento = function(centro_id, seleccionado)
	{
		if(centro_id == ""){
			return false;
		}
		
		//Buscar listado de Areas de Negocios
		//Asociadas al Centro contable seleccionado.
		ajax('colaboradores/ajax-lista-departamentos-asociado-centro', {centro_id: centro_id}).done(function(json){

            //Check Session
			if( $.isEmptyObject(json.session) == false){
				window.location = phost() + "login?expired";
			}
            
			//If json object is empty.
			if($.isEmptyObject(json['result']) == true){
				//remover mensaje loading
				$('.departamento-loader').remove();
				
				//limpiar campos
				limpiar_seleccion_dropdown(campo_departamento);
				limpiar_campos_salario();
				
				return false;
			}
			
			//Popular Campo Departamento
			$(campo_departamento).empty().append('<option value="">Seleccione</option>').removeAttr('disabled');
			$.each(json['result'], function(i, result){
				var selected = seleccionado == result['id'] ? 'selected="selected"' : '';
				$(campo_departamento).append('<option value="'+ result['id'] +'" '+ selected +'>'+ result['nombre'] +'</option>');
			});
			
			//Eliminar variable seleccionado
			delete seleccionado;
			
			//remover mensaje loading
			$('.departamento-loader').remove();
			
			//actualizar campos chosen
			actualizar_chosen();
        });
	};
	
	/**
	 * Selecciona departamentos asociados a
	 * un Centro Contable
	 */
	var lista_cargos = function(parametros){
		return ajax('configuracion_rrhh/ajax-lista-cargos', parametros);
	};
	
	/**
	 * Popular dropdown cargo
	 * segun centro contable
	 * seleccionado.
	 */
	var popular_cargo = function(departamento_id, seleccionado)
	{
		if(departamento_id == ""){
			return false;
		}

		ajax('configuracion_rrhh/ajax-lista-cargos', {departamento_id: departamento_id}).done(function(json){

            //Check Session
			if( $.isEmptyObject(json.session) == false){
				window.location = phost() + "login?expired";
			}
            
			//If json object is empty.
			if($.isEmptyObject(json['result']) == true){
				//remover mensaje loading
				$('.cargo-loader').remove();
				
				//limpiar campos
				limpiar_seleccion_dropdown(campo_cargo);
				limpiar_campos_salario();
				
				return false;
			}
			
			//Popular Campo Cargo
			$(campo_cargo).empty().append('<option value="">Seleccione</option>').removeAttr('disabled');
			$.each(json['result'], function(i, result){
				var selected = seleccionado == result['id'] ? 'selected="selected"' : '';
				$(campo_cargo).append('<option value="'+ result['id'] +'" data-tipo-rata="'+ result['tipo_rata'] +'" data-rata="'+ result['rata'] +'" '+ selected +'>'+ result['nombre'] +'</option>');
			});
			
			//Eliminar variable seleccionado
			delete seleccionado;
			
			//remover mensaje loading
			$('.cargo-loader').remove();
			
			//actualizar campos chosen
			actualizar_chosen();
        });
	};
	
	/**
	 * Calcular total monto
	 * de la columna de deducciones.
	 */
	var calcular_total_deducciones = function(){
		
		var total = 0;
		var deducciones = $('#deduccionesTable').find('input[id*="deduccion"]');
		
		//Recorrer las deducciones introducidas
		$.each(deducciones, function(i, deduccion){
			
			var monto = $(this).val();
			
			if(!isNumber(monto)){
				return;
			}
			
			total += parseFloat(monto);
		});
		
		//Introducir total calculado
		$('#totalDeduccion').prop('value', roundNumber(total, 2));
	};
	
	//Eliminar un Beneficiario
	var ajax = function(url, parametros){
		
		if(parametros == "" || url == ""){
			return false;
		}
		
		return $.ajax({
			url: phost() + url,
			data: $.extend({erptkn: tkn}, parametros),
			type: "POST",
			dataType: "json",
			cache: false,
		});
	};

	return{	    
		init: function() {
			campos();
		},
		recargar: function(){
			//reload jqgrid
			recargar();
		},
		redimensionar: function(){
			//Al redimensionar ventana
			$(".ui-jqgrid").each(function(){
				var w = parseInt( $(this).parent().width()) - 6;
				var tmpId = $(this).attr("id");
				var gId = tmpId.replace("gbox_","");
				$("#"+gId).setGridWidth(w);
			});
		},
		lista_departamentos: lista_departamentos,
		lista_cargos: lista_cargos
	};
})();

$('a[href*="#plantillaTab"]').on('click', function() {
    
    var crearPlantillaForm = $('#crearPlantillaForm');
			//Limpiar formulario
			crearPlantillaForm.append('<input type="hidden" name="colaborador_id" value="'+ colaborador_id +'" />');
                        
			//Enviar formulario
			crearPlantillaForm.submit();
	        $('body').trigger('click');
		});

colaboradores.init();
