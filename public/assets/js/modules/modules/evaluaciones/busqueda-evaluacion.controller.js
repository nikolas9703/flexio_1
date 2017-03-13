/**
 * Controlador Busqueda de Evaluacion
 */
bluapp.controller("busquedaEvaluacionController", function($rootScope, $scope, $document, $http, $rootScope, $compile){
	
	//Popular dropdown tipo de eveluacion
	$scope.cat_tipo_evaluaciones = $.parseJSON(cat_tipo_evaluaciones);
	
	//Popular dropdown centro contable
	$scope.cat_centros = $.parseJSON(cat_centros);
	
	//Popular dropdown resultados
	$scope.cat_resultados = $.parseJSON(cat_resultados);
	
	//Popular dropdown creado por
	$scope.cat_usuarios = $.parseJSON(cat_usuarios);
});