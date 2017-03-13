/**
 * Servicio Requisitos
 */
bluapp.service('requisitosService', ['$http', '$document', function($http, $document) {

	var scope = this;
	var requisito_id = '';
	var formulario = '#requisitosForm';

	/**
	 * Funcion para ejecurtar ajax
	 */
	this.ajax = function(url, data) {

		return $http({
			 method: 'POST',
			 url: url,
			 data : $.param($.extend({erptkn: tkn}, data)),
			 cache: false,
			 headers : { 'Content-Type': 'application/x-www-form-urlencoded'}
		});
	};

	/**
	 * Funcion para inicializar plugins
	 */
	this.init = function() {

		setTimeout(function(){

			//Plugin Datepicker
			$(formulario).find('.fecha-expiracion').daterangepicker({
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

				var url = window.phost() + "colaboradores/ajax-guardar-fecha-requisito";

				//Guardar fecha seleccionada
				scope.ajax(url, {
					requisito_id: $(this).attr('data-requisito-id'),
					colaborador_id: colaborador_id,
		    		fecha: picker.startDate.format('YYYY-MM-DD')
				});
			});

		},800);
    };

    /**
     * Desabilitar botones de adjunto
     * que ya se hallan subido.
     */
    this.verificarAdjuntoSubidos = function() {
    	setTimeout(function(){
    	    var adjuntosElement = $document.find('[id*="adjunto"]');
    	    angular.forEach(adjuntosElement, function(adjunto, key) {
    			if($(adjunto).hasClass('disabled')){
    				$(adjunto).removeAttr("flow-btn").find('input[type="file"]').prop("disabled", "disabled");
    			}else{
    				$(adjunto).prop("flow-btn", "").find('input[type="file"]').removeAttr("disabled");
    			}
    		});
        }, 500);
    };

}]);

/**
 * Provider Directova ng-flow
 */
bluapp.config(['flowFactoryProvider', function (flowFactoryProvider) {
	flowFactoryProvider.defaults = {
		target: phost() + "colaboradores/ajax-subir-documento-requisito",
		permanentErrors: [404, 500, 501],
		maxChunkRetries: 1,
		chunkRetryInterval: 5000,
		progressCallbacksInterval: 100,
		singleFile: true,
		query: {
			erptkn: tkn
		},
		testMethod: 'POST',
		testChunks:false,
		uploadMethod: 'POST'
	};
}]);

/**
 * Controlador Formulario de Requisitos
 */
