var tablaSubContratosVentas = (function() {
    var multiselect = window.location.pathname.match(/subcontratos/g) ? true : false;
    if (typeof proveedor_id === 'undefined') {
        proveedor_id = "";
    }
    var tablaUrl = phost() + 'subcontratos/ajax-listar';
    var gridId = "tablaSubContratosGrid";
    var gridObj = $("#tablaSubContratosGrid");
    var opcionesModal = $('#optionsModal');
    var formularioBuscar = $('#buscarSubContratosForm');
    var documentosModal = $('#documentosModal');
    var pagarRetenidoModal = $('#pagarRetenidoModal');
    var pagarRetenidoForm = '#pagarRetenidoForm';

    var botones = {
        opciones: ".viewOptions",
        buscar: "#searchBtn",
        limpiar: "#clearBtn",
        subirArchivo: ".subirArchivoBtn",
        pagarRetenido: ".pagarRetenidoBtn",
        monto_pagado: ".monto_pagado",
        estadoCuenta: ".imprimir-estado-cuenta",
        guardarBtn: "#guardarBtn"
    };
    var guardarBusquedaLocalStorage = function(dom) {
        localStorage.setItem("sub_numero_subcontrato", $('#numero_subcontrato').val());
        localStorage.setItem("sub_proveedor", $('#proveedor').val());
        localStorage.setItem("sub_proveedor_nombre", $("#proveedor").find('option:selected').text());
        localStorage.setItem("sub_tipo_subcontrato_id", $('#tipo_subcontrato_id').val());
        localStorage.setItem("sub_tipo_subcontrato_nombre", $("#tipo_subcontrato_id").find('option:selected').text());
        localStorage.setItem("sub_monto1", $('#monto1').val());
        localStorage.setItem("sub_monto2", $('#monto2').val());
        localStorage.setItem("sub_centro", $('#centro').val());
        localStorage.setItem("sub_centro_nombre", $("#centro").find('option:selected').text());
        localStorage.setItem("sub_estado", $('#estado').val());
        localStorage.setItem("sub_estado_nombre", $("#estado").find('option:selected').text());
    };
    var limpiarBusquedaLocalStorage = function() {
        if (typeof(Storage) == "undefined") {
            return false;
        }
        localStorage.removeItem("sub_numero_subcontrato");
        localStorage.removeItem("sub_proveedor");
        localStorage.removeItem("sub_proveedor_nombre");
        localStorage.removeItem("sub_tipo_subcontrato_id");
        localStorage.removeItem("sub_tipo_subcontrato_nombre");
        localStorage.removeItem("sub_monto1");
        localStorage.removeItem("sub_monto2");
        localStorage.removeItem("sub_centro");
        localStorage.removeItem("sub_centro_nombre");
        localStorage.removeItem("sub_estado");
        localStorage.removeItem("sub_estado_nombre");
    };
    var getParametrosFiltroInicial = function() {
        //Parametros default
        var data = {
            erptkn: tkn,
            proveedor_id: proveedor_id,
            campo: typeof window.campo !== 'undefined' ? window.campo : {},
        };
        var campo ={campo:{
            proveedor: '',
            tipo_subcontrato: '',
            monto_min: '',
            monto_max: '',
            codigo: '',
            centro_contable: '',
            estado: ''
        } };
        if(multiselect && typeof(Storage) !== "undefined" && window.location.href.match(/ver/gi)==null){
            if(typeof localStorage.sub_numero_subcontrato != "undefined" && localStorage.sub_numero_subcontrato != "null" && localStorage.sub_numero_subcontrato !=""){
                campo.campo.codigo = localStorage.sub_numero_subcontrato;
            }
            if(typeof localStorage.sub_proveedor != "undefined" && localStorage.sub_proveedor != "null" && localStorage.sub_proveedor !=""){
                campo.campo.proveedor = localStorage.sub_proveedor;
            }
            if(typeof localStorage.sub_tipo_subcontrato_id != "undefined" && localStorage.sub_tipo_subcontrato_id != "null" && localStorage.sub_tipo_subcontrato_id !=""){
                campo.campo.tipo_subcontrato = localStorage.sub_tipo_subcontrato_id;
            }
            if(typeof localStorage.sub_monto1 != "undefined" && localStorage.sub_monto1 != "null" && localStorage.sub_monto1 !=""){
                campo.campo.monto_min = localStorage.sub_monto1;
            }
            if(typeof localStorage.sub_monto2 != "undefined" && localStorage.sub_monto2 != "null" && localStorage.sub_monto2 !=""){
                campo.campo.monto_max = localStorage.sub_monto2;
            }
            if(typeof localStorage.sub_centro != "undefined" && localStorage.sub_centro != "null" && localStorage.sub_centro !=""){
                campo.campo.centro_contable = localStorage.sub_centro;
            }
            if(typeof localStorage.sub_estado != "undefined" && localStorage.sub_estado != "null" && localStorage.sub_estado !=""){
                campo.campo.estado = localStorage.sub_estado;
            }
        }

        return Object.assign(data, campo);
    };
    //Mostrar en los campos de busqueda los valores guardados
    //en localStorage
    var setBusquedaDeLocalStorage = function(){
        if (typeof(Storage) == "undefined") {
            return false;
        }
        var haybusqueda = 0;

        if(typeof localStorage.sub_numero_subcontrato != "undefined" && localStorage.sub_numero_subcontrato != "null" && localStorage.sub_numero_subcontrato !=""){
            $('#numero_subcontrato').val(localStorage.sub_numero_subcontrato);
            haybusqueda += 1;
        }
        if(typeof localStorage.sub_proveedor != "undefined" && localStorage.sub_proveedor != "null" && localStorage.sub_proveedor !=""){
            $("#proveedor").append('<option value="' + localStorage.sub_proveedor + '" selected="selected">' + localStorage.sub_proveedor_nombre + '</option>');
            haybusqueda += 1;
        }
        if(typeof localStorage.sub_tipo_subcontrato_id != "undefined" && localStorage.sub_tipo_subcontrato_id != "null" && localStorage.sub_tipo_subcontrato_id !=""){

            $("#tipo_subcontrato_id").append('<option value="' + localStorage.sub_tipo_subcontrato_id + '" selected="selected">' + localStorage.sub_tipo_subcontrato_nombre + '</option>');

            haybusqueda += 1;
        }
        if(typeof localStorage.sub_monto1 != "undefined" && localStorage.sub_monto1 != "null" && localStorage.sub_monto1 !=""){
            $('#monto1').val(localStorage.sub_monto1);
            haybusqueda += 1;
        }
        if(typeof localStorage.sub_monto2 != "undefined" && localStorage.sub_monto2 != "null" && localStorage.sub_monto2 !=""){
            $('#monto2').val(localStorage.sub_monto2);
            haybusqueda += 1;
        }
        if(typeof localStorage.sub_centro != "undefined" && localStorage.sub_centro != "null" && localStorage.sub_centro !=""){

            $("#centro").append('<option value="' + localStorage.sub_centro + '" selected="selected">' + localStorage.sub_centro_nombre + '</option>');

            haybusqueda += 1;
        }
        if(typeof localStorage.sub_estado != "undefined" && localStorage.sub_estado != "null" && localStorage.sub_estado !=""){

            $("#estado").append('<option value="' + localStorage.sub_estado + '" selected="selected">' + localStorage.sub_estado_nombre + '</option>');

            haybusqueda += 1;
        }
        //si existe parametros en localStorage
        //mostrar el panel de busqueda abierto.
        if(haybusqueda > 0){
            $('#centro').closest('.ibox-content').removeAttr("style");
        }

        $("select").trigger("chosen:updated");
        $("#proveedor").trigger('change');
        $("#tipo_subcontrato_id").trigger('change');
        $("#centro").trigger('change');
        $("#estado").trigger('change');
    };
    var tabla = function() {
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames: ['', 'N. de subcontrato', 'Proveedor', 'Tipo de subcontrato', 'Monto original', 'Adendas', 'Monto actual', 'Facturado', 'Por facturar', 'Centro', 'Estado', '', ''],
            colModel: [
                { name: 'uuid', index: 'uuid', width: 30, hidedlg: true, hidden: true },
                { name: 'codigo', index: 'codigo', width: 55, sortable: true },
                { name: 'proveedor_id', index: 'proveedor_id', width: 50, sortable: true },
                { name: 'tipo_subcontrato_id', index: 'tipo_subcontrato_id', width: 40, sortable: true },
                { name: 'monto_original', index: 'monto_original', width: 50, sortable: false, },
                { name: 'adendas', index: 'adendas', width: 50, sortable: false, },
                { name: 'monto_actual', index: 'monto_actual', width: 50, sortable: false },
                { name: 'facturado', index: 'facturado', width: 50, sortable: false },
                { name: 'por_facturar', index: 'por_facturar', width: 50, sortable: false, },
                { name: 'centro', index: 'centro', width: 30, sortable: false },
                { name: 'estado', index: 'estado', width: 30, sortable: false },
                { name: 'options', index: 'options', width: 40 },
                { name: 'link', index: 'link', width: 50, align: "center", sortable: false, resizable: false, hidden: true, hidedlg: true },
            ],
            postData: getParametrosFiltroInicial(),
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
            beforeProcessing: function(data, status, xhr) {
                if ($.isEmptyObject(data.session) === false) {
                    window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function() { //propiedadesGrid_cb
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find("#tablaProveedoresGrid_cb, #jqgh_tablaProveedoresGrid_link").css("text-align", "center");
            },
            loadComplete: function(data, status, xhr) {

                if (gridObj.getGridParam('records') === 0) {
                    $('#gbox_' + gridId).hide();
                    $('#' + gridId + 'NoRecords').empty().append('No se encontraron Subcontratos.').css({ "color": "#868686", "padding": "30px 0 0" }).show();
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
                $('#gridHeader').sticky({
                    getWidthFrom: '.ui-jqgrid-view',
                    className: 'jqgridHeader'
                });
            },
            onSelectRow: function(id) {
                $(this).find('tr#' + id).removeClass('ui-state-highlight');
            }
        });
    };

    var eventos = function() {
        //Bnoton de Opciones
        gridObj.on("click", botones.opciones, function(e) {
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
        //boton limpiaar
        $(botones.limpiar).click(function(e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            formularioBuscar.find('input[type="text"]').prop("value", "");
            formularioBuscar.find('select.select2').val('').change();
            formularioBuscar.find('select').prop("value", "");
            limpiarBusquedaLocalStorage();
            recargar();
        });
        //Al cargar, mostrar resultados guardados
        //en localStorage si existen
        setBusquedaDeLocalStorage();
        //boton Buscar
        $(botones.buscar).click(function(e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var proveedor = $('#proveedor').val();
            var tipo_subcontrato_id = $('#tipo_subcontrato_id').val();
            var monto1 = $('#monto1').val();
            var monto2 = $('#monto2').val();
            var numero_subcontrato = $('#numero_subcontrato').val();
            var centro = $('#centro').val();
            var estado = $('#estado').val();

            if (proveedor !== "" || tipo_subcontrato_id != "" || monto1 !== "" || monto2 !== '' || numero_subcontrato !== "" || centro !== "" || estado !== '') {
                //Reload Grid
                guardarBusquedaLocalStorage();
                gridObj.setGridParam({ postData: null });
                gridObj.setGridParam({
                    url: tablaUrl,
                    datatype: "json",
                    postData: {
                        campo: {
                            proveedor: proveedor,
                            tipo_subcontrato: tipo_subcontrato_id,
                            monto_min: monto1,
                            monto_max: monto2,
                            codigo: numero_subcontrato,
                            centro_contable: centro,
                            estado: estado
                        },
                        erptkn: tkn
                    }
                }).trigger('reloadGrid');
            }
        });

        //Documentos Modal
        $(opcionesModal).on("click", botones.subirArchivo, function(e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            //Cerrar modal de opciones
            opcionesModal.modal('hide');

            var subcontrato_id = $(this).attr("data-id");
            console.log(subcontrato_id);
            //Inicializar opciones del Modal
            documentosModal.modal({
                backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                show: false
            });

            var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();
            scope.safeApply(function() {
                scope.campos.subcontrato_id = subcontrato_id;
            });
            documentosModal.modal('show');
        });

        //Pagar Retenido Modal
        $(opcionesModal).on("click", botones.pagarRetenido, function(e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            //Cerrar modal de opciones
            opcionesModal.modal('hide');

            //render
            var id = $(this).attr("data-id");
            var rowINFO = $.extend({}, gridObj.getRowData(id));
            var options = rowINFO.link;
            //Init Modal
            pagarRetenidoModal.find('.modal-title').empty().append('Pagar retenido: ' + $(rowINFO.codigo).text() + '');
            pagarRetenidoModal.find('.modal-body').empty().append('<p>Por favor, espere un momento...</p>');
            ajax_get_pago_empezable(id);
            pagarRetenidoModal.find('.modal-footer').empty();
            pagarRetenidoModal.modal('show');

        });

        $(opcionesModal).on("click", botones.estadoCuenta, function(e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var id = $(this).attr("data-id");
            $("#subcontrato_id").val(id);
            $("#formExportarEstadoCuenta").submit();
            opcionesModal.modal('hide');
        });

        pagarRetenidoModal.on('change', botones.monto_pagado, function(e) {
            var context = $(this);
            var value = accounting.unformat(context.val());
            var retenido_por_pagar = accounting.unformat(context.data('retenido_por_pagar'));
            context.val(parseFloat(roundNumber(value, 2)) > parseFloat(roundNumber(retenido_por_pagar, 2)) ? redondeo(retenido_por_pagar) : redondeo(value));
        });

        pagarRetenidoModal.on('submit', pagarRetenidoForm, function(e) {
            var context = $(this);
            e.preventDefault();
            var data = context.serializeArray();
            data.push({ name: 'erptkn', value: window.tkn });

            pagarRetenidoModal.modal('hide');
            $.ajax({
                url: phost() + "pagos/ajax_pagar_retenido",
                type: "POST",
                data: data,
                dataType: "json",
                success: function(response) {
                    if (!_.isEmpty(response)) {
                        toastr[response.response ? 'success' : 'error'](response.mensaje);
                    }
                }
            });
        });
    };

    var ajax_get_pago_empezable = function(subcontrato_id) {
        var data = $.extend({ erptkn: window.tkn }, { type: 'retenido', id: subcontrato_id });
        $.ajax({
            url: phost() + "pagos/ajax_get_empezable",
            type: "POST",
            data: data,
            dataType: "json",
            success: function(response) {
                if (!_.isEmpty(response)) {
                    pagarRetenidoModal.find('.modal-body').empty().append(render_pago(response));
                }
            }
        });
    };

    var render_pago = function(response) {
        var html = '<form id="pagarRetenidoForm"><div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">';
        html += '       <table class="table" id="facturaItems">';
        html += '           <thead>';
        html += '               <tr>';
        html += '                   <th width="14%" style="color: white;background-color: #0076BE;border:1px solid white;">No. Documento</th>';
        html += '                   <th width="14%" style="color: white;background-color: #0076BE;border:1px solid white;">Fecha de Emisión</th>';
        html += '                   <th width="14%" style="color: white;background-color: #0076BE;border:1px solid white;">Monto Retenido</th>';
        html += '                   <th width="14%" style="color: white;background-color: #0076BE;border:1px solid white;">Retenido Pagado</th>';
        html += '                   <th width="14%" style="color: white;background-color: #0076BE;border:1px solid white;">Retenido Por Pagar</th>';
        html += '                   <th width="14%" style="color: white;background-color: #0076BE;border:1px solid white;">Pago</th>';
        html += '                 </tr>';
        html += '           </thead>';

        html += '           <tbody>';

        var i = 0;
        _.forEach(response.pagables, function(pagable) {
            html += '               <tr class="item-listing">';
            html += '                   <td style="font-weight: bold;vertical-align: middle;">' + pagable.numero_documento + '</td>';
            html += '                   <td style="font-weight: bold;vertical-align: middle;">' + pagable.fecha_emision + '</td>';
            html += '                   <td style="text-align: right;font-weight: bold;vertical-align: middle;">$' + redondeo(pagable.total) + '</td>'; //monto retenido
            html += '                   <td style="text-align: right;font-weight: bold;vertical-align: middle;">$' + redondeo(pagable.pagado) + '</td>'; //retenido pagado
            html += '                   <td style="text-align: right;font-weight: bold;vertical-align: middle;">$' + redondeo(pagable.saldo) + '</td>'; //retenido por pagar
            html += '                   <td>';
            html += '                       <div class="input-group">';
            html += '                           <span class="input-group-addon">$</span>';
            html += '                           <input type="text" data-retenido_por_pagar="' + pagable.saldo + '" class="form-control monto_pagado" style="text-align: right;" placeholder="0.00" name="items[' + i + '][monto_pagado]">';
            html += '                           <input type="hidden" name="items[' + i + '][pagable_id]" value="' + pagable.pagable_id + '">';
            html += '                           <input type="hidden" name="items[' + i + '][pagable_type]" value="' + pagable.pagable_type + '">';
            html += '                       </div>';
            html += '                   </td>';
            html += '               </tr>';
        });

        html += '           </tbody>';

        html += '       </table>';
        html += '       <div class="form-group col-xs-12 col-sm-12 col-md-10 col-lg-10"></div>';
        html += '       <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">';
        html += '           <input type="hidden" name="campo[empezable_type]" value="subcontrato">';
        html += '           <input type="hidden" name="campo[empezable_id]" value="' + response.id + '">';
        html += '           <input type="hidden" name="campo[id]" value="">';
        html += '           <input type="hidden" name="campo[fecha_pago]" value="' + moment().format('DD/MM/YYYY') + '">';
        html += '           <input type="hidden" name="campo[proveedor_id]" value="' + response.proveedor_id + '">';
        html += '           <input type="hidden" name="campo[estado]" value="por_aprobar">';
        html += '           <input type="hidden" name="campo[depositable_type]" value="' + response.depositable_type + '">';
        html += '           <input type="hidden" name="campo[depositable_id]" value="' + response.depositable_id + '">';
        html += '           <input type="hidden" name="campo[formulario]" value="retenido">';
        html += '           <input type="hidden" name="metodo_pago[0][tipo_pago]" value="efectivo">';
        html += '           <input type="hidden" name="metodo_pago[0][total_pagado]" value="">';
        html += '           <input type="submit" class="btn btn-primary btn-block" value="Guardar">';
        html += '       </div>';
        html += '</div></form>';

        return html;
    };

    var redondeo = function(value) {
        return accounting.formatNumber(value, 2, ",");
    };

    var recargar = function() {
        //Reload Grid
        gridObj.setGridParam({ postData: null });
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                campo: {
                    proveedor: '',
                    tipo_subcontrato: '',
                    monto_min: '',
                    monto_max: '',
                    codigo: '',
                    centro_contable: '',
                    estado: ''
                },
                erptkn: tkn
            }
        }).trigger('reloadGrid');
    };
    var redimencionar_tabla = function() {
        $(window).resizeEnd(function() {
            $(".ui-jqgrid").each(function() {
                var w = parseInt($(this).parent().width()) - 6;
                var tmpId = $(this).attr("id");
                var gId = tmpId.replace("gbox_", "");
                $("#" + gId).setGridWidth(w);
            });
        });
    };

    return {
        init: function() {
            tabla();
            eventos();
            redimencionar_tabla();
        }
    };

})();

$(function() {
    tablaSubContratosVentas.init();
});