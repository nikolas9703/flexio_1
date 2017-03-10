/**
 * Servicio Evaluacion
 */
bluapp.service('entregaInventarioService', ['$http', '$document', '$rootScope', function($http, $document, $rootScope) {

	var scope = this;
	var requisito_id = '';
	var formulario = '#entregaInventarioForm';
	var campo_cantidad = 'input[id="campo[cantidad]"]';

	//Funcion para ejecurtar ajax
	this.ajax = function(url, data) {

		return $http({
			 method: 'POST',
			 url: url,
			 data : $.param($.extend({erptkn: tkn}, data)),
			 cache: false,
		     xsrfCookieName: 'erptknckie_secure',
			 headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
		});
	};

	//Funcion para inicializar plugins
	this.init = function() {

		setTimeout(function(){

			//Plugin Datepicker
			$(formulario).find('.fecha-entrega').daterangepicker({
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

				scope.calculaProximaFechaEntrega();
			});

			//Validacion
			$.validator.setDefaults({
		    	errorPlacement: function(error, element){
		    		return true;
		    	}
			});
			$(formulario).validate({
				focusInvalid: true,
				ignore: '',
				wrapper: ''
			});

			//Inicializar Touchspin en campo de cantidad
			$(formulario).find(campo_cantidad).TouchSpin({
                min: 0,
                step: 1
            });

			//Cambiar height de campo dropdown chosen
			$(formulario).find('#item_id').chosen({
				width: '100%',
	        }).trigger('chosen:updated').on('chosen:showing_dropdown', function(evt, params) {
	        	$('#item_id_chosen').find('ul.chosen-results').css({'height':'100px !important'}).attr("style", "height:100px !important").prop("style", "height:100px !important");
	        });

			$(formulario).find('#duracion_id').chosen({
				width: '100%',
	        }).trigger('chosen:updated').on('chosen:showing_dropdown', function(evt, params) {
	        	$('#duracion_id_chosen').find('ul.chosen-results').css({'height':'100px !important'}).attr("style", "height:100px !important").prop("style", "height:100px !important");
	        });

			$(formulario).find('#entregado_por').chosen({
				width: '100%',
	        }).trigger('chosen:updated').on('chosen:showing_dropdown', function(evt, params) {
	        	$('#entregado_por_chosen').find('ul.chosen-results').css({'height':'100px !important'}).attr("style", "height:100px !important").prop("style", "height:100px !important");
	        });

		}, 1000);
    };

    this.calculaProximaFechaEntrega = function() {
    	var fecha_entrega = $(formulario).find('.fecha-entrega').val();
    	var duracion_id = $(formulario).find('#duracion_id').find('option:selected').val();

    	if(fecha_entrega == '' || duracion_id == ''){
    		$(formulario).find('.fecha-proxima-entrega').val('');
    		return false;
    	}

    		fecha_entrega = fecha_entrega.split("/").reverse().join("-");
    	var duracion = $(formulario).find('#duracion_id').find('option:selected').text();
    	var tiempos = ['anos', 'meses', 'semanas', 'dias', 'horas', 'minutos', 'segundos'];
		var siglas = {
			'anos': 'years',
			'meses': 'months',
			'semanas': 'weeks',
			'dias':'days',
			'horas':'hours',
			'minutos':'minutes',
			'segundos':'seconds'
		};

    	if(duracion != ''){
    		duracion = duracion.split('y');
    	}

    	if(duracion.length > 1){

    		//Duracion 1
    		var t1 = duracion[0];
    		var t1_numero = t1.match(/\d/g)[0];
    		var t1_tipo = $.trim(t1.replace(/\d/g, '').replace(/\u00F1/g, 'n'));

    		//Seleccionar tiempo (year, month, etc)
    		var tiempo1 = tiempos.map(function(current, index, array){
    			if((new RegExp(t1_tipo, 'gi')).test(array[index])){
    				return current;
    			}
    		}).join('');
    		tiempo1 = siglas[tiempo1];

    		//Duracion 2
    		var t2 = duracion[1];
    		var t2_numero = t2.match(/\d/g)[0];
    		var t2_tipo = $.trim(t2.replace(/\d/g, '').replace(/\u00F1/g, 'n'));

    		//Seleccionar tiempo (year, month, etc)
    		var tiempo2 = tiempos.map(function(current, index, array){
    			if((new RegExp(t2_tipo, 'gi')).test(array[index])){
    				return current;
    			}
    		}).join('');
    		tiempo2 = siglas[tiempo2];

    		//Sumarle Tiempo a la fecha de entrerga
    		var proxima_entrega = moment(fecha_entrega).add(t1_numero, tiempo1).add(t2_numero, tiempo2).format("DD/MM/YYYY")
    		$(formulario).find('.fecha-proxima-entrega').val(proxima_entrega);

    	}else{

    		//Duracion
    		var t = duracion[0];
    		var t_numero = t.match(/\d/g)[0];
    		var t_tipo = $.trim(t.replace(/\d/g, '').replace(/\u00F1/g, 'n'));

    		//Seleccionar tiempo (year, month, etc)
    		var tiempo = tiempos.map(function(current, index, array){
    			if((new RegExp(t_tipo, 'gi')).test(array[index])){
    				return current;
    			}
    		}).join('');
    		tiempo = siglas[tiempo];

    		//Sumarle Tiempo a la fecha de entrerga
    		var proxima_entrega = moment(fecha_entrega).add(t_numero, tiempo).format("DD/MM/YYYY")
    		$(formulario).find('.fecha-proxima-entrega').val(proxima_entrega);
    	}
	};

    //Funcion para inicializar plugins
	this.actualizar_chosen = function() {

		//refresh chosen
		setTimeout(function(){
			$(formulario).find('select.chosen-select').trigger('chosen:updated');
		}, 50);
	};

}]);

