//modulo Intereses Asegurados
function removeA(arr) {
    var what, a = arguments, L = a.length, ax;
    while (L > 1 && arr.length) {
        what = a[--L];
        while ((ax = arr.indexOf(what)) !== -1) {
            arr.splice(ax, 1);
        }
    }
    return arr;
}
var tablaInteresesAsegurados = (function () {
    if (typeof uuid_cotizacion === 'undefined') {
        uuid_cotizacion = "";
    }
    var tablaUrl = phost() + 'intereses_asegurados/ajax-listar';
    var gridId = "tablaInteresesAseguradosGrid";
    var gridObj = $("#tablaInteresesAseguradosGrid");
    var opcionesModal = $('#optionsModal');
    var formularioBuscar = '';
    var documentosModal = $('#documentosModal');
    var grid_obj = $("#tablaInteresesAseguradosGrid");
    var botones = {
        opciones: ".viewOptions",
        subir_archivo: ".subir_archivo_intereses",
        buscar: "#searchBtn",
        limpiar: "#clearBtn",
        exportar: "#exportarBtn",
        modalstate: "label.estadoInteres",
        cambiarEstado: "#cambiarEstadoInteresesLnk"
    };
    var tabla = function () {
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames: ['', 'No. de interés asegurado', 'Tipo de interés', 'Identificación', 'Estado', '', '', '', ''],
            colModel: [
                {name: 'id', index: 'id', width: 30, align: "center", sortable: false, hidden: true},
                {name: 'numero', index: 'numero', width: 30},
                {name: 'interesestable_type', index: 'interesestable_type', width: 35},
                {name: 'identificacion', index: 'identificacion', width: 35},
                {name: 'estado', index: 'estado', width: 15},
                {name: 'options', index: 'options', width: 30, align: "center", sortable: false},
                {name: 'link', index: 'link', width: 30, align: "center", sortable: false, hidden: true},
                {name: 'modalstate', index: 'modalstate', width: 30, align: "center", sortable: false, hidden: true},
                {name: 'massState', index: 'massState', width: 30, align: "center", sortable: false, hidden: true}


            ],
            postData: {
                erptkn: tkn
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
            sortname: "numero",
            sortorder: "DESC",
            multiselect: true,
            beforeProcessing: function (data, status, xhr) {
                //Check Session
                if ($.isEmptyObject(data.session) == false) {
                    window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function () {//propiedadesGrid_cb
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find("#tablaInteresesAseguradosGrid_cb, #jqgh_tablaInteresesAseguradosGrid_link").css("text-align", "center");
            },
            beforeRequest: function (data, status, xhr) {},
            loadComplete: function (data, status, xhr) {

                if (gridObj.getGridParam('records') === 0) {
                    $('#gbox_' + gridId).hide();
                    $('#' + gridId + 'NoRecords').empty().append('No se encontraron intereses asegurados.').css({"color": "#868686", "padding": "30px 0 0"}).show();
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
            tablaInteresesAsegurados.redimencionar_tabla();
        });
    };
    var eventos = function () {
        //Bnoton de Opciones
        gridObj.on("click", botones.modalstate, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            var id = $(this).attr("data-id");
            console.log(id);
            var rowINFO = $.extend({}, gridObj.getRowData(id));
            var options = rowINFO.modalstate;
            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.numero).text() + '');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
            var idArray = [rowINFO.id];
            var estado = rowINFO.massState == "Activo" ? "Inactivo" : "Activo";
            var datos = {campo: {estado: estado, ids: idArray}};
            opcionesModal.on("click", ".massive", function (e) {
                var cambio = moduloIntereses.cambiarEstadoIntereses(datos);
                cambio.done(function (response) {
                    opcionesModal.modal('hide');
                    $("#mensaje").hide();
                    recargar();
                });
            });
        });
        gridObj.on("click", botones.opciones, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            var id = $(this).attr("data-id");
            console.log(id);
            var rowINFO = $.extend({}, gridObj.getRowData(id));
            var options = rowINFO.link;
            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.numero).text() + '');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });
    };
    //Documentos Modal
    $(opcionesModal).on("click", ".subir_archivo_intereses", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        //Cerrar modal de opciones
        opcionesModal.modal('hide');
        var intereses_id = $(this).attr("data-id");
        var intereses_type = $(this).attr("data-type");
        //Inicializar opciones del Modal
        documentosModal.modal({
            backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
            show: false
        });
        documentosModal.modal('show');
        var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();
        console.log(scope);
        scope.safeApply(function () {
            scope.campos.id = intereses_id;
            scope.campos.intereses_type = intereses_type;
        });
        documentosModal.modal('show');
    });
    $(botones.limpiar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        $('#buscarInteresesAseguradosForm').find('input[type="text"]').prop("value", "");
        $('#buscarInteresesAseguradosForm').find('select.chosen-select').prop("value", "");
        $('#buscarInteresesAseguradosForm').find('select').prop("value", "");
        $(".chosen-select").trigger("chosen:updated");
        recargar();
    });
    //Boton de Cambiar estado InteresesAsegurados
    $(opcionesModal).on("click", ".eliminar_interes", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        var intereses_id = $(this).attr("data-id");
        if (permiso_eliminar === 1)
        {
            $.ajax({
                type: "POST",
                data: {
                    id: intereses_id,
                    erptkn: tkn
                },
                url: phost() + 'intereses_asegurados/ajax_eliminar_interes',
                success: function (data)
                {
                    if (data === 'No')
                    {
                        toastr.warning('Usted no tiene permisos para eliminar este registro');
                    } else
                    {
                        toastr.success('Registro eliminado');
                    }
                    opcionesModal.modal('hide');
                    recargar();
                }
            });
        } else
        {
            toastr.warning('Usted no tiene permisos para eliminar este registro');
            opcionesModal.modal('hide');
        }

    });
    //Boton de Cambiar estado InteresesAsegurados
    $(botones.cambiarEstado).on("click", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        if ($('#tabla').is(':visible') === true) {
//Exportar Seleccionados del jQgrid

            var ids = [],
                    statesValues = ["Activo", "Inactivo"],
                    opciones,
                    estado,
                    style,
                    button = "",
                    states = [];
            var ids_aprobados = 0;
            var ids_activos = 0;
            var ids_inactivos = 0;
            //console.log(ids);

            ids = gridObj.jqGrid('getGridParam', 'selarrrow');
            if (ids.length > 0) {

                ids_activos = _.filter(ids, function (fila) {
                    var infoFila = $.extend({}, grid_obj.getRowData(fila));
                    if ($(infoFila.estado).text() == 'Activo') {
                        return infoFila.id;
                    }
                });
                ids_inactivos = _.filter(ids, function (fila) {
                    var infoFila = $.extend({}, grid_obj.getRowData(fila));
                    if ($(infoFila.estado).text() == 'Inactivo') {
                        return infoFila.id;
                    }
                });
            }
            ;
            //Verificar si hay seleccionados
            if (ids.length > 0) {

                $('#ids').val(ids);
                Array.prototype.allValuesSame = function () {

                    for (i = 1; i < this.length; i++)
                    {
                        if (this[i] !== this[0])
                            return false;
                    }

                    return true;
                };
                for (i = ids.length - 1; i >= 0; i--) {

                    opciones = $.extend({}, gridObj.getRowData(ids[i]));
                    removeA(statesValues, opciones.massState);
                    states.push(opciones.massState);
                }
                if (states.allValuesSame()) {
                    var politicas_general = moduloIntereses.ajaxcambiarObtenerPoliticasGeneral();
                    var politicas_general_total = moduloIntereses.ajaxcambiarObtenerPoliticasGenerales();
                    var permisos_generales = politicas_general_total.success(function (data) {
                        var permisos_total = [];
                        $.each(data, function (i, filename) {
                            permisos_total.push(filename);
                        });
                        console.log(permisos_total.indexOf(19, 0) + " " + permisos_total.indexOf(20, 0));
                        var permisos_generales = politicas_general.success(function (data) {
                            var politicas = moduloIntereses.ajaxcambiarObtenerPoliticas();
                            var permisos1 = politicas.success(function (data) {
                                var permisos = [];
                                $.each(data, function (i, filename) {
                                    permisos.push(filename);
                                });
                                console.log(permisos.indexOf(19, 0) + " " + permisos.indexOf(20, 0));
                                var politicas_general = moduloIntereses.ajaxcambiarObtenerPoliticasGeneral();
                                var permisos_generales = politicas_general.success(function (data) {
                                    console.log(data);
                                    if (data > 0)
                                    {
                                        if (ids_activos.length > 0)
                                        {
                                            if (((permisos.indexOf(19, 0) == -1) && (permisos_total.indexOf(19, 0) == -1)) || (permisos.indexOf(19, 0) != -1))
                                            {
                                                for (i = statesValues.length - 1; i >= 0; i--) {
                                                    estado = statesValues[i];
                                                    style = estado == "Activo" ? "successful" : 'danger';
                                                    button += "<button class='btn btn-block btn-outline btn-" + style + " massive'>" + estado + "</button>";
                                                }
                                                opcionesModal.find('.modal-title').empty().append('Cambiar estado(s)');
                                                opcionesModal.find('.modal-body').empty().append(button);
                                                opcionesModal.find('.modal-footer').empty();
                                                opcionesModal.modal('show');
                                                //db state type emun

                                                var datos = {campo: {estado: estado, ids: ids}};
                                                opcionesModal.on("click", ".massive", function (e) {
                                                    var cambio = moduloIntereses.cambiarEstadoIntereses(datos);
                                                    cambio.done(function (response) {
                                                        opcionesModal.modal('hide');
                                                        $("#mensaje").hide();
                                                        recargar();
                                                    });
                                                });
                                            } else {
                                                opcionesModal.modal('hide');
                                                $("#mensaje").show();
                                                $("#mensaje").html('<div id="mensaje" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Usted no tiene permisos para cambiar a este estado</div>');
                                            }
                                        } else if (ids_inactivos.length > 0)
                                        {
                                            var datos = {campo: {estado: 'Activo', ids: ids_inactivos}};
                                            if (((permisos.indexOf(20, 0) == -1) && (permisos_total.indexOf(20, 0) == -1)) || (permisos.indexOf(20, 0) != -1))
                                            {
                                                for (i = statesValues.length - 1; i >= 0; i--) {
                                                    estado = statesValues[i];
                                                    style = estado == "Activo" ? "successful" : 'danger';
                                                    button += "<button class='btn btn-block btn-outline btn-" + style + " massive'>" + estado + "</button>";
                                                }
                                                opcionesModal.find('.modal-title').empty().append('Cambiar estado(s)');
                                                opcionesModal.find('.modal-body').empty().append(button);
                                                opcionesModal.find('.modal-footer').empty();
                                                opcionesModal.modal('show');
                                                //db state type emun

                                                var datos = {campo: {estado: estado, ids: ids}};
                                                opcionesModal.on("click", ".massive", function (e) {
                                                    var cambio = moduloIntereses.cambiarEstadoIntereses(datos);
                                                    cambio.done(function (response) {
                                                        opcionesModal.modal('hide');
                                                        $("#mensaje").hide();
                                                        recargar();
                                                    });
                                                });
                                            } else {
                                                opcionesModal.modal('hide');
                                                $("#mensaje").show();
                                                $("#mensaje").html('<div id="mensaje" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Usted no tiene permisos para cambiar a este estado</div>');
                                            }

                                        }
                                    } else {
                                        for (i = statesValues.length - 1; i >= 0; i--) {
                                            estado = statesValues[i];
                                            style = estado == "Activo" ? "successful" : 'danger';
                                            button += "<button class='btn btn-block btn-outline btn-" + style + " massive'>" + estado + "</button>";
                                        }
                                        opcionesModal.find('.modal-title').empty().append('Cambiar estado(s)');
                                        opcionesModal.find('.modal-body').empty().append(button);
                                        opcionesModal.find('.modal-footer').empty();
                                        opcionesModal.modal('show');
                                        //db state type emun

                                        var datos = {campo: {estado: estado, ids: ids}};
                                        opcionesModal.on("click", ".massive", function (e) {
                                            var cambio = moduloIntereses.cambiarEstadoIntereses(datos);
                                            cambio.done(function (response) {
                                                opcionesModal.modal('hide');
                                                $("#mensaje").hide();
                                                recargar();
                                            });
                                        });
//                                opcionesModal.modal('hide');
//                                $("#mensaje").show();
//                                $("#mensaje").html('<div id="mensaje" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Usted no tiene permisos para cambiar a este estado</div>');
                                    }
                                    return permisos;
                                });
                            });
                        });
                    });
                } else {
                    opcionesModal.find('.modal-title').empty().append('Mensaje');
                    opcionesModal.find('.modal-body').empty().append("<button class='btn btn-block btn-outline btn-danger'><p>Los registros</p> <p>no tienen el mismo estado <i class='fa fa-exclamation-triangle'></p></button>");
                    opcionesModal.find('.modal-footer').empty();
                    opcionesModal.modal('show');
                }

            } else {
                opcionesModal.find('.modal-title').empty().append('Mensaje');
                opcionesModal.find('.modal-body').empty().append("<button class='btn btn-block btn-outline btn-warning'>seleccione algún registro <i class='fa fa-check'></button>");
                opcionesModal.find('.modal-footer').empty();
                opcionesModal.modal('show');
            }

        }

    });
    $(botones.buscar).click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        var numero = $('#numero').val();
        var tipo = $('#tipo').val();
        var identificacion = $("#identificacion").val();
        var estado = $("#estado").val();
        if (numero !== "" || tipo !== "" || identificacion !== "" || estado) {
//Reload Grid
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    numero: numero,
                    tipo: tipo,
                    identificacion: identificacion,
                    estado: estado,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }


    });
    //Boton de Exportar Intereses
    $(botones.exportar).on("click", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        if ($('#tabla').is(':visible') === true) {
//Exportar Seleccionados del jQgrid
            var ids = [];
            ids = gridObj.jqGrid('getGridParam', 'selarrrow');
            //Verificar si hay seleccionados
            if (ids.length > 0) {

                $('#ids').val(ids);
                $('form#exportarIntereses').submit();
                $('body').trigger('click');
            }
        }
    });
    var recargar = function () {

        //Reload Grid
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                numero: '',
                tipo: '',
                identificacion: '',
                estado: '',
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
    tablaInteresesAsegurados.init();
    $("#jqgh_tablaInteresesAseguradosGrid_cb span").removeClass("s-ico");
    $('#jqgh_tablaInteresesAseguradosGrid_options span').removeClass("s-ico");
});
$('#tipo').on("change", function () {
    var tipo = $(this).val();
    tipo === '1' ? $('#identificacion_label').text('Identificación') : '';
    tipo === '2' ? $('#identificacion_label').text('No. de liquidación') : '';
    tipo === '3' || tipo === '4' || tipo === '8' ? $('#identificacion_label').text('No. de serie') : '';
    tipo === '5' ? $('#identificacion_label').text('No. de cédula') : '';
    tipo === '6' ? $('#identificacion_label').text('No. de orden o contrato') : '';
    tipo === '7' ? $('#identificacion_label').text('Dirección') : '';
});