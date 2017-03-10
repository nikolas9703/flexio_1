/**
 * Servicio Vacaciones
 */
bluapp.service('vacacionesService', ['$http', '$document', '$rootScope', function($http, $document, $rootScope) {

	var scope = this;
	var requisito_id = '';
	var formulario = '#crearVacacionesForm';

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
			$(formulario).find('#fecha_desde').daterangepicker({
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
				$(formulario).find('#fecha_desde').val(picker.startDate.format('DD/MM/YYYY'));
			});
			$(formulario).find('#fecha_hasta').daterangepicker({
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

				$(formulario).find('#fecha_hasta').val(picker.startDate.format('DD/MM/YYYY'));

				//Calcular cantidad dias entre fecha desde y fecha hasta
				var fecha_desde = $(formulario).find('#fecha_desde').val();
				var fecha_desde = fecha_desde.split("/").reverse().join("-");
				var fecha_hasta = $(formulario).find('#fecha_hasta').val();
				var fecha_hasta = fecha_hasta.split("/").reverse().join("-");

				var a = moment(fecha_desde);
				var b = moment(fecha_hasta);
				var cantidad_dias = b.diff(a, 'days')+1 // 1

				//Popular cantidad dias
				$(formulario).find('input[id="campo[cantidad_dias]"]').val(cantidad_dias)
			});
			$(formulario).find('.fecha-pago').daterangepicker({
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
				$(formulario).find('.fecha-pago').val(picker.startDate.format('DD/MM/YYYY'));
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
 * Controlador Formulario de Vacaciones
 */
bluapp.controller("VacacionesController", function($rootScope, $scope, $document, $http, $rootScope, $compile, vacacionesService){

	var url = window.phost() + "vacaciones/ajax-seleccionar-evaluacion";
	var formulario = '#crearVacacionesForm';

	$scope.fileBtn = 'Seleccione';
	$scope.fileClassBtn = 'btn-default';
	$scope.guardarBtn = 'Guardar';
	$scope.disabledBtn = '';

	//Inicializar variables scope
	$scope.vacaciones = {
		erptkn: tkn,
		id: "",
		dias_disponibles: "",
		fecha_desde: "",
		fecha_hasta: "",
		cantidad_dias: "",
		estado_id: "",
		pago_inmediato_id: "",
		cuenta_pasivo_id: "",
		fecha_pago: "",
		observaciones: "",
		colaborador_id: window.location.href.match(/(colaboradores)/g) != null ? (typeof colaborador_id != 'undefined' ? colaborador_id : '') : ""
	};

	//Inicializar campos, plugins y validacion
	vacacionesService.init();

	//Funcion popular formulario
	//con informacion de la vacacion seleccionada.
	$scope.popularFormulario = function()
	{
		//Before using local storage, check browser support for localStorage and sessionStorage
		if(typeof(Storage) !== "undefined") {
			//Verificar si existe la variable
			//proveniente de Local Storage
			if(localStorage.getItem("vacacion_id")){

				var vacacion_id = localStorage.getItem("vacacion_id");

				//Buscar datos para popular campos
				vacacionesService.ajax(phost() + "vacaciones/ajax-seleccionar-vacacion", {
					id: vacacion_id,
				}).then(function successCallback(response) {

					//Check Session
					if( $.isEmptyObject(response.data.session) == false){
						window.location = phost() + "login?expired";
					}

					$scope.vacaciones.cuenta_pasivo_id 	= typeof response.data.cuenta_pasivo_id != 'undefined' ? response.data.cuenta_pasivo_id.toString() : '';
					$scope.vacaciones.estado_id 		= typeof response.data.estado_id != 'undefined' ? response.data.estado_id.toString() : '';
					$scope.vacaciones.pago_inmediato_id = typeof response.data.pago_inmediato_id != 'undefined' ? response.data.pago_inmediato_id.toString() : '';
					$scope.vacaciones.fecha_desde 		= typeof response.data.fecha_desde != 'undefined' ? response.data.fecha_desde : '';
					$scope.vacaciones.fecha_hasta 		= typeof response.data.fecha_hasta != 'undefined' ? response.data.fecha_hasta : '';
					$scope.vacaciones.fecha_pago		= typeof response.data.fecha_pago != 'undefined' ? response.data.fecha_pago : '';
					$scope.vacaciones.dias_disponibles 	= typeof response.data.dias_disponibles != 'undefined' ? response.data.dias_disponibles : '';
					$scope.vacaciones.cantidad_dias 	= typeof response.data.cantidad_dias != 'undefined' ? response.data.cantidad_dias : '';
					$scope.vacaciones.observaciones 	= typeof response.data.observaciones != 'undefined' ? response.data.observaciones : '';
					$scope.vacaciones.colaborador_id 	= typeof response.data.colaborador_id != 'undefined' ? response.data.colaborador_id : '';
					$scope.vacaciones.id 				= vacacion_id;

					if(window.location.href.match(/(accion_personal)/g)){

						//seleccionar el colaborador en el dropdown
						//que aparece en la barra superior
						$('select#colaborador_id').find('option[value="'+ response.data.colaborador_id +'"]').prop('selected', 'selected');

						//actualizar chosen barra accion personal
						accionPersonal.actualizar_chosen();
					}

					//actualizar chosen
					vacacionesService.actualizar_chosen();

			    }, function errorCallback(response) {
			        // called asynchronously if an error occurs
			        // or server returns response with an error status.
			    });

				//Borrar variable de localstorage
				localStorage.removeItem("vacacion_id");
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

		$scope.vacaciones = {
			erptkn: tkn,
			id: "",
			dias_disponibles: "",
			fecha_desde: "",
			fecha_hasta: "",
			cantidad_dias: "",
			estado_id: "",
			pago_inmediato_id: "",
			cuenta_pasivo_id: "",
			fecha_pago: "",
			observaciones: "",
			colaborador_id: typeof colaborador_id != 'undefined' ? colaborador_id : ''
		};

		//limpiar campos
		$(formulario).find('input[type="text"]').val('');

		//recarcular dias disponibles
		this.calcularDiasDisponiblesVacaciones();

		//reiniciar flow
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
    	vacacionesService.actualizar_chosen();
    };

    $scope.calcularDiasDisponiblesVacaciones = function() {

    	if(typeof colaborador_id == 'undefined' || !$.isNumeric(colaborador_id)){
			$('#crearVacacionesForm').find('input[id="campo[dias_disponibles]"]').val('');
			$('.vacaciones-loader').remove();
			return false;
		}

		vacacionesService.ajax(phost() + 'colaboradores/ajax-colaborador-info', {
			colaborador_id: colaborador_id
		}).then(function successCallback(response) {

			//Check Session
			if( $.isEmptyObject(response.data.session) == false){
				window.location = phost() + "login?expired";
			}

			//If json object is empty.
			if($.isEmptyObject(response.data) == true){
				$('#crearVacacionesForm').find('input[id="campo[dias_disponibles]"]').val('');
				$('.vacaciones-loader').remove();
				return false;
			}

			$('.vacaciones-loader').remove();
			$('#crearVacacionesForm').find('input[id="campo[dias_disponibles]"]').val(response.data.dias_disponibles_vacaciones);

	    }, function errorCallback(response) {
	        // called asynchronously if an error occurs
	        // or server returns response with an error status.
	    });
	};

	//Limpiar seleccion de campo: dropdown
    $scope.limpiar_seleccion_dropdown = function(campo){
		$(formulario).find(campo).attr("disabled", "disabled").empty().append('<option value="">Seleccione</option>').find('option:eq(0)').attr("selected", "selected");

		setTimeout(function(){
			vacacionesService.actualizar_chosen();
		}, 300);
	};

	//Si existe variable colaborador_id
	//ejecutar: calcularDiasDisponiblesVacaciones
	if(typeof colaborador_id != 'undefined'){
		$scope.calcularDiasDisponiblesVacaciones();
	};

	/**
     * Funcion Cancelar Formulario de Vacaciones
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
     * Funcion Guardar Formulario de Vacaciones
     */
	$scope.guardar = function(e, $flow)
    {
    	e.preventDefault();

    	if($(formulario).validate().form() == true)
		{
    		var colaboradorid = $(formulario).find('input[id="campo[colaborador_id]"]').val() != "" ? $(formulario).find('input[id="campo[colaborador_id]"]').val() : $scope.vacaciones.colaborador_id;
    		var url = phost() + "vacaciones/ajax-guardar-vacaciones";

    		//verificar que alla seleccionado
    		//un colaborador de la barra de filtro
    		if(colaboradorid == "" || colaboradorid == undefined){

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

    			vacacionesService.ajax(url, {
    				vacaciones_id: typeof vacacion_id != "undefined" ? vacacion_id : $scope.vacaciones.id,
            		colaborador_id: colaboradorid,
            		dias_disponibles: $(formulario).find('input[id="campo[dias_disponibles]"]').val(),
        			fecha_desde:  $(formulario).find('#fecha_desde').val(),
        			fecha_hasta:  $(formulario).find('#fecha_hasta').val(),
        			cantidad_dias: $(formulario).find('input[id="campo[cantidad_dias]"]').val(),
        			estado_id: $scope.vacaciones.estado_id,
        			pago_inmediato_id: $scope.vacaciones.pago_inmediato_id,
        			cuenta_pasivo_id: $scope.vacaciones.cuenta_pasivo_id,
        			fecha_pago: $(formulario).find('input[id="campo[fecha_pago]"]').val(),
        			observaciones:  $scope.vacaciones.observaciones
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
            		vacaciones_id: typeof vacacion_id != "undefined" ? vacacion_id : $scope.vacaciones.id,
            		colaborador_id: colaboradorid,
            		dias_disponibles: $(formulario).find('input[id="campo[dias_disponibles]"]').val(),
            		fecha_desde:  $(formulario).find('#fecha_desde').val(),
        			fecha_hasta:  $(formulario).find('#fecha_hasta').val(),
        			cantidad_dias: $(formulario).find('input[id="campo[cantidad_dias]"]').val(),
        			estado_id: $scope.vacaciones.estado_id,
        			pago_inmediato_id: $scope.vacaciones.pago_inmediato_id,
        			cuenta_pasivo_id: $scope.vacaciones.cuenta_pasivo_id,
        			fecha_pago: $(formulario).find('input[id="campo[fecha_pago]"]').val(),
        			observaciones:  $scope.vacaciones.observaciones
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
    					toastr.success(response.mensaje);

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
