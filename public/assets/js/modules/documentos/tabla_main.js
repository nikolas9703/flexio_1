//Tabla Accion de Personal
var tablaAccionPersonal = (function () {

	var url = 'documentos/ajax-listar-main';
	var grid_id = "tablaDocumentosMainGrid";
	var grid_obj = $("#tablaDocumentosMainGrid");
	var opcionesModal = $('#opcionesModal, #optionsModal');
	var editNombreModal = $('#documentosModalEditar');
        var actualizarModal = $('#actualizarModal');

	var botones = {
		opciones: ".viewOptions",
		ver: ".verDocumento",
		buscar: "#searchBtn",
		limpiar: "#clearBtn",
		descargar: ".descargarAdjuntoBtn",
		detalle: ".verAdjuntoBtn",
		editNombre: ".editnombreBtn",
		ExportarDocCompras: "#ExportarBtnDocCompras",
		descargarDocCompras: ".descargarDocumComprasIndivBtn",
		descargarZipDocCompras: "#DescargarZipBtnDocCompras",
		cambiarEstadoDocumentos: ".cambiarEstadoDocumentos",
		cambiarEnExpediente: ".cambiarEnExpediente",
		documentDeleting: ".documentDeleting"
	};

	var tabla = function () {

        //inicializar jqgrid
		grid_obj.jqGrid({
			url: phost() + url,
			datatype: "json",
			colNames: ['Relacionado a','Tipo de documento','Nombre de documento','Centro contable','Fecha de carga','Fecha de documento','Usuario','Tama&ntilde;o','Estado','','','','',''],
			colModel: [
                {name: 'relacionado_a',index: 'relacionado_a',width: 40},
                {name: 'tipos_documento',index: 'tipos_documento',width: 40,sortable: false},
                {name: 'nombre_documento',index: 'nombre_documento',width: 40,sortable: false},
                {name: 'centro_contable',index: 'centro_contable',width: 40,sortable: false},
                {name: 'fecha_carga',index: 'fecha_carga',width: 40,sortable: false},
                {name: 'fecha_documento',index: 'fecha_documento',width: 40,sortable: false},
                {name: 'usuario',index: 'usuario',width: 40,sortable: false},
                {name: 'tamanio',index: 'tamanio',width: 40,sortable: false},
                {name: 'estado',index: 'estado',width: 40,sortable: false},
                {name: 'link',index: 'link',width: 25,sortable: false, align:"center"},
                //hiddens
                {name: 'options',index: 'options',hidedlg: true,hidden: true},
                {name: 'archivo_ruta',index: 'archivo_ruta',hidedlg: true,hidden: true},
                {name: 'archivo_nombre',index: 'archivo_nombre',hidedlg: true,hidden: true},
                {name: 'nombre_documento',index: 'nombre_documento',hidedlg: true,hidden: true}
            ],
			mtype: "POST",
			postData: {
				erptkn: tkn,
				campo: typeof window.campo !== 'undefined' ? window.campo : {}
			},
			height: "auto",
			autowidth: true,
			rowList: [10, 20, 50, 100],
			rowNum: 10,
			page: 1,
			pager: "#" + grid_id + "Pager",
			loadtext: '<p>Cargando...</p>',
			hoverrows: false,
			viewrecords: true,
			refresh: true,
			gridview: true,
			multiselect: true,
			sortname: 'created_at',
			sortorder: "DESC",
			beforeProcessing: function (data, status, xhr) {
				//Check Session
				if ($.isEmptyObject(data.session) == false) {
					window.location = phost() + "login?expired";
				}
			},
			loadBeforeSend: function () {},
			beforeRequest: function (data, status, xhr) {},
			loadComplete: function (data) {

				//check if isset data
				if (data['total'] == 0) {
					$('#gbox_' + grid_id).hide();
					$('#' + grid_id + 'NoRecords').empty().append('No se encontraron datos de Documentos.').css({
						"color": "#868686",
						"padding": "30px 0 0"
					}).show();
				} else {
					$('#' + grid_id + 'NoRecords').hide();
					$('#gbox_' + grid_id).show();
				}
			},
			onSelectRow: function (id) {
				$(this).find('tr#' + id).removeClass('ui-state-highlight');
			},
		});

		//Al redimensionar ventana
		$(window).resizeEnd(function () {
			tablaAccionPersonal.redimensionar();
		});
	};

	//Inicializar Eventos de Botones
	var eventos = function () {

		//Boton de Opciones
		grid_obj.on("click", botones.opciones, function (e) {
			e.preventDefault();
			e.returnValue = false;
			e.stopPropagation();

			var id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(id);
			var options = rowINFO["options"];
			var nombre_documento = rowINFO["nombre_documento"];
			if (nombre_documento == '') {
				nombre_documento = rowINFO["archivo_nombre"];
			}
			//Init Modal
			opcionesModal.find('.modal-title').empty().append('Opciones: ' + nombre_documento + '');
			opcionesModal.find('.modal-body').empty().append(options);
			opcionesModal.find('.modal-footer').empty();
			opcionesModal.modal('show');
		});


		$(botones.ExportarDocCompras).on("click", function(e){
				e.preventDefault();
				e.returnValue=false;
				e.stopPropagation();

				//Exportar Seleccionados del jQgrid
				var documentos = [];

				documentos = grid_obj.jqGrid('getGridParam','selarrrow');

				var obj = new Object();
				obj.count = documentos.length;

				if(obj.count) {

					obj.items = new Array();

					for(elem in documentos) {
						//console.log(proyectos[elem]);
						var documento = grid_obj.getRowData(documentos[elem]);

						//Remove objects from associative array
						delete documento['id'];
						delete documento['link'];
						delete documento['options'];
						delete documento['archivo_ruta'];

						//Push to array
						obj.items.push(documento);
					}

					var json = JSON.stringify(obj);
					var csvUrl = JSONToCSVConvertor(json);
					var filename = 'documento_compras_'+ Date.now() +'.csv';

					//Ejecutar funcion para descargar archivo
					downloadURL(csvUrl, filename);

					$('body').trigger('click');
				}
		});

		//Cambiar estado documentos
    $(opcionesModal).on("click", botones.cambiarEstadoDocumentos, function(e){
    e.preventDefault();
    e.returnValue=false;
    e.stopPropagation();
    //Cerrar modal de opciones
    var documento_id = $(this).attr("data-id");
    var html = '';
    html+= '<a href="#" id="por_enviar" data-id="'+ documento_id +'" class="btn btn-block btn-outline btn-default cambiarEstado" style="background:white!important; color:#f8ac59!important;">Por enviar</a>';
    html+= '<a href="#" id="no_se_envia" data-id="'+ documento_id +'" class="btn btn-block btn-outline btn-default cambiarEstado" style="background:white!important; color:black!important;">No se envía</a>';
    html+= '<a href="#" id="enviado" data-id="'+ documento_id +'" class="btn btn-block btn-outline btn-primary cambiarEstado" style="background:white!important; color:#1ab394!important; border-color:#1ab394!important;">Enviado</a>';
    //opcionesModal.modal('hide');

    opcionesModal.find('.modal-title').empty().append('Cambiar estado');
    opcionesModal.find('.modal-body').empty().append(html);
    opcionesModal.find('.modal-footer').empty();
    opcionesModal.modal('show');
    });

    $(opcionesModal).on("click", "#por_enviar", function(e){
    e.preventDefault();
    e.returnValue=false;
    e.stopPropagation();
    var documento_id = $(e.currentTarget).attr("data-id");
        var tipo_estado = 'por_enviar';
        cambiar_estado(tipo_estado, documento_id);
        setTimeout(function () {
            opcionesModal.modal('hide');
        }, 500);
    });

    $(opcionesModal).on("click", "#no_se_envia", function(e){
    e.preventDefault();
    e.returnValue=false;
    e.stopPropagation();
    var documento_id = $(e.currentTarget).attr("data-id");
        var tipo_estado = 'no_se_envia';
        cambiar_estado(tipo_estado, documento_id);
        setTimeout(function () {
            opcionesModal.modal('hide');
        }, 500);
    });

    $(opcionesModal).on("click", "#enviado", function(e){
    e.preventDefault();
    e.returnValue=false;
    e.stopPropagation();
    var documento_id = $(e.currentTarget).attr("data-id");
        var tipo_estado = 'enviado';
        cambiar_estado(tipo_estado, documento_id);
        setTimeout(function () {
            opcionesModal.modal('hide');
        }, 500);
    });

     $(grid_obj).on("click", botones.cambiarEstadoDocumentos, function(e){
    e.preventDefault();
    e.returnValue=false;
    e.stopPropagation();
    var documento_id = $(this).attr("data-id");
    var html = '';
    html+= '<a href="#" id="por_enviar" data-id="'+ documento_id +'" class="btn btn-block btn-outline btn-default cambiarEstado" style="background:white!important; color:#f8ac59!important;">Por enviar</a>';
    html+= '<a href="#" id="no_se_envia" data-id="'+ documento_id +'" class="btn btn-block btn-outline btn-default cambiarEstado" style="background:white!important; color:black!important;">No se envía</a>';
    html+= '<a href="#" id="enviado" data-id="'+ documento_id +'" class="btn btn-block btn-outline btn-primary cambiarEstado" style="background:white!important; color:#1ab394!important; border-color:#1ab394!important;">Enviado</a>';
    //opcionesModal.modal('hide');

    opcionesModal.find('.modal-title').empty().append('Cambiar estado');
    opcionesModal.find('.modal-body').empty().append(html);
    opcionesModal.find('.modal-footer').empty();
    opcionesModal.modal('show');
     });

    //Cambiar En Expediente
    $(opcionesModal).on("click", botones.cambiarEnExpediente, function(e){
        var documento_id = $(this).attr("data-id");
        cambiar_enExpediente(documento_id);
        setTimeout(function () {
            opcionesModal.modal('hide');
        }, 500);
    });

	//document soft deleting
    $(opcionesModal).on("click", botones.documentDeleting, function(e){
        var document_id = $(this).attr("data-id");
        ajaxDocumentDeleting(document_id);
    });

    //Actualizar documento
    $(opcionesModal).on("click", ".actualizarDocumento", function(e){
    e.preventDefault();
    e.returnValue=false;
    e.stopPropagation();
    var documento_id = $(this).attr("data-id");
    var html = '';
    html+= '<form method="POST" id="actualizarDocumentos" action="'+ phost() +'documentos/actualizar-documento" enctype="multipart/form-data" autocomplete="off"><div id="dropTarget" class="drop p-lg text-center" style="border: 2px dotted #ccc; text-">';
    html+= '<span class="btn btn-outline btn-default align-center {{fileClassBtn}} fileinput-button">';
    html+= '<span ng-bind-html="fileBtn">Seleccionar</span>';
    html+= '<input id="documento" type="file" name="documentos" class="fileinput-button"></span>';
    html+= '<input type="hidden" name="erptkn" value="'+ tkn +'" style="display:none">';
    html+= '<input id="documento_id" type="hidden" name="documento_id" value="'+ documento_id +'">';
    html+= '<b>o Arrastre el archivo aqui</b></div></form>';
    var footer = '';
    footer+= '<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6"><input type="submit" id="guardarDocumentosBtn" class="btn btn-primary btn-block" value="Subir"/></div>'
    footer+= '<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6"><input type="button" id="cancelarDocumentosBtn" class="btn btn-default btn-block" value="Cancelar"/></div>'

    opcionesModal.modal('hide');
    actualizarModal.find('.modal-title').empty().append('Actualizar documento');
    actualizarModal.find('.modal-body').empty().append(html);
    actualizarModal.find('.modal-footer').empty().append(footer);
    actualizarModal.modal('show');
    });

    $(actualizarModal).on("click", "#guardarDocumentosBtn", function(e){
        e.preventDefault();
        e.returnValue=false;
	e.stopPropagation();
        setTimeout(function () {
            $('form#actualizarDocumentos').submit();
            $('body').trigger('click');
        }, 500);
    });

    $(actualizarModal).on("click", "#cancelarDocumentosBtn", function(e){
        e.preventDefault();
        e.returnValue=false;
	e.stopPropagation();
    actualizarModal.modal('hide');
    });

		opcionesModal.on("click", botones.descargarDocCompras, function(e){

                e.preventDefault();
                e.returnValue=false;
                e.stopPropagation();

                var documento_id = $(this).attr("data-id");

                //Descargar archivo
                descargar_documento(documento_id);

                        //Ocultar modal
                opcionesModal.modal('hide');
		});

		$(botones.descargarZipDocCompras).on("click", function(e){


			e.preventDefault();
			e.returnValue = false;
			e.stopPropagation();



					//Exportar Seleccionados del jQgrid
					var ids = [];

					ids =  grid_obj.jqGrid('getGridParam', 'selarrrow');

					//Verificar si hay seleccionados
					if (ids.length > 0) {

							$('#ids').val(ids);
							console.log(ids);
							$('form#descargarZipDocumentos').submit();
							$('body').trigger('click');
					}


			});


		//Ver Detalle
		$('#optionsModal, #opcionesModal').on("click", botones.detalle, function (e) {
			e.preventDefault();
			e.returnValue = false;
			e.stopPropagation();
			//Cerrar modal de opciones
			$('#optionsModal').modal('hide');
			var evaluacion_id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(evaluacion_id);

			var no_accion = rowINFO["No. Accion personal"];
			var archivo_nombre = rowINFO["archivo_nombre"];
			var archivo_ruta = rowINFO["archivo_ruta"];
			var fileurl = phost() + archivo_ruta + '/' + archivo_nombre;
			console.log(fileurl);
			window.open(fileurl)

			//Verificar si existe o no variable
			//colaborador_id

		});
		$('#optionsModal, #opcionesModal').on("click", botones.editNombre, function (e) {
			e.preventDefault();
			e.returnValue = false;
			e.stopPropagation();
			//Cerrar modal de opciones
			$('#optionsModal').modal('hide');
			var document_id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(document_id);
			var nombre_documento = rowINFO["nombre_documento"];
			if (nombre_documento == '') {
				nombre_documento = rowINFO["archivo_nombre"];
			}
			editNombreModal.find('#nombre_actual').val(nombre_documento);
			editNombreModal.find('#documen_id').val(document_id);

			editNombreModal.find('#interes_id').val(data.numero);
			editNombreModal.modal('show');

			//Verificar si existe o no variable
			//colaborador_id

		});
		$("#formEditNombre").submit(function (event) {
			event.preventDefault();
			if ($('#nombre_document').val() == '') {
				toastr.warning('Ingrese un nombre.');
				return false;
			} else {
				$.ajax({
					url: phost() + "documentos/ajax_actualizar",
					type: "POST",
					data: $("#formEditNombre").serialize(),
					dataType: "json",
					success: function (data) {
						if (data.estado === 200) {
							toastr.success(data.mensaje);
							recargar();
							editNombreModal.modal('hide');
							$('#nombre_document').val("")
						} else {
							toastr.error(data.mensaje);
						}
					}

				});
			}
		});
		//Ver Detalle
		$(opcionesModal).on("click", botones.detalle, function (e) {

		});

		//Boton de Descargar Evaluacion
		opcionesModal.on("click", botones.descargar, function (e) {
			e.preventDefault();
			e.returnValue = false;
			e.stopPropagation();

			var evaluacion_id = $(this).attr("data-id");
			var rowINFO = grid_obj.getRowData(evaluacion_id);

			var no_accion = rowINFO["No. Accion personal"];
			var archivo_nombre = rowINFO["archivo_nombre"];
			var archivo_ruta = rowINFO["archivo_ruta"];
			var fileurl = phost() + archivo_ruta + '/' + archivo_nombre;

			//console.log(fileurl);
			if (archivo_nombre == '' || archivo_nombre == undefined) {
				return false;
			}

			if (rowINFO["Tipo de accion personal"].match(/incapacidades/ig)) {

				var archivos = $.parseJSON(archivo_nombre);
				if (archivos.length > 1) {

					//inicializar plugin
					var zip = new JSZip();

					//recorrer arreglo de archivos y agregarlos al zip
					$.each(archivos, function (i, filename) {
						fileurl = phost() + archivo_ruta + '/' + filename;
						zip.file(filename, urlToPromise(fileurl), {
							binary: true
						});
					});

					// when everything has been downloaded, we can trigger the dl
					zip.generateAsync({
						type: "blob"
					}, function updateCallback(metadata) {
						//console.log( metadata.percent );
					}).then(function callback(blob) {
						//see FileSaver.js
						saveAs(blob, $(no_accion).text() + ".zip");
					}, function (e) {
						//console.log(e);
					});
					return false;

				} else {

					fileurl = phost() + archivo_ruta + '/' + archivos;

					//Descargar archivo
					downloadURL(fileurl, archivo_nombre);

				}

				console.log(archivos);

			} else {
				//Descargar archivo
				downloadURL(fileurl, archivo_nombre);
			}

			//Ocultar modal
			//opcionesModal.modal('hide');
		});

		//Boton de Buscar
		$('#tabla').on("click", botones.buscar, function (e) {
			e.preventDefault();
			e.returnValue = false;
			e.stopPropagation();

			buscar();
		});

		//Boton de Reiniciar jQgrid
		$('#tabla').on("click", botones.limpiar, function (e) {
			e.preventDefault();
			e.returnValue = false;
			e.stopPropagation();

			recargar();
			limpiarCampos();
		});


	};

	/**
	 * Fetch the content and return the associated promise.
	 * @param {String} url the url of the content to fetch.
	 * @return {Promise} the promise containing the data.
	 */
	var urlToPromise = function (url) {
		return new Promise(function (resolve, reject) {
			JSZipUtils.getBinaryContent(url, function (err, data) {
				if (err) {
					reject(err);
				} else {
					resolve(data);
				}
			});
		});
	};

	//Reload al jQgrid
	var recargar = function () {

		//Reload Grid
		grid_obj.setGridParam({postData:null});
		grid_obj.setGridParam({
			url: phost() + url,
			datatype: "json",
			postData: {
				campo:{},
				erptkn: tkn
			}
		}).trigger('reloadGrid');
	};

	//Buscar cargo en jQgrid
	var buscar = function () {

		var relacionado_a = $('#tabla').find('#relacionado_a').val();
		var tipo_id = $('#tabla').find('#tipo_id').val();
		var fecha_desde = $('#tabla').find('#fecha_desde').val();
		var fecha_hasta = $('#tabla').find('#fecha_hasta').val();
		var centro_contable_id = $('#tabla').find('#centro_contable_id').val();
		var subido_por = $('#tabla').find('#subido_por').val();
		var etapa = $('#tabla').find('#etapa').val();


		if (relacionado_a !== "" || tipo_id || fecha_desde !== "" || fecha_hasta !== "" || centro_contable_id || subido_por || etapa) {
			//Reload Grid
			grid_obj.setGridParam({postData:null});
			grid_obj.setGridParam({
				url: phost() + url,
				datatype: "json",
				postData: {
					campo:{
						relacionado_a:relacionado_a,
						tipo:tipo_id,
						fecha_desde:fecha_desde,
						fecha_hasta:fecha_hasta,
						centro_contable:centro_contable_id,
						subido_por:subido_por,
						etapa:etapa
					},
					erptkn: window.tkn
				}
			}).trigger('reloadGrid');
		}
	};

	//Limpiar campos de busqueda
	var limpiarCampos = function () {
		$('#buscador').find('input[type="text"]').prop("value", "");
		$('#buscador').find('select').val(' ');
		actualizar_chosen();
	};

	var actualizar_chosen = function () {
		//refresh chosen
		setTimeout(function () {
			$('#tabla').find('select').change();
		}, 50);
	};

        var cambiar_estado = function (tipo_estado, documento_id) {
        $.ajax({
            url: phost() + "documentos/ajax-cambiar-estado",
            type:"POST",
            data:{
                erptkn:tkn,
                documento_id: documento_id,
                etapa: tipo_estado
            },
            dataType:"json",
            success: function(data){
                    recargar();
                    $(".cambiarEnExpediente").css('display', 'none');
                    toastr.success("¡&Eacute;xito! Se ha cambiado correctamente el estado.");
            }

        });
        }



        var cambiar_enExpediente = function (documento_id) {
        $.ajax({
            url: phost() + "documentos/ajax-cambiar-en-expediente",
            type:"POST",
            data:{
                erptkn:tkn,
                documento_id: documento_id
            },
            dataType:"json",
            success: function(data){
                    recargar();
                    toastr.success("¡&Eacute;xito! Se ha guardado correctamente.");
            }

        });
        }

		var ajaxDocumentDeleting = function (document_id){
			$.ajax({
				url: phost() + "documentos/document-deleting",
            	type:"POST",
            	data:{erptkn:tkn, document_id: document_id},
            	dataType:"json",
            	success: function(data){
					opcionesModal.modal('hide');
                    recargar();
					var aux = {200: 'success', 500: 'error'};
                    toastr[aux[data.estado]](data.mensaje);
            	}

        	});
        };

        var descargar_documento = function (documento_id) {
        $.ajax({
            url: phost() + "documentos/ajax-descargar-documento",
            type:"POST",
            data:{
                erptkn:tkn,
                documento_id: documento_id
            },
            dataType:"json",
            success: function(data){
            var fileurl = phost() + data.file_url;
            //Descargar archivo
            downloadURL(fileurl, data.file_name);
            }

        });
        }


	return {
		init: function () {
			tabla();
			eventos();
		},
		recargar: function () {
			//reload jqgrid
			recargar();
		},
		redimensionar: function () {
			//Al redimensionar ventana
			$(".ui-jqgrid").each(function () {
				var w = parseInt($(this).parent().width()) - 6;
				var tmpId = $(this).attr("id");
				var gId = tmpId.replace("gbox_", "");
				$("#" + gId).setGridWidth(w);
			});
		}
	};
})();

tablaAccionPersonal.init();
