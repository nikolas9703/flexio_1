/**
 * Controlador Tabs de Cargos
 */
bluapp.controller("configCargosCtrl", function($scope, $http, $rootScope){
	var url = window.phost() + "menu/navbar";

	$scope.cargo = {
		erptkn: tkn,
		id: "",
		departamento_id: "",
		nombre: "",
		descripcion: "",
		tipo_rata: "",
		rata: "",
		guardarcargoBtn: "Guardar"
	};

	//Popular dropdown de departamentos
	$rootScope.departamentosList1 = $.parseJSON(lista_departamentos);

	//Popular dropdown tipo de ratas
	$scope.tipo_ratas = $.parseJSON(tipo_ratas);

	//Copiar los valores vacios del formulario
	$scope.emptyfields = angular.copy($scope.cargo);

	//Event: Guardar Departamento
    $scope.guardar = function(e){
		e.preventDefault();

		//Mostrar en el Boton Progreso
		$('#guardarCargoBtn').addClass('disabled').empty().append('<i class="fa fa-circle-o-notch fa-spin"></i> Guardando...');

		if($('#crearCargoForm').validate().form() == true)
		{
			var url = window.phost() + "configuracion_rrhh/ajax-guardar-cargo";
			$http({
				 method: 'POST',
				 url: url,
				 data: $.param($scope.cargo),
				 cache: false,
				 xsrfCookieName: 'erptknckie_secure',
				 headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
			}).then(function (results) {

				if(results.data.id == false && results.data.id == undefined){
					toastr.error(results.data.mensaje);
				}else{
					toastr.success(results.data.mensaje);
				}

				//Recargar Lista de Cargos
				tablaCargos.recargar();

				//Habilitar boton
			    $('#guardarCargoBtn').removeClass('disabled').empty().append('Guardar');
		    });

			//Hacer un reset de los valores del formulario
			$scope.cargo = angular.copy($scope.emptyfields);
		    $scope.crearCargoForm.$setPristine();
		}
		else
		{
			//Habilitar boton
		    $('#guardarCargoBtn').removeClass('disabled').empty().append('Guardar');
		}
    };

    //Event: Cancelar Formulario
    $scope.cancelar = function(e){
		e.preventDefault();
		//Hacer un reset de los valores del formulario
		$scope.cargo = angular.copy($scope.emptyfields);
	    $scope.crearCargoForm.$setPristine();
    };
});

// Liquidaciones

bluapp.controller("configLiquidacionesCtrl", function($scope, $http, $rootScope){
	var url = window.phost() + "menu/navbar";
	
	$scope.liquidaciones = {
		erptkn: tkn,
		nombre:'',
		estado:'',
		id:'',
		guardarliquidacionesBtn: "Guardar"
	};
	//Popular dropdown de estado
	$rootScope.estado_liquidaciones = estado_liquidaciones;

	//Event: Guardar Departamento
    $scope.guardarLiquidaciones = function(e){
		e.preventDefault();

		//Mostrar en el Boton Progreso
		$('#guardarLiquidacionesBtn').addClass('disabled').empty().append('<i class="fa fa-circle-o-notch fa-spin"></i> Guardando...');

	 	if($('#liquidacionesForm').validate().form() == true)
		{
			var url = window.phost() + "configuracion_rrhh/ajax-guardar-liquidaciones";
			$http({
				 method: 'POST',
				 url: url,
				 data: $.param($scope.liquidaciones),
				 cache: false,
				 xsrfCookieName: 'erptknckie_secure',
				 headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
			}).then(function (results) {

				if(results.data.id == false && results.data.id == undefined){
					toastr.error(results.data.mensaje);
				}else{
					toastr.success(results.data.mensaje);
				}

				//Recargar Lista de Cargos
				tablaLiquidaciones.recargar();

				//Habilitar boton
			    $('#guardarLiquidacionesBtn').removeClass('disabled').empty().append('Guardar');
		    });

			//Hacer un reset de los valores del formulario
			//$scope.liquidaciones = angular.copy($scope.emptyfields);
		    //$scope.liquidacionesForm.$setPristine();
		}
		else
		{
			//Habilitar boton
		    $('#guardarLiquidacionesBtn').removeClass('disabled').empty().append('Guardar');
		}
    };

    //Event: Cancelar Formulario
    $scope.cancelarLiquidaciones = function(e){
		e.preventDefault();
		//Hacer un reset de los valores del formulario
		$scope.liquidaciones = angular.copy($scope.emptyfields);
	  //  $scope.liquidacionesForm.$setPristine();
    };
});

