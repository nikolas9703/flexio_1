/**
 * Servicio Evaluacion
 */
bluapp.service('evaluacionService', ['$http', '$document', '$rootScope', function($http, $document, $rootScope) {

	var scope = this;
	var requisito_id = '';
	var formulario = '#crearEvaluacionForm';

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
			$(formulario).find('.fecha-evaluacion').daterangepicker({
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
				$(formulario).find('.fecha-evaluacion').val(picker.startDate.format('DD/MM/YYYY'));
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

		}, 1000);
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
 * Provider Directova ng-flow
 */
bluapp.config(['flowFactoryProvider', function (flowFactoryProvider) {

	flowFactoryProvider.factory = fustyFlowFactory;

	flowFactoryProvider.defaults = {
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
	};
}]);

/**
 * Controlador Formulario de Evaluacion
 */
bluapp.controller("EvaluacionesController", function($rootScope, $scope, $document, $http, $rootScope, $compile, evaluacionService){

	var url = window.phost() + "evaluaciones/ajax-seleccionar-evaluacion";
	var formulario = '#crearEvaluacionForm';
	var modal = $('#formularioEvaluacionModal');
	var botones = ['<div class="row">',
	     		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
 		   		'<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal" ng-click="limpiarFormulario($event, $flow)">Cancelar</button>',
 		   '</div>',
 		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
 		   		'<button class="btn btn-w-m btn-primary btn-block {{disabledBtn}}" type="button" ng-click="guardarEvaluacion($event, $flow)" ng-bind-html="guardarBtn"></button>',
 		   '</div>',
 		   '</div>'
	].join('\n');
	var campo_departamento = '#departamento_id';
	var campo_cargo = '#cargo_id';

	//Compilar botones en angularjs
	botones = $compile(botones)($scope);

	//Agregar botones al modal
	//modal.find('.modal-footer').empty().append(botones);

	$scope.fileBtn = 'Seleccione';
	$scope.fileClassBtn = 'btn-default';
	$scope.guardarBtn = 'Guardar';
	$scope.disabledBtn = '';

	//Inicializar variables scope
	$scope.evaluacion = {
		erptkn: tkn,
		id: "",
		tipo_evaluacion_id: "",
		resultado_id: "",
		estado_id: "",
		colaborador_id: "",
		numero: "",
		fecha: "",
		evaluado_por: "",
		calificacion: "",
		documento_evaluacion: "",
		observaciones: "",
		colaborador_id: window.location.href.match(/(colaboradores)/g) == true ? typeof colaborador_id != 'undefined' ? colaborador_id : '' : ""
	};

	//Inicializar campos, plugins y validacion
	evaluacionService.init();

	//Funcion popular formulario
	//con informacion de la evaluacion seleccionada.
	$scope.popularFormulario = function()
	{
		//Before using local storage, check browser support for localStorage and sessionStorage
		if(typeof(Storage) !== "undefined") {
			//Verificar si existe la variable
			//proveniente de Local Storage
			if(localStorage.getItem("evaluacion_id")){

				var evaluacion_id = localStorage.getItem("evaluacion_id");

				//Buscar datos para popular campos
				evaluacionService.ajax(phost() + "evaluaciones/ajax-seleccionar-evaluacion", {
					id: evaluacion_id,
				}).then(function successCallback(response) {

					//Check Session
					if( $.isEmptyObject(response.data.session) == false){
						window.location = phost() + "login?expired";
					}

					$scope.evaluacion.tipo_evaluacion_id 	= response.data.tipo_evaluacion_id.toString();
					$scope.evaluacion.estado_id 			= response.data.estado_id.toString();
					$scope.evaluacion.resultado_id 			= response.data.resultado_id.toString();
					$scope.evaluacion.evaluado_por 			= response.data.evaluado_por.toString();
					$scope.evaluacion.fecha 				= response.data.fecha;
					$scope.evaluacion.calificacion  		= response.data.calificacion;
					$scope.evaluacion.observaciones  		= response.data.observaciones;
					$scope.evaluacion.documento_evaluacion  = response.data.documento_evaluacion;
					$scope.evaluacion.colaborador_id 		= response.data.colaborador_id;
					$scope.evaluacion.id 					= evaluacion_id;

					if(window.location.href.match(/(accion_personal)/g)){

						//seleccionar el colaborador en el dropdown
						//que aparece en la barra superior
						$('select#colaborador_id').find('option[value="'+ response.data.colaborador_id +'"]').prop('selected', 'selected');

						//actualizar chosen barra accion personal
						accionPersonal.actualizar_chosen();
					}

					//actualizar chosen
					evaluacionService.actualizar_chosen();

			    }, function errorCallback(response) {
			        // called asynchronously if an error occurs
			        // or server returns response with an error status.
			    });

				//Borrar variable de localstorage
				localStorage.removeItem("evaluacion_id");
			}
		}
	};

	//Ejecutar funcion
	$scope.popularFormulario();

	//evento: seleccion de archivo
	$scope.archivoSeleccionado = function($file, e, $flow)
    {
		e.preventDefault();

		//agregar texto de archivo seleccionado.
		$scope.fileClassBtn = 'btn-default';
		$scope.fileBtn = '<i class="fa fa-upload"></i> 1 archivo seleccionado';
    };

    //evento: seleccion de archivo
	$scope.limpiarFormulario = function(e, $flow)
    {
		if(typeof e != 'undefined'){
			e.preventDefault();
		}

		$scope.evaluacion = {
			erptkn: tkn,
			id: "",
			tipo_evaluacion_id: "",
			resultado_id: "",
			estado_id: "",
			colaborador_id: "",
			numero: "",
			fecha: "",
			evaluado_por: "",
			calificacion: "",
			documento_evaluacion: "",
			observaciones: "",
			colaborador_id: typeof colaborador_id != 'undefined' ? colaborador_id : ''
		};

		$(formulario).find('input[type="text"], input[type="checkbox"]').val('').removeAttr("checked");
		$(formulario).find('textarea').empty().val('');

		if(typeof $flow != 'undefined'){
			//Cancelar upload de archivo
	    	$flow.cancel();
		}

		//campos select
		$scope.limpiar_seleccion_dropdown();

		//Botones
		$scope.fileBtn = 'Seleccione';
		$scope.fileClassBtn = 'btn-default';
		$scope.guardarBtn = 'Guardar';
		$scope.disabledBtn = '';

		//refresh chosen
    	evaluacionService.actualizar_chosen();
    };

    /**
     * Evento Change del Campo Centro Contable
     */
    $scope.popularDepartamento = function(e)
    {
    	if($scope.evaluacion.centro_contable_id == ""){
			return false;
		}

    	var seleccionado = '';

    	//Mensaje de Loading
		$('.departamento-loader').remove();
		$(campo_departamento).closest('div').append('<div class="departamento-loader"><small class="text-success">Buscando &Aacute;reas de Negocio... <i class="fa fa-circle-o-notch fa-spin"></i></small></div>');


    	//Buscar listado de Areas de Negocios
		//Asociadas al Centro contable seleccionado.
    	$scope.popular_departamento($scope.evaluacion.centro_contable_id);
    };

    /**
	 * Popular dropdown departamento/area de negocio
	 * segun centro contable
	 * seleccionado.
	 */
    $scope.popular_departamento = function(centro_id, seleccionado)
	{
    	if(centro_id == ""){
			return false;
		}

    	var url = window.phost() + "colaboradores/ajax-lista-departamentos-asociado-centro";

    	evaluacionService.ajax(url, {
    		centro_id: centro_id
		}).then(function successCallback(json) {

			 //Check Session
			if( $.isEmptyObject(json.data.session) == false){
				window.location = phost() + "login?expired";
			}

			//If json object is empty.
			if($.isEmptyObject(json.data['result']) == true){
				//remover mensaje loading
				$('.departamento-loader').remove();

				//limpiar campos
				$scope.limpiar_seleccion_dropdown(campo_departamento);
				return false;
			}

			//Popular Campo Departamento
			$(formulario).find(campo_departamento).empty().append('<option value="">Seleccione</option>').removeAttr('disabled');
			$.each(json.data['result'], function(i, result){
				var selected = seleccionado == result['id'] ? 'selected="selected"' : '';
				$(formulario).find(campo_departamento).append('<option value="'+ result['id'] +'" '+ selected +'>'+ result['nombre'] +'</option>');
			});

			//remover mensaje loading
			$('.departamento-loader').remove();

			//refresh chosen
	    	evaluacionService.actualizar_chosen();

	    }, function errorCallback(response) {
	        // called asynchronously if an error occurs
	        // or server returns response with an error status.
	    });
	};

	$scope.popularCargo = function(e)
    {
    	if($scope.evaluacion.departamento_id == ""){
			return false;
		}

    	var seleccionado = '';

    	//Mensaje de Loading
		$('.cargo-loader').remove();
		$(campo_cargo).closest('div').append('<div class="cargo-loader"><small class="text-success">Buscando Cargos... <i class="fa fa-circle-o-notch fa-spin"></i></small></div>');


    	//Buscar listado de Areas de Negocios
		//Asociadas al Centro contable seleccionado.
    	$scope.popular_cargo($scope.evaluacion.departamento_id);
    };

    /**
	 * Popular dropdown cargo
	 * segun Area de Negocio
	 * seleccionado.
	 */
    $scope.popular_cargo = function(departamento_id, seleccionado)
	{
		if(departamento_id == ""){
			return false;
		}

		var url = window.phost() + 'colaboradores/ajax-lista-cargos';

    	evaluacionService.ajax(url, {
    		departamento_id: departamento_id
		}).then(function successCallback(json) {

			 //Check Session
			if( $.isEmptyObject(json.data.session) == false){
				window.location = phost() + "login?expired";
			}

			//If json object is empty.
			if($.isEmptyObject(json.data['result']) == true){
				//remover mensaje loading
				$('.cargo-loader').remove();

				//limpiar campos
				$scope.limpiar_seleccion_dropdown(campo_cargo);
				return false;
			}

			//Popular Campo Cargo
			$(formulario).find(campo_cargo).removeAttr('disabled').empty().append('<option value="">Seleccione</option>');
			$.each(json.data['result'], function(i, result){
				var selected = seleccionado == result['id'] ? 'selected="selected"' : '';
				$(formulario).find(campo_cargo).append('<option value="'+ result['id'] +'" data-tipo-rata="'+ result['tipo_rata'] +'" data-rata="'+ result['rata'] +'" '+ selected +'>'+ result['nombre'] +'</option>');
			});

			//remover mensaje loading
			$('.cargo-loader').remove();

			//refresh chosen
	    	evaluacionService.actualizar_chosen();

	    }, function errorCallback(response) {
	        // called asynchronously if an error occurs
	        // or server returns response with an error status.
	    });
	};

    //Limpiar seleccion de campo: dropdown
    $scope.limpiar_seleccion_dropdown = function(campo){
		$(formulario).find(campo).attr("disabled", "disabled").empty().append('<option value="">Seleccione</option>').find('option:eq(0)').attr("selected", "selected");

		setTimeout(function(){
			evaluacionService.actualizar_chosen();
		}, 300);
	};

	/**
     * Funcion Cancelar Formulario
     */
	$scope.cancelar = function(e)
    {
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		//Verificar si el formulario esta siendo usado desde
		//Ver Detalle de Colaborador
		if(window.location.href.match(/(colaboradores)/g)){

			//recargar tabla
			tablaAccionPersonal.recargar();

			//Limpiar formulario
			$scope.limpiarFormulario();

		}else{
			window.location = phost() + 'accion_personal/listar';
		}
    };

	/**
     * Funcion Guardar Formulario de Evluacion
     */
	$scope.guardarEvaluacion = function(e, $flow)
    {
    	e.preventDefault();

    	if($(formulario).validate().form() == true)
		{
    		var colaborador_id = $(formulario).find('input[id="campo[colaborador_id]"]').val() != "" ? $(formulario).find('input[id="campo[colaborador_id]"]').val() : $scope.evaluacion.colaborador_id;
    		var url = phost() + "evaluaciones/ajax-guardar-evaluacion";

    		//verificar que alla seleccionado
    		//un colaborador de la barra de filtro
    		if(colaborador_id == "" || colaborador_id == undefined){

    			toastr.warning('Debe seleccionar un colaborador.');
    			return false;
    		}

    		//Estado de guardando en boton
    		$scope.guardarBtn = '<i class="fa fa-circle-o-notch fa-spin"></i> Guardando...';
    		$scope.disabledBtn = 'disabled';

    		// Si en editar no se selecciona
    		// ningun archivo para subir
    		// guardar el formulario por ajax
    		if($flow.files.length == 0){

    			evaluacionService.ajax(url, {
    				evaluacion_id: typeof evaluacion_id != "undefined" ? evaluacion_id : $scope.evaluacion.id,
            		colaborador_id: colaborador_id,
            		fecha: $(formulario).find('input[id="campo[fecha]"]').val(),
            		tipo_evaluacion_id: $scope.evaluacion.tipo_evaluacion_id,
            		estado_id: $scope.evaluacion.estado_id,
            		evaluado_por: $scope.evaluacion.evaluado_por,
            		calificacion: $scope.evaluacion.calificacion,
            		resultado_id: $scope.evaluacion.resultado_id,
            		documento_evaluacion: $scope.evaluacion.documento_evaluacion,
            		observaciones: $scope.evaluacion.observaciones
    			}).then(function successCallback(response) {

    				//Check Session
    				if( $.isEmptyObject(response.data.session) == false){
    					window.location = phost() + "login?expired";
    				}

    				//Verificar si el formulario esta siendo usado desde
    				//Ver Detalle de Colaborador
    				if(window.location.href.match(/(colaboradores)/g)){

    					//mostrar mensaje
    					toastr.success(response.data.mensaje);

    					//recargar tabla
    					tablaAccionPersonal.recargar();

    					//Limpiar formulario
    					$scope.limpiarFormulario();

    				}else{
    					if(response.data.guardado == true){
                			window.location = phost() + 'accion_personal/listar';
                		}
    				}

    		    }, function errorCallback(response) {
    		        // called asynchronously if an error occurs
    		        // or server returns response with an error status.
    		    });

    		}else{

    			//Sobreescribir extra datos
    			$flow.opts.target = url;
        		$flow.opts.query = {
        			erptkn: tkn,
        			evaluacion_id: typeof evaluacion_id != "undefined" ? evaluacion_id : $scope.evaluacion.id,
	        		colaborador_id: colaborador_id,
	        		fecha: $(formulario).find('input[id="campo[fecha]"]').val(),
	        		tipo_evaluacion_id: $scope.evaluacion.tipo_evaluacion_id,
	        		estado_id: $scope.evaluacion.estado_id,
	        		evaluado_por: $scope.evaluacion.evaluado_por,
	        		calificacion: $scope.evaluacion.calificacion,
	        		resultado_id: $scope.evaluacion.resultado_id,
	        		documento_evaluacion: $scope.evaluacion.documento_evaluacion,
	        		observaciones: $scope.evaluacion.observaciones
            	};

            	//Subir archivo
            	$flow.upload();

            	//Evento: al completar subida de archivo.
            	$flow.on('fileSuccess', function(file, message, chunk) {

            		//response
            		var response = $.parseJSON(message);

            		//Check Session
    				if( $.isEmptyObject(response.session) == false){
    					window.location = phost() + "login?expired";
    				}

    				//Verificar si el formulario esta siendo usado desde
    				//Ver Detalle de Colaborador
    				if(window.location.href.match(/(colaboradores)/g)){

    					//mostrar mensaje
    					toastr.success(response.data.mensaje);

    					//recargar tabla
    					tablaAccionPersonal.recargar();

    					//Limpiar formulario
    					$scope.limpiarFormulario();

    				}else{
    					if(response.guardado == true){
                			window.location = phost() + 'accion_personal/listar';
                		}
    				}
            	});
    		}
		}
    };
});
