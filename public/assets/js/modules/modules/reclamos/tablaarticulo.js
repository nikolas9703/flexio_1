var tablaReclamosArticulo = (function () {

    var unico = $("#detalleunico").val();
    var id_poliza = $("#poliza_seleccionado").val();
    if (id_poliza == "") { id_poliza = 0; }
    var tablaUrl = phost() + 'reclamos/ajax_listar_articulo';
    var gridId = "tablaReclamosArticulo";
    var gridObj = $('#verModalIntereses').find("#tablaReclamosArticulo");
    var opcionesModal = $('#verModalIntereses');
    var grid_obj = $("#tablaReclamosArticulo");

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
            colNames: ['No. Interés', 'Nombre', 'Clase de equipo', 'Marca','Modelo','Año','Serie','Condición','Valor','Fecha inclusión', 'Fecha exclusión','Estado',''],
            colModel: 
            [
                {name:'numero', index:'numero', width:30},
                {name:'nombre', index:'nombre', width:40},
                {name:'clase_equipo', index:'clase_equipo', width:40},
                {name:'marca', index:'marca', width: 40},
                {name:'modelo', index:'modelo', width:40},
                {name:'anio', index:'anio', width:40},
                {name:'numero_serie', index:'numero_serie', width: 40},
                {name:'id_condicion', index:'id_condicion', width:40},
                {name:'valor', index:'valor', width: 40},
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
                $(this).closest("div.ui-jqgrid-view").find("#tablaReclamosArticuloGrid_cb, #jqgh_tablaReclamosArticuloGrid_link").css("text-align", "center");
            },
            beforeRequest: function (data, status, xhr) {},
            loadComplete: function (data, status, xhr) {

                var DataGrid = gridObj;
                 //sets the grid size initially
                 gridObj.jqGrid('setGridWidth', $(".modal-lg").width()-70);
                 console.log($('.intereses_modal').width()); 
                //---------
                // Cargar plugin jquery Sticky Objects
                //----------
                //add class to headers
                gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeaderArticulo");
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
            tablaReclamosArticulo.redimencionar_tabla();
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

    var recargar = function () {
        //Reload Grid
        var id_poliza = $("#poliza_seleccionado").val();
        if (id_poliza == "") { id_poliza = 0; }

        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                numero: '',
                nombre: '',
                numero_serie: '',
                clase_equipo: '',
                marca: '',
                modelo: '',
                anio: '',
                valor: '',
                fecha: '',
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
        var nombre = $('#modal_nombre_articulo').val();
        var modelo = $('#modal_modelo_articulo').val();
        var serie = $('#modal_serie_articulo').val(); 

        if (id_poliza != "" || nombre != "" || modelo != "" || serie != "" )
        {
            //Reload Grid
            grid_obj.setGridParam({
                url: phost() + 'reclamos/ajax_listar_articulo',
                datatype: "json",
                postData: {
                    numero: '',
                    nombre: nombre,
                    numero_serie: serie,
                    clase_equipo: '',
                    marca: '',
                    modelo: modelo,
                    anio: '',
                    valor: '',
                    fecha: '',
                    id_poliza: id_poliza,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }
    };
    //Limpiar campos de busqueda
    var limpiarCampos = function () {
        $('#modal_nombre_articulo').val('');
        $('#modal_modelo_articulo').val('');
        $('#modal_serie_articulo').val('');
        recargar();
    };


    return{
        init: function () {
            tabla();
            eventos();
            redimencionar_tabla();
        },
        recargar: function () {
            //Reload Grid
            var id_poliza = $("#poliza_seleccionado").val();
            if (id_poliza == "") { id_poliza = 0; }
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    numero: '',
                    nombre: '',
                    numero_serie: '',
                    clase_equipo: '',
                    marca: '',
                    modelo: '',
                    anio: '',
                    valor: '',
                    fecha: '',
                    id_poliza: id_poliza,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }
    };

})();



$(function () {
    tablaReclamosArticulo.init();
    $("#jqgh_tablaReclamosArticuloGrid_cb span").removeClass("s-ico");
    $('#jqgh_tablaReclamosArticuloGrid_options span').removeClass("s-ico");
});