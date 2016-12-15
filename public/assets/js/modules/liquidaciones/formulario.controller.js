/**
 * Servicio Liquidacion
 */
bluapp.service('liquidacionesService', ['$http', '$document', '$rootScope', function($http, $document, $rootScope) {
	
	var scope = this;
	var requisito_id = '';
	var formulario = '#liquidacionForm';

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
			$(formulario).find('.fecha-apartir').daterangepicker({
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
				$(formulario).find('.fecha-apartir').val(picker.startDate.format('DD/MM/YYYY'));
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
bluapp.controller("LiquidacionesController", function($rootScope, $scope, $document, $http, $rootScope, $compile, liquidacionesService){
	
	var formulario = '#liquidacionForm';

	$scope.fileBtn = 'Seleccione';
	$scope.fileClassBtn = 'btn-default';
	$scope.guardarBtn = 'Guardar';
	$scope.disabledBtn = '';
	
	//Inicializar variables scope
	$scope.liquidacion = {
		erptkn: tkn,
		id: "",
		tipo_liquidacion_id: "",
		fecha_apartir: "",
		firmado_por: "",
		cuenta_pasivo_id: "",
		motivo: "",
		estado_id: "",
		solicitud: "",
		colaborador_id: window.location.href.match(/(colaboradores)/g) != null ? (typeof colaborador_id != 'undefined' ? colaborador_id : '') : ""
	};
	
	//Inicializar campos, plugins y validacion
	liquidacionesService.init();
	
	//Funcion popular formulario
	//con informacion de la liquidacion seleccionada.
	$scope.popularFormulario = function()
	{
		//Before using local storage, check browser support for localStorage and sessionStorage
		if(typeof(Storage) !== "undefined") {
			//Verificar si existe la variable
			//proveniente de Local Storage
			if(localStorage.getItem("liquidacion_id")){
				
				var liquidacion_id = localStorage.getItem("liquidacion_id");
				
				//Buscar datos para popular campos
				liquidacionesService.ajax(phost() + "liquidaciones/ajax-seleccionar-liquidacion", {
					id: liquidacion_id,
				}).then(function successCallback(response) {

					//Check Session
					if( $.isEmptyObject(response.data.session) == false){
						window.location = phost() + "login?expired";
					}
					
					$scope.liquidacion.cuenta_pasivo_id 	= response.data.cuenta_pasivo_id.toString();
					$scope.liquidacion.estado_id 			= response.data.estado_id.toString();
					$scope.liquidacion.tipo_liquidacion_id 	= response.data.tipo_liquidacion_id.toString();
					$scope.liquidacion.fecha_apartir 		= response.data.fecha_apartir;
					$scope.liquidacion.firmado_por 			= response.data.firmado_por.toString();
					$scope.liquidacion.motivo 				= response.data.motivo;
					$scope.liquidacion.solicitud 			= response.data.solicitud;
					$scope.liquidacion.colaborador_id 		= response.data.colaborador_id;
					$scope.liquidacion.id 					= liquidacion_id;
					
					if(window.location.href.match(/(accion_personal)/g)){
						
						//seleccionar el colaborador en el dropdown
						//que aparece en la barra superior
						$('select#colaborador_id').find('option[value="'+ response.data.colaborador_id +'"]').prop('selected', 'selected');
						
						//actualizar chosen barra accion personal
						accionPersonal.actualizar_chosen();
					}
					
					//actualizar chosen
					liquidacionesService.actualizar_chosen();
					
			    }, function errorCallback(response) {
			        // called asynchronously if an error occurs
			        // or server returns response with an error status.
			    });

				//Borrar variable de localstorage
				localStorage.removeItem("liquidacion_id");
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
		
		$scope.liquidacion = {
			erptkn: tkn,
			id: "",
			tipo_liquidacion_id: "",
			fecha_apartir: "",
			firmado_por: "",
			cuenta_pasivo_id: "",
			motivo: "",
			estado_id: "",
			solicitud: "",
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
    	liquidacionesService.actualizar_chosen();
    };
  
    //Limpiar seleccion de campo: dropdown
    $scope.limpiar_seleccion_dropdown = function(campo){
		$(formulario).find(campo).attr("disabled", "disabled").empty().append('<option value="">Seleccione</option>').find('option:eq(0)').attr("selected", "selected");
		
		setTimeout(function(){
			liquidacionesService.actualizar_chosen();
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
     * Funcion Guardar Formulario de Vacaciones
     */
	$scope.guardar = function(e, $flow)
    {
    	e.preventDefault();
    	
    	if($(formulario).validate().form() == true)
		{
    		var colaboradorid = $(formulario).find('input[id="campo[colaborador_id]"]').val() != "" ? $(formulario).find('input[id="campo[colaborador_id]"]').val() : $scope.liquidacion.colaborador_id;
    		var url = phost() + "liquidaciones/ajax-guardar-liquidacion";
    		
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
    			
    			liquidacionesService.ajax(url, {
    				liquidacion_id: typeof liquidacion_id != "undefined" ? liquidacion_id : $scope.liquidacion.id,
            		colaborador_id: colaboradorid,
            		tipo_liquidacion_id: $scope.liquidacion.tipo_liquidacion_id,
            		fecha_apartir:  $(formulario).find('input[id="campo[fecha_apartir]"]').val(),
            		firmado_por: $scope.liquidacion.firmado_por,
            		cuenta_pasivo_id: $scope.liquidacion.cuenta_pasivo_id,
            		motivo:  $scope.liquidacion.motivo,
            		estado_id: $scope.liquidacion.estado_id,
            		solicitud: $scope.liquidacion.solicitud
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
            		liquidacion_id: typeof liquidacion_id != "undefined" ? liquidacion_id : $scope.liquidacion.id,
            		colaborador_id: colaboradorid,
            		tipo_liquidacion_id: $scope.liquidacion.tipo_liquidacion_id,
            		fecha_apartir:  $(formulario).find('input[id="campo[fecha_apartir]"]').val(),
            		firmado_por: $scope.liquidacion.firmado_por,
            		cuenta_pasivo_id: $scope.liquidacion.cuenta_pasivo_id,
            		motivo:  $scope.liquidacion.motivo,
            		estado_id: $scope.liquidacion.estado_id,
            		solicitud: $scope.liquidacion.solicitud
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