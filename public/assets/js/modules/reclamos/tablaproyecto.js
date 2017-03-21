var tablaReclamosProyecto = (function () {

      var unico = $("#detalleunico").val();
      var id_poliza = $("#poliza_seleccionado").val();
      if (id_poliza == "") { id_poliza = 0; }
      var tablaUrl = phost() + 'reclamos/ajax_listar_proyecto';
      var gridId = "tablaReclamosProyecto";
      var gridObj = $('#verModalIntereses').find("#tablaReclamosProyecto");
      var opcionesModal = $('#verModalIntereses');
      var grid_obj = $("#tablaReclamosProyecto");
      var documentosModal = $('#documentosModal');


      var botones = {
         opciones: ".seleccionarpoliza",
         buscar: "#modal_filtrar",
         limpiar: "#modal_limpiar",
         modal: "#modalInteres"
     };

		var tabla = function () {
			gridObj.jqGrid({
				url: tablaUrl,
				mtype: "POST",
				datatype: "json",
				colNames: ['No. interés', 'Nombre del proyecto', 'No. Orden', 'Ubicación','Fecha de inclusión','Fecha de exclusión', 'Estado',''],
				colModel: 
                    [
                    {name:'numero', index:'numero', width:30},
                    {name:'nombre_proyecto', index:'nombre_proyecto', width:40},
                    {name:'no_orden', index:'no_orden', width:40},
                    {name:'ubicacion', index:'ubicacion', width: 40},
                    {name:'fecha_inclusion', index:'created_at', width: 40},
                    {name:'fecha_exclusion', index:'created_at', width: 40},
                    {name:'estado', index:'estado', width: 40},
                    {name:'options', index:'options', width:50, sortable:false, resizable:false, hidedlg:true, align:"center"}
                    ],
				postData: {
					detalle_unico: unico,
					desde: vista,
					erptkn: tkn,
                    id_poliza: id_poliza,
				},
				height: "auto",
				autowidth: true,
				rowList: [10, 20, 50, 100],
				rowNum: 10,
				page: 1,
				pager: "#" + gridId + "Pager",
				loadtext: '<p>Cargando...</p>',
				hoverrows: false,
				viewrecords: true,
				refresh: true,
				gridview: true,
				sortname: "estado",
				sortorder: "ASC",

				beforeProcessing: function (data, status, xhr) {
                //Check Session
                if ($.isEmptyObject(data.session) == false) {
                	window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function () {//propiedadesGrid_cb

            	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
            	$(this).closest("div.ui-jqgrid-view").find("#tablaReclamosProyectoGrid_cb, #jqgh_tablaReclamosProyectoGrid_link").css("text-align", "center");
            },
            beforeRequest: function (data, status, xhr) {},
            loadComplete: function (data, status, xhr) {

                //---------
                // Cargar plugin jquery Sticky Objects
                //----------
                //add class to headers

                gridObj.jqGrid('setGridWidth', $(".modal-lg").width()-70);
              
                gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeaderProyecto");
                //floating headers
                
                //Arreglar tamaño de TD de los checkboxes
                //FALTA ADAPTAR EL CODIGO PARA QUE LOS CHECKBOX SE VEAN BIEN
                $('#jqgh_' + gridId + "_cb").css("text-align", "center");
                $('.s-ico').removeAttr('style');
            },
            onSelectRow: function (id) {
            	$(this).find('tr#' + id).removeClass('ui-state-highlight');
            }
        });
        //Al redimensionar ventana
        $(window).resizeEnd(function () {
            tablaReclamosProyecto.redimencionar_tabla();
        });
    };



    var eventos = function () {

        gridObj.on("click", botones.opciones, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            var id = $(this).attr("data-id");
            var idpoliza = $(this).attr("data-poliza");
            var certificado = $(this).attr("data-certificado");
            var rowINFO = $.extend({}, gridObj.getRowData(id));
            var options = rowINFO.link;
            formularioCrear.getCoberturasPolizaInfo(idpoliza, id);
            formularioCrear.interesesPoliza( idpoliza,"modal", id);
            opcionesModal.modal('hide');
        }); 

        $(botones.modal).on("click", function (e) {
            
            if (id_tipo_poliza == 2) {
                if (vista == "crear" || ( vista == "editar" && permiso_editar == 1 && typeof formularioCrear.reclamoInfo.estado != "undefined" && formularioCrear.reclamoInfo.estado != "Cerrado" && formularioCrear.reclamoInfo.estado != "Anulado") ) {
                    recargar();
                    $('#verModalIntereses').modal('show');                
                    
                    //Boton de Buscar Colaborador
                    $(botones.buscar).on("click", function (e) {
                        e.preventDefault();
                        e.returnValue = false;
                        e.stopPropagation();
                        buscarReclamos();
                    });
                    //Boton de Reiniciar jQgrid
                    $(botones.limpiar).on("click", function (e) {
                        e.preventDefault();
                        e.returnValue = false;
                        e.stopPropagation();
                        recargar();
                        limpiarCampos();
                    });

                    gridObj.on("click", botones.opciones, function (e) {
                        e.preventDefault();
                        e.returnValue = false;
                        e.stopPropagation();
                        var id = $(this).attr("data-id");
                        var idpoliza = $(this).attr("data-poliza");
                        var certificado = $(this).attr("data-certificado");
                        var rowINFO = $.extend({}, gridObj.getRowData(id));
                        var options = rowINFO.link;

                        formularioCrear.interesesPoliza( idpoliza,"modal", id);
                        //Init Modal
                        console.log(rowINFO.numero);
                        opcionesModal.modal('hide');
                    });
                }
            }           
                
        }); 
    };
    
    	
	//Fin funciones para botones del grid de maritimo


	var recargar = function () {
        var id_poliza = $("#poliza_seleccionado").val();
        if (id_poliza == "") { id_poliza = 0; }
        //Reload Grid
        gridObj.setGridParam({
        	url: tablaUrl,
        	datatype: "json",
        	postData: {
        		numero: '',
        		nombre_proyecto: '',
        		no_orden: '',
        		ubicacion: '',
        		fecha_inclusion: '',
        		estado: '',
                id_poliza: id_poliza,
        		erptkn: tkn
        	}
        }).trigger('reloadGrid');

    };
    var redimencionar_tabla = function () {
    	$(window).resizeEnd(function () {
    		$(".ui-jqgrid").each(function () {
    			var w = parseInt($(this).parent().width()) - 6;
    			var tmpId = $(this).attr("id");
    			var gId = tmpId.replace("gbox_", "");
    			$("#" + gId).setGridWidth(w);
    		});
    	});
    };

    //Buscar cargo en jQgrid
    var buscarReclamos = function () {
        var id_poliza = $('#poliza_seleccionado').val();
        if (id_poliza == "") { id_poliza = 0; }
        var nombre = $('#modal_nombre_proyecto').val();
        var orden = $('#modal_orden_proyecto').val();
        var ubicacion = $('#modal_ubicacion_proyecto').val();

        if (id_poliza != "" || nombre != "" || orden != "" || ubicacion != "" )
        {
            //Reload Grid
            grid_obj.setGridParam({
                url: phost() + 'reclamos/ajax_listar_proyecto',
                datatype: "json",
                postData: {
                    numero: '',
                    nombre_proyecto: nombre,
                    no_orden: orden,
                    ubicacion: ubicacion,
                    fecha_inclusion: '',
                    estado: '',
                    id_poliza: id_poliza,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }
    };
    //Limpiar campos de busqueda
    var limpiarCampos = function () {
        $('#modal_nombre_proyecto').val('');
        $('#modal_orden_proyecto').val('');
        $('#modal_ubicacion_proyecto').val('');
        recargar();
    };

    return{
    	init: function () {
    		tabla();
    		eventos();
             //gridObj.jqGrid('setGridWidth', "auto");
            redimencionar_tabla();
        },
        recargar: function () {
            var id_poliza = $("#poliza_seleccionado").val();
            if (id_poliza == "") { id_poliza = 0; }
            //Reload Grid
            gridObj.setGridParam({
            	url: tablaUrl,
            	datatype: "json",
            	postData: {
            		numero: '',
            		nombre_proyecto: '',
            		no_orden: '',
            		ubicacion: '',
            		fecha_inclusion: '',
            		estado: '',
                    id_poliza: id_poliza,
            		erptkn: tkn
            	}
            }).trigger('reloadGrid');

        }
    };

})();

$(function () {
	tablaReclamosProyecto.init();
	$("#jqgh_tablaReclamosProyectoGrid_cb span").removeClass("s-ico");
	$('#jqgh_tablaReclamosProyectoGrid_options span').removeClass("s-ico");
});


