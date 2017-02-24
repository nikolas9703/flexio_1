//modulo clientes

var multiselect = window.location.pathname.match(/facturas_seguros/g) ? true : false;

var tablaFacturas = (function () {

    if (typeof cliente_id === 'undefined') {
        cliente_id = "";
    }
    var tablaUrl = phost() + 'facturas_seguros/ajax_listar_tabla';
    var gridId = "tablaFacturasGrid";
    var gridObj = $("#tablaFacturasGrid");
    var opcionesModal = $('#optionsModal');
    var estadosModal = $('#estadosModal');
    var formularioBuscar = '';
    var documentosModal = $('#documentosModal');

    var botones = {
        opciones: ".viewOptions",
        buscar: "#searchBtn",
        limpiar: "#clearBtn",
        exportar: "#exportarListaFacturas",
        subirDocumento: ".subirArchivoBtn",
        cambiarEstado: ".cambioindividual"
    };

    var tabla = function () {
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames: ['', 'No.Factura', 'Cliente', 'No. Poliza', 'Vigencia', 'Ramo', 'Fecha de emisión', 'Fecha de vencimiento', 'Monto', 'Saldo por cobrar', 'Sitio de Pago','Centro Contable', 'Estado',  '', ''],
            colModel: [
                {name: 'id', index: 'fac_facturas.id' , align: "center", hidedlg: true, hidden: true},
                {name: 'codigo', index: 'fac_facturas.codigo', sortable: true},
                {name: 'cliente', index: 'cli_clientes.nombre', sortable: true},
                {name: 'poliza', index: 'pol_polizas.numero', sortable: true},
                {name: 'vigencia', index: 'vigencia', sortable: false, },
                {name: 'ramo', index: 'pol_polizas.ramo',  sortable: true},
                {name: 'fecha_desde', index: 'fac_facturas.fecha_desde', sortable: true, },
                {name: 'fecha_hasta', index: 'fac_facturas.fecha_hasta', sortable: true, },
                {name: 'monto', index: 'fac_facturas.total',  sortable: false},
                {name: 'saldo', index: 'saldo',  sortable: false}, 
                {name: 'sitio_pago', index: 'pol_poliza_prima.sitio_pago',  sortable: true},       
                {name: 'centro_contable',align: "center", index: 'cen_centros.nombre',  sortable: true},
                {name: 'estado', index: 'estado',  align: "center", sortable: false, width: 190},
                {name: 'options', index: 'options'},
                {name: 'link', index: 'link',  align: "center", sortable: false, resizable: false, hidden: true, hidedlg: true},
            ],
            postData: {
                erptkn: tkn,
                cliente_id: cliente_id,
                contrato_alquiler_id:(typeof contrato_alquiler_id === 'undefined' || _.toString(window.contrato_alquiler_id) == "[object HTMLInputElement]") ? '' : contrato_alquiler_id,
                contrato_id:(typeof sp_contrato_id === 'undefined') ? '' : sp_contrato_id,
                orden_alquiler_id:(typeof sp_orden_alquiler_id === 'undefined') ? '' : sp_orden_alquiler_id,
                ms_selected: typeof(Storage) !== "undefined" ? localStorage.getItem("ms-selected") : "",
                campo: typeof window.campo !== 'undefined' ? window.campo : {}
            },
            height: "auto",
            autowidth: true,
            rowList: [10, 20, 50, 100],
            rowNum: 10,
            page: 1,
            pager: gridId + "Pager",
            loadtext: '<p>Cargando...',
            hoverrows: false,
            viewrecords: true,
            refresh: true,
            gridview: true,
            multiselect: multiselect,
            sortname: 'fac_facturas.codigo',
            sortorder: "DESC",
            beforeProcessing: function (data, status, xhr) {
                if ($.isEmptyObject(data.session) === false) {
                    window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function () {//propiedadesGrid_cb
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find("#tablaClientesGrid_cb, #jqgh_tablaClientesGrid_link").css("text-align", "center");
            },
            loadComplete: function (data, status, xhr) {

                if (gridObj.getGridParam('records') === 0) {
                    $('#gbox_' + gridId).hide();
                    $('#' + gridId + 'NoRecords').empty().append('No se encontraron Facturas.').css({"color": "#868686", "padding": "30px 0 0"}).show();
                } else {
                    $('#gbox_' + gridId).show();
                    $('#' + gridId + 'NoRecords').empty();
                }

                //Boton de Exportar Facturas
                $(botones.exportar).on("click", function (e) {

                    e.preventDefault();
                    e.returnValue = false;
                    e.stopPropagation();

                    if ($('#tabla').is(':visible') == true) {

                        //Exportar Seleccionados del jQgrid
                        var ids = [];

                        ids = gridObj.jqGrid('getGridParam', 'selarrrow');

                        //Verificar si hay seleccionados
                        if (ids.length > 0) {

                            $('#ids').val(ids);
                            console.log(ids);
                            $('form#exportarFacturas').submit();
                            $('body').trigger('click');
                        }
                    }
                });

                if(multiselect)
                {
                    //header flotante
                    gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");
                    //floating headers
                    $('#gridHeader').sticky({
                        getWidthFrom: '.ui-jqgrid-view',
                        className: 'jqgridHeader'
                    });
                }

                $('#jqgh_' + gridId + "_cb").css("text-align", "center");


            },
            onSelectRow: function (id) {
                $(this).find('tr#' + id).removeClass('ui-state-highlight');
            }
        });
    };

    var eventos = function () {
        //Bnoton de Opciones
        gridObj.on("click", botones.opciones, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var id = $(this).attr("data-id");

            var rowINFO = $.extend({}, gridObj.getRowData(id));
            var options = rowINFO.link;
            console.log(rowINFO);
            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.codigo).text() + '');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });


        gridObj.on('click', '.cambioindividual', function (e) {
            var id = $(this).attr("data-id");
            var rowINFO = $.extend({}, gridObj.getRowData(id));
            var options = rowINFO.link;

            if ($(rowINFO.estado).text() == 'Por Aprobar') {
                bodymodal = "<button data-estado-anterior='por_aprobar' data-id='"+id+"' data-estado='por_cobrar' class='btn btn-block btn-outline btn-warning individual' >Por Cobrar</button>";
                bodymodal += "<button data-estado-anterior='por_aprobar' data-id='"+id+"' data-estado='cobrado_parcial' class='btn btn-block btn-outline btn-info individual' >Cobrado Parcial</button>";
                bodymodal += "<button data-estado-anterior='por_aprobar' data-id='"+id+"' data-estado='cobrado_completo' class='btn btn-block btn-outline btn-success individual' >Cobrado Completo</button>";
                bodymodal += "<button data-estado-anterior='por_aprobar' data-id='"+id+"' data-estado='anulada' class='btn btn-block btn-outline btn-danger individual' >Anulada</button>";
            }else if ($(rowINFO.estado).text() == 'Por Cobrar') {
                bodymodal = "<button data-estado-anterior='por_cobrar' data-id='"+id+"' data-estado='por_aprobar' class='btn btn-block btn-outline btn-warning individual' >Por Aprobar</button>";
                bodymodal += "<button data-estado-anterior='por_cobrar' data-id='"+id+"' data-estado='cobrado_parcial' class='btn btn-block btn-outline btn-info individual' >Cobrado Parcial</button>";
                bodymodal += "<button data-estado-anterior='por_cobrar' data-id='"+id+"' data-estado='cobrado_completo' class='btn btn-block btn-outline btn-success individual' >Cobrado Completo</button>";
                bodymodal += "<button data-estado-anterior='por_cobrar' data-id='"+id+"' data-estado='anulada' class='btn btn-block btn-outline btn-danger individual' >Anulada</button>";
            }else if ($(rowINFO.estado).text() == 'Cobrado Parcial') {
                bodymodal = "<button data-estado-anterior='cobrado_parcial' data-id='"+id+"' data-estado='por_cobrar' class='btn btn-block btn-outline btn-warning individual' >Por Cobrar</button>";
                bodymodal += "<button data-estado-anterior='cobrado_parcial' data-id='"+id+"' data-estado='por_aprobar' class='btn btn-block btn-outline btn-info individual' >Por Aprobar</button>";
                bodymodal += "<button data-estado-anterior='cobrado_parcial' data-id='"+id+"' data-estado='cobrado_completo' class='btn btn-block btn-outline btn-success individual' >Cobrado Completo</button>";
                bodymodal += "<button data-estado-anterior='cobrado_parcial' data-id='"+id+"' data-estado='anulada' class='btn btn-block btn-outline btn-danger individual' >Anulada</button>";
            }else if ($(rowINFO.estado).text() == 'Cobrado Completo') {
                bodymodal = "<button data-estado-anterior='cobrado_completo' data-id='"+id+"' data-estado='por_cobrar' class='btn btn-block btn-outline btn-warning individual' >Por Cobrar</button>";
                bodymodal += "<button data-estado-anterior='cobrado_completo' data-id='"+id+"' data-estado='por_aprobar' class='btn btn-block btn-outline btn-info individual' >Por Aprobar</button>";
                bodymodal += "<button data-estado-anterior='cobrado_completo' data-id='"+id+"' data-estado='cobrado_parcial' class='btn btn-block btn-outline btn-success individual' >Cobrado Parcial</button>";
                bodymodal += "<button data-estado-anterior='cobrado_completo' data-id='"+id+"' data-estado='anulada' class='btn btn-block btn-outline btn-danger individual' >Anulada</button>";
            }else if ($(rowINFO.estado).text() == 'Anulada') {
                bodymodal = "<button data-estado-anterior='anulada' data-id='"+id+"' data-estado='por_cobrar' class='btn btn-block btn-outline btn-warning individual' >Por Cobrar</button>";
                bodymodal += "<button data-estado-anterior='anulada' data-id='"+id+"' data-estado='por_aprobar' class='btn btn-block btn-outline btn-info individual' >Por Aprobar</button>";
                bodymodal += "<button data-estado-anterior='anulada' data-id='"+id+"' data-estado='cobrado_parcial' class='btn btn-block btn-outline btn-default individual' >Cobrado Parcial</button>";
                bodymodal += "<button data-estado-anterior='anulada' data-id='"+id+"' data-estado='cobrado_completo' class='btn btn-block btn-outline btn-success individual' >Cobrado Completo</button>";
            }

            estadosModal.find('.modal-title').empty().append('Cambio Estado: ' + $(rowINFO.codigo).text() + '');
            estadosModal.find('.modal-body').empty().append(bodymodal);
            estadosModal.find('.modal-footer').empty();
            estadosModal.modal('show');
            var cont = 0;
            $("#estadosModal").on('click', '.individual', function (e) {
                if (cont == 0) {
                    var estado = $(this).attr("data-estado");
                    var ids = [];
                    ids.push($(this).attr("data-id"));
                    var datos = {campo: {estado: estado, ids: ids}};
                    console.log(datos);
                    var cambio = moduloFacturas.cambiarEstadoFacturas(datos);
                    var estado_anterior = $(this).attr("data-estado-anterior");                            
                    cambio.done(function (response) {
                        $("#estadosModal").modal('hide');
                        tablaFacturas.recargar();
                        toastr.success('Se ha efectuado Cambio de Estado correctamente.');
                        cambio.fail(function (response) {
                            toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
                        });
                    });
                }
                cont = 1;
            });
        });


    };
    $(botones.limpiar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        $('#buscarFacturasForm').find('input[type="text"]').prop("value", "");
        $('#buscarFacturasForm').find('select.chosen-select').prop("value", "");
        $('#buscarFacturasForm').find('select').prop("value", "");
        $(".chosen-select").trigger("chosen:updated");

        recargar();
    });
    $(botones.buscar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        var no_factura = $('#no_factura').val();
        var cliente = $('#cliente').val();
        var no_poliza = $('#no_poliza').val();
        var ramo = $('#ramo').val();
        var desde = $('#fecha1').val();
        var hasta = $('#fecha2').val();
        var etapa = $('#etapa').val();
        var sitiopag = $('#sitio_pago').val();
        var vencimientodesde = $("#fechavencimiento1").val();
        var vencimientohasta = $("#fechavencimiento2").val();
        //var vendedor = $('#vendedor').val();

        console.log(no_factura);
        console.log(cliente);
        console.log(no_poliza);
        console.log(ramo);
        console.log(desde);
        console.log(hasta);
        console.log(etapa);
        console.log(sitiopag);
        console.log(vencimientodesde);
        console.log(vencimientohasta);      


        if (no_factura !== "" || cliente !== "" || desde !== "" || hasta !== "" || etapa !== "" || no_poliza !== "" || ramo !== "" || sitio_pago !== "" || vencimientodesde !== "" || vencimientohasta !== "") {
            //Reload Grid
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    no_factura: no_factura,
                    cliente: cliente,
                    no_poliza: no_poliza,
                    ramo: ramo,
                    desde: desde,
                    hasta: hasta,
                    etapa: etapa,
                    centro_contable: '',
                    sitio_pago: sitiopag,
                    vencimientodesde: vencimientodesde,
                    vencimientohasta: vencimientohasta,
                    //vendedor: vendedor,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }


    });

     opcionesModal.on("click", botones.subirDocumento, function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        opcionesModal.modal('hide');

        var factura = $(this).attr("data-id");
         var factura_code = $(this).attr("data-codigo");
        // console.log(factura_id);
            //Inicializar opciones del Modal
            documentosModal.modal({
                backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                show: false
            });

            var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();
            scope.safeApply(function(){
                scope.campos.numero_factura = factura_code;
                scope.campos.factura_id = factura;

            });
            documentosModal.modal('show');
    });
    var recargar = function () {

        //Reload Grid
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                no_factura: '',
                cliente: '',
                no_poliza: '',
                ramo: '',
                desde: '',
                hasta: '',
                etapa: '',
                centro_contable: '',
                sitio_pago: '',
                vencimientodesde: '',
                vencimientohasta: '',
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
    return{
        init: function () {
            tabla();
            eventos();
            redimencionar_tabla();
        },
        recargar: function () {
            //Reload Grid
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    no_factura: '',
                    cliente: '',
                    no_poliza: '',
                    ramo: '',
                    desde: '',
                    hasta: '',
                    etapa: '',
                    centro_contable: '',
                    sitio_pago: '',                    
                    vencimientodesde: '',
                    vencimientohasta: '',
                    erptkn: tkn
                }
            }).trigger('reloadGrid');

        }
    };

})();


