//Tabla Accion de Personal
var tablaAccionPersonal = (function(){

	var url = 'documentos/ajax-listar-seguros';
	var grid_id = "tablaDocumentosGrid";
	var grid_obj = $("#tablaDocumentosGrid");
	var opcionesModal = $('#opcionesModal, #optionsModal');
  var editNombreModal = $('#documentosModalEditar');

	var botones = {
		opciones: ".viewOptions",
		ver: ".verDocumento",
		buscar: "#searchBtn",
		limpiar: "#clearBtn",
		descargar: ".descargarAdjuntoBtn",
    detalle: ".verAdjuntoBtn",
    editNombre: ".editnombreBtn",
	exportar:"#exportarBtn"
	};

	var tabla = function(){
		var spordenalquilerid = "";
		var contratoid = "";
		var facturaventaid = "";
		var pedidoid = "";
		var ordencompraid = "";
		var facturacompra_id = "";
		var colaboradorid = "";
		var equipoid = "";
		var interesesaseguradosidpersona = "";
		var interesesaseguradosidvehiculo = "";
		var interesesaseguradosidmaritimo = "";
		var interesesaseguradosidaereo = "";
		var interesesaseguradosidproyecto = "";
		var interesesaseguradosidcarga = "";
    var interesesaseguradosidarticulo = "";
    var interesesaseguradosidubicacion = "";
    var clientes = "";
    var cotizaciones = "";
    var ordenesventas = "";
    var proveedoresid = "";
    var itemsid = "";
    var cajasId = "";
	var moduloId = "";
	var solicitudId = "";
	var polizaId = "";
	var facturassegurosId="";
	var endosoId="";
	var reclamoId="";
		var ocultar_opciones = false;

		if(typeof modulo_id != "undefined"){
			moduloId = modulo_id;
		}
		if(typeof sp_orden_alquiler_id != "undefined"){
			spordenalquilerid = sp_orden_alquiler_id;
		}
		if(typeof contrato_id != "undefined"){
			contratoid = $.parseJSON(contrato_id);
		}
		if(typeof factura_venta_id != "undefined"){
			facturaventaid = $.parseJSON(factura_venta_id);
		}
		if(typeof window.sp_pedido_id != "undefined"){
			pedidoid = $.parseJSON(window.sp_pedido_id);
		}

		if(typeof factura_compra_id != "undefined"){
			facturacompra_id = $.parseJSON(factura_compra_id);
		}
		if(typeof ordencompra_id != "undefined"){
			ordencompraid = $.parseJSON(ordencompra_id);
		}
		if(typeof equipoID != "undefined"){
			equipoid = equipoID;
		}
                if(typeof intereses_asegurados_id_persona != "undefined"){
			interesesaseguradosidpersona = intereses_asegurados_id_persona;
		}
                if(typeof intereses_asegurados_id_vehiculo != "undefined"){
			interesesaseguradosidvehiculo = intereses_asegurados_id_vehiculo;
		}
                if(typeof intereses_asegurados_id_casco_maritimo != "undefined"){
			interesesaseguradosidmaritimo = intereses_asegurados_id_casco_maritimo;
		}
                if(typeof intereses_asegurados_id_casco_aereo != "undefined"){
			interesesaseguradosidaereo = intereses_asegurados_id_casco_aereo;
		}
                if(typeof intereses_asegurados_id_proyecto_actividad != "undefined"){
			interesesaseguradosidproyecto = intereses_asegurados_id_proyecto_actividad;
		}
                if(typeof intereses_asegurados_id_carga != "undefined"){
			interesesaseguradosidcarga = intereses_asegurados_id_carga;
		}
                if(typeof intereses_asegurados_id_articulo != "undefined"){
			interesesaseguradosidarticulo = intereses_asegurados_id_articulo;
		}
                if(typeof intereses_asegurados_id_ubicacion != "undefined"){
			interesesaseguradosidubicacion = intereses_asegurados_id_ubicacion;
		}
                if(typeof clientes_id != "undefined"){
			clientes = clientes_id;
		}
                if(typeof sp_cotizacion_id != "undefined"){
			cotizaciones = sp_cotizacion_id;
		}
                if(typeof ordenes_ventas_id != "undefined"){
			ordenesventas = ordenes_ventas_id;
		}
                if(typeof proveedores_id != "undefined"){
			proveedoresid = proveedores_id;
		}
                if(typeof item_id != "undefined"){
			itemsid = item_id;
		}
                if(typeof caja_id != "undefined"){
			cajasId = caja_id;
		}
		
		  if(typeof solicitud_id != "undefined"){
			solicitudId = solicitud_id;
		}
		if(typeof poliza_id != "undefined"){
			polizaId = poliza_id;
		}
		if(typeof endoso_id != "undefined"){
			endosoId = endoso_id;
		}
		if(typeof id_reclamo != "undefined"){
			reclamoId = id_reclamo;
		}
		
		if(typeof facturas_seguros_id != "undefined")
		{
			facturassegurosId=facturas_seguros_id;
		}
		
		//inicializar jqgrid
		grid_obj.jqGrid({
		   	url: phost() + url,
		   	datatype: "json",
		   	colNames:[
					'Nombre del archivo',
					'Tipo',
					'Fecha creación',
					'Usuario',
					'',
					'',
					'',
					'',
	        '',
					''
			],
		   	colModel:[
			   	{name:'No. Accion personal', index:'no_accion', width: 40},
				{name:'Tipo_archivo', index:'tipo', width: 40},
					{name:'Tipo de accion personal', index:'accionable_type', width:40},
					{name:'Colaborador', index:'colaborador_id', width:40},
					{name:'link', index:'link', width:25, sortable:false, resizable:false, hidedlg:true, align:"center", hidden: ocultar_opciones, search:false},
					{name:'options', index:'options', hidedlg:true, hidden: true},
					{name:'archivo_ruta', index:'archivo_ruta', hidedlg:true, hidden: true},
					{name:'archivo_nombre', index:'archivo_nombre', hidedlg:true, hidden: true},
	                {name:'nombre_documento', index:'nombre_documento', hidedlg:true, hidden: true},
					{name:'accionable_id', index:'accionable_id', hidedlg:true, hidden: true},
		   	],
			mtype: "POST",
		   	postData: {
		   		erptkn: tkn,
		   		contrato_id: contratoid,
		   		factura_id: facturaventaid,
		   		pedido_id: pedidoid,
		   		facturacompra_id: facturacompra_id,
		   		ordencompra_id: moduloId,
		   		colaborador_id: colaboradorid,
		   		equipo_id: equipoid,
          intereses_asegurados_id_persona:interesesaseguradosidpersona,
          intereses_asegurados_id_vehiculo:interesesaseguradosidvehiculo,
          intereses_asegurados_id_casco_maritimo:interesesaseguradosidmaritimo,
          intereses_asegurados_id_casco_aereo:interesesaseguradosidaereo,
          intereses_asegurados_id_proyecto_actividad:interesesaseguradosidproyecto,
          intereses_asegurados_id_carga:interesesaseguradosidcarga,
          intereses_asegurados_id_articulo:interesesaseguradosidarticulo,
          intereses_asegurados_id_ubicacion:interesesaseguradosidubicacion,
          id_cliente:clientes,
          cotizacion_id:cotizaciones,
          ordenes_ventas_id:ordenesventas,
					orden_alquiler_id: spordenalquilerid,
          proveedores_id:proveedoresid,
          item_id:itemsid,
          caja_id:cajasId,
		  solicitud_id:solicitudId,
		  poliza_id:polizaId,
		  factura_seguro:facturassegurosId,
		  endoso_id:endosoId,
		  reclamo_id:reclamoId,

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
			search:true,
		    sortname: 'archivo_nombre',
		    sortorder: "DESC",
		    beforeProcessing: function(data, status, xhr){
		    	//Check Session
				if( $.isEmptyObject(data.session) == false){
					window.location = phost() + "login?expired";
				}
		    },
		    loadBeforeSend: function () {
				$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
				grid_obj.find('input[type="text"]').css("width", "95% !important");
				$(this).closest("div.ui-jqgrid-view").find("#tablaDocumentosGrid_cb, #jqgh_tablaDocumentosGrid_link").css("text-align", "center");
			},
		    beforeRequest: function(data, status, xhr){},
			loadComplete: function(data){
				//check if isset data
				$('#'+ grid_id +'NoRecords').hide();
					$('#gbox_'+ grid_id).show();
			},
			onSelectRow: function(id){
				$(this).find('tr#'+ id).removeClass('ui-state-highlight');
			},
		});

		//Al redimensionar ventana
		$(window).resizeEnd(function() {
			tablaAccionPersonal.redimensionar();
		});
		
		grid_obj.jqGrid('navGrid',grid_id,{del:false,add:false,edit:false,search:true});
		grid_obj.jqGrid('filterToolbar',{searchOnEnter : false});
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
                        var nombre_documento = rowINFO["nombre_documento"];
                        if(nombre_documento == ''){
                            nombre_documento = rowINFO["archivo_nombre"];
                        }
	 	    //Init Modal
		    opcionesModal.find('.modal-title').empty().append('Opciones: '+ nombre_documento +'');
		    opcionesModal.find('.modal-body').empty().append(options);
		    opcionesModal.find('.modal-footer').empty();
		    opcionesModal.modal('show');


		});

		//Ver Detalle
		$('#optionsModal, #opcionesModal').on("click", botones.detalle, function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			//Cerrar modal de opciones
			$('#optionsModal').modal('hide');
			var evaluacion_id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(evaluacion_id);

			var no_accion = rowINFO["No. Accion personal"];
			var archivo_nombre = rowINFO["archivo_nombre"];
                        var archivo_ruta = rowINFO["archivo_ruta"];
                        var fileurl = phost() + archivo_ruta +'/'+ archivo_nombre;
			console.log(fileurl);
                        window.open(fileurl)

			//Verificar si existe o no variable
			//colaborador_id

		});

		//Boton subir documentos
	    $("#subir_documento").on('click', function (e) {
		    e.preventDefault();
		    e.returnValue=false;
		    e.stopPropagation();
		    $('#opcionesModal').modal('hide');

		    var id = $("#idPoliza").val();
		    console.log(id);

		    //Inicializar opciones del Modal
		    $('#documentosModal').modal({
		            backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
		            show: false
		    });
		    
		    $('#documentosModal').modal('show');
		    $('#id_poliza').val(id);
	    });

	    var counter = 2;
	    $('#del_file_poliza').hide();
	    $('#add_file_poliza').click(function(){
	            
	        $('#file_tools_poliza').before('<div class="file_upload_poliza row" id="fpoliza'+counter+'"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"><br><br></div>');
	        $('#del_file_poliza').fadeIn(0);
	    counter++;
	    });
	    $('#del_file_poliza').click(function(){
	        if(counter == 3){
	            $('#del_file_poliza').hide();
	        }   
	        counter--;
	        $('#fpoliza'+counter).remove();
	    });



                $('#optionsModal, #opcionesModal').on("click", botones.editNombre, function(e){
                        e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			//Cerrar modal de opciones
			$('#optionsModal').modal('hide');
			var document_id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(document_id);
                        var nombre_documento = rowINFO["nombre_documento"];
                        if(nombre_documento == ''){
                            nombre_documento = rowINFO["archivo_nombre"];
                        }
                        editNombreModal.find('#nombre_actual').val(nombre_documento);
                        editNombreModal.find('#documen_id').val(document_id);
						
						if(typeof data.numero != "undefined")
						{
							 editNombreModal.find('#interes_id').val(data.numero);
						}
						if(typeof data.codigo != "undefined")
						{
							 editNombreModal.find('#interes_id').val(data.codigo);
						}
						else
						{
							editNombreModal.find('#interes_id').val(numero);
						}
                       
                        editNombreModal.modal('show');

			//Verificar si existe o no variable
			//colaborador_id

		});
                $( "#formEditNombre" ).submit(function( event ) {
                    event.preventDefault();
                    if($('#nombre_document').val() == ''){
    			toastr.warning('Ingrese un nombre.');
    			return false;
                    }else{
                        $.ajax({
                            url: phost() +"documentos/ajax_actualizar",
                            type:"POST",
                            data:$( "#formEditNombre" ).serialize(),
                            dataType:"json",
                            success: function(data){
                                if(data.estado === 200)
                                {
                                    toastr.success(data.mensaje);
                                    recargar();
                                    editNombreModal.modal('hide');
                                   $('#nombre_document').val("")
                                }else{
                                    toastr.error(data.mensaje);
                                }
                            }

                        });
                    }
                });
		//Ver Detalle
		$(opcionesModal).on("click", botones.detalle, function(e){
			/*e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			//Cerrar modal de opciones
			opcionesModal.modal('hide');

			var formulario = $(this).attr("data-formulario");
			var accion_id = $(this).attr("data-accion-id");
			var modulo_name_id = formulario.replace(/(es|s)$/g, '') + '_id';

			//Before using local storage, check browser support for localStorage and sessionStorage
			if(typeof(Storage) !== "undefined") {

				//Grabar id de la accion
				localStorage.setItem(modulo_name_id, accion_id);
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
					//console.log( formulario.replace(/(es|s)$/g, '') );
				}

			}else{
				window.location = phost() + 'accion_personal/crear/' + formulario;
			}*/
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

                     //console.log(fileurl);
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
		
		//Boton de Exportar contacto
		$(botones.exportar).on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();                        
			if($('#id_tab_documentos').is(':visible') == true){			
				//Exportar Seleccionados del jQgrid
				var ids = [];
				ids = grid_obj.jqGrid('getGridParam','selarrrow');
				//Verificar si hay seleccionados
				if(ids.length > 0){
				console.log(ids);	
					$('#ids_documentos').val(ids);
			        $('form#exportarDocumentos').submit();
			        $('body').trigger('click');
					
					if($("#cb_"+grid_id).is(':checked')) {
						$("#cb_"+grid_id).trigger('click');
					}
					else
					{
						$("#cb_"+grid_id).trigger('click');
						$("#cb_"+grid_id).trigger('click');
					}
				}
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
				tipo:'',
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
