/**
 * Servicio Ausencias
 */
bluapp.service('ausenciaService', ['$http', '$document', '$rootScope', function($http, $document, $rootScope) {
	
	var scope = this;
	var requisito_id = '';
	var formulario = '#crearAusenciaForm';

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
 * Controlador Formulario de Ausencias
 */
bluapp.controller("AusenciasController", function($rootScope, $scope, $document, $http, $rootScope, $compile, ausenciaService){
	
	var url = window.phost() + "ausencias/ajax-seleccionar-evaluacion";
	var formulario = '#crearAusenciaForm';

	$scope.fileBtn = 'Seleccione';
	$scope.fileClassBtn = 'btn-default';
	$scope.guardarBtn = 'Guardar';
	$scope.disabledBtn = '';
	
	//Inicializar variables scope
	$scope.ausencia = {
		erptkn: tkn,
		id: "",
		tipo_ausencia_id: "",
		justificacion_id: "",
		fecha_desde: "",
		fecha_hasta: "",
		cuenta_pasivo_id: "",
		observaciones: "",
		estado_id: "",
		colaborador_id: window.location.href.match(/(colaboradores)/g) != null ? (typeof colaborador_id != 'undefined' ? colaborador_id : '') : ""
	};
	
	//Inicializar campos, plugins y validacion
	ausenciaService.init();
	
	//Funcion popular formulario
	//con informacion de la ausencia seleccionada.
	$scope.popularFormulario = function()
	{
		//Before using local storage, check browser support for localStorage and sessionStorage
		if(typeof(Storage) !== "undefined") {
			//Verificar si existe variable local ausencia_id existe
			if(localStorage.getItem("ausencia_id")){
				
				var ausencia_id = localStorage.getItem("ausencia_id");
				
				//Buscar datos para popular campos
				ausenciaService.ajax(phost() + "ausencias/ajax-seleccionar-ausencia", {
					id: ausencia_id,
				}).then(function successCallback(response) {

					//Check Session
					if( $.isEmptyObject(response.data.session) == false){
						window.location = phost() + "login?expired";
					}
					
					$scope.ausencia.cuenta_pasivo_id 	= response.data.cuenta_pasivo_id.toString();
					$scope.ausencia.estado_id 			= response.data.estado_id.toString();
					$scope.ausencia.tipo_ausencia_id 	= response.data.tipo_ausencia_id.toString();
					$scope.ausencia.justificacion_id 	= response.data.justificacion_id.toString();
					$scope.ausencia.fecha_desde 		= response.data.fecha_desde;
					$scope.ausencia.fecha_hasta 		= response.data.fecha_hasta;
					$scope.ausencia.observaciones 		= response.data.observaciones;
					$scope.ausencia.colaborador_id 		= response.data.colaborador_id;
					$scope.ausencia.id 					= ausencia_id;

					if(window.location.href.match(/(accion_personal)/g)){
						
						//seleccionar el colaborador en el dropdown
						//que aparece en la barra superior
						$('select#colaborador_id').find('option[value="'+ response.data.colaborador_id +'"]').prop('selected', 'selected');
						
						//actualizar chosen barra accion personal
						accionPersonal.actualizar_chosen();
					}
					
					//actualizar chosen
					ausenciaService.actualizar_chosen();
					
			    }, function errorCallback(response) {
			        // called asynchronously if an error occurs
			        // or server returns response with an error status.
			    });

				//Borrar variable de localstorage
				localStorage.removeItem("ausencia_id");
			}
		}
	};
	
	//Ejecutar funcion
	$scope.popularFormulario();

    //evento: seleccion de archivo
	$scope.limpiarFormulario = function(e)
    {
		if(typeof e != 'undefined'){
			e.preventDefault();
		}
		
		//Inicializar variables scope
		$scope.ausencia = {
			erptkn: tkn,
			id: "",
			tipo_ausencia_id: "",
			justificacion_id: "",
			fecha_desde: "",
			fecha_hasta: "",
			cuenta_pasivo_id: "",
			observaciones: "",
			estado_id: "",
			colaborador_id: typeof colaborador_id != 'undefined' ? colaborador_id : ''
		};
		
		$(formulario).find('input[type="text"], input[type="checkbox"]').val('').removeAttr("checked");

		//campos select
		$scope.limpiar_seleccion_dropdown();
		
		//Botones
		$scope.fileBtn = 'Seleccione';
		$scope.fileClassBtn = 'btn-default';
		$scope.guardarBtn = 'Guardar';
		$scope.disabledBtn = '';

		//refresh chosen
    	ausenciaService.actualizar_chosen();
    };
    
    //Limpiar seleccion de campo: dropdown
    $scope.limpiar_seleccion_dropdown = function(campo){
		$(formulario).find(campo).attr("disabled", "disabled").empty().append('<option value="">Seleccione</option>').find('option:eq(0)').attr("selected", "selected");
		
		setTimeout(function(){
			ausenciaService.actualizar_chosen();
		}, 300);
	};
	
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
     * Funcion Guardar Formulario de Ausencia
     */
	$scope.guardar = function(e)
    {
    	e.preventDefault();
    	
    	if($(formulario).validate().form() == true)
		{
    		var colaboradorid = $(formulario).find('input[id="campo[colaborador_id]"]').val() != "" ? $(formulario).find('input[id="campo[colaborador_id]"]').val() : $scope.ausencia.colaborador_id;
    		var url = phost() + "ausencias/ajax-guardar-ausencia";
    		
    		//verificar que alla seleccionado
    		//un colaborador de la barra de filtro
    		if(colaboradorid == "" || colaboradorid == undefined){
    			
    			toastr.warning('Debe seleccionar un colaborador.');
    			return false;
    		}
    		
    		//Estado de guardando en boton
    		setTimeout(function () {
				$rootScope.safeApply(function () {
					$scope.guardarBtn = '<i class="fa fa-circle-o-notch fa-spin"></i> Guardando...';
		    		$scope.disabledBtn = 'disabled';
		          });
		     }, 100);

			ausenciaService.ajax(url, {
				ausencia_id: typeof ausencia_id != "undefined" ? ausencia_id : $scope.ausencia.id,
        		colaborador_id: colaboradorid,
        		tipo_ausencia_id: $scope.ausencia.tipo_ausencia_id,
    			justificacion_id:  $scope.ausencia.justificacion_id,
    			fecha_desde:  $(formulario).find('#fecha_desde').val(),
    			fecha_hasta:  $(formulario).find('#fecha_hasta').val(),
    			cuenta_pasivo_id:  $scope.ausencia.cuenta_pasivo_id,
    			estado_id:  $scope.ausencia.estado_id,
        		observaciones: $scope.ausencia.observaciones
			}).then(function successCallback(response) {

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

		}
    };
});