/**
 * Controlador Formulario de Evaluacion
 */
bluapp.controller("formularioEntregaInventarioController", function($rootScope, $scope, $document, $http, $rootScope, $compile, entregaInventarioService){

	var url = window.phost() + "colaboradores/ajax-seleccionar-entrega-inventario";
	var formulario = '#entregaInventarioForm';
	var modal = $('#entregaInventarioModal');
	var botones = ['<div class="row">',
	     		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
 		   		'<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal" ng-click="limpiarFormulario($event)">Cancelar</button>',
 		   '</div>',
 		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
 		   		'<button class="btn btn-w-m btn-primary btn-block {{disabledBtn}}" type="button" ng-click="guardarEntrega($event)" ng-bind-html="guardarBtn"></button>',
 		   '</div>',
 		   '</div>'
	].join('\n');
	var campo_items = '#item_id';
	var campo_bodega = '#bodega_uuid';
	var campo_cantidad = 'input[id="campo[cantidad]"]';
	var campo_tipo_reemplazo = '#tipo_reemplazo_id';
	var btn_cantidad_item = '#cantidadBtn';

	//Compilar botones en angularjs
	botones = $compile(botones)($scope);

	//Agregar botones al modal
	modal.find('.modal-footer').empty().append(botones);

	//Inicializar scope de boton de subir archivo
	//y boton de guardar
	$scope.fileBtn = 'Seleccione';
	$scope.fileClassBtn = 'btn-default';
	$scope.guardarBtn = 'Guardar';
	$scope.disabledBtn = '';

	//Inicializar variables scope de formulario
	$scope.entrega = {
		erptkn: tkn,
		id: "",
		departamento_id: "",
		bodega_uuid: "",
		categoria_id: "",
		item_id: "",
		codigo: "",
		cantidad: "",
		duracion_id: "",
		tipo_reemplazo_id: "",
		fecha_entrega: "",
		proxima_entrega: "",
		comprobante_entrega: ""
	};

	//scope de popover
	$scope.item = {
		disponible: '',
		no_disponible: '',
		total: '',
	};

	//Inicializar pluin flowjs
	var flow = new Flow({
		target: phost() + "colaboradores/ajax-guardar-entrega",
		permanentErrors: [404, 500, 501],
		maxChunkRetries: 1,
		chunkRetryInterval: 5000,
		progressCallbacksInterval: 100,
		singleFile: true,
		query: {
			erptkn: tkn
		},
		testMethod: 'POST',
		testChunks:false,
		uploadMethod: 'POST'
	});
	flow.assignBrowse(document.getElementById('comprobante_entrega'));

	//Cambiar texto de botkon de subir, al seleccionar un archivo
	flow.on('fileAdded', function(file, e){

		e.preventDefault();

		setTimeout(function () {
			$rootScope.safeApply(function () {
				//agregar texto de archivo seleccionado.
				$scope.fileClassBtn = 'btn-default';
				$scope.fileBtn = '<i class="fa fa-upload"></i> 1 archivo seleccionado';
	        });
	     }, 50);
	});

	//Inicializar campos, plugins y validacion
	entregaInventarioService.init();

	//Al abrir modal verificar si existe id de entrega de inventario
	modal.on('shown.bs.modal', function(e){

		var entrega_id = $(formulario).find('input[id="campo[id]"]').val();
		var departamento_id = modal.attr('data-departamento-id');
		$scope.entrega.departamento_id = departamento_id;

		if(entrega_id != "" && entrega_id != undefined){
			//Buscar informacion de la evaluacion

			entregaInventarioService.ajax(url, {
				colaborador_id: colaborador_id,
				entrega_id: entrega_id
			}).then(function(response) {

				if($.isEmptyObject(response.data) == true){
					$scope.limpiarFormulario();
					return false;
				}

				//Si existe Categoria
				if(response.data["categoria_id"]){
					setTimeout(function () {
						$rootScope.safeApply(function () {
							//Popular campo de Items
							$scope.entrega.item_id = response.data["item_id"].toString();
							$scope.popular_items(response.data["categoria_id"], response.data["bodega_uuid"], response.data["item_id"]);
				        });
				     }, 50);
				}

				//Si item id tiene valor, habilitar campo de cantidad
				if(response.data["item_id"]){

					//$(formulario).find(campo_items).trigger('change');
					$(formulario).find(btn_cantidad_item).removeAttr('disabled');
					$(formulario).find(campo_cantidad).removeAttr('disabled');
				}

				$scope.entrega.bodega_uuid 			= response.data["bodega_uuid"].toString();
				$scope.entrega.departamento_id 		= response.data["departamento_id"].toString();
				$scope.entrega.categoria_id 		= response.data["categoria_id"].toString();
				$scope.entrega.codigo 				= response.data["codigo"];
				$scope.entrega.fecha_entrega 		= moment(response.data["fecha_entrega"]).format("DD/MM/YYYY");
				$scope.entrega.proxima_entrega 		= moment(response.data["proxima_entrega"]).format("DD/MM/YYYY");
				$scope.entrega.duracion_id 			= response.data["duracion_id"].toString();
				$scope.entrega.tipo_reemplazo_id 	= response.data["tipo_reemplazo_id"].toString();
				$scope.entrega.entregado_por 		= response.data["entregado_por"].toString();
				$scope.entrega.cantidad 			= response.data["cantidad"];

				//actualizar campos chosen
		    	entregaInventarioService.actualizar_chosen();

		    }, function errorCallback(response) {
		        // called asynchronously if an error occurs
		        // or server returns response with an error status.
		    });
		}

		//actualizar campos chosen
		entregaInventarioService.actualizar_chosen();
	});

	//Al cerra modal
	modal.on('hidden.bs.modal', function(e){

		//Redimensionar tabla de evaluaciones
		//tablaEvaluaciones.redimensionar();
	});

	//AngularJS safe $apply (prevent "Error: $apply already in progress")
	$rootScope.safeApply = function safeApply(operation) {
		var phase = this.$root.$$phase;
		if (phase !== '$apply' && phase !== '$digest') {
			this.$apply(operation);
			return;
		}

		if (operation && typeof operation === 'function')
		operation();
	};

    //evento: seleccion de archivo
	$scope.limpiarFormulario = function(e, $flo)
    {
		if(typeof e != 'undefined'){
			e.preventDefault();
		}

		$scope.entrega = {
			erptkn: tkn,
			id: "",
			departamento_id: "",
			bodega_uuid: "",
			categoria_id: "",
			item_id: "",
			codigo: "",
			cantidad: "",
			duracion_id: "",
			fecha_entrega: "",
			proxima_entrega: "",
			comprobante_entrega: ""
		};

		if(typeof flow != 'undefined'){
			//Cancelar upload de archivo
	    	flow.cancel();
		}

		//refresh chosen
    	entregaInventarioService.actualizar_chosen();
    };

    //Evento Change del Campo Categoria
    $scope.popularItems = function(e)
    {
    	var seleccionado = '';

    	//Mensaje de Loading
		$('.items-loader').remove();
		$(campo_items).closest('.form-group').append('<div class="items-loader"><small class="text-success">Buscando Items... <i class="fa fa-circle-o-notch fa-spin"></i></small></div>');

		//Reiniciar/desabilitar cantidad, codigo
		$scope.entrega.codigo = '';
		$scope.entrega.item_id = '';
		$(formulario).find(btn_cantidad_item).webuiPopover('hide');
		$(formulario).find(btn_cantidad_item).prop('disabled','disabled');
		$(formulario).find(campo_cantidad).val('').prop('disabled','disabled');

    	//Buscar listado de Items
		//Asociadas a Categoria seleccionado.
    	$scope.popular_items($scope.entrega.categoria_id, $scope.entrega.bodega_uuid);
    };

    /**
	 * Popular dropdown departamento/area de negocio
	 * segun centro contable
	 * seleccionado.
	 */
    $scope.popular_items = function(categoria_id, bodega_uuid, seleccionado)
	{
    	if(categoria_id == "" && bodega_uuid == ""){
    		//remover mensaje loading
			$('.items-loader').remove();

			//limpiar campos
			$scope.entrega.codigo = '';
			$scope.entrega.item_id = '';
			$scope.limpiar_seleccion_dropdown(campo_items);
			$(formulario).find(btn_cantidad_item).webuiPopover('hide');
			$(formulario).find(btn_cantidad_item).prop('disabled','disabled');
			$(formulario).find(campo_cantidad).val('').prop('disabled','disabled');
    		return false;
		}

    	var url = window.phost() + "colaboradores/ajax-lista-items-por-categoria";

    	entregaInventarioService.ajax(url, {
    		categoria_id: categoria_id,
    		bodega_uuid: bodega_uuid
		}).then(function successCallback(json) {

			 //Check Session
			if( $.isEmptyObject(json.data.session) == false){
				window.location = phost() + "login?expired";
			}

			//If json object is empty.
			if($.isEmptyObject(json.data['result']) == true){
				//remover mensaje loading
				$('.items-loader').remove();

				//limpiar campos
				$scope.entrega.codigo = '';
				$scope.limpiar_seleccion_dropdown(campo_items);
				$(formulario).find(btn_cantidad_item).webuiPopover('hide');
				$(formulario).find(btn_cantidad_item).attr('disabled','disabled');
				$(formulario).find(campo_cantidad).val('').attr('disabled','disabled');
				return false;
			}

			//Popular Campo Departamento
			$(formulario).find(campo_items).empty().append('<option value="">Seleccione</option>');

			//Verificar si no se trata de un reemplazo
			if($(formulario).find(campo_tipo_reemplazo).closest('.form-group').is(':visible') == false){
				$(formulario).find(campo_items).removeAttr('disabled');
			}

			$.each(json.data['result'], function(i, result){
				var selected = seleccionado == result['id'] ? 'selected="selected"' : '';
				$(formulario).find(campo_items).append('<option value="'+ result['id'] +'" '+ selected +' data-codigo="'+ result['codigo'] +'" data-disponible="'+ result['existencia']['cantidadDisponibleBase'] +'" data-no-disponible="'+ result['existencia']['cantidadNoDisponibleBase'] +'">'+ result['nombre'] +'</option>');
			});

			//remover mensaje loading
			$('.items-loader').remove();

			//refresh chosen
	    	entregaInventarioService.actualizar_chosen();

	    }, function errorCallback(response) {
	        // called asynchronously if an error occurs
	        // or server returns response with an error status.
	    });
	};

	//Al cambiar duracion, calcular proxima fecha de entrega
	$scope.calcularProximaFechaEntrega = function()
    {

		if($scope.entrega.duracion_id == ''){
    		$(formulario).find('.fecha-proxima-entrega').val('');
    		return false;
    	}

		entregaInventarioService.calculaProximaFechaEntrega();
    };

    //Al seleccionar bodega
	$scope.establecerCantidad = function()
    {
		//si seleccion de item es vacio
		if($scope.entrega.item_id == ""){
    		//desabilitar cantidad
			$scope.entrega.codigo = '';
    		$(formulario).find(btn_cantidad_item).attr('disabled','disabled');
    		$(formulario).find(btn_cantidad_item).webuiPopover('hide');
			$(formulario).find(campo_cantidad).val('').attr('disabled','disabled');
    		return false;
		}

		//Habilitar boton de ayuda
    	$(formulario).find(btn_cantidad_item).removeAttr('disabled');
		$(formulario).find(campo_cantidad).removeAttr('disabled');

		//Establecer cantidad maxima disponible a seleccionar
		var disponible = parseInt($(formulario).find(campo_items).find('option:selected').attr('data-disponible'));

		//Establacer cantidad maxima a seleccionar
		$(formulario).find(campo_cantidad).trigger("touchspin.updatesettings", {max: disponible});

		//Popular campo de codigo
		$scope.entrega.codigo = $(formulario).find(campo_items).find('option:selected').attr('data-codigo');
    };

    //Limpiar seleccion de campo: dropdown
    $scope.limpiar_seleccion_dropdown = function(campo){
		$(formulario).find(campo).attr("disabled", "disabled").empty().append('<option value="">Seleccione</option>').find('option:eq(0)').attr("selected", "selected");

		setTimeout(function(){
			entregaInventarioService.actualizar_chosen();
		}, 300);
	};

	/**
     * Funcion Guardar Formulario de Entrega de Inventario
     */
	$scope.guardarEntrega = function(e)
    {
    	e.preventDefault();

    	if($(formulario).validate().form() == true)
		{
    		var entrega_id = $(formulario).find('input[id="campo[id]"]').val();
    		var url = phost() + "colaboradores/ajax-guardar-entrega";

    		// Verificar seleccion de archivo
    		// Y si no existe variable "evaluacion_id"
    		if( $.isEmptyObject(flow.files) == true && entrega_id == ""){

    			//Debe selccionar archivo para poder guaradr evaluacion
    			$scope.fileClassBtn = 'btn-danger';
    			return false;
        	}

    		//Estado de guardando en boton
    		$scope.guardarBtn = '<i class="fa fa-circle-o-notch fa-spin"></i> Guardando...';
    		$scope.disabledBtn = 'disabled';

    		// Si en editar no se selecciona
    		// ningun archivo para subir
    		// guardar el formulario por ajax
    		if(flow.files.length == 0){

    			entregaInventarioService.ajax(url, {
    				entrega_id: entrega_id,
            		colaborador_id: $(formulario).find('input[id="campo[colaborador_id]"]').val(),
            		bodega_uuid: $(formulario).find('#bodega_uuid').find('option:selected').val(),
            		departamento_id: $(formulario).find('#departamento_id').find('option:selected').val(),
            		categoria_id: $(formulario).find('#categoria_id').find('option:selected').val(),
            		item_id: $scope.entrega.item_id,
            		codigo: $scope.entrega.codigo,
            		cantidad: $scope.entrega.cantidad,
            		duracion_id: $scope.entrega.duracion_id,
            		tipo_reemplazo_id: $(formulario).find(campo_tipo_reemplazo).find('option:selected').val(),
            		entregado_por: $(formulario).find('#entregado_por').find('option:selected').val(),
            		fecha_entrega: $(formulario).find('input[id="campo[fecha_entrega]"]').val(),
            		proxima_entrega: $(formulario).find('input[id="campo[proxima_entrega]"]').val(),
    			}).then(function successCallback(response) {

    				if(response.data.guardado == true)
    				{
	    				//Mensaje
	    				toastr.success(response.data.mensaje);

	    				//Limpiar Formulario
	    				$scope.limpiarFormulario();

	    				$scope.guardarBtn = 'Guardar';
	    				$scope.disabledBtn = '';
	    				$scope.fileBtn = 'Seleccione';
	    				$scope.fileClassBtn = 'btn-default';

	    				//Cerrar modal
	    				modal.modal('hide');

	    				//Recargar tabla de evaluaciones
        				tablaInventario.recargar();

    				}else{
            			//mensaje
            			toastr.error(response.data.mensaje);

            			//reset boton
            			$scope.fileBtn = 'Seleccione';
            			$scope.fileClassBtn = 'btn-default';
            			$scope.guardarBtn = 'Guardar';
            			$scope.disabledBtn = '';
            		}

    		    }, function errorCallback(response) {
    		        // called asynchronously if an error occurs
    		        // or server returns response with an error status.
    		    });

    		}else{

    			//Sobreescribir extra datos
        		flow.target = url;
        		flow.opts.query = {
            		erptkn: tkn,
            		entrega_id: entrega_id,
            		colaborador_id: $(formulario).find('input[id="campo[colaborador_id]"]').val(),
            		bodega_uuid: $(formulario).find('#bodega_uuid').find('option:selected').val(),
            		departamento_id: $(formulario).find('#departamento_id').find('option:selected').val(),
            		categoria_id: $(formulario).find('#categoria_id').find('option:selected').val(),
            		item_id: $scope.entrega.item_id,
            		codigo: $scope.entrega.codigo,
            		cantidad: $scope.entrega.cantidad,
            		duracion_id: $scope.entrega.duracion_id,
            		tipo_reemplazo_id: $(formulario).find(campo_tipo_reemplazo).find('option:selected').val(),
            		entregado_por: $(formulario).find('#entregado_por').find('option:selected').val(),
            		fecha_entrega: $(formulario).find('input[id="campo[fecha_entrega]"]').val(),
            		proxima_entrega: $(formulario).find('input[id="campo[proxima_entrega]"]').val()
            	};

            	//Subir archivo
            	flow.upload();
            	flow.resume();

            	//Evento: al completar subida de archivo.
            	flow.on('fileSuccess', function(file, message, chunk) {

            		//response
            		var response = $.parseJSON(message);
            		var colaborador_uuid = modal.attr("data-uuid");

            		if(response.guardado == true){

            			if(entrega_id == ''){
            				window.location = phost() + 'colaboradores/ver/'+ colaborador_uuid;
            			}else{

            				//Mensaje
            				toastr.success(response.mensaje);

            				//Limpiar Formulario
            				$scope.limpiarFormulario(e, flow);

            				setTimeout(function () {
                				$rootScope.safeApply(function () {
                					//reset boton
                					$scope.guardarBtn = 'Guardar';
                    				$scope.disabledBtn = '';
                    				$scope.fileBtn = 'Seleccione';
                    				$scope.fileClassBtn = 'btn-default';
                		          });
                		     }, 100);

            				//Cerrar modal
            				modal.modal('hide');

            				//Recargar tabla de evaluaciones
            				tablaInventario.recargar();
            			}
            		}else{

            			setTimeout(function () {
            				$rootScope.safeApply(function () {
            					//reset boton
            					$scope.guardarBtn = 'Guardar';
                				$scope.disabledBtn = '';
                				$scope.fileBtn = 'Seleccione';
                				$scope.fileClassBtn = 'btn-default';
            		          });
            		     }, 100);

            			//Cancelar upload de archivo
            	    	flow.cancel();

            			//mensaje
            			toastr.error(response.mensaje);
            		}

            	});
    		}
		}
    };
});

