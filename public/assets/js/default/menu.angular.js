/**
 * @ngdoc module
 * @name menu.angular
 * @description
 *
 * Modulo principal Flexio
 *
 */
bluapp = angular.module('bluapp', ['infinite-scroll', 'ngCookies', 'relativeDate', 'ngSanitize', 'flow', 'ngAnimate']);
bluapp.factory('$localstorage', ['$window', function($window) {
  return {
    set: function(key, value) {
      $window.localStorage[key] = value;
    },
    get: function(key, defaultValue) {
      return $window.localStorage[key] || defaultValue;
    },
    remove: function(key) {
    	$window.localStorage.removeItem(key);
    },
    setObject: function(key, value) {
      $window.localStorage[key] = JSON.stringify(value);
    },
    getObject: function(key) {
      return JSON.parse($window.localStorage[key] || '{}');
    }
  };
}]);

/**
 * Controlador Menu Lateral
 */
bluapp.controller("sideBarMenuCtrl", function($scope, $cookies, $localstorage){

	$scope.menu_lateral_seleccionado = $localstorage.get('ml-selected') !== '' ? $localstorage.get('ml-selected') : $cookies.get('ml-selected');
	$scope.menu_lateral_navsecond = $localstorage.get('ml-parent-selected') !== '' ? $localstorage.get('ml-parent-selected') : $cookies.get('ml-parent-selected');

	/**
	 * Abrir/Cerrar sub menus del Menu Lateral
	 */
	$scope.collapse = function(e) {
		e.preventDefault();

		var href = $(e.target).attr('href') !== undefined ? $(e.target).attr('href') : '';

		//Verificar si el enlace seleccionado contiene un sub-menu
		//o es un enlace directo.
		if(href.match(/http:/g)){

			//----------------------------------------
			// Guardar seleccion de menu en cookies
			// y localStorage
			//----------------------------------------

			//Si la seleccion del menu superior marca undefined
			//Verificar la seleccion dentro del dropdown "tabdrop"
			if($('.navtop-menu').find('li.active').find('a').attr('data-grupo') === undefined){

				//Establecer $cookie seleccion de menu superior
				$cookies.put('ms-selected', $('.navtop-menu').find('li.tabdrop.active').find('ul').find('li.active').find('a').attr('data-grupo'));
				$localstorage.set('ms-selected', $('.navtop-menu').find('li.tabdrop.active').find('ul').find('li.active').find('a').attr('data-grupo'));
			}else{
				//Establecer $cookie seleccion de menu superior
				$cookies.put('ms-selected', $('.navtop-menu').find('li.active').find('a').attr('data-grupo'));
				$localstorage.set('ms-selected', $('.navtop-menu').find('li.active').find('a').attr('data-grupo'));
			}

			//Establecer $cookie seleccion de menu lateral
			$cookies.put('ml-selected', $.trim($(e.target).text()));
			$cookies.put('ml-parent-selected', $.trim($(e.target).closest('ul.nav-second-level').closest('li').find('a').first().text()));

			$localstorage.set('ml-selected', $.trim($(e.target).text()));
			$localstorage.set('ml-parent-selected', $.trim($(e.target).closest('ul.nav-second-level').closest('li').find('a').first().text()));

			//Redireccionar hachia el url
			setTimeout(function(){
				window.location.href = href;
			}, 100);
		}

		if( $(e.target).closest('li').has('ul').children('a') ){
			$(e.target).closest('li').toggleClass('active').children('ul').collapse('toggle');
		}
	};

});

/**
 * Controlador Menu Superior
 */
bluapp.controller("navBarMenuCtrl", function($scope, $http, $rootScope, $cookies, $localstorage){

	var url = window.phost() + "menu/navbar";

	/**
	 * Desplegar Menu Superior
	 */
	$http({
		 method: 'POST',
		 url: url,
		 data : $.param({
			 erptkn: tkn,
		 }),  // pass in data as strings
		 cache: false,
		 headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
	}).then(function (results) {
		if(results.data){
			 $scope.menus = results.data;

			 $(window).trigger('resize');
		}
    });

	$scope.menu_superior_seleccionado = $localstorage.get('ms-selected') !== '' ? $localstorage.get('ms-selected') : $cookies.get('ms-selected');

	console.log($scope.menu_superior_seleccionado);
	//Si existe el cookie del menu superior seleccionado
	//Desplegar menu lateral.

	if($scope.menu_superior_seleccionado !== ""){
		setTimeout(function(){
			$scope.menu_lateral();
		}, 100);
	}

	/**
	 * Buscar en DB el menu lateral, segun menu superior seleccionado.
	 */
	$scope.menu_lateral = function(e){

		var url = window.phost() + "menu/sidebar";
		$http({
			 method: 'POST',
			 url: url,
			 data : $.param({
				 erptkn: tkn,
				 grupo: e !== undefined  && $(e.target).attr('data-grupo') !== "" ? $(e.target).attr('data-grupo') : $scope.menu_superior_seleccionado

			 }),
			 cache: false,
			 xsrfCookieName: 'erptknckie_secure',
			 headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function (results) {
			$rootScope.sidemenu = results.data;

	    });
	};

	/**
	 * Cambiar opciones del menu lateral.
	 */
	$scope.togglemenu = function(e){
		e.preventDefault();

		//Primero desmarxar todos las lista
		$(e.target).closest('ul').find('li').removeClass('active');

		//Luego marcar la que acaba de ser seleccionada.
		$(e.target).closest('li').addClass('active');

		$scope.menu_lateral(e);
	};

	/**
	 * Logout Event
	 */
	$scope.logout = function(e){
		e.preventDefault();

		//Borrar las cookies de
		//seleccion del menu.
		$cookies.remove('ms-selected');
		$cookies.remove('ml-selected');
		$cookies.remove('ml-parent-selected');

		//Borrar localstorage de
		//seleccion del menu.
		$localstorage.remove('ms-selected');
		$localstorage.remove('ml-selected');
		$localstorage.remove('ml-parent-selected');

		//salir del sistema
		window.location = $(e.target).attr('href');
	};

	$('.navtop-menu').tabdrop();
});
