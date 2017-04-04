//Listar Accion de Personal
var listarAccionPersonal = (function(){
 
	var grid_obj = $("#tablaAccionPersonalGrid");
	var opcionesModal = $('#opcionesModal');
	var pagarAccionPersonalModal = $('#pagarAccionPersonalModal');
	var pagarAccionPersonalForm = $("#pagarAccionPersonalForm");
	
	var botones = {
		modalOpcionesCrear: ".modalOpcionesCrear",
		pagarVacaciones: '#pagarVacacionesLnk',
		liquidar: '#pagarLiquidacionesLnk'
	};
	
	//HTML Botones del Modal
	var botones_modal = ['<div class="row">',
	     		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
 		   		'<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>',
 		   '</div>',
 		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
 		   		'<button id="pagarAccionPersonalModalBtn" class="btn btn-w-m btn-primary btn-block" type="button">Confirmar</button>',
 		   '</div>',
 		   '</div>'
	].join('\n');
	
	var botones_crear = [
	    '<a class="btn btn-block btn-outline btn-success" href="'+ phost() + 'accion_personal/crear/vacaciones' +'">Vacaciones</a>',
	    '<a class="btn btn-block btn-outline btn-success" href="'+ phost() + 'accion_personal/crear/ausencias' +'">Ausencias</a>',
	    '<a class="btn btn-block btn-outline btn-success" href="'+ phost() + 'accion_personal/crear/incapacidades' +'">Incapacidades</a>',
	    '<a class="btn btn-block btn-outline btn-success" href="'+ phost() + 'accion_personal/crear/licencias' +'">Licencias</a>',
	    '<a class="btn btn-block btn-outline btn-success" href="'+ phost() + 'accion_personal/crear/permisos' +'">Permisos</a>',
	    '<a class="btn btn-block btn-outline btn-success" href="'+ phost() + 'accion_personal/crear/liquidaciones' +'">Liquidaciones</a>',
	    '<a class="btn btn-block btn-outline btn-success" href="'+ phost() + 'accion_personal/crear/evaluaciones' +'">Evaluaciones</a>',
	].join('\n');
	
	//Inicializar Eventos de Botones
	var eventos = function(){
		
		//Boton de Opciones
		$('#moduloOpciones').on("click", botones.modalOpcionesCrear, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			//Inicializar opciones del Modal
			opcionesModal.modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			});
		    opcionesModal.find('.modal-title').empty().append('Acci&oacute;n personal: Crear');
		    opcionesModal.find('.modal-body').empty().append(botones_crear);
		    opcionesModal.find('.modal-footer').empty();
		    opcionesModal.modal('show');
		});
		
		/**
		 * Funcion Procesa pagos (vacaciones, liquidacion, licencia)
		 */
		$('#moduloOpciones').on("click", 'a[id*="pagar"]', function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			var tipopago = this.id.replace(/(pagar|Lnk)/g, '').toLowerCase();
			var prefijo = tipopago.substring(0, 3).toUpperCase();

			var rows = '';
			var seleccionados = grid_obj.jqGrid('getGridParam','selarrrow');
			
			//Verificar cantidad de seleccionados
			if(seleccionados.length == 0){
				toastr.warning('Debe seleccionar uno o varias acciones personales de '+ tipopago);
				return false;
			}else{
				
				//Limpiar formulario
				pagarAccionPersonalForm.find('input[name*="'+ tipopago +'"]').remove();
				
				//Verificar que las acciones seleccionadas sean SOLO de vacaciones
				var check = true;
				var aprobados = true;
				var regex = new RegExp(prefijo,"gi");
				
				$.each(seleccionados, function(indice, accion_id){
					
					//Armar datos de la tabla
					var rowinfo = grid_obj.getRowData(accion_id);
					var estado = $.trim($(rowinfo["Estado"]).text());
					var tipo_accion = $.trim(rowinfo["Tipo de accion personal"]);
					var accionable_id = $.trim(rowinfo["accionable_id"]);
					
					if(!tipo_accion.match(regex)){
						check = false;
					}
					if(!estado.match(/(Aprobado|Enviado)/gi)){
						aprobados = false;
					}

					rows += '<tr><td>'+ rowinfo["No. Accion personal"] +'</td><td>'+ $(rowinfo["Colaborador"]).text() +'</td></tr>';
					
					//Agregar campos a formulario
					pagarAccionPersonalForm.append('<input type="hidden" name="'+ tipopago +'['+ indice +']" value="'+ accionable_id +'" />');
					//tipo_accion.replace( /^\D+/g, '')
				});
				
				if(check == false){
					toastr.warning('Debe seleccionar acciones personales de tipo '+ tipopago);
					return false;
				}
				if(aprobados == false){
					toastr.warning('Debe seleccionar '+ ucFirst(tipopago) + ' con estado '+  (tipopago.match(/(liquidaciones)/g) ? 'Enviado.' : 'Aprobado.') );
					return false;
				}
			}

			//HTML Tabla con listado de Colaboradores
			var html = [
			'<div class="m-b-sm"><h3 class="m-xs">'+ seleccionados.length + (seleccionados.length > 1 ? ' Colaboradores' : ' Colaborador') +'</h3></div>',
				'<table class="table table-bordered">',
					'<thead>',
						'<tr>',
							'<th>Accion Personal</th>',
							'<th>Nombre</th>',
						'</tr>',
					'</thead>',
					'<tbody>',
						rows,
					'</tbody>',
				'</table>',
			].join('\n');
			
			
			//Inicializar opciones del Modal
			pagarAccionPersonalModal.modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			});
			pagarAccionPersonalModal.find('.modal-title').empty().append('Validar creaci&oacute;n de planilla de '+ tipopago);
			pagarAccionPersonalModal.find('.modal-body').empty().append(html);
			pagarAccionPersonalModal.find('.modal-footer').empty().append(botones_modal);
			pagarAccionPersonalModal.modal('show');
		});
		
		//Confirmar Pago
		$(pagarAccionPersonalModal).on("click", '#pagarAccionPersonalModalBtn', function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			//Cambiar seleccion del menu
			localStorage.setItem('ms-selected', 'nomina');
			localStorage.setItem('ml-selected', 'Planilla');
			
			//Enviar formulario
			pagarAccionPersonalForm.submit();
	        $('body').trigger('click');
		});
	};

	return{	    
		init: function() {
			eventos();
		}
	};
})();

listarAccionPersonal.init();