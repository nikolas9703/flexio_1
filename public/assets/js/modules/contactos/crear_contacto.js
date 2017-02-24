bluapp.controller("contactoFormularioController", function($scope, $http){
		var objFrom = {
	  contactoForm: $('#crearContacto'),
	  };

		var ruta = {
	    guardarContacto:  phost() + 'contactos/ajax-guardar-contacto',
			contactoInfo: phost() + 'contactos/ajax-contacto-info'
	  };
	$scope.opcionFormulario = {natural: false, pasaporte: false};
	$scope.tipos = '';
	$scope.naturalLetra = {
		valor: null
	};
	$scope.indentificacion = [
		{tipo: 'natural', nombre: 'Cédula'},
		{tipo: 'pasaporte', nombre: 'Pasaporte'}
	];
	if (tipo_id_cont === 'natural' ||  $scope.tipos  === 'natural') {
		$scope.tipos = $scope.indentificacion[0];
		$scope.opcionFormulario.natural = true;
		$scope.naturalLetra.valor = letra;
	} else if (tipo_id_cont === 'pasaporte'||  $scope.tipos  === 'pasaporte') {
		$scope.tipos = $scope.indentificacion[1];
		$scope.opcionFormulario.pasaporte = true;
	}else{
		$scope.tipos = '';
		$scope.opcionFormulario.natural = false;
		$scope.opcionFormulario.pasaporte = false;
	}
	$scope.verTipo = function (tipos) {
		if (!angular.isObject(tipos)) {
			$scope.opcionFormulario.natural = false;
			$scope.opcionFormulario.pasaporte = false;
			return false;
		}
		if (tipos.tipo === 'natural') {
			$scope.opcionFormulario.natural = true;
			$scope.opcionFormulario.pasaporte = false;
		} else if (tipos.tipo === 'pasaporte') {
			$scope.opcionFormulario.natural = false;
			$scope.opcionFormulario.pasaporte = true;
		}
	};
	$scope.letras = function (valor) {
		$scope.naturalLetra.valor = valor;
		if (valor === 'N' || valor === 'PAS' || valor === 'PE') {
			document.getElementById("natural[provincia]").value = "";
		}

	};
		$scope.contacto = {};

		$scope.cancelarBtn = function(e){
				e.preventDefault();
				clienteProvider.config(false);
				angular.element("#vistaFormularioContacto").addClass('hide');
				angular.element("#vistaCliente").removeClass('hide');
				$scope.contacto = {};
		};

		$scope.guardarBtn = function(contacto, tipos, letra){

				console.log('pase natural');
			    contacto.tipo = tipos;
		    	contacto.letra = letra;
			var formularioContacto = objFrom.contactoForm;
			if(formularioContacto.valid() === true){
				contacto.uuidcliente = cliente.uuid_cliente;
				$scope.contacto = contacto;
				var datos = $.extend({erptkn: tkn},$scope.contacto);
				$http({
							url: ruta.guardarContacto,
							method: 'POST',
							data : $.param(datos),
							cache: false,
							xsrfCookieName: 'erptknckie_secure',
							headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
					 }).then(function (response) {
						 if(response){
							 var respuesta = response.data;
							 $("#mensaje_info").empty().html('<div id="success-alert" class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+respuesta.contenido+'</div>');
							 $scope.contacto = {};
							 $("#contactosGrid").trigger("reloadGrid");
							 clienteProvider.config(false);
			 				 angular.element("#vistaFormularioContacto").addClass('hide');
			 				 angular.element("#vistaCliente").removeClass('hide');
						 }
					 });
			}

		};

		$('body').on('click','a.editarContacto',function(e){
			e.preventDefault();

			var uuid = $(this).data('contactouuid');
			var datos = {erptkn: tkn, uuid_contacto: uuid};
			$scope.contactoInfo(datos);
		});

		$('body').on('click','a.clienteVerContacto',function(e){
			e.preventDefault();

			var uuid = $(this).data('contactouuid');
			var datos = {erptkn: tkn, uuid_contacto: uuid};
			$('#optionsModal').modal('hide');
			$scope.contactoInfo(datos);
		});

	 $scope.contactoInfo = function(datos){

			$http({
				url: ruta.contactoInfo,
				method: 'POST',
				data : $.param(datos),
				cache: false,
				xsrfCookieName: 'erptknckie_secure',
				headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
			}).then(function (response) {
				if(response){
					var dataContacto = response.data;
					setTimeout(function () {
					$('#tipo_identificacion1').val(dataContacto.tipo_identificacion).change();
					}, 1000);
					$scope.naturalLetra.valor = dataContacto.letra;
					$scope.contacto = dataContacto;
					clienteProvider.config(true);
					angular.element("#vistaCliente").addClass('hide');
					angular.element("#vistaFormularioContacto").removeClass('hide');
				}
			});

	 };

		$scope.inicializar = function(){
	    objFrom.contactoForm.validate({
	      ignore: '',
	      wrapper: '',
	    });
	  };
		$scope.inicializar();
	});

bluapp.controller("aseguradoraFormularioController", function($scope, $http){
	var objFrom = {
		contactoForm: $('#crearContacto'),
	};

	var ruta = {
		guardarContacto:  phost() + 'contactos/ajax-guardar-contacto',
		contactoInfo: phost() + 'contactos/ajax-contacto-info',
		contactoInactivo: phost() + 'contactos/ajax-contacto-inactivo'
	};

	$scope.contacto = {};

	$scope.cancelarBtn = function(e){
		e.preventDefault();
        aseguradoraProvider.config(false);
		angular.element("#vistaFormularioContacto").addClass('hide');
		angular.element("#vistaFormularioAseguradora").removeClass('hide');
		$scope.contacto = {};
	};

	$scope.guardarBtn = function(contacto){

		var formularioContacto = objFrom.contactoForm;
		if(formularioContacto.valid() === true){
			contacto.uuidcliente = cliente.uuid_cliente;
            contacto.vista = "aseguradoras";
			$scope.contacto = contacto;
			var datos = $.extend({erptkn: tkn},$scope.contacto);
			$http({
				url: ruta.guardarContacto,
				method: 'POST',
				data : $.param(datos),
				cache: false,
				xsrfCookieName: 'erptknckie_secure',
				headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
			}).then(function (response) {
				if(response){
					var respuesta = response.data;
					$("#mensaje_info").empty().html('<div id="success-alert" class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+respuesta.contenido+'</div>');
					$scope.contacto = {};
					$("#contactosGrid").trigger("reloadGrid");
                    aseguradoraProvider.config(false);
					angular.element("#vistaFormularioContacto").addClass('hide');
					angular.element("#vistaFormularioAseguradora").removeClass('hide');
				}
			});
		}

	};
	$('body').on('click','a.aseguradoraEditarContacto',function(e){
		e.preventDefault();

		var uuid = $(this).data('contactouuid');
		var datos = {erptkn: tkn, uuid_contacto: uuid};
		$('#optionsModal').modal('hide');
		$scope.contactoInfo(datos);
	});
	$('body').on('click','a.aseguradoraEstadoContacto',function(e){
		e.preventDefault();

		var uuid = $(this).data('contactouuid');
		var datos = {erptkn: tkn, uuid_contacto: uuid};
		$('#optionsModal').modal('hide');
		$scope.contactoInactivo(datos);
	});

	$scope.contactoInactivo = function(datos){
		$http({
			url: ruta.contactoInactivo,
			method: 'POST',
			data : $.param(datos),
			cache: false,
			xsrfCookieName: 'erptknckie_secure',
			headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function (response) {
			if(response){
				var respuesta = response.data;
				//$("#mensaje_info").empty().html('<div id="success-alert" class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+respuesta.contenido+'</div>');
				$scope.contacto = respuesta;
				$("#contactosGrid").trigger("reloadGrid");
				aseguradoraProvider.config(false);
				angular.element("#vistaFormularioContacto").addClass('hide');
				angular.element("#vistaFormularioAseguradora").removeClass('hide');
			}
		});
	};

	$scope.contactoInfo = function(datos){
		$http({
			url: ruta.contactoInfo,
			method: 'POST',
			data : $.param(datos),
			cache: false,
			xsrfCookieName: 'erptknckie_secure',
			headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function (response) {
			if(response){
				var dataContacto = response.data;
				$scope.contacto = dataContacto;
                aseguradoraProvider.config(true);
				angular.element("#vistaFormularioAseguradora").addClass('hide');
				angular.element("#vistaFormularioContacto").removeClass('hide');
			}
		});
	};

	$scope.inicializar = function(){
		objFrom.contactoForm.validate({
			ignore: '',
			wrapper: '',
		});
	};
	$scope.inicializar();
});
