var tablaOportunidades = (function () {

    var tablaUrl = phost() + 'oportunidades/ajax-listar';
    var gridId = "tablaOportunidadesGrid";
    var gridObj = $("#" + gridId);
    var opcionesModal = $('#optionsModal');
    var opcionesModalGrupal = $("#opcionesModalGrupal");
    var formularioBuscar = $('#buscarOportunidadesForm');

    var botones = {
        opciones: ".viewOptions",
        buscar: "#searchBtn",
        limpiar: "#clearBtn",
        exportar: "#exportarOportunidades",
        cambiarEstadoGrupal: "#cambiarEstadoGrupal"
    };

    var tabla = function () {
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames: ['', 'No. Oportunidad', 'Nombre de la oportundad', 'Monto', 'Fecha de creaci&oacute;n', 'Asignado a', 'Estado', '', ''],
            colModel: [
                {name: 'uuid', index: 'uuid', width: 30, hidedlg: true, hidden: true},
                {name: 'codigo', index: 'codigo', width: 45, sortable: true},
                {name: 'nombre', index: 'nombre', width: 60, sortable: true},
                {name: 'monto', index: 'monto', width: 50, sortable: false},
                {name: 'fecha_creacion', index: 'fecha_creacion', width: 50, sortable: false,},
                {name: 'asignado_a', index: 'asignado_a', width: 50, sortable: false,},
                {name: 'etapa_id', index: 'estapa_id', width: 50, sortable: false, align: 'center'},
                {name: 'options', index: 'options', width: 40, align: 'center'},
                {
                    name: 'link',
                    index: 'link',
                    width: 50,
                    align: "center",
                    sortable: false,
                    resizable: false,
                    hidden: true,
                    hidedlg: true
                },
            ],
            postData: {
                erptkn: tkn,
                cliente_id: this.cliente_id != null ? this.cliente_id : '',
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
            multiselect: true,
            sortname: 'codigo',
            sortorder: "DESC",
            beforeProcessing: function (data, status, xhr) {
                if ($.isEmptyObject(data.session) === false) {
                    window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function () {//propiedadesGrid_cb
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find("#tablaProveedoresGrid_cb, #jqgh_tablaProveedoresGrid_link").css("text-align", "center");
            },
            loadComplete: function (data, status, xhr) {

                if (gridObj.getGridParam('records') === 0) {
                    $('#gbox_' + gridId).hide();
                    $('#' + gridId + 'NoRecords').empty().append('No se encontraron Oportunidades.').css({
                        "color": "#868686",
                        "padding": "30px 0 0"
                    }).show();
                } else {
                    $('#gbox_' + gridId).show();
                    $('#' + gridId + 'NoRecords').empty();
                }

                gridObj.find('td').css('vertical-align', 'middle');


                /**
                 * Generando eventos para el label de etapa
                 */
                gridObj.find("span.label").css("cursor", "pointer").click(function () {
                    var id = $(this).closest("tr").attr("id");
                    gridObj.jqGrid('resetSelection');
                    gridObj.jqGrid('setSelection', id);
                    setTimeout(function () {
                        changeStatus(id);
                    }, 300);
                });


                //---------
                // Cargar plugin jquery Sticky Objects
                //----------
                //add class to headers
                gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");
                //floating headers
                $('#gridHeader').sticky({
                    getWidthFrom: '.ui-jqgrid-view',
                    className: 'jqgridHeader'
                });

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
            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.codigo).text() + '');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });

        opcionesModal.on("click", '.agregar-cotizacion', function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var id = $(this).attr("data-id");
            var cliente_id = $(this).attr("data-cliente_id");
            var html = '';
            html += '    <div class="row" style="margin-left:-15px;">';
            html += '        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">';
            html += '            <select id="cotizacion_id" class="form-control"><option value="">Seleccione</option></select>';
            html += '        </div>';
            html += '    </div>';
            html += '    <div class="row" style="margin-left:-15px;margin-bottom:-20px;">';
            html += '        <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">';
            html += '            <br>';
            html += '        </div>';
            html += '        <div class="form-group col-xs-12 col-sm-12 col-md-5 col-lg-5">';
            html += '            <button class="btn btn-default btn-block btn-asociar-cotizacion" data-value="0"> Cancelar</button>';
            html += '        </div>';
            html += '        <div class="form-group col-xs-12 col-sm-12 col-md-5 col-lg-5">';
            html += '            <button class="btn btn-success btn-block btn-asociar-cotizacion" data-value="1"> Guardar</button>';
            html += '        </div>';
            html += '    </div>';

            opcionesModal.find('.btn-asociar-cotizacion').unbind();
            opcionesModal.find('.modal-title').empty().append('Agregar');
            opcionesModal.find('.modal-body').empty().append(html);
            opcionesModal.find('.modal-footer').empty();

            opcionesModal.find("#cotizacion_id").empty().append('<option value="">Seleccione</option>');
            _.forEach(window.cotizaciones, function (cotizacion) {
                if (cotizacion.cliente.id == cliente_id) {
                    opcionesModal.find("#cotizacion_id").append('<option value="' + cotizacion.id + '">' + cotizacion.codigo + ' - ' + cotizacion.cliente.nombre + '</option>');
                }
            });

            opcionesModal.find('.btn-asociar-cotizacion').on('click', function () {
                var boton = $(this);
                opcionesModal.modal('hide');

                if (boton.data('value') == '1' && opcionesModal.find("#cotizacion_id").val().length) {

                    asociar_cotizacion({oportunidad_id: id, cotizacion_id: opcionesModal.find("#cotizacion_id").val()});

                }
            });

        }).on("click", '.cambio-estado', function (e) {
            opcionesModal.modal('hide');
            changeStatus($(this).attr("data-uuid"));
        });

        $(botones.exportar).click(function () {

            //contiene un arreglo con los identificadores de los proveedores (uuid_proveedor)
            var registros_jqgrid = gridObj.jqGrid('getGridParam', 'selarrrow');

            if (registros_jqgrid.length) {
                var url = phost() + "oportunidades/ajax-exportar";
                var vars = "";
                $.each(registros_jqgrid, function (i, val) {
                    vars += '<input type="hidden" name="ids[]" value="' + val + '">';
                });
                var form = $(
                    '<form action="' + url + '" method="post" style="display:none;">' +
                    vars +
                    '<input type="hidden" name="erptkn" value="' + tkn + '">' +
                    '<input type="submit">' +
                    '</form>'
                );
                $('body').append(form);
                form.submit();
            }
        });

        $(botones.cambiarEstadoGrupal).click(function () {

            changeStatus();
        });

        //boton limpiaar
        $(botones.limpiar).click(function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            formularioBuscar.find('input[type="text"]').prop("value", "");
            formularioBuscar.find('select.select2').val('').change();
            formularioBuscar.find('select').prop("value", "");
            recargar();
        });
        //boton Buscar
        $(botones.buscar).click(function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var nombre = $('#nombre').val();
            var monto_desde = $('#monto_desde').val();
            var monto_hasta = $('#monto_hasta').val();
            var fecha_desde = $('#fecha_desde').val();
            var fecha_hasta = $('#fecha_hasta').val();
            var asignado_a_id = $('#asignado_a_id').val();
            var estado_id = $('#estado_id').val();

            if (nombre !== "" || monto_desde !== "" || monto_hasta !== "" || fecha_desde !== "" || fecha_hasta !== "" || estado_id !== '' || asignado_a_id !== '') {
                //Reload Grid
                gridObj.setGridParam({
                    url: tablaUrl,
                    datatype: "json",
                    postData: {
                        nombre: nombre,
                        monto_desde: monto_desde,
                        monto_hasta: monto_hasta,
                        fecha_desde: fecha_desde,
                        fecha_hasta: fecha_hasta,
                        asignado_a_id: asignado_a_id,
                        etapa_id: estado_id,
                        erptkn: tkn
                    }
                }).trigger('reloadGrid');
            }
        });
    };

    var asociar_cotizacion = function (params) {

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
                    if (response.estado === 200) {
                        toastr.success(response.mensaje);
                    } else if (response.estado === 500) {
                        toastr.error(response.mensaje);
                    }
                }
            }
        });

    };

    var recargar = function () {
        //Reload Grid
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                nombre: '',
                monto_desde: '',
                monto_hasta: '',
                fecha_desde: '',
                fecha_hasta: '',
                asignado_a_id: '',
                etapa_id: '',
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
    var changeStatus = function (idSelected) {
        //contiene un arreglo con los identificadores de los proveedores (uuid_proveedor)
        var registros_jqgrid = gridObj.jqGrid('getGridParam', 'selarrrow');

        if (registros_jqgrid.length < 1 && typeof idSelected == "undefined") {
            toastr.error("Debe seleccionar uno o más opciones")
            return false;
        }

        var options = '<a href="#" data-type="1" class="btn btn-block btn-outline btn-warning change-status">Prospecto</a>' +
            '<a href="javascript:;" data-type="2" class="btn btn-block btn-outline btn-info change-status">En negociación</a>' +
            '<a href="javascript:;" data-type="3" class="btn btn-block btn-outline btn-ganada change-status">Ganada</a>' +
            '<a href="javascript:;" data-type="4" class="btn btn-block btn-outline btn-danger change-status">Perdida</a>' +
            '<a href="javascript:;" data-type="5" class="btn btn-block btn-outline btn-anulado change-status">Anulada</a>';

        opcionesModalGrupal.unbind("click").on("click", ".change-status", function (e) {
            var registros_jqgrid = gridObj.jqGrid('getGridParam', 'selarrrow');
            var selected = typeof idSelected == "undefined" ? registros_jqgrid : [idSelected];
            console.log(selected);
            $.ajax({
                url: phost() + "oportunidades/ajax-change-status",
                type: "POST",
                data: {
                    erptkn: tkn,
                    status: $(this).attr("data-type"),
                    ids: selected
                },
                dataType: "json",
                success: function (res) {
                    console.log(res);
                    if (res.status != 200) {
                        toastr.error(res.message);
                        return;
                    }

                    toastr.success(res.message);
                    recargar();
                    opcionesModalGrupal.modal("hide");
                },
                error: function (res) {
                    toastr.error("No fue posible realizar el cambio de estado");
                    console.log("error Cambio de estado".res);
                }
            });
        });

        var text = " a " + registros_jqgrid.length + " elementos";
        if (typeof idSelected != "undefined") {

            text = ": " + $('#' + idSelected + ' td:eq(2)').text();
        }

        opcionesModalGrupal.find('.modal-title').empty().append('Cambio de estado' + text);
        opcionesModalGrupal.find('.modal-body').empty().append(options);
        opcionesModalGrupal.find('.modal-footer').empty();
        opcionesModalGrupal.modal('show');
    }

    return {
        init: function () {
            tabla();
            eventos();
            redimencionar_tabla();
        }
    };

})();

$(function () {
    tablaOportunidades.init();
});
