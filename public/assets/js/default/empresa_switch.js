function vhost()
{
    return window.phost();
}
bluapp.controller("navBarEmpresaCtrl", function($scope, $http, $rootScope){
	var url = window.phost() + "empresa/lista";

	$http({
		 method: 'POST',
		 url: url,
		 data : $.param({
			 erptkn: tkn,
		 }),  // pass in data as strings
		 cache: true,
		 xsrfCookieName: 'erptknckie_secure',
		 headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
	}).then(function (results) {
		if(results.data){
			 $scope.empresas = results.data;
		}
    });

	$scope.switch_empresa = function(e){
		e.preventDefault();

    var uuid_empresa = $(e.target).attr('data-item');
    var nombre = $(e.target).attr('data-nombre');
		var url = window.phost() + "empresa/cambio";
		$http({
			 method: 'POST',
			 url: url,
			 data : $.param({
				 erptkn: tkn,
				 uuid_empresa: uuid_empresa
			 }),
			 cache: false,
			 xsrfCookieName: 'erptknckie_secure',
			 headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function (results) {
      $scope.empresas.default.uuid_empresa = uuid_empresa;
      $scope.empresas.default.nombre = nombre;
			location.reload();
      		//window.location.href =  window.phost();
	    });

	};
});
