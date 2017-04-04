/**
 * Servicio Evaluacion
 */
bluapp.service('licenciasService', ['$http', '$document', '$rootScope', function($http, $document, $rootScope) {
	
	var scope = this;
	var requisito_id = '';
	var formulario = '#licenciaForm';

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
bluapp.controller("LicenciasController", function($rootScope, $scope, $document, $http, $rootScope, $compile, licenciasService){
	
	var formulario = '#licenciaForm';

	$scope.fileBtn = 'Seleccione';
	$scope.fileClassBtn = 'btn-default';
	$scope.guardarBtn = 'Guardar';
	$scope.disabledBtn = '';
	
	//Inicializar variables scope
	$scope.licencia = {
		erptkn: tkn,
		id: "",
		tipo_licencia_id: "",
		fecha_desde: "",
		fecha_hasta: "",
		cuenta_pasivo_id : "",
		estado_id : "",
		licencia_pagada_id: "",
		carta_sindical: "",
		observaciones: "",
		colaborador_id: window.location.href.match(/(colaboradores)/g) != null ? (typeof colaborador_id != 'undefined' ? colaborador_id : '') : ""
	};
	
	//Inicializar campos, plugins y validacion
	licenciasService.init();
	
	//Funcion popular formulario
	//con informacion de la licencia seleccionada.
	$scope.popularFormulario = function()
	{
		//Before using local storage, check browser support for localStorage and sessionStorage
		if(typeof(Storage) !== "undefined") {
			//Verificar si existe la variable
			//proveniente de Local Storage
			if(localStorage.getItem("licencia_id")){
				
				var licencia_id = localStorage.getItem("licencia_id");
				
				//Buscar datos para popular campos
				licenciasService.ajax(phost() + "licencias/ajax-seleccionar-licencia", {
					id: licencia_id,
				}).then(function successCallback(response) {

					//Check Session
					if( $.isEmptyObject(response.data.session) == false){
						window.location = phost() + "login?expired";
					}
					
					$scope.licencia.cuenta_pasivo_id 	= response.data.cuenta_pasivo_id.toString();
					$scope.licencia.estado_id 			= response.data.estado_id.toString();
					$scope.licencia.tipo_licencia_id 	= response.data.tipo_licencia_id.toString();
					$scope.licencia.licencia_pagada_id 	= response.data.licencia_pagada_id.toString();
					$scope.licencia.fecha_desde 		= response.data.fecha_desde;
					$scope.licencia.fecha_hasta 		= response.data.fecha_hasta;
					$scope.licencia.observaciones 		= response.data.observaciones;
					$scope.licencia.carta_sindical 		= response.data.carta_sindical;
					$scope.licencia.colaborador_id 		= response.data.colaborador_id;
					$scope.licencia.id 					= licencia_id;
					
					if(window.location.href.match(/(accion_personal)/g)){
						
						//seleccionar el colaborador en el dropdown
						//que aparece en la barra superior
						$('select#colaborador_id').find('option[value="'+ response.data.colaborador_id +'"]').prop('selected', 'selected');
						
						//actualizar chosen barra accion personal
						accionPersonal.actualizar_chosen();
					}
					
					//actualizar chosen
					licenciasService.actualizar_chosen();
					
			    }, function errorCallback(response) {
			        // called asynchronously if an error occurs
			        // or server returns response with an error status.
			    });

				//Borrar variable de localstorage
				localStorage.removeItem("licencia_id");
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
		
		$scope.licencia = {
			erptkn: tkn,
			id: "",
			tipo_licencia_id: "",
			fecha_desde: "",
			fecha_hasta: "",
			cuenta_pasivo_id : "",
			estado_id : "",
			licencia_pagada_id: "",
			carta_sindical: "",
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
    	licenciasService.actualizar_chosen();
    };
    
    //Limpiar seleccion de campo: dropdown
    $scope.limpiar_seleccion_dropdown = function(campo){
		$(formulario).find(campo).attr("disabled", "disabled").empty().append('<option value="">Seleccione</option>').find('option:eq(0)').attr("selected", "selected");
		
		setTimeout(function(){
			licenciasService.actualizar_chosen();
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
    		var colaboradorid = $(formulario).find('input[id="campo[colaborador_id]"]').val() != "" ? $(formulario).find('input[id="campo[colaborador_id]"]').val() : $scope.licencia.colaborador_id;
    		var url = phost() + "licencias/ajax-guardar-licencia";
    		
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
    			
    			licenciasService.ajax(url, {
    				licencia_id: typeof licencia_id != "undefined" ? licencia_id : $scope.licencia.id,
            		colaborador_id: colaboradorid,
            		tipo_licencia_id: $scope.licencia.tipo_licencia_id,
        			fecha_desde:  $(formulario).find('#fecha_desde').val(),
        			fecha_hasta:  $(formulario).find('#fecha_hasta').val(),
        			cuenta_pasivo_id: $scope.licencia.cuenta_pasivo_id,
        			estado_id: $scope.licencia.estado_id,
        			licencia_pagada_id: $scope.licencia.licencia_pagada_id,
        			carta_sindical:  $scope.licencia.carta_sindical,
        			observaciones:  $scope.licencia.observaciones
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
            		licencia_id: typeof licencia_id != "undefined" ? licencia_id : $scope.licencia.id,
            		colaborador_id: colaboradorid,
            		tipo_licencia_id: $scope.licencia.tipo_licencia_id,
        			fecha_desde:  $(formulario).find('#fecha_desde').val(),
        			fecha_hasta:  $(formulario).find('#fecha_hasta').val(),
        			cuenta_pasivo_id: $scope.licencia.cuenta_pasivo_id,
        			estado_id: $scope.licencia.estado_id,
        			licencia_pagada_id: $scope.licencia.licencia_pagada_id,
        			carta_sindical:  $scope.licencia.carta_sindical,
        			observaciones:  $scope.licencia.observaciones
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