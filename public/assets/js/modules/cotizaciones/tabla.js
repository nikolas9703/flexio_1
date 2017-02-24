//modulo clientes
//console.log(cliente_id);
var tablaCotizaciones = (function () {

    var tablaUrl = phost() + 'cotizaciones/ajax-listar';
    var gridId = "tablaCotizacionesGrid";
    var gridObj = $("#tablaCotizacionesGrid");
    var opcionesModal = $('#optionsModal');
    var formularioBuscar = '';
    var multiselect = window.location.pathname.match(/cotizaciones/g) ? true : false;
    var cotizaciones_alquiler_url = window.location.pathname.match(/cotizaciones_alquiler/g) ? true : false;

    var botones = {
        opciones: ".viewOptions",
        buscar: "#searchBtn",
        limpiar: "#clearBtn",
        exportar: "#exportarListaCotizaciones",
    };

    var tabla = function () {
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames: ['', 'No. Cotización', 'Cliente', 'Fecha de emisión', 'Válido hasta', 'Estado', 'Vendedor', '', ''],
            colModel: [
                {name: 'uuid', index: 'uuid', width: 30, hidedlg: true, hidden: true},
                {name: 'codigo', index: 'codigo', width: 30, sortable: true},
                {name: 'cliente', index: 'cliente', width: 70, sortable: true},
                {name: 'fecha_desde', index: 'fecha_desde', width: 50, sortable: false, },
                {name: 'fecha_hasta', index: 'fecha_hasta', width: 70, sortable: false, },
                {name: 'estado', index: 'estado', width: 30, sortable: false},
                {name: 'vendedor', index: 'vendedor', width: 30, sortable: false},
                {name: 'options', index: 'options', width: 40},
                {name: 'link', index: 'link', width: 50, align: "center", sortable: false, resizable: false, hidden: true, hidedlg: true},
            ],
            postData: {
                erptkn: tkn,
                sp_orden_venta_id: (typeof window.sp_orden_venta_id !== 'undefined') ? _.toString(window.sp_orden_venta_id) : '',
                cliente_id: (typeof window.cliente_id !== 'undefined' && window.cliente_id != '[object HTMLSelectElement]') ? _.toString(window.cliente_id) : '',
                campo: typeof window.campo !== 'undefined' ? window.campo : {},
                factura_id: (typeof(this.infofactura) !== 'undefined') ? _.toString(this.infofactura.id) : '',
                tipoFiltro: cotizaciones_alquiler_url ? 'cotizaciones_alquiler' : ''
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
            sortname: 'codigo',
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
                                          $('form#exportarCotizaciones').submit();
                                          $('body').trigger('click');
                                      }
                                  }
                              });



                if (gridObj.getGridParam('records') === 0) {
                    $('#gbox_' + gridId).hide();
                    $('#' + gridId + 'NoRecords').empty().append('No se encontraron Cotizaciones.').css({"color": "#868686", "padding": "30px 0 0"}).show();
                } else {
                    $('#gbox_' + gridId).show();
                    $('#' + gridId + 'NoRecords').empty();
                }

                //---------
                // Cargar plugin jquery Sticky Objects
                //----------
                //add class to headers
                gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");
                //floating headers
                if (multiselect === true){
                $('#gridHeader').sticky({
                    getWidthFrom: '.ui-jqgrid-view',
                    className: 'jqgridHeader'
                });
                }
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
            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.codigo).text() + '');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });

        opcionesModal.on("click", '.agregar-oportunidad', function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var id = $(this).attr("data-id");
            var cliente_id = $(this).attr("data-cliente_id");
            var html = '';
            html += '    <div class="row" style="margin-left:-15px;">';
            html += '        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">';
            html += '            <select id="oportunidad_id" class="form-control"><option value="">Seleccione</option></select>';
            html += '        </div>';
            html += '    </div>';
            html += '    <div class="row" style="margin-left:-15px;margin-bottom:-20px;">';
            html += '        <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">';
            html += '            <br>';
            html += '        </div>';
            html += '        <div class="form-group col-xs-12 col-sm-12 col-md-5 col-lg-5">';
            html += '            <button class="btn btn-default btn-block btn-asociar-oportunidad" data-value="0"> Cancelar</button>';
            html += '        </div>';
            html += '        <div class="form-group col-xs-12 col-sm-12 col-md-5 col-lg-5">';
            html += '            <button class="btn btn-success btn-block btn-asociar-oportunidad" data-value="1"> Guardar</button>';
            html += '        </div>';
            html += '    </div>';

            opcionesModal.find('.btn-asociar-oportunidad').unbind();
            opcionesModal.find('.modal-title').empty().append('Agregar');
            opcionesModal.find('.modal-body').empty().append(html);
            opcionesModal.find('.modal-footer').empty();

            opcionesModal.find("#oportunidad_id").empty().append('<option value="">Seleccione</option>');
            _.forEach(window.oportunidades,function(oportunidad){
                if(oportunidad.cliente && oportunidad.cliente.id == cliente_id){
                    opcionesModal.find("#oportunidad_id").append('<option value="'+ oportunidad.id +'">'+ oportunidad.codigo +' - '+ oportunidad.cliente.nombre +'</option>');
                }
            });

            opcionesModal.find('.btn-asociar-oportunidad').on('click',function(){
                var boton = $(this);
                opcionesModal.modal('hide');

                if(boton.data('value') == '1' && opcionesModal.find("#oportunidad_id").val().length){

                    asociar_oportunidad({cotizacion_id:id,oportunidad_id:opcionesModal.find("#oportunidad_id").val()});

                }
            });

        });

    };

    //Documentos Modal
    $("#optionsModal").on("click", ".subirArchivoBtn", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            //Cerrar modal de opciones
            $("#optionsModal").modal('hide');
            var cotizacion_id = $(this).attr("data-id");

            //Inicializar opciones del Modal
            $('#documentosModal').modal({
                    backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                    show: false
            });

            //$('#pedido_id').val(pedido_id);
            var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();

        scope.safeApply(function(){
            scope.campos.cotizacion_id = cotizacion_id;
        });
            $('#documentosModal').modal('show');
    });

    var asociar_oportunidad = function(params){

        $.ajax({
            url: phost() + "oportunidades/ajax-asociar-cotizacion",
            type: "POST",
            data: {
                erptkn: tkn,
                cotizacion_id: params.cotizacion_id,
                oportunidad_id: params.oportunidad_id
            },
            dataType: "json",
            success: function (response) {
                if (!_.isEmpty(response)) {
                    if(response.estado === 200){
                      toastr.success(response.mensaje);
                    }else if(response.estado === 500){
                      toastr.error(response.mensaje);
                    }
                }
            }
        });

    };

    $(botones.limpiar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        $('#buscarCotizacionesForm').find('input[type="text"]').prop("value", "");
        $('#buscarCotizacionesForm').find('select.chosen-select').prop("value", "");
        $('#buscarCotizacionesForm').find('select').prop("value", "");
        $(".chosen-select").trigger("chosen:updated");

        recargar();
    });
    $(botones.buscar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        var no_cotizacion = $('#no_cotizacion').val();
        var cliente = $('#cliente').val();
        var desde = $('#fecha1').val();
        var hasta = $('#fecha2').val();
        var etapa = $('#etapa').val();
        console.log("estado: "+etapa);
        var vendedor = $('#vendedor').val();

        if (no_cotizacion !== '' || cliente !== "" || desde !== "" || hasta !== "" || etapa !== "" || vendedor !== "") {
            //Reload Grid
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    no_cotizacion: no_cotizacion,
                    cliente: cliente,
                    desde: desde,
                    hasta: hasta,
                    etapa: etapa,
                    vendedor: vendedor,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }


    });
    var recargar = function () {

        //Reload Grid
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                no_cotizacion: '',
                cliente: '',
                desde: '',
                hasta: '',
                etapa: '',
                vendedor: '',
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
        }
    };

})();

$(function () {
    //Al redimensionar ventana

    tablaCotizaciones.init();
// $(window).resizeEnd(function() {
// 	tablaColaboradores.redimencionar_tabla();
// });
});