var moduloFacturas = (function(){
  return {
      cambiarEstadoFacturas : function (parametros){
        return $.post(phost() + "facturas_seguros/ajax_cambiar_estado_facturas", $.extend({
          erptkn: tkn
        },parametros));
      },
       ajaxcambiarObtenerPoliticas: function () {
            return $.ajax({
                url: "facturas_seguros/obtener_politicas",
                dataType: "json",
            });
        },
        ajaxcambiarObtenerPoliticasGeneral: function () {
            return $.ajax({
                url: "facturas_seguros/obtener_politicas_general",
                dataType: "json",
            });
        },

    };
})();


$(function () {

    tablaFacturas.init();

    $("#cambiarEstadoFacturas").click(function (e) {

        if (permiso_estado==1) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            
            if ($('#tabla').is(':visible') == true) {

                var ids = [],
                    statesValues = ["Por Aprobar", "Por Cobrar", "Cobrado Parcial", "Cobrado Completo", "Anulada"],
                    //opciones,
                    estado,
                    //style,
                    button = "",
                    states = [];
                var ids_poraprobar = 0;
                var ids_porcobrar = 0;
                var ids_cobradoparcial = 0;
                var ids_cobradocompleto = 0;
                var ids_anulado = 0;
                var grid_obj = $("#tablaFacturasGrid");
                ids = grid_obj.jqGrid('getGridParam', 'selarrrow');

                //Verificar si hay seleccionados
                if (ids.length > 0) {
        
                    ids_poraprobar = _.filter(ids, function (fila) {
                        var infoFila = $.extend({}, grid_obj.getRowData(fila));
                        if ($(infoFila.estado).text() == 'Por Aprobar') {
                            return infoFila.codigo;
                        }
                    });
                    ids_porcobrar = _.filter(ids, function (fila) {
                        var infoFila = $.extend({}, grid_obj.getRowData(fila));
                        if ($(infoFila.estado).text() == 'Por Cobrar') {
                            return infoFila.codigo;
                        }
                    });
                    ids_cobradoparcial = _.filter(ids, function (fila) {
                        var infoFila = $.extend({}, grid_obj.getRowData(fila));
                        if ($(infoFila.estado).text() == 'Cobrado Parcial') {
                            return infoFila.codigo;
                        }
                    });
                    ids_cobradocompleto = _.filter(ids, function (fila) {
                        var infoFila = $.extend({}, grid_obj.getRowData(fila));
                        if ($(infoFila.estado).text() == 'Cobrado Completo') {
                            return infoFila.codigo;
                        }
                    });
                    ids_anulado = _.filter(ids, function (fila) {
                        var infoFila = $.extend({}, grid_obj.getRowData(fila));
                        if ($(infoFila.estado).text() == 'Anulada') {
                            return infoFila.codigo;
                        }
                    });

                    var bodymodal, titulomodal;
                    titulomodal="Cambiar Estados";

                    if ((ids_poraprobar.length > 0 && ids_porcobrar.length == 0 && ids_cobradoparcial.length == 0 && ids_cobradocompleto.length == 0 && ids_anulado.length == 0) || (ids_poraprobar.length == 0 && ids_porcobrar.length > 0 && ids_cobradoparcial.length == 0 && ids_cobradocompleto.length == 0 && ids_anulado.length == 0) || (ids_poraprobar.length == 0 && ids_porcobrar.length == 0 && ids_cobradoparcial.length > 0 && ids_cobradocompleto.length == 0 && ids_anulado.length == 0) || (ids_poraprobar.length == 0 && ids_porcobrar.length == 0 && ids_cobradoparcial.length == 0 && ids_cobradocompleto.length > 0 && ids_anulado.length == 0) || (ids_poraprobar.length == 0 && ids_porcobrar.length == 0 && ids_cobradoparcial.length == 0 && ids_cobradocompleto.length == 0 && ids_anulado.length > 0)) {
                        
                        if (ids_poraprobar.length > 0 ) {
                            bodymodal = "<button data-estado-anterior='por_aprobar' data-estado='por_cobrar' class='btn btn-block btn-outline btn-warning massive' >Por Cobrar</button>";
                            bodymodal += "<button data-estado-anterior='por_aprobar' data-estado='cobrado_parcial' class='btn btn-block btn-outline btn-info massive' >Cobrado Parcial</button>";
                            bodymodal += "<button data-estado-anterior='por_aprobar' data-estado='cobrado_completo' class='btn btn-block btn-outline btn-success massive' >Cobrado Completo</button>";
                            bodymodal += "<button data-estado-anterior='por_aprobar' data-estado='anulada' class='btn btn-block btn-outline btn-danger massive' >Anulada</button>";
                        }else if (ids_porcobrar.length > 0) {
                            bodymodal = "<button data-estado-anterior='por_cobrar' data-estado='por_aprobar' class='btn btn-block btn-outline btn-warning massive' >Por Aprobar</button>";
                            bodymodal += "<button data-estado-anterior='por_cobrar' data-estado='cobrado_parcial' class='btn btn-block btn-outline btn-info massive' >Cobrado Parcial</button>";
                            bodymodal += "<button data-estado-anterior='por_cobrar' data-estado='cobrado_completo' class='btn btn-block btn-outline btn-success massive' >Cobrado Completo</button>";
                            bodymodal += "<button data-estado-anterior='por_cobrar' data-estado='anulada' class='btn btn-block btn-outline btn-danger massive' >Anulada</button>";
                        }else if (ids_cobradoparcial.length > 0 ) {
                            bodymodal = "<button data-estado-anterior='cobrado_parcial' data-estado='por_cobrar' class='btn btn-block btn-outline btn-warning massive' >Por Cobrar</button>";
                            bodymodal += "<button data-estado-anterior='cobrado_parcial' data-estado='por_aprobar' class='btn btn-block btn-outline btn-info massive' >Por Aprobar</button>";
                            bodymodal += "<button data-estado-anterior='cobrado_parcial' data-estado='cobrado_completo' class='btn btn-block btn-outline btn-success massive' >Cobrado Completo</button>";
                            bodymodal += "<button data-estado-anterior='cobrado_parcial' data-estado='anulada' class='btn btn-block btn-outline btn-danger massive' >Anulada</button>";
                        }else if (ids_cobradocompleto.length > 0 ) {
                            bodymodal = "<button data-estado-anterior='cobrado_completo' data-estado='por_cobrar' class='btn btn-block btn-outline btn-warning massive' >Por Cobrar</button>";
                            bodymodal += "<button data-estado-anterior='cobrado_completo' data-estado='por_aprobar' class='btn btn-block btn-outline btn-info massive' >Por Aprobar</button>";
                            bodymodal += "<button data-estado-anterior='cobrado_completo' data-estado='cobrado_parcial' class='btn btn-block btn-outline btn-success massive' >Cobrado Parcial</button>";
                            bodymodal += "<button data-estado-anterior='cobrado_completo' data-estado='anulada' class='btn btn-block btn-outline btn-danger massive' >Anulada</button>";
                        }else if (ids_anulado.length > 0) {
                            bodymodal = "<button data-estado-anterior='anulada' data-estado='por_cobrar' class='btn btn-block btn-outline btn-warning massive' >Por Cobrar</button>";
                            bodymodal += "<button data-estado-anterior='anulada' data-estado='por_aprobar' class='btn btn-block btn-outline btn-info massive' >Por Aprobar</button>";
                            bodymodal += "<button data-estado-anterior='anulada' data-estado='cobrado_parcial' class='btn btn-block btn-outline btn-default massive' >Cobrado Parcial</button>";
                            bodymodal += "<button data-estado-anterior='anulada' data-estado='cobrado_completo' class='btn btn-block btn-outline btn-success massive' >Cobrado Completo</button>";
                        }

                        $("#estadosModal").find('.modal-title').empty().append(titulomodal);
                        $("#estadosModal").find('.modal-body').empty().append(bodymodal);
                        $("#estadosModal").find('.modal-footer').empty();
                        $("#estadosModal").modal('show');

                        var cont = 0;
                        $("#estadosModal").on('click', '.massive', function (e) {
                            if (cont == 0) {
                                var estado = $(this).attr("data-estado");
                                var datos = {campo: {estado: estado, ids: ids}};
                                console.log(datos);
                                //tablaFacturas.recargar;
                                var cambio = moduloFacturas.cambiarEstadoFacturas(datos);
                                var estado_anterior = $(this).attr("data-estado-anterior");                            
                                cambio.done(function (response) {
                                    $("#estadosModal").modal('hide');
                                    tablaFacturas.recargar();
                                    toastr.success('Se ha efectuado Cambio de Estado correctamente.');
                                    cambio.fail(function (response) {
                                        toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
                                    });
                                });
                            }
                            cont = 1;
                        });

                    }else{
                        toastr.error("Los estados no coinciden.");
                    }

                }else{
                    toastr.error("Debe seleccionar al menos una factura.");
                }            

            }
        }else{
            toastr.error("Usted no tiene Permiso para efectuar esta acción.");
        }        
    });
    


});
