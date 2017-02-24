/**
 * Servicio Docuemntos
 */
bluapp.service('subirDocumentosService', ['$http', '$document', '$rootScope', function($http, $document, $rootScope) {

	var scope = this;
	var requisito_id = '';
	var formulario = '#subirDocumentosForm';
	var modal = $('#documentosModal');
	var filesList = [], filesNames = [], paramNames = [];

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
			$(formulario).find('input[class*="fecha"]').daterangepicker({
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

				$rootScope.campos[this.name] = picker.startDate.format('DD/MM/YYYY');
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


		}, 1000);
    };

    this.initFileUpload = function()
    {
    	var scope =  this;
    	var formulario = '#subirDocumentosForm';

    	$('#documento').fileupload({
            //url: phost() + "documentos/ajax-guardar-documentos",
            url: $(formulario).attr('action'),
            type: 'POST',
            dataType: 'json',
            autoUpload: false,
            singleFileUploads: false,
            dropZone: document.getElementById('dropTarget'),
            //acceptFileTypes: /(\.|\/)(gif|jpe?g|png|mp4|mp3)$/i,
            add: function (e, data) {

            	$.each(data.files, function (index, file) {
            	      //verificar si existe el archivo en el arreglo
            		  var found = filesNames.indexOf(file.name);
            		  filesNames.push(file.name);

            	      //para evitar duplicidad de archivos
            	      if(found<0){
            	    	  var fieldname = $(e.delegatedEvent.currentTarget).find('input').attr('name') !== undefined ? $(e.delegatedEvent.currentTarget).find('input').attr('name') : $(e.delegatedEvent.currentTarget).attr('name');
            	    	  filesList.push(file);
    	        	      paramNames.push(fieldname);
            	      }
            	});

            	setTimeout(function () {
        			$rootScope.safeApply(function () {
								
        				//agregar texto de archivo seleccionado.
        				$rootScope.fileClassBtn = 'btn-default';
        				$rootScope.fileBtn = '<i class="fa fa-upload"></i> '+ data.files.length +' archivo'+ (data.files.length > 1 ? 's' : '') +' seleccionado' + (data.files.length > 1 ? 's' : '');
        	        });
        	     }, 50);
            },
            done: function(e, data) {
            	if( $.isEmptyObject(data.result.session) === false){
        			window.location = phost() + "login?expired";
        		}

        		//mostrar mensaje
        		toastr.success(data.result.mensaje);

        		//Remove file upload widget
        		scope.destroyFileUpload();

        		//Init file upload widget
        		scope.initFileUpload();

        		//Limpiar formulario
        		$rootScope.limpiarFormulario();

        		modal.modal('hide');
            }
        });
    };

    this.destroyFileUpload = function()
    {
    	$('#documento').fileupload('destroy');
    	filesList = [], filesNames = [], paramNames = [];
    };

    //filesList = [], filesNames = [], paramNames
    this.getFilesList = function(){
    	return filesList;
    };

    this.getFilesNames = function(){
    	return filesNames;
    };

    this.getParamNames = function(){
    	return paramNames;
    };
}]);

/**
 * Controlador Formulario de Evaluacion
 */
bluapp.controller("subirDocumentosController", function($rootScope, $scope, $document, $http, $rootScope, $compile, subirDocumentosService){

	var url = phost() + "colaboradores/ajax-seleccionar-entrega-inventario";
	var formulario = '#subirDocumentosForm';
	var modal = $('#documentosModal');
	var botones = ['<div class="row">',
	     		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
 		   		'<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal" ng-click="limpiarFormulario($event)">Cancelar</button>',
 		   '</div>',
 		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
 		   		'<button class="btn btn-w-m btn-primary btn-block guardarDocBoton {{disabledBtn}}" type="button" ng-click="subirDocumento($event)" ng-bind-html="guardarBtn"></button>',
 		   '</div>',
 		   '</div>'
	].join('\n');

	//Compilar botones en angularjs
	botones = $compile(botones)($scope);

	//Agregar botones al modal
	modal.find('.modal-footer').empty().append(botones);

	$rootScope.campos = {
		erptkn: tkn
	};
	$scope.guardado = false;

	//Inicializar scope de boton de subir archivo
	//y boton de guardar
	$rootScope.fileBtn = 'Seleccione';
	$rootScope.fileClassBtn = 'btn-default';
	$scope.guardarBtn = 'Guardar';
	$scope.disabledBtn = '';

	subirDocumentosService.initFileUpload();

	//Efecto Drop
	var drop = document.getElementById('dropTarget');
	drop.addEventListener("dragenter", change, false);
	drop.addEventListener("dragleave",change_back,false);

	function change() {
	  drop.style.backgroundColor = '#F7F1AD'; // #5cb85c
	}
	function change_back(){
	  drop.style.backgroundColor = 'transparent';
	}

	//Inicializar campos, plugins y validacion
	subirDocumentosService.init();

	//Al abrir modal verificar si existe id de entrega de inventario
	modal.on('shown.bs.modal', function(e){
		$scope.guardado = false;
	});

	//Al cerra modal
	modal.on('hidden.bs.modal', function(e){});

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

    //Evento: limpiar formulario
	$rootScope.limpiarFormulario = function(e)
    {
		if(typeof e != 'undefined'){
			e.preventDefault();
		}

		if(typeof flow != 'undefined'){

			//Remove file upload widget
			subirDocumentosService.destroyFileUpload();

			//Init file upload widget
			subirDocumentosService.initFileUpload();
		}

		//limpiar color de seleccion drop
		change_back();

		setTimeout(function () {
			$rootScope.safeApply(function () {

				//reset campos
				$scope.campos = {
					erptkn: tkn
				};

				//reset boton
				$scope.guardarBtn = 'Guardar';
				$scope.disabledBtn = '';
				$rootScope.fileBtn = 'Seleccione';
				$rootScope.fileClassBtn = 'btn-default';
	        });
	     }, 100);
    };

	/**
     * Funcion Guardar Formulario de Entrega de Inventario
     */
	$scope.subirDocumento = function(e)
    {
    	e.preventDefault();

    	if($(formulario).validate().form() === true)
		{
    		//Verificar seleccion de archivo
    		if(subirDocumentosService.getFilesList().length===0){
    			toastr.warning('Debe seleccionar uno o varios documentos.');
    			$scope.fileClassBtn = 'btn-danger';
    			return false;
    		}

    		//Agregar datos extras del formulario
    		//al upload de documentos.
    		formData = $scope.campos;

    		//Estado de guardando en boton
    		$scope.guardarBtn = '<i class="fa fa-circle-o-notch fa-spin"></i> Guardando...';
    		$scope.disabledBtn = 'disabled';

    		//Submit datos
    		//$scope.uploadform();
    		$('#documento').fileupload('send', {
    			files: subirDocumentosService.getFilesList(),
    			paramName: subirDocumentosService.getParamNames(),
    			formData: formData
    		});
		}
    };
});