//Directiva para popover de boton de ayuda de campo Cantidad
bluapp.directive('popOver', function ($compile) {
    var itemsTemplate = ['<table class="table table-bordered no-margins">',
	'<thead>',
	    '<tr>',
			'<th>Disp.</th>',
			'<th>No Disponible</th>',
			'<th>Total</th>',
		'</tr>',
		'</thead>',
		'<tbody>',
			'<tr>',
				'<td>{{item.disponible}}</td>',
				'<td>{{item.no_disponible}}</td>',
				'<td>{{item.total}}</td>',
			'</tr>',
		'</tbody>',
	'</table>'
	].join('\n');

    var getTemplate = function (contentType) {
        var template = '';
        switch (contentType) {
            case 'items':
                template = itemsTemplate;
                break;
        }
        return template;
    }
    return {
        restrict: "A",
        transclude: true,
        scope: true,
        replace: true,
        template: "<span ng-transclude></span>",
        link: function (scope, element, attrs, controller) {

        	var formulario = '#entregaInventarioForm';
        	var campo_items = '#item_id';
        	var campo_cantidad = 'input[id="campo[cantidad]"]';
        	var btn_cantidad_item = '#cantidadBtn';
        	var html = getTemplate("items");
            popOverContent = $compile(html)(scope);

        	scope.$watch('entrega.item_id', function(item_id) {

        		//si seleccion de item es vacio
        		if(item_id == ""){
            		return false;
        		}

        		setTimeout(function(){

            		//LLenar tabla informativa de disponibilidad de Items
            		var disponibles = parseFloat($(formulario).find(campo_items).find('option[value="'+ item_id +'"]').attr('data-disponible'));
            		var no_disponibles = parseFloat($(formulario).find(campo_items).find('option[value="'+ item_id +'"]').attr('data-no-disponible'));
            		var total = disponibles + no_disponibles;

            		scope.item = {
            			disponible: disponibles,
            			no_disponible: no_disponibles,
            			total: total,
            		};

            		scope.safeApply(function () {
            			scope.item = {
                			disponible: disponibles,
                			no_disponible: no_disponibles,
                			total: total,
                		};
    		        });

            		//Destruir popover
            		$(formulario).find(btn_cantidad_item).webuiPopover('destroy');

            		//Inicializar Popover
            		$(formulario).find(btn_cantidad_item).webuiPopover({
            			content:popOverContent,
            		});

        		}, 1000);
    	    });

            $(element).webuiPopover({
            	placement:'top',
				trigger:'click',
				title:'',
				multi:true,
				closeable:false,
				style:'',
				delay:300,
				padding:true,
				backdrop:false,
				content:popOverContent,
			});
        }
    };
});
