var tablaReclamosMaritimo = (function () {

    var unico = $("#detalleunico").val();
    var id_poliza = $("#poliza_seleccionado").val();
    if (id_poliza == "") { id_poliza = 0; }
    var tablaUrl = phost() + 'reclamos/ajax_listar_maritimo';
    var gridId = "tablaReclamosMaritimo";
    var gridObj = $('#verModalIntereses').find("#tablaReclamosMaritimo");
    var opcionesModal = $('#verModalIntereses');
    var formularioBuscar = '';
    var documentosModal = $('#documentosModal');
    var grid_obj = $("#tablaReclamosMaritimo");



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
            colNames: ['No. Interés', 'Serie', 'Nombre embarcación','Tipo','Marca','Valor','Acreedor','Fecha de inclusión','Fecha de exclusión','Estado',''],
            colModel: 
            [
                {name:'numero', index:'numero', width:30},
                {name:'serie', index:'serie', width:40},
                {name:'nombre_embarcacion', index:'nombre_embarcacion', width:40},
                {name:'tipo', index:'tipo', width: 40},
                {name:'marca', index:'matricula', width:40},
                {name:'valor', index:'valor', width:40},
                {name:'acreedor', index:'acreedor', width: 40},
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
            sortname: "pol_poliza_maritimo.estado",
            sortorder: "ASC",
            beforeProcessing: function (data, status, xhr) {
                //Check Session
                if ($.isEmptyObject(data.session) == false) {
                    window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function () {//propiedadesGrid_cb
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find("#tablaReclamosMaritimoGrid_cb, #jqgh_tablaReclamosMaritimoGrid_link").css("text-align", "center");
            },
            beforeRequest: function (data, status, xhr) {},
            loadComplete: function (data, status, xhr) {

				var DataGrid = gridObj;

                 //sets the grid size initially
				//DataGrid.jqGrid('setGridWidth', parseInt(grid_obj) - 20);
				
				
				DataGrid.jqGrid('setGridWidth', $(".modal-lg").width()-70);
				
                //---------
                // Cargar plugin jquery Sticky Objects
                //----------
                //add class to headers
                gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeaderMaritimo");
                
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
            //tablaReclamosVehiculo.redimencionar_tabla();
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
        
    };	//Fin intereses
  
    
    
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
                    nombre_embarcacion: '',
                    serie: '',
                    valor: '',
                    tipo: '',
                    marca: '',   
                    fecha_inclusion: '',
                    acreedor: '',
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
        console.log("click buscar");
        var id_poliza = $('#poliza_seleccionado').val();
        if (id_poliza == "") { id_poliza = 0; }
        var serie = $('#modal_serie_maritimo').val();
        var nombre = $('#modal_nombre_maritimo').val();
        var marca = $('#modal_marca_maritimo').val();

        if (id_poliza != "" || serie != "" || nombre != "" || marca != "" )
        {
            //Reload Grid
            grid_obj.setGridParam({
                url: phost() + 'reclamos/ajax_listar_maritimo',
                datatype: "json",
                postData: {
                    numero: '',
                    nombre_embarcacion: nombre,
                    serie: serie,
                    valor: '',
                    tipo: '',
                    marca: marca,   
                    fecha_inclusion: '',
                    acreedor: '',
                    estado: '',
                    id_poliza: id_poliza,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }
    };
    //Limpiar campos de busqueda
    var limpiarCampos = function () {
        console.log("limpiar");
        $('#modal_serie_maritimo').val('');
        $('#modal_nombre_maritimo').val('');
        $('#modal_marca_maritimo').val('');
        recargar();
    };

    return{
        init: function () {
            tabla();
            eventos();
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
                    nombre_embarcacion: '',
                    serie: '',
                    valor: '',
                    tipo: '',
                    marca: '',   
                    fecha_inclusion: '',
                    acreedor: '',
                    estado: '',
                    id_poliza: id_poliza,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');

        }
    };

})();

$(function () {
    tablaReclamosMaritimo.init();
    $("#jqgh_tablaReclamosMaritimoGrid_cb span").removeClass("s-ico");
    $('#jqgh_tablaReclamosMaritimoGrid_options span').removeClass("s-ico");
});