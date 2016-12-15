/**
 * Servicio Descuentos
 */
bluapp.service('descuentoService', ['$http', '$document', '$rootScope', function($http, $document, $rootScope) {

	var scope = this;
	var formulario = '#descuentoForm';

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
			$(formulario).find('.fecha-inicio').daterangepicker({
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

			//dropdown colaborador
			$('.filtro-formularios').on('change', 'select#colaborador_id', function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();

				var colaborador_id = this.options[this.selectedIndex].value;

				if(colaborador_id == ''){
					$(formulario).find('input[name="campo[colaborador_id]"]').val('');
				}else{
					//Popular campo colaborador_id hidden
					$(formulario).find('input[name="campo[colaborador_id]"]').val(colaborador_id);
				}

				$rootScope.consultarInformacionColaborador();
			});

			//-------------------------------------
			// Inicializar jQuery Input Mask plugin
			//-------------------------------------
			//Primero verificar si existe la funcion, para evitar errores de js
			if (typeof $.fn.inputmask !== 'undefined' && $.isFunction($.fn.inputmask)) {
				if($(formulario).find(':input[data-inputmask]').attr('class') != undefined){
					setTimeout(function(){
						$(formulario).find(':input[data-inputmask]').inputmask();
					}, 500);
				}
			}

			//Cambiar height de campo dropdown chosen
			$(formulario).find('select.chosen-select').chosen({
				width: '100%',
	        }).trigger('chosen:updated');

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
 * Controlador Formulario de Evaluacion
 */
bluapp.controller("formularioDescuentoController", function($rootScope, $scope, $document, $http, $rootScope, $compile, descuentoService){

	var formulario = '#descuentoForm';

	//HTML de notificacion
	var notificacion = ['<div class="alert alert-warning" ng-if="notificacion.campos.length || notificacion.limiteCapacidadAlcanzado.length">',
	    '<div ng-if="notificacion.campos.length">',
		    '<b>No es posible crear un nuevo descuento para este colaborador.</b><br> <b>Los siguientes datos deben ser completados en detalle del colaborador:</b>',
		    '<ul>',
		    	'<li ng-repeat="notificacion in notificacion.campos track by $index" ng-bind-html="notificacion"></li>',
		    '</ul>',
	    '</div>',
	    '<div ng-if="notificacion.limiteCapacidadAlcanzado.length" ng-bind-html="notificacion.limiteCapacidadAlcanzado">',
	    '</div>',
    '</div>'].join('\n');

	//-------------------------------
	// Insetar Notificacion en DOM
	//-------------------------------
	//Compilar en angularjs
	notificacion = $compile(notificacion)($scope);


	/*setTimeout(function () {
		$rootScope.safeApply(function () {
			//Agregar al modal
			modal.find('.modal-body').prepend(notificacion);
          });
     }, 50);*/

	//Compilar en Angular dropdown de Colaborador
	//$compile($('select#colaborador_id'))($scope);
	//$('select#colaborador_id').closest('div').empty().append(colaborador_dropdown);

	//-------------------------------
	//Inicializar scope de boton de subir archivo
	//y boton de guardar
	//-------------------------------
	$scope.fileBtn = 'Seleccione';
	$scope.fileClassBtn = 'btn-default';
	$scope.guardarBtn = 'Guardar';
	$scope.disabledBtn = '';

	//Scope de formulario
	$scope.descuento = {
		erptkn: tkn,
		id: "",
		tipo_descuento_id: "",
		acreedor_id: "",
		cuenta_pasivo_id: "",
		fecha_inicio: "",
		referencia: "",
		ciclo_id: "",
		monto_total: "",
		monto_por_ciclo: "",
		porcentaje_capacidad: "",
		estado_id: "",
		observaciones: "",
		descuento_diciembre: "",
		carta_descuento: "",
		colaborador_id: typeof colaborador_id != 'undefined' && isNumber(colaborador_id) ? colaborador_id : ""
	};

	var filesList = [], paramNames = [];

	$('.fileinput-button').fileupload({
        url: phost() + "descuentos/ajax-guardar-descuento",
        type: 'POST',
        dataType: 'json',
        autoUpload: false,
        singleFileUploads: false,
        add: function (e, data) {
        	for (var i = 0; i < data.files.length; i++) {
                filesList.push(data.files[i]);
                paramNames.push(e.delegatedEvent.target.name);
          }
					if(filesList.length > 0)
						$scope.seleccion();
        },
        done: function (e, data) {

        	if( $.isEmptyObject(data.result.session) == false){
				window.location = phost() + "login?expired";
			}

			//Verificar si el formulario esta siendo usado desde
			//Ver Detalle de Colaborador
			if(window.location.href.match(/(colaboradores)/g)){

				//mostrar mensaje
				toastr.success(response.mensaje);

				//recargar tabla
				tablaDescuentos.recargar();

				//Limpiar formulario
				$scope.limpiarFormulario();

			}else{
				if(data.result.guardado == true){
        			window.location = phost() + 'descuentos/listar';
        		}
			}
        }
    });

	//Notificacion scope
	$scope.notificacion = {
		campos: [],
		limiteCapacidadAlcanzado: ''
	};

	//Inicializar campos, plugins y validacion
	descuentoService.init();

	$scope.seleccion = function()
    {
			$('#adjunto_carta_descuento').closest('div').find('label').empty().append('1 Archivo Seleccionado');
    }

	//Funcion popular formulario
	//con informacion de la vacacion seleccionada.
	$scope.popularFormulario = function()
	{
		//Before using local storage, check browser support for localStorage and sessionStorage
		if(typeof(Storage) !== "undefined") {

			//Verificar si existe la variable
			//proveniente de Local Storage
			//o existe variable descuento_id
			var descuentoid = typeof descuento_id != 'undefined' ? descuento_id : (localStorage.getItem("descuento_id") != null ? localStorage.getItem("descuento_id") : "");

			if(descuentoid==''){
				return false;
			}

			//Buscar datos para popular campos
			descuentoService.ajax(phost() + "descuentos/ajax-seleccionar-descuento", {
				id: descuentoid,
			}).then(function successCallback(response) {

				//Check Session
				if( $.isEmptyObject(response.data.session) == false){
					window.location = phost() + "login?expired";
				}

				$scope.descuento.tipo_descuento_id 	= response.data.tipo_descuento_id.toString();
				$scope.descuento.estado_id 			= response.data.estado_id.toString();
				$scope.descuento.cuenta_pasivo_id 	= response.data.plan_contable_id.toString();
				$scope.descuento.acreedor_id 		= response.data.acreedor_id.toString();
				$scope.descuento.ciclo_id 			= response.data.ciclo_id.toString();
				$scope.descuento.estado_id 			= response.data.estado_id.toString();
				$scope.descuento.fecha_inicio 		= response.data.fecha_inicio;
				$scope.descuento.monto_total  		= response.data.monto_adeudado ;
				$scope.descuento.monto_por_ciclo	= response.data.monto_ciclo;
				$scope.descuento.porcentaje_capacidad = response.data.porcentaje_capacidad ;
				$scope.descuento.observaciones 		= response.data.detalle;
				$scope.descuento.referencia 		= response.data.no_referencia;
				$scope.descuento.descuento_diciembre = response.data.descuento_diciembre;
				$scope.descuento.carta_descuento 	= response.data.carta_descuento;
				$scope.descuento.colaborador_id 	= response.data.colaborador_id;
				$scope.descuento.id 				= descuento_id;

				//actualizar chosen
				descuentoService.actualizar_chosen();

		    }, function errorCallback(response) {
		        // called asynchronously if an error occurs
		        // or server returns response with an error status.
		    });

			//Borrar variable de localstorage
			localStorage.removeItem("descuento_id");
		}
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

	$rootScope.consultarInformacionColaborador = function() {

		var colaboradorid = $(formulario).find('input[id="campo[colaborador_id]"]').val() != "" ? $(formulario).find('input[id="campo[colaborador_id]"]').val() : $scope.descuento.colaborador_id;

		if(colaboradorid == 'undefined' || colaboradorid == ''){
			$scope.limpiarFormulario();
			$scope.descuento.ciclo_id = "";
			descuentoService.actualizar_chosen();
			return false;
		}

		//consultar capacidad de descuento
		$scope.popular_capacidad_descuento();

		descuentoService.ajax(phost() + 'colaboradores/ajax-colaborador-info', {
			colaborador_id: colaboradorid
		}).then(function successCallback(response) {

			//Check Session
			if( $.isEmptyObject(response.data.session) == false){
				window.location = phost() + "login?expired";
			}

			//If json object is empty.
			if($.isEmptyObject(response.data) == true){
				$scope.descuento.ciclo_id = "";
				return false;
			}

			$scope.descuento.ciclo_id = response.data.ciclo_id.toString();
			//$(formulario).find('select#ciclo_id').find('option[value="'+ response.data.ciclo_id +'"]').prop('selected', 'selected');

			//refresh chosen
	    	descuentoService.actualizar_chosen();

	    }, function errorCallback(response) {
	        // called asynchronously if an error occurs
	        // or server returns response with an error status.
	    });
	};

    $scope.popular_capacidad_descuento = function()
	{
    	var colaboradorid = $(formulario).find('input[id="campo[colaborador_id]"]').val() != "" ? $(formulario).find('input[id="campo[colaborador_id]"]').val() : $scope.descuento.colaborador_id;
    	var url = window.phost() + "descuentos/ajax-calcular-capacidad-endeudamiento";

    	if(colaboradorid == 'undefined' || colaboradorid == ''){
    		return false;
    	}

    	descuentoService.ajax(url, {
    		colaborador_id: colaboradorid,
    	}).then(function successCallback(json) {

			 //Check Session
			if( $.isEmptyObject(json.data.session) == false){
				window.location = phost() + "login?expired";
			}

			//If json object is empty.
			if($.isEmptyObject(json.data['result']) == true){
				return false;
			}

			//Mostrar notificacion si el usuario tiene datos incompletos
			if(json.data['result']['completo'] == false){
				$.each(json.data['result']['campos'], function(i, campo){
					setTimeout(function () {
						$rootScope.safeApply(function () {
							$scope.notificacion.campos.push(campo);
						});
					}, 50);
				});
				return false;
			}

			//Establecer porcentaje de Capacidad
            setTimeout(function () {
            	$rootScope.safeApply(function () {
					$scope.descuento.porcentaje_capacidad = json.data['result']['capacidad'];
				});

            	//Verificar limite de Capacidad
                $scope.verificar_limite_capcidad();

			}, 50);

			//remover mensaje loading
			$('.items-loader').remove();

	    }, function errorCallback(response) {
	        // called asynchronously if an error occurs
	        // or server returns response with an error status.
	    });
	};

	//Verificar si limite de capacidad es igual o mayor a 50
	$scope.verificar_limite_capcidad = function()
    {
		if(parseInt($scope.descuento.porcentaje_capacidad) <= 50){
			$rootScope.safeApply(function () {
				$scope.notificacion.limiteCapacidadAlcanzado = '<b>No es posible crear un nuevo descuento para este colaborador. Ha alcanzado el &#37; de capacidad maximo de descuentos.<b>';
			});
		}
    };

    //Limpiar seleccion de campo: dropdown
    $scope.limpiar_seleccion_dropdown = function(campo){
		$(formulario).find(campo).attr("disabled", "disabled").empty().append('<option value="">Seleccione</option>').find('option:eq(0)').attr("selected", "selected");

		setTimeout(function(){
			descuentoService.actualizar_chosen();
		}, 300);
	};

	//evento: seleccion de archivo
	$scope.limpiarFormulario = function(e)
    {
		if(typeof e != 'undefined'){
			e.preventDefault();
		}

		//Scope de formulario
		$scope.descuento = {
			erptkn: tkn,
			id: "",
			tipo_descuento_id: "",
			acreedor_id: "",
			cuenta_pasivo_id: "",
			fecha_inicio: "",
			referencia: "",
			ciclo_id: "",
			monto_total: "",
			monto_por_ciclo: "",
			porcentaje_capacidad: "",
			estado_id: "",
			observaciones: "",
			descuento_diciembre: "",
			carta_descuento: "",
			colaborador_id: window.location.href.match(/(colaboradores)/g) != null ? (typeof colaborador_id != 'undefined' ? colaborador_id : '') : ""
		};

		$(formulario).find('input[type="text"], input[type="checkbox"]').val('').removeAttr("checked");
		$(formulario).find('textarea').empty().val('');

		//Botones
		$scope.fileBtn = 'Seleccione';
		$scope.fileClassBtn = 'btn-default';
		$scope.guardarBtn = 'Guardar';
		$scope.disabledBtn = '';

		if(window.location.href.match(/(colaboradores)/g) != null){
			//Popular campo "Cacpacidad de Descuento"
			$scope.popular_capacidad_descuento();

			//popular informacion colaborador
			$scope.consultarInformacionColaborador();
		}

		//campos select
		$scope.limpiar_seleccion_dropdown();

		//refresh chosen
    	descuentoService.actualizar_chosen();
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
			//tablaAccionPersonal.recargar();

			//Limpiar formulario
			$scope.limpiarFormulario();

		}else{
			window.location = phost() + 'descuentos/listar';
		}
    };

	$scope.guardar = function(e)
    {
    	e.preventDefault();

    	if($(formulario).validate().form() == true)
		{
    		var colaboradorid = $(formulario).find('input[id="campo[colaborador_id]"]').val() != "" ? $(formulario).find('input[id="campo[colaborador_id]"]').val() : $scope.descuento.colaborador_id;
    		var url = phost() + "descuentos/ajax-guardar-descuento";

    		//verificar que alla seleccionado
    		//un colaborador de la barra de filtro
    		if(colaboradorid == "" || colaboradorid == undefined){

    			toastr.warning('Debe seleccionar un colaborador.');
    			return false;
    		}

    		//Agregar datos extras del formulario
    		//al upload de documentos.
    		formData = {
            	erptkn: tkn,
            	descuento_id: typeof descuento_id != "undefined" ? descuento_id : $scope.descuento.id,
        		colaborador_id: colaboradorid,
        		cuenta_pasivo_id: $scope.descuento.cuenta_pasivo_id,
        		tipo_descuento_id: $scope.descuento.tipo_descuento_id,
        		acreedor_id: $scope.descuento.acreedor_id,
        		ciclo_id: $scope.descuento.ciclo_id,
        		fecha_inicio: $(formulario).find('input[id="campo[fecha_inicio]"]').val(),
        		monto_total: $scope.descuento.monto_total,
        		monto_ciclo: $scope.descuento.monto_por_ciclo,
        		detalle: $scope.descuento.observaciones,
        		referencia: $scope.descuento.referencia,
        		estado_id: $scope.descuento.estado_id,
        		porcentaje_capacidad: $scope.descuento.porcentaje_capacidad,
        		descuento_diciembre: $scope.descuento.descuento_diciembre,
        		carta_descuento: $scope.descuento.carta_descuento
            };

    		//Estado de guardando en boton
    		$scope.guardarBtn = '<i class="fa fa-circle-o-notch fa-spin"></i> Guardando...';
    		$scope.disabledBtn = 'disabled';

    		//Verificar si ha seleccionado
    		//o no algun archivo
    		if(filesList.length == 0){

    			descuentoService.ajax(url, formData).then(function successCallback(response) {

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
    					tablaDescuentos.recargar();

    					//Limpiar formulario
    					$scope.limpiarFormulario();

    				}else{
    					if(response.data.guardado == true){
                			window.location = phost() + 'descuentos/listar';
                		}
    				}

    		    }, function errorCallback(response) {
    		        // called asynchronously if an error occurs
    		        // or server returns response with an error status.
    		    });

    		}else{

    			//Submit datos
        		//$scope.uploadform();
        		$('.fileinput-button').fileupload('send', {
        			files:filesList,
        			paramName: paramNames,
        			formData: formData
        		});
    		}
		}
    };

    //Si existe variable colaborador_id
    //Popular informacion del colaborador
    if(typeof colaborador_id != 'undefined' && isNumber(colaborador_id)){

    	//popular informacion colaborador
    	$scope.consultarInformacionColaborador();

    	if(window.location.href.match(/(descuentos)/g)){
    		$('select#colaborador_id').attr("disabled", "disbaled").find('option[value="'+ colaborador_id +'"]').prop('selected', 'selected');
                if(estado_finalizado == 0){
                $('select#estado_id').find('option[value="7"]').css('display', 'none');
            }else{
                $('select#estado_id').find('option[value="7"]').css('display', '');
            }
            }
    }

    //Si existe variable descuento_id
    //Popular informacion del Descuento
    if(typeof descuento_id != 'undefined' && isNumber(descuento_id)){
    	//popular informacion Descuento
    	$scope.popularFormulario();
    }
});
