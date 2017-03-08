/**
 * Controlador Busqueda de Entrega de Inventario
 */
bluapp.controller("busquedaEntregaInventarioController", function($rootScope, $scope, $document, $http, $rootScope, $compile){
	
	//Popular dropdown Duracion
	$scope.cat_duracion = $.parseJSON(cat_duracion);
	
	//Popular Tipo de Reemplazo
	$scope.cat_tipo_reemplazo = $.parseJSON(cat_tipo_reemplazo);
	
	//Popular dropdown Entregado por
	$scope.cat_usuarios = $.parseJSON(cat_usuarios);
	
	
	//Inicializar plugin de Datepicker de jQuery UI
	//jQuery Daterange
	$("#fecha_entrega_desde").datepicker({
		defaultDate: "+1w",
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
			$("#fecha_entrega_hasta").datepicker( "option", "minDate", selectedDate );
		}
	});
	$("#fecha_entrega_hasta").datepicker({
		defaultDate: "+1w",
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		numberOfMonths: 1,
		onClose: function( selectedDate ) {
			$("#fecha_entrega_desde").datepicker( "option", "maxDate", selectedDate );
	    }
	});
});