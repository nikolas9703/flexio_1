//Tabla Accion de Personal
var tablaAccionPersonal = (function(){

	var url = 'accion_personal/ajax-listar';
	var grid_id = "tablaAccionPersonalGrid";
	var grid_obj = $("#tablaAccionPersonalGrid");
	var opcionesModal = $('#opcionesModal');

	var botones = {
		opciones: ".viewOptions",
		detalle: ".verDetalle",
		buscar: "#searchBtn",
		limpiar: "#clearBtn",
		descargar: ".descargarAdjuntoBtn",
	};

	var tabla = function(){

		var vacacionid = "";
		var licenciaid = "";
		var liquidacionid = "";
		var colaboradorid = "";
		var estadoplanilla = "";
		var ocultar_opciones = false;

		if(typeof vacacion_id != "undefined"){
			vacacionid = $.parseJSON(vacacion_id);
			ocultar_opciones = true;
		}
		if(typeof licencia_id != "undefined"){
			licenciaid = $.parseJSON(licencia_id);
			ocultar_opciones = true;
		}
		if(typeof liquidacion_id != "undefined"){
			liquidacionid = $.parseJSON(liquidacion_id);
			ocultar_opciones = true;
		}
		if(typeof colaborador_id != "undefined"){
			colaboradorid = $.parseJSON(colaborador_id);
		}

		if(typeof estado_planilla != "undefined"){
			estadoplanilla = estado_planilla;
 		}

	    var location_url = window.location.pathname.match(/planilla/g) ? true : false;

 	    if(location_url ==  true){
	    	ocultar_opciones = false;
		}
		//inicializar jqgrid
		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
		   	    'No. Acci&oacute;n personal',
				'Tipo de acci&oacute;n personal',
				'Colaborador',
				'C&eacute;dula',
				'&Aacute;rea de Negocio',
				'Estado',
				'',
				'',
				'',
				'',
				''
			],
		   	colModel:[
		   	    {name:'No. Accion personal', index:'no_accion', width: 40},
				{name:'Tipo de accion personal', index:'accionable_type', width:40},
				{name:'Colaborador', index:'colaborador_id', width:40},
				{name:'Cedula', index:'cedula', width:25 },
				{name:'Area de Negocio', index:'departamento', width: 60},
		   		{name:'Estado', index:'estado', width:30 },
				{name:'link', index:'link', width:25, sortable:false, resizable:false, hidedlg:true, align:"center", hidden: ocultar_opciones},
				{name:'options', index:'options', hidedlg:true, hidden: true},
				{name:'archivo_ruta', index:'archivo_ruta', hidedlg:true, hidden: true},
				{name:'archivo_nombre', index:'archivo_nombre', hidedlg:true, hidden: true},
				{name:'accionable_id', index:'accionable_id', hidedlg:true, hidden: true},
		   	],
			mtype: "POST",
		   	postData: {
		   		erptkn: tkn,
		   		vacacion_id: vacacionid,
		   		licencia_id: licenciaid,
		   		liquidacion_id: liquidacionid,
		   		colaborador_id: colaboradorid,
					estado_planilla: estadoplanilla
		   	},
			height: "auto",
			autowidth: true,
			rowList: [10, 20,50, 100],
			rowNum: 10,
			page: 1,
			pager: "#"+ grid_id +"Pager",
			loadtext: '<p>Cargando...</p>',
			hoverrows: false,
		    viewrecords: true,
		    refresh: true,
		    gridview: true,
		    multiselect: true,
		    sortname: 'created_at',
		    sortorder: "DESC",
		    beforeProcessing: function(data, status, xhr){
		    	//Check Session
				if( $.isEmptyObject(data.session) == false){
					window.location = phost() + "login?expired";
				}
		    },
		    loadBeforeSend: function () {},
		    beforeRequest: function(data, status, xhr){},
			loadComplete: function(data){

				//check if isset data
				if( data['total'] == 0 ){
					$('#gbox_'+ grid_id).hide();
					$('#'+ grid_id +'NoRecords').empty().append('No se encontraron datos de Accion personal.').css({"color":"#868686","padding":"30px 0 0"}).show();
				}
				else{
					$('#'+ grid_id +'NoRecords').hide();
					$('#gbox_'+ grid_id).show();
				}
			},
			onSelectRow: function(id){
				$(this).find('tr#'+ id).removeClass('ui-state-highlight');
			},
		});

		//Al redimensionar ventana
		$(window).resizeEnd(function() {
			tablaAccionPersonal.redimensionar();
		});
	};

	//Inicializar Eventos de Botones
	var eventos = function(){

		//Boton de Opciones
		grid_obj.on("click", botones.opciones, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			var id = $(this).attr("data-id");

			var rowINFO = grid_obj.getRowData(id);
		    var options = rowINFO["options"];
		    options = options.replace(/0000/gi, id);

	 	    //Init Modal
		    opcionesModal.find('.modal-title').empty().append('Opciones: '+ rowINFO["Tipo de accion personal"] +'');
		    opcionesModal.find('.modal-body').empty().append(options);
		    opcionesModal.find('.modal-footer').empty();
		    opcionesModal.modal('show');
		});

		//Ver Detalle
		grid_obj.on("click", botones.detalle, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			//Cerrar modal de opciones
			//opcionesModal.modal('hide');

			var formulario = $(this).attr("data-formulario");
			var accion_id = $(this).attr("data-accion-id");
			var id = $(this).attr("data-id");
			var modulo_name_id = formulario.replace(/(es|s)$/g, '') + '_id';

			//Before using local storage, check browser support for localStorage and sessionStorage
			if(typeof(Storage) !== "undefined") {

				//Grabar id de la accion
				localStorage.setItem(modulo_name_id, accion_id);
				localStorage.setItem('accion_personal_id', id);
			}

			//Verificar si existe o no variable
			//colaborador_id
			if(typeof colaborador_id != 'undefined'){
				//Verificar si el formulario esta siendo usado desde
				//Ver Detalle de Colaborador
				if(window.location.href.match(/(colaboradores)/g)){


					var scope = angular.element('[ng-controller="'+ ucFirst(formulario) +'Controller"]').scope();
					scope.popularFormulario();

					//Activar Tab
					//$('#moduloOpciones').find('ul').find("a:contains('"+ formulario.replace(/(es|s)$/g, '') +"')").trigger('click');
					$('#moduloOpciones').find('ul').find('a[href*="'+ formulario.replace(/(es|s)$/g, '') +'"]').trigger('click');
				}

			}else{
				window.location = phost() + 'accion_personal/crear/' + formulario +'/' + id;
			}
		});

		//Ver Detalle
		$(opcionesModal).on("click", botones.detalle, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			//Cerrar modal de opciones
			//opcionesModal.modal('hide');

			var formulario = $(this).attr("data-formulario");
			var accion_id = $(this).attr("data-accion-id");
			var id = $(this).attr("data-id");
			var modulo_name_id = formulario.replace(/(es|s)$/g, '') + '_id';

			//Before using local storage, check browser support for localStorage and sessionStorage
			if(typeof(Storage) !== "undefined") {

				//Grabar id de la accion
				localStorage.setItem(modulo_name_id, accion_id);
				localStorage.setItem('accion_personal_id', id);
			}

			//Verificar si existe o no variable
			//colaborador_id
			if(typeof colaborador_id != 'undefined'){
				//Verificar si el formulario esta siendo usado desde
				//Ver Detalle de Colaborador
				if(window.location.href.match(/(colaboradores)/g)){


					var scope = angular.element('[ng-controller="'+ ucFirst(formulario) +'Controller"]').scope();
					scope.popularFormulario();

					//Activar Tab
					//$('#moduloOpciones').find('ul').find("a:contains('"+ formulario.replace(/(es|s)$/g, '') +"')").trigger('click');
					$('#moduloOpciones').find('ul').find('a[href*="'+ formulario.replace(/(es|s)$/g, '') +'"]').trigger('click');
				}

			}else{
				window.location = phost() + 'accion_personal/crear/' + formulario +'/' + id;
			}
		});

		//Boton de Descargar Evaluacion
		opcionesModal.on("click", botones.descargar, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			var evaluacion_id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(evaluacion_id);

			var no_accion = rowINFO["No. Accion personal"];
			var archivo_nombre = rowINFO["archivo_nombre"];
	    	var archivo_ruta = rowINFO["archivo_ruta"];
	    	var fileurl = phost() + archivo_ruta +'/'+ archivo_nombre;

	    	if(archivo_nombre == '' || archivo_nombre == undefined){
	    		return false;
	    	}

	    	if(rowINFO["Tipo de accion personal"].match(/incapacidades/ig)){

	    		var archivos = $.parseJSON(archivo_nombre);
	    		if(archivos.length > 1){

	    			//inicializar plugin
	    			var zip = new JSZip();

	    			//recorrer arreglo de archivos y agregarlos al zip
	    			$.each(archivos, function(i, filename){
	    				fileurl = phost() + archivo_ruta +'/'+ filename;
	    				zip.file(filename, urlToPromise(fileurl), {binary:true});
	    			});

	    			// when everything has been downloaded, we can trigger the dl
	    	        zip.generateAsync({type:"blob"}, function updateCallback(metadata) {
	    	            //console.log( metadata.percent );
	    	        }).then(function callback(blob) {
	    	            //see FileSaver.js
	    	        	saveAs(blob, $(no_accion).text() +".zip");
	    	        }, function (e) {
	    	        	//console.log(e);
	    	        });
	    	        return false;

	    		}else{

	    			fileurl = phost() + archivo_ruta +'/'+ archivos;

	    			//Descargar archivo
			    	downloadURL(fileurl, archivo_nombre);
	    		}

	    		console.log(archivos);

	    	}else{
	    		//Descargar archivo
		    	downloadURL(fileurl, archivo_nombre);
	    	}

		    //Ocultar modal
			//opcionesModal.modal('hide');
		});

		//Boton de Buscar
		$('#buscarAccionPersonalForm').on("click", botones.buscar, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			buscar();
		});

		//Boton de Reiniciar jQgrid
		$('#buscarAccionPersonalForm').on("click", botones.limpiar, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			recargar();
			limpiarCampos();
		});

		//jQuery Daterange
		$('#buscarAccionPersonalForm').find("#fecha_ap_desde").datepicker({
			defaultDate: "+1w",
			dateFormat: 'dd/mm/yy',
			changeMonth: true,
			numberOfMonths: 1,
			onClose: function( selectedDate ) {
				$('#buscarAccionPersonalForm').find("#fecha_ap_hasta").datepicker( "option", "minDate", selectedDate );
			}
		});
		$('#buscarAccionPersonalForm').find("#fecha_ap_hasta").datepicker({
			defaultDate: "+1w",
			dateFormat: 'dd/mm/yy',
			changeMonth: true,
			numberOfMonths: 1,
			onClose: function( selectedDate ) {
				$('#buscarAccionPersonalForm').find("#fecha_ap_desde").datepicker( "option", "maxDate", selectedDate );
		    }
		});
	};

	/**
     * Fetch the content and return the associated promise.
     * @param {String} url the url of the content to fetch.
     * @return {Promise} the promise containing the data.
     */
     var urlToPromise = function(url) {
        return new Promise(function(resolve, reject) {
            JSZipUtils.getBinaryContent(url, function (err, data) {
                if(err) {
                    reject(err);
                } else {
                    resolve(data);
                }
            });
        });
    }

	//Reload al jQgrid
	var recargar = function(){

		//Reload Grid
		grid_obj.setGridParam({
			url: phost() + url,
			datatype: "json",
			postData: {
				no_accion_personal: '',
				nombre_colaborador: '',
				cargo: '',
				cedula: '',
				departamento_id: '',
				tipo_accion: '',
				estado: '',
				fecha_desde: '',
				fecha_hasta: '',
				centro_id: '',
				cargo_id: '',
				erptkn: tkn
			}
		}).trigger('reloadGrid');
	};

	//Buscar cargo en jQgrid
	var buscar = function(){

		var no_accion_personal 	= $('#buscarAccionPersonalForm').find('#no_accion_personal').val();
		var nombre_colaborador 	= $('#buscarAccionPersonalForm').find('#nombre_colaborador').val();
		var cedula 				= $('#buscarAccionPersonalForm').find('#cedula').val();
		var departamento_id 	= $('#buscarAccionPersonalForm').find('#departamento_id').val();
		var estado_id 			= $('#buscarAccionPersonalForm').find('#estado_id').val();
		var tipo_accion 		= $('#buscarAccionPersonalForm').find('#tipo_accion').val();
		var fecha_desde 		= $('#buscarAccionPersonalForm').find('#fecha_ap_desde').val();
		var fecha_hasta 		= $('#buscarAccionPersonalForm').find('#fecha_ap_hasta').val();
		var centro_id 			= $('#buscarAccionPersonalForm').find('#centro_id').find('option:selected').val();
		var cargo_id			= $('#buscarAccionPersonalForm').find('#cargo_id').find('option:selected').val();
		var estado				= $('#buscarAccionPersonalForm').find('#estado').val();

		if(nombre_colaborador != "" || no_accion_personal != "" || cedula != "" || departamento_id != "" || cargo_id != "" || tipo_accion  != "" || fecha_desde != "" || fecha_hasta != "" || estado != "" || centro_id != "")
		{
			//Reload Grid
			grid_obj.setGridParam({
				url: phost() + url,
				datatype: "json",
				postData: {
					no_accion_personal: no_accion_personal,
					nombre_colaborador: nombre_colaborador,
					cedula: cedula,
					departamento_id: departamento_id,
					tipo_accion: tipo_accion,
					estado: estado,
					fecha_desde: fecha_desde,
					fecha_hasta: fecha_hasta,
					centro_id: centro_id,
					cargo_id: cargo_id,
					erptkn: tkn
				}
			}).trigger('reloadGrid');
		}
	};

	//Limpiar campos de busqueda
	var limpiarCampos = function(){
		$('#buscarAccionPersonalForm').find('input[type="text"]').prop("value", "");
		$('#buscarAccionPersonalForm').find('select').find('option:eq(0)').prop("selected", "selected");
		actualizar_chosen();
	};

	var actualizar_chosen = function() {
		//refresh chosen
		setTimeout(function(){
			$('#buscarAccionPersonalForm').find('select.chosen-select').trigger('chosen:updated');
		}, 50);
	};

	return{
		init: function() {
			tabla();
			eventos();
		},
		recargar: function(){
			//reload jqgrid
			recargar();
		},
		redimensionar: function(){
			//Al redimensionar ventana
			$(".ui-jqgrid").each(function(){
				var w = parseInt( $(this).parent().width()) - 6;
				var tmpId = $(this).attr("id");
				var gId = tmpId.replace("gbox_","");
				$("#"+gId).setGridWidth(w);
			});
		}
	};
})();

tablaAccionPersonal.init();
