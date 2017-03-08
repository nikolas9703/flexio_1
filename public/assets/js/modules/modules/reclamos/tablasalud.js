var tablaReclamosSalud = (function () {

    var unico = $("#detalleunico").val();
    var id_interes = $("#reclamoidinteres").val();
    if (id_interes == "" || id_interes == undefined) { id_interes = 0; }
    var tablaUrl = phost() + 'reclamos/ajax_listar_salud';
    var gridId = "tablaReclamosSalud";
    var gridObj = $("#tablaReclamosSalud");
    var opcionesModal = $('#verModalIntereses');
    var grid_obj = $("#tablaReclamosSalud");

    console.log(unico, id_interes);

    var botones = {
        opciones: ".seleccionarreclamo",
        buscar: "#modal_filtrar",
        limpiar: "#modal_limpiar",
        modal: "#modalSalud"
    };

    var tabla = function () {
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames: ['No. Certificado', 'Nombre', 'No. Reclamo', 'Tipo','Hospital','Especialidad','Doctor','Detalle','Fecha','Monto','Deducible','',''],
            colModel: 
            [
                {name:'no_certificado', index:'no_certificado', width:30},
                {name:'nombre', index:'nombre', width:40},
                {name:'numero_reclamo', index:'numero_reclamo', width:40},
                {name:'tipo_salud', index:'tipo_salud', width: 40},
                {name:'hospital', index:'hospital', width:40},
                {name:'especialidad_salud', index:'especialidad_salud', width:40},
                {name:'doctor', index:'doctor', width: 40},
                {name:'detalle_salud', index:'detalle_salud', width:40},
                {name:'fecha_salud', index:'fecha_salud', width: 40},
                {name:'monto_salud', index:'monto_salud', width: 40},  
                {name:'deducible', index:'deducible', width: 40},              
                {name:'options', index:'options', width:50, sortable:false, resizable:false, hidedlg:true, align:"center"},
                {name:'link', index:'link', hidedlg:true, hidden: true}
            ],
            postData: {
                detalle_unico: unico,
                erptkn: tkn,
                id_interes: id_interes,
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
            sortname: "id",
            sortorder: "ASC",

            beforeProcessing: function (data, status, xhr) {
                //Check Session
                if ($.isEmptyObject(data.session) == false) {
                    window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function () {//propiedadesGrid_cb
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find("#tablaReclamosSaludGrid_cb, #jqgh_tablaReclamosSaludGrid_link").css("text-align", "center");
            },
            beforeRequest: function (data, status, xhr) {},
            loadComplete: function (data, status, xhr) {


                //---------
                // Cargar plugin jquery Sticky Objects
                //----------
                //add class to headers
                gridObj.jqGrid('setGridWidth', $('.tabladetalle_salud').width());
                console.log($('.intereses_modal').width());
                gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeaderSalud");
                //floating headers
                 var DataGrid = gridObj;

                 //sets the grid size initially
                 gridObj.jqGrid('setGridWidth', parseInt(grid_obj.width()));    
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
            tablaReclamosSalud.redimencionar_tabla();
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


    $(".agregar_detalle_salud").click(function () {
        var tipo = $("#tipo_salud").val();
        var hospital = $("#hospital").val();
        var doctor = $("#doctor").val();
        var especialidad = $("#especialidad_salud").val();
        var detalle = $("#detalle_salud").val();
        var fecha = $("#fecha_salud").val();
        var monto = $("#monto").val();
        var unico = $("#detalle_unico").val();

        var val = {campos: {tipo_salud: tipo, hospital: hospital, doctor: doctor, especialidad_salud:especialidad, detalle_salud: detalle, fecha_salud: fecha, monto_salud: monto, detalle_unico: unico}};
        var agrega = agregarDetalleSalud.agregar(val);
        agrega.done(function (response) {
            $("#tipo_salud").val("");
            $("#hospital").val("");
            $("#doctor").val("");
            $("#especialidad_salud").val("");
            $("#detalle_salud").val("");
            $("#fecha_salud").val("");
            $("#monto").val("");

            recargar();
        });

    });


    var recargar = function () {
        var id_interes = $("#reclamoidinteres").val();
        if (id_interes == "" || id_interes == undefined) { id_interes = 0; }
        //Reload Grid
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                detalle_unico: unico,
                id_interes: id_interes,
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
        var id_interes = $('#reclamoidinteres').val();
        if (id_interes == "") { id_interes = 0; }
        var no_liquidacion = $('#modal_liquidacion_carga').val();
        var medio = $('#modal_medio_carga').val();

        if (id_poliza != "" || no_liquidacion != "" || medio != "" )
        {
            //Reload Grid
            grid_obj.setGridParam({
                url: phost() + 'reclamos/ajax_listar_salud',
                datatype: "json",
                postData: {
                    numero: '',
                    no_liquidacion: no_liquidacion,
                    fecha_despacho: '',
                    fecha_arribo: '',
                    medio_transporte: medio,
                    valor: '',
                    origen: '',
                    destino: '',
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
        $('#modal_liquidacion_carga').val('');
        $('#modal_medio_carga').val('');
        recargar();
    };


    return{
        init: function () {
            tabla();
            eventos();
            //redimencionar_tabla();           
        },
        recargar: function () {
            var id_interes = $("#reclamoidinteres").val();
            if (id_interes == "") { id_interes = 0; }
            //Reload Grid
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    numero: '',
                    no_liquidacion: '',
                    fecha_despacho: '',
                    fecha_arribo: '',
                    medio_transporte: '',
                    valor: '',
                    origen: '',
                    destino: '',
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
    tablaReclamosSalud.init();
    $("#jqgh_tablaReclamosSaludGrid_cb span").removeClass("s-ico");
    $('#jqgh_tablaReclamosSaludGrid_options span').removeClass("s-ico");
});



    var agregarDetalleSalud = (function () {
        return {
            agregar: function (parametros) {
                return $.post(phost() + "reclamos/agregar_detalle_salud", $.extend({
                    erptkn: tkn
                }, parametros));
            }
        };
    })();

