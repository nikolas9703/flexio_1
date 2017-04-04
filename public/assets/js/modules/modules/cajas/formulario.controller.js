/**
 * Servicio Cajas
 */
bluapp.service('cajaService', ['$http', '$document', '$rootScope', function($http, $document, $rootScope) {
	
	var scope = this;
	var requisito_id = '';
	var formulario = '#cajasForm';

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
			
			//Verificar si existe la funcion, para evitar errores de js
			if (typeof $.fn.inputmask !== 'undefined' && $.isFunction($.fn.inputmask)) {
				if($(':input[data-inputmask]').attr('class') != undefined){
					setTimeout(function(){
						$(':input[data-inputmask]').inputmask();
					}, 500);
				}
			}

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
 * Controlador Formulario de Cajas
 */
bluapp.controller("FormularioCajaController", function($rootScope, $scope, $document, $http, $rootScope, $compile, cajaService){
	
	var formulario = '#cajasForm';

	var notificacion = ['<div class="alert alert-warning" ng-if="configurado.length == 0">',
	    '<div>',
		    '<b>No es posible crear una nueva caja, debe configurar la cuenta de Caja. <a href="'+ phost() + 'configuracion_contabilidad' +'">Ir a configuracion</a>.</b>',
	    '</div>',
    '</div>'].join('\n');
	
	//-------------------------------
	// Insetar Notificacion en DOM
	//-------------------------------
	//Compilar en angularjs
	var notificacion = $compile(notificacion)($scope);
	
	$scope.guardarBtn = 'Guardar';
	$scope.disabledBtn = '';
	$scope.centroContableList = $.parseJSON(centroContableList);
	$scope.usuariosList = $.parseJSON(usuariosList);
	$scope.estadosList = $.parseJSON(estadosList);

	setTimeout(function () {
		$rootScope.safeApply(function () {
			//agregar notificacion 
			$('.wrapper-content').prepend(notificacion);
			
			//inicializar valor de scope
			$scope.configurado = $.parseJSON(configurado);
          });
     }, 100);
	
	//Inicializar variables scope
	$scope.caja = {
		erptkn: tkn,
		id: typeof caja_id != 'undefined' ? caja_id : '',
		nombre: typeof nombrev != 'undefined' ? nombrev : '',
		centro_contable_id: typeof centro_idv != 'undefined' ? centro_idv.toString() : '',
		responsable_id: typeof responsable_idv != 'undefined' ? responsable_idv.toString() : '',
		limite: typeof limitev != 'undefined' ? limitev.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") : '',
		estado_id: typeof estado_idv != 'undefined' ? estado_idv.toString() : '',
		saldo: typeof saldov != 'undefined' ? saldov.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") : '',
		maxportransferir: typeof maxportransferir != 'undefined' ? maxportransferir : '',
	};
	
	//Inicializar campos, plugins y validacion
	cajaService.init();
	
    //evento: seleccion de archivo
	$scope.limpiarFormulario = function(e)
    {
		if(typeof e != 'undefined'){
			e.preventDefault();
		}
		
		$scope.caja = {
			erptkn: tkn,
			id: "",
			nombre: "",
			centro_contable_id: "",
			responsable_id: "",
			limite: "",
			estado_id: ""
		};
		
		$(formulario).find('input[type="text"], input[type="checkbox"]').val('').removeAttr("checked");
    };
    
    //Limpiar seleccion de campo: dropdown
    $scope.limpiar_seleccion_dropdown = function(campo){
		$(formulario).find(campo).attr("disabled", "disabled").empty().append('<option value="">Seleccione</option>').find('option:eq(0)').attr("selected", "selected");
		
		setTimeout(function(){
			cajaService.actualizar_chosen();
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
     * Funcion Guardar
     */
	$scope.guardar = function(e)
    {
    	e.preventDefault();
    	
    	//Si el estado es inactivo
    	if($scope.caja.estado_id == 2){
    		
    		//Y aun cuenta con saldo
    		if($scope.caja.saldo > 0){
    			//Mensaje
				toastr.warning('No es posible inactivar la caja ya que actualmente cuenta con saldo.');
    			return false;
    		}
    	}
    	
    	//Verificar que el limite sea mayor/igual que el saldo
    	if($scope.caja.id != ''){
    		if(parseFloat($scope.caja.limite) < parseFloat($scope.caja.saldo)){
    			//Mensaje
				toastr.warning('El limite no puede ser menor al saldo actual.');
    			return false;
    		}
    	}

    	if($(formulario).validate().form() == true)
		{
    		var caja_id = $(formulario).find('input[id="campo[id]"]').val();
    		var url = phost() + "cajas/ajax-guardar-caja";
    		
    		//Estado de guardando en boton
    		setTimeout(function(){
				$rootScope.safeApply(function(){
					$scope.guardarBtn = '<i class="fa fa-circle-o-notch fa-spin"></i> Guardando...';
		    		$scope.disabledBtn = 'disabled';
				});
		     }, 100);

			cajaService.ajax(url, {
				id: $scope.caja.id,
				nombre: $scope.caja.nombre,
				centro_contable_id:  $scope.caja.centro_contable_id,
				responsable_id:  $scope.caja.responsable_id,
				limite:  $scope.caja.limite,
			}).then(function successCallback(response) {

				if(response.data.tipo == 'success'){
					window.location = phost() + 'cajas/listar';
				}else{
					//Mensaje
					toastr.warning(response.data.mensaje);
				}
				
		    }, function errorCallback(response) {
		        // called asynchronously if an error occurs
		        // or server returns response with an error status.
		    });

		}
    };
 
    $(".limite").inputmask('currency',{
      prefix: "",
      autoUnmask : true,
      removeMaskOnSubmit: true
    });
    
});