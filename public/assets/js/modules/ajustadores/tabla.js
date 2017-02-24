//Modulo
var tablaAjustadores = (function () {
    var url = 'ajustadores/ajax_listar';
    var grid_id = "tablaAjustadoresGrid";
    var grid_obj = $("#tablaAjustadoresGrid");
    var opcionesModal = $('#opcionesModal');
    var opcionesModalEstado = $('#opcionesModalEstado');
    var crearContactoForm = $("#crearContactoForm");

    var botones = {
        opciones: ".viewOptions",
        editar: "",
        crearContacto: ".agregarContacto",
        buscar: "#searchBtn",
        limpiar: "#clearBtn",
        exportar: "#exportarAjustadoresLnk",
        cambioGrupal: "#cambiarEstadoLnk"
    };

    var tabla = function () {

        //inicializar jqgrid
        grid_obj.jqGrid({
            url: phost() + url,
            datatype: "json",
            colNames: [
                '',
                'Nombre',
                'RUC',
                'Teléfono',
                'Correo electrónico',
                'Dirección',
                'Estado',
                '',
                '',
            ],
            colModel: [
                {name: 'id', index: 'id', width: 30, hidedlg: true, hidden: true},
                {name: 'nombre', index: 'nombre', width: 50},
                {name: 'ruc', index: 'ruc', width: 70},
                {name: 'telefono', index: 'telefono', width: 40},
                {name: 'email', index: 'email', width: 50},
                {name: 'direccion', index: 'direccion', width: 60},
                {name: 'estado', index: 'estado', width: 30, align: "center"},
                {name: 'link', index: 'link', width: 50, sortable: false, resizable: false, hidedlg: true, align: "center"},
                {name: 'options', index: 'options', hidedlg: true, hidden: true}
            ],
            mtype: "POST",
            postData: {
                erptkn: tkn
            },
            height: "auto",
            autowidth: true,
            rowList: [10, 20, 50, 100],
            rowNum: 10,
            page: 1,
            pager: "#" + grid_id + "Pager",
            loadtext: '<p>Cargando...</p>',
            hoverrows: false,
            viewrecords: true,
            refresh: true,
            gridview: true,
            multiselect: true,
            sortname: 'nombre',
            sortorder: "ASC",
            beforeProcessing: function (data, status, xhr) {
                //Check Session
                if ($.isEmptyObject(data.session) == false) {
                    window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function () {
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find("#tablaAjustadoresGrid_cb, #jqgh_tablaAjustadoresGrid_link").css("text-align", "center");
            },
            beforeRequest: function (data, status, xhr) {},
            loadComplete: function (data) {

                //check if isset data
                if (data['total'] == 0) {
                    $('#gbox_' + grid_id).hide();
                    $('#' + grid_id + 'NoRecords').empty().append('No se encontraron Ajustadores.').css({"color": "#868686", "padding": "30px 0 0"}).show();
                } else {
                    $('#' + grid_id + 'NoRecords').hide();
                    $('#gbox_' + grid_id).show();
                }
            },
            onSelectRow: function (id) {
                $(this).find('tr#' + id).removeClass('ui-state-highlight');
            },
        });

        //Al redimensionar ventana
        $(window).resizeEnd(function () {
            tablaAjustadores.redimensionar();
        });
    };

    //Inicializar Eventos de Botones
    var eventos = function () {
        //Boton Opciones
        grid_obj.on("click", botones.opciones, function (e) {
            localStorage.removeItem('tomo_n');
            localStorage.removeItem('asiento_n');
            localStorage.removeItem('pasaporte');
            localStorage.removeItem('provincia');
            localStorage.removeItem('letras');
            localStorage.removeItem('folio');
            localStorage.removeItem('tomo_j');
            localStorage.removeItem('asiento_j');
            localStorage.removeItem('digverificador');
            localStorage.removeItem('nombre');
            localStorage.removeItem('telefono');
            localStorage.removeItem('email');
            localStorage.removeItem('direccion');
            localStorage.removeItem('estado');
            localStorage.removeItem('identificacion');
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var id = $(this).attr("data-id");
            var rowINFO = grid_obj.getRowData(id);
            var option = rowINFO["options"];
            //evento para boton collapse sub-menu Accion Personal
            opcionesModal.on('click', 'a[href="#collapse' + id + '"]', function () {
                opcionesModal.find('#collapse' + id).collapse();
            });

            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: ' + rowINFO["nombre"] + '');
            opcionesModal.find('.modal-body').empty().append(option);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
            // alert(rowINFO["nombre"]);
        });

        $(opcionesModal).on("click", botones.crearContacto, function (e) {

            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            //Cerrar modal de opciones
            opcionesModal.modal('hide');
            var ajustadores_id = $(this).attr("data-id");
            var ajustadores_uuid = $(this).attr("data-uuid");
            //Limpiar formulario
            crearContactoForm.attr('action', phost() + 'ajustadores/editar/' + ajustadores_uuid);
            crearContactoForm.find('input[name*="ajustadores_"]').remove();
            crearContactoForm.append('<input type="hidden" name="ajustadores_id" value="' + ajustadores_id + '" />');
            crearContactoForm.append('<input type="hidden" name="agregar_contacto" value="1" />');
            //Enviar formulario
            crearContactoForm.submit();
            $('body').trigger('click');
        });

        //Boton de Buscar Colaborador
        $(botones.buscar).on("click", function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            buscarajustadores();
        });

        //Boton de Reiniciar jQgrid
        $(botones.limpiar).on("click", function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            recargar();
            limpiarCampos();
        });
        //Boton de Exportar ajustadores
        $(botones.exportar).on("click", function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            if ($('#tabla').is(':visible') == true) {
                //Exportar Seleccionados del jQgrid
                var ids = [];
                ids = grid_obj.jqGrid('getGridParam', 'selarrrow');

                //Verificar si hay seleccionados
                if (ids.length > 0) {
                    console.log(ids);
                    $('#ids').val(ids);
                    $('form#exportarAjustadores').submit();
                    $('body').trigger('click');
                }
            }
        });

        //Boton de Activar Ajustador
        $(botones.activar).on("click", function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            if ($('#tabla').is(':visible') == true) {

                //Exportar Seleccionados del jQgrid
                var colaboradores = [];
                colaboradores = grid_obj.jqGrid('getGridParam', 'selarrrow');

                //Verificar si hay seleccionados
                if (colaboradores.length > 0) {
                    //Cambiar Estado
                    toggleColaborador({colaboradores: colaboradores, estado_id: 1});
                }
            }
        });
    };
    $(botones.cambioGrupal).on("click", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        $("#opcionesModal").on("click", ".activo", function () {
            //Seleccionados del jQgrid
            var ids = [];
            var ids_aprobados = 0;
            var ids_activos = 0;
            var ids_inactivos = 0;
            ids = grid_obj.jqGrid('getGridParam', 'selarrrow');
            console.log(ids);
            if (ids.length > 0) {
                ids_aprobados = _.filter(ids, function (fila) {
                    var infoFila = $.extend({}, grid_obj.getRowData(fila));
                    if ($(infoFila.estado).text() == 'Por aprobar') {
                        return infoFila.id;
                    }
                });
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

            var politicas_general = moduloAjustadores.ajaxcambiarObtenerPoliticasGeneral();
            var permisos_generales = politicas_general.success(function (data) {

                if (data > 0)
                {
                    var politicas = moduloAjustadores.ajaxcambiarObtenerPoliticas();
                    var politicas_general_total = moduloAjustadores.ajaxcambiarObtenerPoliticasGenerales();
                    var permisos_generales = politicas_general_total.success(function (data) {
                        var permisos_total = [];
                        $.each(data, function (i, filename) {
                            permisos_total.push(filename);

                        });
                        console.log(permisos_total.indexOf(13, 0) + " " + permisos_total.indexOf(14, 0) + " " + permisos_total.indexOf(15, 0) + " <br> ");

                        var permisos1 = politicas.success(function (data) {
                            var permisos = [];
                            $.each(data, function (i, filename) {
                                permisos.push(filename);
                            });
                            console.log(permisos.indexOf(13, 0) + " " + permisos.indexOf(14, 0) + " " + permisos.indexOf(15, 0));
                             console.log(permisos.indexOf(15, 0)+ " " + permisos_total.indexOf(15, 0));
//                            if (permisos.indexOf(13, 0) != -1 || permisos.indexOf(14, 0) != -1 || permisos.indexOf(15, 0) != -1)
//                            {
                            if (ids_aprobados.length > 0)
                            {
                                if (((permisos.indexOf(13, 0) == -1) && (permisos_total.indexOf(13, 0) == -1)) || (permisos.indexOf(13, 0) != -1))
                                {
                                    var datos = {campo: {estado: 'Activo', ids: ids_aprobados}};
                                    var cambio = moduloAjustadores.ajaxcambiarEstados(datos);
                                    cambio.done(function (response) {
                                        var ajustadores = response;
                                        _.map(ajustadores, function (ant) {
                                            $("#tablaAjustadoresGrid").jqGrid('setCell', ant.id, 'estado', ant.estado);
                                        });
                                        opcionesModal.modal('hide');
                                        $("#mensaje").hide();
                                    });
                                } else {
                                    opcionesModal.modal('hide');
                                    $("#mensaje").show();
                                    $("#mensaje").html('<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button> Usted no tiene permisos para cambiar a este estado');
                                }
                            } else if (ids_inactivos.length > 0)
                            {
                                var datos = {campo: {estado: 'Activo', ids: ids_inactivos}};

                                if (((permisos.indexOf(15, 0) == -1) && (permisos_total.indexOf(15, 0) == -1)) || (permisos.indexOf(15, 0) != -1))
                                {
                                    var cambio = moduloAjustadores.ajaxcambiarEstados(datos);
                                    cambio.done(function (response) {
                                        var ajustadores = response;
                                        _.map(ajustadores, function (ant) {
                                            $("#tablaAjustadoresGrid").jqGrid('setCell', ant.id, 'estado', ant.estado);
                                        });
                                        opcionesModal.modal('hide');
                                        $("#mensaje").hide();
                                    });
                                } else {
                                    opcionesModal.modal('hide');
                                    $("#mensaje").show();
                                    $("#mensaje").html('<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button> Usted no tiene permisos para cambiar a este estado');
                                }
                            }
//                            } else {
//                                opcionesModal.modal('hide');
//                                $("#mensaje").show();
//                                $("#mensaje").html('<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button> Usted no tiene permisos para cambiar a este estado');
//                            }
                            return permisos;
                        });
                    });
                } else
                {
                    if (ids_inactivos.length > 0)
                    {
                        var datos = {campo: {estado: 'Activo', ids: ids_inactivos}};
                    }
                    if (ids_aprobados.length > 0)
                    {
                        var datos = {campo: {estado: 'Activo', ids: ids_aprobados}};
                    }
                    if (ids_inactivos.length > 0 || ids_aprobados.length > 0)
                    {
                        var cambio = moduloAjustadores.ajaxcambiarEstados(datos);
                        cambio.done(function (response) {
                            var ajustadores = response;
                            _.map(ajustadores, function (ant) {
                                $("#tablaAjustadoresGrid").jqGrid('setCell', ant.id, 'estado', ant.estado);
                            });
                            opcionesModal.modal('hide');
                            $("#mensaje").hide();
                        });
                    }
                }

            });
        });
        $("#opcionesModal").on("click", ".inactivo", function () {
            //Seleccionados del jQgrid
            var ids = [];
            var ids_aprobados = 0;
            var ids_activos = 0;
            var ids_inactivos = 0;
            ids = grid_obj.jqGrid('getGridParam', 'selarrrow');
            console.log(ids);
            if (ids.length > 0) {
                ids_aprobados = _.filter(ids, function (fila) {
                    var infoFila = $.extend({}, grid_obj.getRowData(fila));
                    if ($(infoFila.estado).text() == 'Por aprobar') {
                        return infoFila.id;
                    }
                });
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

            var politicas_general = moduloAjustadores.ajaxcambiarObtenerPoliticasGeneral();
            var permisos_generales = politicas_general.success(function (data) {

                if (data > 0)
                {
                    var politicas = moduloAjustadores.ajaxcambiarObtenerPoliticas();
                    var politicas_general_total = moduloAjustadores.ajaxcambiarObtenerPoliticasGenerales();
                    var permisos_generales = politicas_general_total.success(function (data) {
                        var permisos_total = [];
                        $.each(data, function (i, filename) {
                            permisos_total.push(filename);

                        });
                        console.log(permisos_total.indexOf(13, 0) + " " + permisos_total.indexOf(14, 0) + " " + permisos_total.indexOf(15, 0) + " <br> ");
                        var permisos1 = politicas.success(function (data) {
                            var permisos = [];
                            $.each(data, function (i, filename) {
                                permisos.push(filename);
                            });
                            console.log(permisos.indexOf(13, 0) + " " + permisos.indexOf(14, 0) + " " + permisos.indexOf(15, 0));

//                        if (permisos.indexOf(13, 0) != -1 || permisos.indexOf(14, 0) != -1 || permisos.indexOf(15, 0) != -1)
//                        {
                            if (ids_activos.length > 0)
                            {
                                if (((permisos.indexOf(14, 0) == -1) && (permisos_total.indexOf(14, 0) == -1)) || (permisos.indexOf(14, 0) != -1))
                                {
                                    var datos = {campo: {estado: 'Inactivo', ids: ids_activos}};
                                    var cambio = moduloAjustadores.ajaxcambiarEstados(datos);
                                    cambio.done(function (response) {
                                        var ajustadores = response;
                                        _.map(ajustadores, function (ant) {
                                            $("#tablaAjustadoresGrid").jqGrid('setCell', ant.id, 'estado', ant.estado);
                                        });
                                        opcionesModal.modal('hide');
                                        $("#mensaje").hide();
                                    });
                                } else {
                                    opcionesModal.modal('hide');
                                    $("#mensaje").show();
                                    $("#mensaje").html('<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button> Usted no tiene permisos para cambiar a este estado');
                                }
                            }
//                        } else {
//                            opcionesModal.modal('hide');
//                            $("#mensaje").show();
//                            $("#mensaje").html('<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button> Usted no tiene permisos para cambiar a este estado');
//                        }
                            return permisos;
                        });
                    });
                } else
                {
                    if (ids_activos.length > 0)
                    {
                        var datos = {campo: {estado: 'Inactivo', ids: ids_activos}};
                        var cambio = moduloAjustadores.ajaxcambiarEstados(datos);
                        cambio.done(function (response) {
                            var ajustadores = response;
                            _.map(ajustadores, function (ant) {
                                $("#tablaAjustadoresGrid").jqGrid('setCell', ant.id, 'estado', ant.estado);
                            });
                            opcionesModal.modal('hide');
                            $("#mensaje").hide();
                        });
                    }
                }
            });
        });

        //Seleccionados del jQgrid
        var ids = [];
        var ids_aprobados = 0;
        var ids_activos = 0;
        var ids_inactivos = 0;
        ids = grid_obj.jqGrid('getGridParam', 'selarrrow');
        console.log(ids);
        if (ids.length > 0) {
            ids_aprobados = _.filter(ids, function (fila) {
                var infoFila = $.extend({}, grid_obj.getRowData(fila));
                if ($(infoFila.estado).text() == 'Por aprobar') {
                    return infoFila.id;
                }
            });
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


        if ((ids_aprobados.length > 0 && ids_activos.length == 0 && ids_inactivos.length == 0) || (ids_aprobados.length == 0 && ids_activos.length > 0 && ids_inactivos.length == 0) || (ids_aprobados.length == 0 && ids_activos.length == 0 && ids_inactivos.length >= 0))
        {

            if (ids_aprobados.length > 0 && ids_activos.length == 0 && ids_inactivos.length == 0)
            {
                var options = '<a href="#" id="activargrupal" class="btn btn-block btn-outline btn-success activo">Activo</a>';
            }

            if (ids_aprobados.length == 0 && ids_activos.length > 0 && ids_inactivos.length == 0)
            {
                var options = '<a href="#" class="btn btn-block btn-outline btn-success inactivo">Inactivo</a>';
            }

            if (ids_aprobados.length == 0 && ids_activos.length == 0 && ids_inactivos.length > 0)
            {
                var options = '<a href="#" class="btn btn-block btn-outline btn-success activo">Activo</a>';
            }

            opcionesModal.find('.modal-title').empty().append('Cambiar estado');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
            $("#mensaje").hide();
        } else
        {
            if (ids.length == 0)
            {
                $("#mensaje").show();
                opcionesModal.modal('hide');
                $("#mensaje").html('<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>Por favor seleccione los registros');
            } else
            {
                $("#mensaje").show();
                opcionesModal.modal('hide');
                $("#mensaje").html('<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button> Las ajustadores seleccionadas tienen estados diferentes');
            }
        }
    });

//    $(botones.cambioGrupal).on("click", function (e) {
//        e.preventDefault();
//        e.returnValue = false;
//        e.stopPropagation();
//
//        //Seleccionados del jQgrid
//        var ids = [];
//        ids = grid_obj.jqGrid('getGridParam', 'selarrrow');
//        console.log(ids);
//        if (ids.length > 0) {
//            var ids_aprobados = _.filter(ids, function (fila) {
//                var infoFila = $.extend({}, grid_obj.getRowData(fila));
//                if ($(infoFila.estado).text() == 'Por aprobar') {
//                    return infoFila.id;
//                }
//            });
//            var ids_activos = _.filter(ids, function (fila) {
//                var infoFila = $.extend({}, grid_obj.getRowData(fila));
//                if ($(infoFila.estado).text() == 'Activo') {
//                    return infoFila.id;
//                }
//            });
//            var ids_inactivos = _.filter(ids, function (fila) {
//                var infoFila = $.extend({}, grid_obj.getRowData(fila));
//                if ($(infoFila.estado).text() == 'Inactivo') {
//                    return infoFila.id;
//                }
//            });
//        }
//        ;
//
//        $("#opcionesModal").on("click", ".activo", function () {
//            if (ids_aprobados.length > 0)
//                var datos = {campo: {estado: 'Activo', ids: ids_aprobados}};
//            else if (ids_inactivos.length > 0)
//                var datos = {campo: {estado: 'Activo', ids: ids_inactivos}};
//            if (ids_aprobados.length > 0 || ids_inactivos.length > 0) {
//                var cambio = moduloAjustadores.ajaxcambiarEstados(datos);
//                cambio.done(function (response) {
//                    var ajustadores = response;
//                    _.map(ajustadores, function (ant) {
//                        $("#tablaAjustadoresGrid").jqGrid('setCell', ant.id, 'estado', ant.estado);
//                    });
//                    opcionesModal.modal('hide');
//                });
//            }
//        });
//        $("#opcionesModal").on("click", ".inactivo", function () {
//            if (ids_activos.length > 0) {
//                var datos = {campo: {estado: 'Inactivo', ids: ids_activos}};
//                var cambio = moduloAjustadores.ajaxcambiarEstados(datos);
//                cambio.done(function (response) {
//                    var ajustadores = response;
//                    _.map(ajustadores, function (ant) {
//                        $("#tablaAjustadoresGrid").jqGrid('setCell', ant.id, 'estado', ant.estado);
//                    });
//                    opcionesModal.modal('hide');
//                });
//            }
//        });
//
//
//        if ((ids_aprobados.length > 0 && ids_activos.length == 0 && ids_inactivos.length == 0) || (ids_aprobados.length == 0 && ids_activos.length > 0 && ids_inactivos.length == 0) || (ids_aprobados.length == 0 && ids_activos.length == 0 && ids_inactivos.length > 0))
//        {
//
//            if (ids_aprobados.length > 0 && ids_activos.length == 0 && ids_inactivos.length == 0)
//            {
//                var options = '<a href="#" id="activargrupal" class="btn btn-block btn-outline btn-success activo">Activo</a><a href="#" class="btn btn-block btn-outline btn-success inactivo">Inactivo</a>';
//            }
//
//            if (ids_aprobados.length == 0 && ids_activos.length > 0 && ids_inactivos.length == 0)
//            {
//                var options = '<a href="#" class="btn btn-block btn-outline btn-success inactivo">Inactivo</a>';
//            }
//
//            if (ids_aprobados.length == 0 && ids_activos.length == 0 && ids_inactivos.length > 0)
//            {
//                var options = '<a href="#" class="btn btn-block btn-outline btn-success activo">Activo</a>';
//            }
//
//            opcionesModal.find('.modal-title').empty().append('Cambiar estado');
//            opcionesModal.find('.modal-body').empty().append(options);
//            opcionesModal.find('.modal-footer').empty();
//            opcionesModal.modal('show');
//        }
//    });

    //Reload al jQgrid
    var recargar = function () {

        //Reload Grid
        grid_obj.setGridParam({
            url: phost() + url,
            datatype: "json",
            postData: {
                nombre: '',
                ruc: '',
                telefono: '',
                email: '',
                direccion: '',
                estado: ''
            }
        }).trigger('reloadGrid');
    };

    var buscarajustadores = function () {

        var nombre = $('#nombre').val();
        var ruc = $('#ruc').val();
        var telefono = $('#telefono').val();
        var direccion = $('#direccion').val();
        var email = $('#email').val();
        var estado = $('#estado').val();
        if (nombre != "" || ruc != "" || telefono != "" || direccion != "" || email != "" || estado != "")
        {
            //Reload Grid
            grid_obj.setGridParam({
                url: phost() + url,
                datatype: "json",
                postData: {
                    nombre: nombre,
                    ruc: ruc,
                    telefono: telefono,
                    direccion: direccion,
                    email: email,
                    estado: estado
                }
            }).trigger('reloadGrid');
        }
    };

    //Limpiar campos de busqueda
    var limpiarCampos = function () {
        $('#buscarAjustadoresForm').find('input[type="text"]').prop("value", "");
        $('#buscarAjustadoresForm').find('input[type="input-left-addon"]').prop("value", "");
        $('#buscarAjustadoresForm').find('select').val('');
        $('#buscarAjustadoresForm').find('.chosen-select').val('').trigger('chosen:updated');
    };

    return{
        init: function () {
            tabla();
            eventos();
        },
        recargar: function () {
            //reload jqgrid
            recargar();
        },
        redimensionar: function () {
            //Al redimensionar ventana
            $(".ui-jqgrid").each(function () {
                var w = parseInt($(this).parent().width()) - 6;
                var tmpId = $(this).attr("id");
                var gId = tmpId.replace("gbox_", "");
                $("#" + gId).setGridWidth(w);
            });
        }
    };

})();
tablaAjustadores.init();