/**
 * Controlador Tabs de Departamentos
 */
bluapp.controller("configDepartamentosCtrl", function($scope, $http, $rootScope){
	var url = window.phost() + "menu/navbar";

	//Init input departamento
    $scope.nombre_departamento = '';

    //Array de departamentos seleccionados
    $scope.selected = {};

    //Array de departamentos seleccionados
    $scope.centro_contables = {};

    //Popular dropdown de departamentos
    $scope.departamentosList2 = $.parseJSON(departamentos);

    //Popular dropdown de Centro Contable
    $scope.centroContableList = $.parseJSON(centros);

    //Inicializar plugin chosen, para centro contable
    setTimeout(function(){
    	$('select.chosen-centro').chosen({
            width: '100%',
        });
    }, 800);

	//Event: Guardar Departamento
    $scope.guardar = function(e){
		e.preventDefault();

		//Mostrar en el Boton Progreso
		$('#guardarDepartamentoBtn').addClass('disabled').empty().append('<i class="fa fa-circle-o-notch fa-spin"></i> Guardando...');

		if($('#departamentoForm').validate().form() == true)
		{
			var url = window.phost() + "configuracion_rrhh/ajax-guardar-departamento";
			$http(
							{
									method: 'POST',
									url: url,
									data : $.param
									(
										{
											erptkn: tkn,
											guardar: true,
											nombre: $scope.nombre_departamento
								 		}
									),
							 cache: false,
							 xsrfCookieName: 'erptknckie_secure',
							 headers : {
								  					'Content-Type': 'application/x-www-form-urlencoded'
													}
						}).then(function (results)
					{

					if(results.data.id == false || results.data.id == undefined)
					{
						toastr.error(results.data.mensaje)
					}else
					{
						toastr.success(results.data.mensaje)
					}

				//Agregar el nuevo departamento al dropdown de departamentos
				$scope.departamentosList2.push({"nombre": $scope.nombre_departamento, id: results.data.id, estado: results.data.estado});

				//Actualizar dropdown de departamentos
				//de formulario de cargos
				$rootScope.departamentosList1.push({nombre: $scope.nombre_departamento, id: results.data.id, estado: 'Activo'});

				//Limpiar input departamento
				$scope.nombre_departamento = '';

				//Habilitar boton
			    $('#guardarDepartamentoBtn').removeClass('disabled').empty().append('Guardar');
		    });
		}
		else
		{
			 //Habilitar boton
		    $('#guardarDepartamentoBtn').removeClass('disabled').empty().append('Guardar');
		}
	};

	//Event: Activar/Desactivar Departamento
    $scope.toggleEstado = function(e){
    	e.preventDefault();

    	var opcion = $('#opciones option:selected').val();
    	//var $('input[name="departamento[]"]')

    	if(opcion=='' || $('input[name*="departamento[]"]:checked').val() == undefined){
    		return false;
    	}

    	var url = window.phost() + "configuracion_rrhh/ajax-cambiar-estado-departamento";
		$http({
			 method: 'POST',
			 url: url,
			 data : $.param({
				 erptkn: tkn,
				 estado: opcion,
				 id: $scope.selected
			 }),
			 cache: false,
			 xsrfCookieName: 'erptknckie_secure',
			 headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function (results) {

			//Actualizar Lisbox de departamentos
			$scope.departamentosList2 = results.data;

			//Actualizar select de departamentos
			//de formulario de cargos
			$rootScope.departamentosList1 = results.data;

			//limpiar seleccionados
			$scope.selected = {};
	    });
	};

	//Event: Activar/Desactivar Departamento
    $scope.relacionarCentro = function(e){
    	e.preventDefault();

    	var centro_contable_id = $('#centro_contable_id').filter(function(){
    		return $(this).find('option:selected').val();
    	}).val();

    	if(centro_contable_id=='' || $('input[name*="departamento[]"]:checked').val() == undefined){
    		return false;
    	}

    	var url = window.phost() + "configuracion_rrhh/ajax-relacionar-departamento-centros";
		$http({
			 method: 'POST',
			 url: url,
			 data : $.param({
				 erptkn: tkn,
				 centro_contable_id: centro_contable_id,
				 departamento_id: $scope.selected
			 }),
			 cache: false,
			 headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function (results) {

			if(results.data.estado == 500){
				toastr.error(results.data.mensaje);

				//limpiar seleccionados
				$scope.selected = {};
				$scope.centro_contables = {};

				//refresh chosen
				setTimeout(function(){
					$('select.chosen-centro').chosen({
				        width: '100%',
				    }).trigger('chosen:updated');
				}, 500);

				return false;
			}else{
				toastr.success(results.data.mensaje);
			}

			//Actualizar Lisbox de departamentos
			$scope.departamentosList2 = results.data.listaCompletaDepartamentos;

			//Actualizar Dropdown
			$scope.departamentosList1 = results.data.listaDepartamentos;

			//actualizar chosen
			setTimeout(function(){
				$(".chosen-field").chosen({
					width: '100%'
				});
			}, 1500);

			//limpiar seleccionados
			$scope.selected = {};
			$scope.centro_contables = {};

			//refresh chosen
			setTimeout(function(){
				$('select.chosen-centro').chosen({
			        width: '100%',
			    }).trigger('chosen:updated');
			}, 500);
	    });
	};

});

/**
 * Controlador Tabs de Departamentos
 */
bluapp.controller("configTiempoContratacionCtrl", function($scope, $http, $rootScope){

	//Init input tiempo
    $scope.tiempo_contratacion = '';

	//Popular tabla de Tiempos
    $scope.lista_tiempo_contratacion = $.parseJSON(lista_tiempo_contratacion);

	//Event: Guardar Tiempo de Contratacion
    $scope.guardarTiempoContratacion = function(e){
		e.preventDefault();

		//Mostrar en el Boton Progreso
		$('#guardarTiempoContratacionBtn').addClass('disabled').empty().append('<i class="fa fa-circle-o-notch fa-spin"></i> Guardando...');

		if($('#tiempoContratacionForm').validate().form() == true)
		{
			var url = window.phost() + "configuracion_rrhh/ajax-guardar-tiempo-contratacion";
			$http({
				 method: 'POST',
				 url: url,
				 data : $.param({
					 erptkn: tkn,
					 guardar: true,
					 tiempo: $scope.tiempo_contratacion
				 }),
				 cache: false,
				 headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
			}).then(function (results) {

				if(results.data.estado == 500){
					toastr.error(results.data.mensaje);

					//limpiar seleccionados
					$scope.selected = {};
					$scope.centro_contables = {};
					return false;

				}else{
					toastr.success(results.data.mensaje);
				}

				//Agregar el nuevo departamento al dropdown de departamentos
				$scope.lista_tiempo_contratacion.push({"tiempo": $scope.tiempo_contratacion, id: results.data.id});


				//Limpiar input tiempo contratacion
				$scope.tiempo_contratacion = '';

				//Habilitar boton
			    $('#guardarTiempoContratacionBtn').removeClass('disabled').empty().append('Guardar');
		    });
		}
		else
		{
			 //Habilitar boton
		    $('#guardarTiempoContratacionBtn').removeClass('disabled').empty().append('Guardar');
		}
	};

	//Event: Activar/Desactivar Departamento
    $scope.eliminarTiempoContratacion = function(e){
    	e.preventDefault();

    	var boton = $(e.currentTarget);
    	var tiempo_contratacion_id = boton.attr('data-tiempo-id');
    	var tiempo_contratacion_valor = boton.attr('data-tiempo-valor');
    	var url = window.phost() + "colaboradores/ajax-eliminar-tiempo-contratacion";

    	//Modal
    	var opcionesModal = $('#opcionesModal');
		opcionesModal.find('.modal-title').empty().append('Confirme');
	    opcionesModal.find('.modal-body').empty().append('&#191;Esta seguro que desea eliminar el tiempo de contratacion de ('+ tiempo_contratacion_valor +')?');
	    opcionesModal.find('.modal-footer').empty()
		.append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
		.append(
			$('<button class="btn btn-w-m btn-danger" type="button" />').append('Eliminar').click(function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();

				$http({
					 method: 'POST',
					 url: url,
					 data : $.param({
						 erptkn: tkn,
						 eliminar: true,
						 tiempo_contratacion_id: tiempo_contratacion_id
					 }),
					 cache: false,
					 headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
				}).then(function (results) {

					if(results.data.estado == 500){
						toastr.error(results.data.mensaje);

						return false;
					}else{
						toastr.success(results.data.mensaje);
					}

					//Popular tabla de Tiempos
				    $scope.lista_tiempo_contratacion = results.data.tiempos;

					//Remover el tiempo de contratacion de la tabla
					boton.closest('tr').remove();

					//Ocultar Modal
					opcionesModal.modal('hide');
			    });

			})
		);
	    opcionesModal.modal('show');
	};
});


$(function(){

	$('#dtable, #tcntable').DataTable({
	    paging: true,
	    searching: false,
	    lengthMenu: [5, 10, 20, 50],
	    autoWidth: true,
	    scrollCollapse: true,
	    pageLength: 5,
	    ordering: false,
	    order: [[ 1, 'asc']],
	    language: {
        	"sProcessing":     "Procesando...",
        	"sLengthMenu":     "Mostrar _MENU_ registros",
        	"sZeroRecords":    "No se encontraron resultados",
        	"sEmptyTable":     "Ning&uacute;n dato disponible en esta tabla",
        	"sInfo":           "Mostrando _START_ de _END_ de _TOTAL_ registros",
        	"sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
        	"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
        	"sInfoPostFix":    "",
        	"sSearch":         "Buscar:",
        	"sUrl":            "",
        	"sInfoThousands":  ",",
        	"sLoadingRecords": "Cargando...",
        	"oPaginate": {
        		"sFirst":    "Primero",
        		"sLast":     "&Uacute;ltimo",
        		"sNext":     "Siguiente",
        		"sPrevious": "Anterior"
        	},
        	"oAria": {
        		"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
        		"sSortDescending": ": Activar para ordenar la columna de manera descendente"
        	}
       }
	});

	/**
	 * Inicializar opciones del Modal
	 */
	$('#opcionesModal').modal({
		backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
		show: false
	});

	//Primero verificar si existe la funcion, para evitar errores de js
	if ($().chosen) {
		if($(".chosen-field").attr("class") != undefined){
			setTimeout(function(){
				$(".chosen-field").chosen({
					width: '100%'
				});
			}, 1500);
		}
	}

	/**
	 * Validar formulario crear de cargos
	 */
	$.validator.setDefaults({
    	errorPlacement: function(error, element){
	    	/*if($(element).attr('id') == "rata"){
	    		element.parent().after(error);
	    	}else{
				$(element).after(error);
	    	}*/
    		return true;
    	}
	});
	$('#crearCargoForm').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: ''
	});
	$('#departamentoForm').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: ''
	});
	$('#liquidacionesForm').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: ''
	});

	//Verificar permiso para crear cargo
	if(permiso_crear_cargo == 'false'){
		$('#crearCargoForm').find('input, select, button').attr('disabled', 'disabled').addClass('disabled');
	}

	//Verificar permiso para crear cargo
	if(permiso_crear_departamento == 'false'){
		$('#departamentoForm').find('input[name="nombre"], #guardarDepartamentoBtn').attr('disabled', 'disabled').addClass('disabled');
	}

      $("#rata").inputmask('currency',{
      prefix: "",
      autoUnmask : true,
      removeMaskOnSubmit: true
    });
});
