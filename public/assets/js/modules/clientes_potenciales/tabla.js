$(function () {

    //-------------------------
    // Inicializar jqGrid
    //-------------------------
    $("#clientesPotencialesGrid").jqGrid({
        url: phost() + 'clientes_potenciales/ajax-listar-clientes-potenciales',
        datatype: "json",
        colNames: [
            'Nombre',
            'Tel&eacute;fono',
            'Email',
            'Toma de Contacto',
            '',
            ''
        ],
        colModel: [
            {name: 'nombre', index: 'nombre', width: 40,  align:'left'},
            {name: 'telefono', index: 'cp.telefono', width: 40,  align:'left'},
            {name: 'correo', index: 'cp.correo', width: 40,  align:'left'},
            {name: 'toma_contacto', index: 'toma_contacto', width: 40,  align:'left'},
            {name: 'link', index: 'link', width: 40, align: "center", sortable: false, hidedlg: true, resizable: false},
            {name: 'options', index: 'options', hidedlg: true, hidden: true}
        ],
        mtype: "POST",
        postData: {
            erptkn: tkn
        },
        height: "auto",
        autowidth: true,
        rowList: [10, 20, 30, 50],
        rowNum: 10,
        page: 1,
        pager: "#pager",
        loadtext: '<p>Cargando...',
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
        loadBeforeSend: function () {//propiedadesGrid_cb
            $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
            $(this).closest("div.ui-jqgrid-view").find("#clientesPotencialesGrid_cb, #jqgh_clientesPotencialesGrid_link").css("text-align", "center");
        },
        loadComplete: function (data) {

            //check if isset data
            if (data['total'] == 0) {
                $('#gbox_clientesPotencialesGrid').hide();
                $('.NoRecords').empty().append('No se encontraron clientes potenciales.').css({"color": "#868686", "padding": "30px 0 0"}).show();
            } else {
                $('.NoRecords').hide();
                $('#gbox_clientesPotencialesGrid').show();
            }

            //add class to headers
            $("#clientesPotencialesGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");

            //floating headers
            $('#gridHeader').sticky({
                getWidthFrom: '.ui-jqgrid-view',
                className: 'jqgridHeader'
            });

            $("#clientesPotencialesGrid_cb").css("width", "40px");
            //$("#clientesPotencialesGrid tbody tr").children().first("td").css("width", "40px");
        },
        onSelectRow: function (id) {
            $(this).find('tr#' + id).removeClass('ui-state-highlight');
        }
    });
    $("#clientesPotencialesGrid").jqGrid('columnToggle');

    //-------------------------
    // Redimensioanr Grid al cambiar tamaño de la ventanas.
    //-------------------------
    $(window).resizeEnd(function () {
        $(".ui-jqgrid").each(function () {
            var w = parseInt($(this).parent().width()) - 6;
            var tmpId = $(this).attr("id");
            var gId = tmpId.replace("gbox_", "");
            $("#" + gId).setGridWidth(w);
        });
    });

    //-------------------------
    // Boton de opciones
    //-------------------------
    $("#clientesPotencialesGrid").on("click", ".viewOptions", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        var id_rol = $(this).attr("data-rol");
        var rowINFO = $("#clientesPotencialesGrid").getRowData(id_rol);
        var options = rowINFO["options"];
        var nombre_cliente = $(rowINFO["nombre"]).text();


        //Init boton de opciones
        $('#optionsModal').find('.modal-title').empty().append('Opciones: ' + nombre_cliente);
        $('#optionsModal').find('.modal-body').empty().append(options);
        $('#optionsModal').find('.modal-footer').empty();
        $('#optionsModal').modal('show');
    });

    //-------------------------
    // Opciones:
    //-------------------------
    //Convertir a cliente
    $('#optionsModal').on("click", "#convertirACliente", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        //Cierra modal.
        $('#optionsModal').modal('hide');

        var id = $(this).attr("data-id");

        if (typeof (Storage) !== "undefined") {

            //Grabar colaborador id en local storage
            localStorage.setItem('id', id);
        }
        window.location = phost() + 'clientes/crear';

    });
    //Editar Rol
    $('#optionsModal').on("click", "#eliminarClienteIndividual", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        var id_cliente = $(this).attr("data-cliente");

        var footer_buttons = ['<div class="row">',
            '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
            '<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>',
            '</div>',
            '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
            '<button id="confirmarEliminarClienteIndividual" data-cliente="' + id_cliente + '" class="btn btn-w-m btn-danger btn-block" type="button">Eliminar</button>',
            '</div>',
            '</div>'
        ].join('\n');

        //Init boton de opciones
        $('#optionsModal').find('.modal-title').empty().append('Confirme');
        $('#optionsModal').find('.modal-body').empty().append('¿Esta seguro que desea eliminar este Cliente Potencial?');
        $('#optionsModal').find('.modal-footer').empty().append(footer_buttons);
        $('#optionsModal').modal('show');
    });
    //Abrir ventana de Crear Actividad
    $("#optionsModal").on("click", "#crearActividad", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        //Desabilitar (Disable) campos en el modal de Crear Actividad
        $('#crearActividadModal').find('#uuid_clienteBtn, #uuid_sociedad, #uuid_contacto, #relacionado_con, #uuid_relacion').prop("disabled", "disabled").find('option:eq(0)').prop("selected", "selected");
        $('#crearActividadModal').find('#uuid_sociedad').append('<option value="" selected="selected">Seleccione</option>');
        $('#crearActividadModal').find('#uuid_contacto').append('<option value="" selected="selected">Seleccione</option>');

        $('form#crearActividad').find('select[name="campo[uuid_cliente]"]').append('<option value="" selected="selected">Seleccione</option>').prop("disabled", "disabled");
        setTimeout(function () {
            $(".chosen-select").chosen({
                width: '100%'
            }).trigger('chosen:updated');
        }, 500);

        //Limpiar formulario por si acaso
        $('form#crearActividad').find('textarea[id="campo[apuntes]"]').empty();
        $('form#crearActividad').find('input[id="campo[asunto]"], input[id="campo[fecha]"], input[id="campo[duracion]"]').prop("value", "");
        $('form#crearActividad').find('#uuid_tipo_actividad option:eq(0), #uuid_asignado option:eq(0)').prop("selected", "selected");
        $('form#crearActividad').find('textarea[id="campo[apuntes]"]').each(function () {
            var name = $(this).attr('name');
            CKEDITOR.instances[name].setData('');
        });

        //Lenar Datos de la oportunidad seleccionada

        var uuid_potencial = $(this).attr("data-cliente-potencial");
        var rowINFO = $("#clientesPotencialesGrid").getRowData(uuid_potencial);
        var nombre_potencial = rowINFO["nombre"];

        $('form#crearActividad').removeAttr("action");
        $('form#crearActividad').find("#relacionado_con option:contains('Clientes Potenciales')").prop("selected", "selected");
        $('form#crearActividad').find('#uuid_relacion').empty().append('<option value="' + uuid_potencial + '" selected="selected">' + nombre_potencial + '</option>');
        $('form#crearActividad').find('#cancelar, input[id="campo[guardar]"]').parent().remove();

        //Por defecto asignado a debe ser la uuid_usuario logeada
        $('form#crearActividad').find('select[name*="campo[uuid_asignado]"] option[value="' + uuid_usuario + '"]').prop('selected', 'selected');


        $('#optionsModal').modal('hide');
        $('#crearActividadModal').modal('show');
    });

    //Guardar formulario de Actividad
    //Pasando cluientes a Naturales
    $('#crearActividadModal').on("click", "#guardarActividadBtn", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        $('form#crearActividad').validate({
            focusInvalid: true,
            ignore: '',
            wrapper: '',
        });
        $('form#crearActividad').find('select[id*="uuid_tipo_actividad"]').rules(
                "add", {
                    required: true,
                    messages: {required: 'Introduzca el tipo.'}
                });

        if ($("form#crearActividad").valid() != false) {
            $('form#crearActividad').find('input:disabled, select:disabled').removeAttr("disabled");

            $.ajax({
                url: phost() + 'actividades/crear-actividad',
                data: $('form#crearActividad').serialize(),
                type: "POST",
                dataType: "json",
                cache: false,
            }).done(function (json) {

                //Check Session
                if ($.isEmptyObject(json.session) == false) {
                    window.location = phost() + "login?expired";
                }

                //If json object is empty.
                if ($.isEmptyObject(json.results) == true) {
                    return false;
                }

                var class_mensaje = json.results[0] == true ? 'alert-success' : 'alert-danger';
                var mensaje = json.results[0] == true ? 'Se ha creado satisfactoriamente la actividad.' : 'Hubo un error tratando de crear la actividad';

                //Mostrar Mensaje
                mensaje_alerta(mensaje, class_mensaje);

                //Ocultar Ventana
                $('#crearActividadModal').modal('hide');
            });

        }

    });

    //Eliminar Clientes
    $('#optionsModal').on("click", "#confirmarEliminarClienteIndividual", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        var clientes = [];
        clientes.push($(this).attr("data-cliente"));
//console.log('data cliente: '+clientes);
        $.ajax({
            url: phost() + 'clientes_potenciales/eliminar',
            data: {
                erptkn: tkn,
                id_clientes: clientes
            },
            type: "POST",
            dataType: "json",
            cache: false,
        }).done(function (json) {

            //Check Session
            if ($.isEmptyObject(json.session) == false) {
                window.location = phost() + "login?expired";
            }

            //If json object is empty.
            if ($.isEmptyObject(json.results[0]) == true) {
                return false;
            }

            $class_mensaje = json.results[0]['respuesta'] == true ? 'alert-success' : 'alert-danger';

            //Mostrar Mensaje
            mensaje_alerta(json.results[0]['mensaje'], $class_mensaje);

            //Recargar grid si la respuesta es true
            if (json.results[0]['respuesta'] == true)
            {
                //Recargar Grid
                $("#clientesPotencialesGrid").setGridParam({
                    url: phost() + 'clientes_potenciales/ajax-listar-clientes-potenciales',
                    datatype: "json",
                    postData: {
                        nombre: '',
                        compania: '',
                        telefono: '',
                        correo: '',
                        erptkn: tkn
                    }
                }).trigger('reloadGrid');
            }
        });

        //Ocultar ventana
        $('#optionsModal').modal('hide');

    });

    //Convertir Cliente Potencia a Juridico - Confirmacion
    $('#optionsModal').on("click", "#confirmarConvertirJuridicoBtn", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        var id_cliente = $(this).attr("data-cliente");

        //Ventana de Confirmacion
        var footer_buttons = ['<div class="row">',
            '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
            '<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>',
            '</div>',
            '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
            '<button id="convertirJuridicoBtn" data-cliente="' + id_cliente + '" class="btn btn-w-m btn-succsess btn-block" type="button">Convertir</button>',
            '</div>',
            '</div>'
        ].join('\n');

        //Init boton de opciones
        $('#optionsModal').find('.modal-title').empty().append('Confirme');
        $('#optionsModal').find('.modal-body').empty().append('¿Esta seguro que desea convertir este Cliente Potencial a Cliente Juridico?');
        $('#optionsModal').find('.modal-footer').empty().append(footer_buttons);
        $('#optionsModal').modal('show');
    });

    //Convertir Cliente Potencia a Natural - Confirmacion
    $('#optionsModal').on("click", "#confirmarConvertirNaturalBtn", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        var id_cliente = $(this).attr("data-cliente");

        //Ventana de Confirmacion
        var footer_buttons = ['<div class="row">',
            '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
            '<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>',
            '</div>',
            '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
            '<button id="convertirNaturalBtn" data-cliente="' + id_cliente + '" class="btn btn-w-m btn-success btn-block" type="button">Convertir</button>',
            '</div>',
            '</div>'
        ].join('\n');

        //Init boton de opciones
        $('#optionsModal').find('.modal-title').empty().append('Confirme');
        $('#optionsModal').find('.modal-body').empty().append('¿Esta seguro que desea convertir este Cliente Potencial a Cliente Natural?');
        $('#optionsModal').find('.modal-footer').empty().append(footer_buttons);
        $('#optionsModal').modal('show');
    });
    $('#optionsModal').on("click", "#convertirJuridicoBtn", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        var clientes = [];
        clientes.push($(this).attr("data-cliente"));

        $.ajax({
            url: phost() + 'clientes_potenciales/ajax-convertir-juridico',
            data: {
                erptkn: tkn,
                id_clientes: clientes
            },
            type: "POST",
            dataType: "json",
            cache: false,
        }).done(function (json) {

            //Check Session
            if ($.isEmptyObject(json.session) == false) {
                window.location = phost() + "login?expired";
            }

            //If json object is empty.
            if ($.isEmptyObject(json.results[0]) == true) {
                return false;
            }

            $class_mensaje = json.results[0]['respuesta'] == true ? 'alert-success' : 'alert-danger';

            //Mostrar Mensaje
            mensaje_alerta(json.results[0]['mensaje'], $class_mensaje);

            //Recargar grid si la respuesta es true
            if (json.results[0]['respuesta'] == true)
            {
                //Recargar Grid
                $("#clientesPotencialesGrid").setGridParam({
                    url: phost() + 'clientes_potenciales/ajax-listar-clientes-potenciales',
                    datatype: "json",
                    postData: {
                        nombre: '',
                        compania: '',
                        telefono: '',
                        correo: '',
                        erptkn: tkn
                    }
                }).trigger('reloadGrid');
            }
        });

        //Ocultar ventana
        $('#optionsModal').modal('hide');
    });

    //Acciones para el cliente Natural
    $('#optionsModal').on("click", "#convertirNaturalBtn", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        var clientes = [];
        clientes.push($(this).attr("data-cliente"));

        $.ajax({
            url: phost() + 'clientes_potenciales/ajax-convertir-natural',
            data: {
                erptkn: tkn,
                id_clientes: clientes
            },
            type: "POST",
            dataType: "json",
            cache: false,
        }).done(function (json) {

            //Check Session
            if ($.isEmptyObject(json.session) == false) {
                window.location = phost() + "login?expired";
            }

            //If json object is empty.
            if ($.isEmptyObject(json.results[0]) == true) {
                return false;
            }

            $class_mensaje = json.results[0]['respuesta'] == true ? 'alert-success' : 'alert-danger';

            //Mostrar Mensaje
            mensaje_alerta(json.results[0]['mensaje'], $class_mensaje);

            //Recargar grid si la respuesta es true
            if (json.results[0]['respuesta'] == true)
            {
                //Recargar Grid
                $("#clientesPotencialesGrid").setGridParam({
                    url: phost() + 'clientes_potenciales/ajax-listar-clientes-potenciales',
                    datatype: "json",
                    postData: {
                        nombre: '',
                        compania: '',
                        telefono: '',
                        correo: '',
                        erptkn: tkn
                    }
                }).trigger('reloadGrid');
            }
        });

        //Ocultar ventana
        $('#optionsModal').modal('hide');
    });

    //-------------------------
    // Botones de formulario de Busqueda
    //-------------------------
    $('#searchBtn').bind('click', searchBtnHlr);
    $('#clearBtn').click(function (e) {
        e.preventDefault();
        console.log('limpiar');
        $("#clientesPotencialesGrid").setGridParam({
            url: phost() + 'clientes_potenciales/ajax-listar-clientes-potenciales',
            datatype: "json",
            postData: {
                nombre: '',
                compania: '',
                telefono: '',
                correo: '',
                erptkn: tkn
            }
        }).trigger('reloadGrid');

        //Reset Fields
        $('#nombre, #compania, #telefono, #correo').val('');
    });

    //Boton de Exportar Clientes Potenciales
    $('#exportarClientePotencialBtn').on("click", function (e) {

        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

      //  if ($('#tabla').is(':visible') == true) {
           // console.log('Exportar JS');
            //Exportar Seleccionados del jQgrid
            var ids = [];
            ids = $('#clientesPotencialesGrid').jqGrid('getGridParam', 'selarrrow');

            //Verificar si hay seleccionados
            if (ids.length > 0) {

                $('#ids').val(ids);
                $('form#exportarClientesPotenciales').submit();
                $('body').trigger('click');
            }
        //}
    });

     //Boton de Exportar Clientes Potenciales
    $('#eliminarClientePotencialBtn').on("click", function (e) {

        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();


            //Exportar Seleccionados del jQgrid
            var ids = [];
            ids = $('#clientesPotencialesGrid').jqGrid('getGridParam', 'selarrrow');
            //console.log("ID de cliente:"+ids);
            //Verificar si hay seleccionados
           /* if (ids.length > 0) {

                $('#idss').val(ids);
                $('form#eliminarClientesPotenciales').submit();
                $('body').trigger('click');
            }*/
            //$('#clientesPotencialesGrid').jqGrid().trigger('reloadGrid');
           // window.location = phost() + 'clientes_potenciales/listar';

           var footer_buttons = ['<div class="row">',
            '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
            '<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>',
            '</div>',
            '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
            '<button id="confirmarEliminarClienteIndividual" data-cliente="' + ids + '" class="btn btn-w-m btn-danger btn-block" type="button">Eliminar</button>',
            '</div>',
            '</div>'
        ].join('\n');

        //Init boton de opciones
        $('#optionsModal').find('.modal-title').empty().append('Confirme');
        $('#optionsModal').find('.modal-body').empty().append('¿Esta seguro que desea eliminar este Cliente Potencial?');
        $('#optionsModal').find('.modal-footer').empty().append(footer_buttons);
        $('#optionsModal').modal('show');
        //$('form#eliminarClientesPotenciales').modal('hide');
        //$('.dropdown-menu').hide();
    });

});


function searchBtnHlr(e) {
    e.preventDefault();
    $('#searchBtn').unbind('click', searchBtnHlr);

    var nombre = $('#nombre').val();
    var compania = $('#compania').val();
    var telefono = $('#telefono').val();
    var correo = $('#correo').val();

    if (nombre != "" || compania != "" || telefono != "" || correo != "")
    {
        //console.log('buscar');
        $("#clientesPotencialesGrid").setGridParam({
            url: phost() + 'clientes_potenciales/ajax-listar-clientes-potenciales',
            datatype: "json",
            postData: {
                nombre: nombre,
                compania: compania,
                telefono: telefono,
                correo: correo,
                erptkn: tkn
            }
        }).trigger('reloadGrid');

        $('#searchBtn').bind('click', searchBtnHlr);
    } else {
        $('#searchBtn').bind('click', searchBtnHlr);
    }
}
