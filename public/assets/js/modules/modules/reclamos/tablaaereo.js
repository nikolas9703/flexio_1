var tablaReclamosAereo = (function () {

    var unico = $("#detalleunico").val();
    var id_poliza = $("#poliza_seleccionado").val();
    if (id_poliza == "") { id_poliza = 0; }
    var tablaUrl = phost() + 'reclamos/ajax_listar_aereo';
    var gridId = "tablaReclamosAereo";
    var gridObj = $('#verModalIntereses').find("#tablaReclamosAereo");
    var opcionesModal = $('#verModalIntereses');
    var formularioBuscar = '';
    var documentosModal = $('#documentosModal');
    var grid_obj = $("#tablaReclamosAereo");



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
            colNames: ['No. Interés', 'Serie', 'Marca','Modelo','Matricula','Valor','Pasajeros','Tripulacion','Fecha de inclusión','Fecha de exclusión','Estado',''],
            colModel:
            [
                {name:'numero', index:'numero', width:30},
                {name:'serie', index:'serie', width:40},
                {name:'marca', index:'marca', width:40},
                {name:'modelo', index:'modelo', width: 40},
                {name:'matricula', index:'matricula', width:40},
                {name:'valor', index:'valor', width:40},
                {name:'pasajeros', index:'pasajeros', width: 40},
                {name:'tripulacion', index:'tripulacion', width:40},
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
                $(this).closest("div.ui-jqgrid-view").find("#tablaReclamosAereoGrid_cb, #jqgh_tablaReclamosAereoGrid_link").css("text-align", "center");
            },
            beforeRequest: function (data, status, xhr) {},
            loadComplete: function (data, status, xhr) {


                //---------
                // Cargar plugin jquery Sticky Objects
                //----------
                //add class to headers
                gridObj.jqGrid('setGridWidth', $(".modal-lg").width()-70);
                gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeaderAereo");
                //floating headers
                var DataGrid = gridObj;

                 //sets the grid size initially
                 DataGrid.jqGrid('setGridWidth', parseInt(grid_obj.width())); 
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
            tablaReclamosAereo.redimencionar_tabla();
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
                marca: '',
                modelo: '',
                matricula: '',
                serie: '',
                valor: '',
                pasajeros: '',
                tripulacion: '',   
                fecha_inclusion: '',
                fecha_exclusion: '',
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
        var serie = $('#modal_serie_aereo').val();
        var modelo = $('#modal_modelo_aereo').val();
        var matricula = $('#modal_matricula_aereo').val();

        if (id_poliza != "" || serie != "" || modelo != "" || matricula != "" )
        {
            //Reload Grid
            grid_obj.setGridParam({
                url: phost() + 'reclamos/ajax_listar_aereo',
                datatype: "json",
                postData: {
                    numero: '',
                    marca: '',
                    modelo: modelo,
                    matricula: matricula,
                    serie: serie,
                    valor: '',
                    pasajeros: '',
                    tripulacion: '',   
                    fecha_inclusion: '',
                    fecha_exclusion: '',
                    estado: '',
                    id_poliza: id_poliza,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }
    };
    //Limpiar campos de busqueda
    var limpiarCampos = function () {
        $('#modal_serie_aereo').val('');
        $('#modal_modelo_aereo').val('');
        $('#modal_matricula_aereo').val('');
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
                    marca: '',
                    modelo: '',
                    matricula: '',
                    serie: '',
                    valor: '',
                    pasajeros: '',
                    tripulacion: '',   
                    fecha_inclusion: '',
                    fecha_exclusion: '',
                    estado: '',
                    id_poliza: id_poliza,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');

        }
    };

})();



$(function () {
    tablaReclamosAereo.init();
    $("#jqgh_tablaReclamosAereoGrid_cb span").removeClass("s-ico");
    $('#jqgh_tablaReclamosAereoGrid_options span').removeClass("s-ico");
});