bluapp.controller("RequisitosController", function($document, $scope, $http, $rootScope, requisitosService, $compile){

	var url = window.phost() + "menu/navbar";
	var opcionesModal = $('#opcionesModal');
	var requisitosArray = $.parseJSON(requisitos);

	$scope.seleccion = {};

	//Recorrer array de requisitos, para marcar
	//los seleccionados.
	angular.forEach(requisitosArray, function(requisito, key) {
		if(requisito.checked == true){
			$scope.seleccion[requisito.id] = true;
		}
	});

	$scope.colaborador_id = typeof colaborador_id != 'undefined' ? colaborador_id : '';

	//Popular dropdown de departamentos
    $scope.requisitos = requisitosArray;

    //Desabilitar/Habilitar botones de adjunto
    //que ya se hallan subido.
    requisitosService.verificarAdjuntoSubidos();

    //Inicializar plugins
    requisitosService.init();

    //Event: Guardar campo
    $scope.guardarSeleccion = function(){

    	var url = window.phost() + "colaboradores/ajax-guardar-seleccion-requisito";

    	requisitosService.ajax(url, {
    		colaborador_id: colaborador_id,
    		requisitos: $scope.seleccion
    	});
    };

    /**
     * Funcion Ejecuta el Upload de Archivo.
     */
    $scope.subirAdjunto = function($file, $event, $flow, requisito_id)
    {
    	//sobreescribir extra datos
    	$flow.opts.target = phost() + "colaboradores/ajax-subir-documento-requisito";
    	$flow.opts.query = {
    		erptkn: tkn,
    		requisito_id: requisito_id,
    		colaborador_id: colaborador_id
    	};

    	//Subir archivo
    	$flow.upload();

    	//Seleccionar boton
    	var l = Ladda.create(document.querySelector('#adjunto'+ requisito_id));

    	//Inicializar barra de progreso
		l.start();

    	//Evento: progreso de subida
		$flow.on('fileProgress', function(file, chunk) {
    		l.setProgress($flow.progress());
    	});

		//Evento: al completar subida de archivo.
    	$flow.on('fileSuccess', function(file, message, chunk) {
    		//Stop loading
    		l.stop();

    		//Desabilitar Boton de Subir Archivo
    		$('#adjunto'+ requisito_id).addClass("disabled").find('input[type="file"]').prop("disabled", "disabled");

    		//response
    		var response = $.parseJSON(message);

    		//mensaje completado
    		toastr.success(response.mensaje);

    		//Actualizar lista de requisitos.
    		$scope.requisitos = response.requisitos;
    	});
    };

    /**
     * Funcion Modal.
     */
    $scope.modal = function(e)
    {
    	e.preventDefault();

    	var requisito_nombre = $(e.currentTarget).closest('tr').find('td:eq(1)').text();
    	var requisito_id = $(e.currentTarget).attr('data-requisito-id');

    	var opciones = [
    	   '<a class="btn btn-block btn-outline btn-success" data-requisito-id="'+ requisito_id +'" data-ng-click="descargarAdjunto($event)">Descargar Adjunto</a>',
    	   '<a class="btn btn-block btn-outline btn-success" data-requisito-id="'+ requisito_id +'" data-ng-click="confirmarEliminarAdjunto($event)">Eliminar Adjunto</a>'
    	].join('\n');

    	var opciones = $compile(opciones)($scope);

    	 //Init Modal
	    opcionesModal.find('.modal-title').empty().append('Opciones: '+ requisito_nombre);
	    opcionesModal.find('.modal-body').empty().append(opciones);
	    opcionesModal.find('.modal-footer').empty();
	    opcionesModal.modal('show');
    };

    /**
     * Funcion Descargar Archivo Adjunto.
     */
    $scope.descargarAdjunto = function(e)
    {
    	e.preventDefault();
    	var requisito_id = $(e.currentTarget).attr('data-requisito-id');
    	var archivo_nombre = $('#req'+ requisito_id).find('button').attr('data-archivo-nombre');
    	var archivo_ruta = $('#req'+ requisito_id).find('button').attr('data-archivo-ruta');
    	var fileurl = phost() + archivo_ruta +'/'+ archivo_nombre;

    	if(archivo_nombre == ''){
    		return false;
    	}

    	//Descargar archivo
    	downloadURL(fileurl, archivo_nombre);
    };

    /**
     * Funcion Confirmacion para Eliminar Archivo Adjunto.
     */
    $scope.confirmarEliminarAdjunto = function(e)
    {
    	e.preventDefault();
    	var requisito_id = $(e.currentTarget).attr('data-requisito-id');
    	var requisito_nombre = $('#req'+ requisito_id).find('td:eq(1)').text();
    	var archivo_nombre = $('#req'+ requisito_id).find('button').attr('data-archivo-nombre');

    	if(archivo_nombre == ''){
    		return false;
    	}

    	var botones = [
     	   '<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>',
     	   '<a class="btn btn-w-m btn-danger" data-requisito-id="'+ requisito_id +'" data-ng-click="eliminarAdjunto($event)">Eliminar</a>'
     	].join('\n');

    	var botones = $compile(botones)($scope);

    	//Confirmar eliminar archivo
    	 //Init Modal
	    opcionesModal.find('.modal-title').empty().append('Confirmar');
	    opcionesModal.find('.modal-body').empty().append('&#191;Esta seguro que desea eliminar el adjunto de '+ requisito_nombre +'?');
	    opcionesModal.find('.modal-footer').empty().append(botones);
	    opcionesModal.modal('show');
    };

    /**
     * Funcion Eliminar Archivo Adjunto.
     */
    $scope.eliminarAdjunto = function(e)
    {
    	e.preventDefault();

    	var url = window.phost() + "colaboradores/ajax-eliminar-adjunto-requisito";
    	var requisito_id = $(e.currentTarget).attr('data-requisito-id');

    	//Ajax eliminar archivo
    	requisitosService.ajax(url, {
			colaborador_id: colaborador_id,
			requisito_id: requisito_id
		}).then(function successCallback(response) {

			//Mensaje
			toastr.success(response.data.mensaje);

			//Cerrar Modal.
			opcionesModal.modal('hide');

			//Actualizar lista de requisitos.
    		$scope.requisitos = response.data.requisitos;

    		//Desabilitar/Habilitar botones de adjunto
    	    //que ya se hallan subido.
    	    requisitosService.verificarAdjuntoSubidos();

	    }, function errorCallback(response) {
	        // called asynchronously if an error occurs
	        // or server returns response with an error status.
	    });
    };

});
