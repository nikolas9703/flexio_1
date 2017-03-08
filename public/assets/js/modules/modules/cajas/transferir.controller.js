/**
 * Servicio Cajas
 */
bluapp.service('transferirService', ['$http', '$document', '$compile', '$rootScope', function($http, $document, $compile, $rootScope) {

	var scope = this;
	var formulario = '#transferirForm';

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
 //('#estado').prop('disabled', true);
 //	$(formulario).find('#estado option[value="aprobado"]').prop('selected', true);//;
	var formulario_detalle = window.location.pathname.match(/transferir_detalle/g) ? true : false;

		setTimeout(function(){


				if(formulario_detalle == false){
					$(formulario).find('#estado').val( "por_aprobar").prop('disabled', true);
				}


			//Plugin Datepicker
			$(formulario).find('#fecha').daterangepicker({
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
				$(formulario).find('#fecha').val(picker.startDate.format('DD/MM/YYYY'));
			});

			scope.pluginInputMask();
			//Ya no debe salir num de cheque,esos numeros se generan en el mismpo modulo de cheques y no desde aqui
		/*	$(formulario).find('#pagosTable').on('change', 'select[id*="tipo_pago_id"]', function(e){
				 e.preventDefault();
			     e.returnValue=false;
			     e.stopPropagation();

			     var tipo_pago = $(this).find('option:selected').text().toLowerCase();

			     //verificar si tipo pago es cheque
			     if(tipo_pago.match(/cheque/g)){
			    	 //si es cheque mostrar campos
			    	 //adicionales de cheque
			    	 $(this).closest('tr').find('input:hidden').attr('type', 'text').removeAttr('disabled').removeClass('hide');

			     }else{
			    	 //ocultar y limpiar valores de campos
			    	 $(this).closest('tr').find('input[id*="no_cheque"]').val('').attr('type', 'hidden').addClass('hide').attr('disabled', 'disabled');
			    	 $(this).closest('tr').find('input[id*="banco"]').val('').attr('type', 'hidden').addClass('hide').attr('disabled', 'disabled');
			     }
			});*/

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

			if(formulario_detalle == true && estado == 'aprobado'){
					$(formulario).find('input, select, button').prop('disabled', true);
			}
		}, 1000);
    };

    this.pluginInputMask = function(){
    	//Verificar si existe la funcion, para evitar errores de js
		if (typeof $.fn.inputmask !== 'undefined' && $.isFunction($.fn.inputmask)) {
			if($(':input[data-inputmask]').attr('class') != undefined){
				setTimeout(function(){
					$(':input[data-inputmask]').inputmask();
				}, 500);
			}
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
 * Controlador Formulario de Cajas
 */
bluapp.controller("TransferirACajaController", function($rootScope, $scope, $document, $http, $rootScope, $compile, transferirService){

	var formulario = '#transferirForm';

	$scope.guardarBtn = 'Guardar';
	$scope.disabledBtn = '';

	$scope.cuentas_bancosList = $.parseJSON(cuentas_bancosList);
	$scope.estados = $.parseJSON(estados);
  var cuenta_id = typeof window.cuenta_id !== 'undefined' ? window.cuenta_id: '';
  var estado = typeof window.estado !== 'undefined' ? window.estado: '';
	$(formulario).find('#estado').val('por_aprobar');

	$scope.transferir = {
		erptkn: tkn,
		id: "",
		cuenta_id: cuenta_id.toString(),
		estado: estado.toString(),
		monto: ""
		//montopagar: []
	};

	//Inicializar Tabla Dinamica Plugin
	$(formulario).find('.agregarPagosBtn').tablaDinamica({

		afterAddRow: function(row){

			//Selecionar option index 0, para campos select
			$(row).find('select').find('option:eq(0)').prop('selected', 'selected').trigger('change');

			//Compilar row en angularjs
            $rootScope.safeApply(function(){
            	 $compile(row)($scope);
			});

            //inicialiar plugin
            setTimeout(function(){
            	$(row).find(':input[data-inputmask]').inputmask();
			}, 500);
		},
		afterDeleteRow: function(){
			$scope.CalcularMontoTotal();
		},
		onDeleteRow: function(){},
		onClickDeleteBtn: function(tabla_id, row){}
	});

	$scope.CalcularMontoTotal = function(e){
		if(typeof e != 'undefined'){
			e.preventDefault();
		}


		//sumar montos
		var total = 0;
		$.each($('#pagosTable').find('input[id*="monto_a_pagar"]'), function(indice, campo){
			var monto = isNumber(this.value) == true ? this.value : 0;
			total = parseFloat(total) + parseFloat(monto);
		});

		//actualizar total
		$rootScope.safeApply(function(){
			$scope.total = roundNumber(total,2);
		});
	};

	//Inicializar campos, plugins y validacion
	transferirService.init();

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


    	 var monto = $(formulario).find('input[id="monto"]').val();


 			 var formulario_detalle = window.location.pathname.match(/transferir_detalle/g) ? true : false;
 		 var validado = false;
			if(formulario_detalle == true) //edicion
			{
				if($(formulario).validate().form()){
					var validado = true;
				}else{
 					toastr.warning("Revisa los montos");

					return false;

				}

			}else{ //Creacion
 				if($(formulario).validate().form() && maximo_transferir>0 && maximo_transferir >= monto && formulario_detalle == false){
					validado = true;
				}else{
					toastr.warning("Revisa los montos");

					return false;

				}
			}
			if(validado == true){
						$(formulario).find('input, select').attr( "disabled", false );
						var caja_id = $(formulario).find('input[id="campo[id]"]').val();
						var url = phost() + "cajas/ajax-guardar-transferencia";

						//Estado de guardando en boton
						setTimeout(function(){
							$rootScope.safeApply(function(){
								$scope.guardarBtn = '<i class="fa fa-circle-o-notch fa-spin"></i> Guardando...';
									$scope.disabledBtn = 'disabled';
							});
						}, 100);

					transferirService.ajax(url, $(formulario).serializeObject()).then(function successCallback(response) {
								if(response.data.tipo == 'success'){
										window.location = phost() + 'cajas/crear/'+response.data.uuid_caja;
								}else{
										toastr.warning(response.data.mensaje);
								}
					}, function errorCallback(response) {});
			}






		//}
    };
});
