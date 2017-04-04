// Definimos la variable tabs la cual contendrá todo nuestro modulo.
var tablaGrupoClientes = (function () {

    var reloadGrid = function () {
        $('#tablaGrupoClientesGrid').jqGrid().trigger('reloadGrid');
    };
    // console.log("me ejecute");
    // Objeto al cual establecemos valores que vamos a usar mas adelante en este ambito.
    var st = {
        jqGrid: "#tablaGrupoClientesGrid",
        jqObj: $("#tablaGrupoClientesGrid"),
        jqObj1: $("#tablaGrupoClientesGrid_34_t"),
        jqPager: "#tablaGrupoClientesGridPager",
        jqTogle: "#jqgrid-column-togleGrupoClientes",
        noRecords: ".NoRecordsGrupoClientes",
        optionsModal: "#opcionesModal",
        optionsModalEliminar: "#optionsModal",
        searchBtn: "#searchBtn",
        clearBtn: "#clearBtn",
        segmento2: "grupo_clientes",
        //campos del formulario de busqueda
        nombre_cliente: "#nombre_cliente",
        telefono: "#telefono",
        correo: "#email",
        inputsSearch: "#nombre_cliente, #telefono, #email",
        exportarBtn: "#exportarClientePotencialBtn",
        editarBtn: "#editarClienteBtn",
        desagruparBtn: "#desagruparClienteBtn",
        agregarClienteBtn: "#agregarClienteBtn"

    };

    // Objeto vacío que guardará elementos que se manejan por HTML.
    var dom = {};

    // Función que llenará al objeto dom con los objetos HTML a través de jQuery ($).
    var catchDom = function () {

        dom.jqGrid = $(st.jqGrid);
        dom.optionsModal = $(st.optionsModal);
        dom.searchBtn = $(st.searchBtn);
        dom.clearBtn = $(st.clearBtn);
        dom.noRecords = $(st.noRecords);
        dom.jqObj = $(st.jqObj);
        dom.jqObj1 = $(st.jqObj1);
        //campos del formulario de busqueda
        dom.nombre_cliente = $(st.nombre_cliente);
        dom.telefono = $(st.telefono);
        dom.email = $(st.email);
        dom.inputsSearch = $(st.inputsSearch);
        dom.exportarBtn = $(st.exportarBtn);
        dom.optionsModalEliminar = $(st.optionsModalEliminar);
        dom.editarBtn = $(st.editarBtn);
        dom.desagruparBtn = $(st.desagruparBtn);
        dom.agregarClienteBtn = $(st.agregarClienteBtn);


    };


    // Función donde establecemos los eventos que tendrán cada elemento.
    var suscribeEvents = function () {
        dom.jqGrid.on("click", ".viewOptions", events.eMostrarModal);
        dom.jqGrid.on("click", ".viewOptionss", events.eMostrarModals);
        dom.searchBtn.bind('click', events.eSearchBtnHlr);
        dom.clearBtn.on('click', events.eClearBtn);
        dom.exportarBtn.on('click', events.eExportarBtn);
        dom.optionsModal.on('click', "#eliminarClienteBtn", events.eEliminarBtn);
        dom.optionsModal.on('click', "#editarClienteBtn", events.eEditarBtn);
        dom.optionsModalEliminar.on('click', "#confirmarEliminarClienteIndividual", events.eConfirmarEliminar);
        dom.optionsModal.on('click', "#desagruparClienteBtn", events.eDesagruparBtn);
        dom.optionsModal.on('click', "#agregarClienteBtn", events.eAgregarClienteBtn);
        //dom.botonCrear.on('click', events.modal);
        //  var tablaId = $(this).parent().parent().closest("table").attr('id');
        // tablaId.on('click', st.desagruparBtn, events.eDesagrupar);
    };

    /* Objeto que guarda métodos que se van a usar en cada evento definido
     en la función suscribeEvents. */
    var events = {
        modal: function (e) {
            // st.formId.find('#idEdicion').remove();
            // st.crear.find('.modal-title').empty().html('Crear: Grupo de Cliente');
            //st.crear.modal('show');

        },
        eMostrarModal: function (e)
        {
            // console.log("Option modal");
            var self = $(this);
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var nombre = '';
            var id = self.attr("data-id");

            var rowINFO = dom.jqObj.getRowData(id);
            var options = rowINFO["options"];


            nombre = rowINFO['nombre'];
            // console.log("Option modal: " + id);
            //console.log("Option modal: " + nombre);

            //Init boton de opciones
            dom.optionsModal.find('.modal-title').empty().append('Opciones: ' + nombre);
            dom.optionsModal.find('.modal-body').empty().append(options);
            dom.optionsModal.find('.modal-footer').empty();
            dom.optionsModal.modal('show');
        },
        eMostrarModals: function (e)
        {
            //console.log("Option modal: xxxxxxx" );
            var self = $(this);
            var tablaId = $(self).parent().parent().closest("table").attr('id');

            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            //var nombre = '';
            var id = self.attr("data-id");
            // console.log(id);
            var nombre = self.attr("data-name");
            // console.log(nombre);
            //var rowINFO = $('#'+tablaId).jqGrid().getRowData(id);

            var rowINFO = $.extend({}, $('#' + tablaId).jqGrid().getRowData(id));
            var options = rowINFO.link;

            //Init Modal
            dom.optionsModal.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.nombre).text() + '');
            dom.optionsModal.find('.modal-body').empty().append(options);
            dom.optionsModal.find('.modal-footer').empty();
            dom.optionsModal.modal('show');
        },
        eAgregarClienteBtn:function (e) {
            var self = $(this);
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            //var nombre = '';
            var id = self.attr("data-id");
             console.log(id);
            if(typeof(Storage) !== "undefined") {
            localStorage.setItem("id_grupo_cliente", id);
                }
            window.location = phost() + "clientes/listar";
            /*    $.ajax(
            {
                url: phost() + 'clientes/ajax-listar',
                    data: {
                erptkn: tkn,
            },
                type: "POST",
                    dataType: "json",
                cache: false
            }).done(function (json) {})*/
        },
        eDesagruparBtn: function (e) {
            // console.log("Desagrupar");
            var self = $(this);
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var id = self.attr("data-id");
            //console.log("Option modal eEliminarBtn " + id);
            var clientes = [];
            clientes.push(self.attr("data-id"));

            $.ajax({
                url: phost() + st.segmento2 + '/desagrupar',
                data: {
                    erptkn: tkn,
                    id_clientes: clientes
                },
                type: "POST",
                dataType: "json",
                cache: false
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
                    dom.jqGrid.setGridParam({
                        url: phost() + st.segmento2 + '/ajax-listar',
                        datatype: "json",
                        postData: {
                            nombre: '',
                            telefono: '',
                            correo: '',
                            erptkn: tkn
                        }
                    }).trigger('reloadGrid');
                }
            });

            $('#opcionesModal').modal('hide');

        },
        eSearchBtnHlr: function (e) {

            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            var nombre_cliente = $('#nombre_cliente').val();
            var telefono = $('#telefono').val();
            var correo = $('#email').val();

            if (nombre_cliente !== "" || telefono !== "" || correo !== "")
            {
                $.ajax({
                    url: phost() + st.segmento2 + '/ajax-buscar',
                    data: {
                        nombre: nombre_cliente,
                        telefono: telefono,
                        correo: correo,
                        erptkn: tkn
                    },
                    type: "POST",
                    dataType: "json",
                    cache: false

                }).done(function (result) {

                    recargar_tabla(result, nombre_cliente, telefono, correo);
                });

            } else {
                dom.searchBtn.bind('click', events.eSearchBtnHlr);
            }

        },
        eClearBtn: function (e) {
            e.preventDefault();
            dom.jqGrid.setGridParam({
                url: phost() + st.segmento2 + '/ajax-listar',
                datatype: "json",
                postData: {
                    ids: '',
                    erptkn: tkn
                },
                subGridOptions: {
                    //expand all rows on load
                    "expandOnLoad": false
                },
                subGridRowExpanded: function (subgrid_id, row_id) {
                    var subgrid_table_id, pager_id;
                    subgrid_table_id = subgrid_id + "_t";
                    var subGridId = $("#" + subgrid_table_id);
                    pager_id = "p_" + subgrid_table_id;
                    $("#" + subgrid_id).html("<table id='" + subgrid_table_id + "' class='scroll'></table><div id='" + pager_id + "' class='scroll'></div>");
                    jQuery("#" + subgrid_table_id).jqGrid({
                        //url: "subgrid.php?q=2&id=" + row_id,
                        url: phost() + st.segmento2 + '/ajax-listar-clientes',
                        mtype: "POST",
                        datatype: "json",
                        colNames: ['', 'No. Cliente', 'Nombre', 'Teléfono', 'E-mail', 'Crédito a favor', 'Saldo por cobrar', '', ''],
                        colModel: [
                            {name: 'uuid', index: 'uuid', width: 30, hidedlg: true, hidden: true},
                            {name: 'codigo', index: 'codigo', width: 30, sortable: true},
                            {name: 'nombre', index: 'nombre', width: 70, sortable: true},
                            {name: 'telefono', index: 'telefono', width: 50, sortable: false},
                            {name: 'correo', index: 'correo', width: 65, sortable: false},
                            {name: 'credito', index: 'credito', width: 35, sortable: false},
                            {name: 'saldo', index: 'saldo', width: 35, sortable: false},
                            {name: 'options', index: 'options', width: 35},
                            {name: 'link', index: 'link', width: 50, align: "center", sortable: false, resizable: false, hidden: true, hidedlg: true},
                        ],
                        postData: {
                            nombre: '',
                            telefono: '',
                            correo: '',
                            erptkn: tkn,
                            id: row_id
                        },
                        height: "auto",
                        autowidth: true,
                        rowList: [10, 20, 50, 100],
                        rowNum: 10,
                        page: 1,
                        pager: pager_id,
                        loadtext: '<p>Cargando...',
                        hoverrows: false,
                        viewrecords: true,
                        refresh: true,
                        gridview: true,
                        // multiselect: true,
                        sortname: 'nombre',
                        sortorder: "ASC",
                    });
                    jQuery("#" + subgrid_table_id).jqGrid('navGrid', "#" + pager_id, {edit: false, add: false, del: false});
                }
            }).trigger('reloadGrid');

            //Reset Fields
            dom.inputsSearch.val('');

            //Reset Chosens
            // dom.inputsSearch.trigger("chosen:updated");
        },
        eExportarBtn: function (e) {
            //console.log("hola mundo");  
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            //Exportar Seleccionados del jQgrid
            //var ids = [];
            //ids = $('#tablaGrupoClientesGrid').jqGrid('getGridParam', 'selarrrow');

            //Verificar si hay seleccionados
            // if (ids.length > 0) {

            //  $('#ids').val(ids);
            $('form#exportarGrupoClientes').submit();
            $('body').trigger('click');
            // }
        },
        eEliminarBtn: function (e) {
            //console.log("Option modal eEliminarBtn");
            var self = $(this);
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var id = self.attr("data-id");
            //console.log("Option modal eEliminarBtn " + id);
            var clientes = [];
            clientes.push(self.attr("data-id"));

            $.ajax({
                url: phost() + st.segmento2 + '/eliminar',
                data: {
                    erptkn: tkn,
                    id_clientes: clientes
                },
                type: "POST",
                dataType: "json",
                cache: false
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
                    dom.jqGrid.setGridParam({
                        url: phost() + st.segmento2 + '/ajax-listar',
                        datatype: "json",
                        postData: {
                            nombre: '',
                            telefono: '',
                            correo: '',
                            erptkn: tkn
                        }
                    }).trigger('reloadGrid');
                }
            });

            $('#opcionesModal').modal('hide');

        },
        eEditarBtn: function (e) {
            var self = $(this);
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var nombre = '';
            var descripcion = '';
            var id = self.attr("data-id");
            var rowINFO = dom.jqObj.getRowData(id);
            var options = rowINFO["options"];

            nombre = rowINFO['nombre'];
            descripcion = rowINFO['descripcion'];
            //console.log(nombre);
            // console.log(descripcion);
            var clientes = [];
            clientes.push(self.attr("data-id"));
            //console.log(clientes);
            $.ajax({
                url: phost() + st.segmento2 + '/ajax-ver',
                data: {
                    erptkn: tkn,
                    id_clientes: clientes
                },
                type: "POST",
                dataType: "json",
                cache: false
                        /*,success: function (result, status) {
                         console.log(result);
                         console.log(status);
                         }*/

            }).done(function (results) {
                //console.log(results);
                var data = results[0];
                // console.log(data.id_catalog_agrupador);
                //data.nombre;
                $('input[id="campo[nombre]"]').val(data.nombre);
                // $('#crearGrupoClienteForm').find('#campo[nombre]').prop('data-rule-required', false);
                $('#crearGrupoClienteForm').find('#padre_idCheck').trigger('click');
                $('#crearGrupoClienteForm').find('#select-id').prop('disabled', false);
                $('#crearGrupoClienteForm').find('#select-id').prop('value', data.id_catalog_agrupador);
                $('input[id="campo[descripcion]"]').val(data.descripcion);
            });
            $('#crearGrupoClienteForm').find('#ids').val(id);
            // $('form#crearGrupoClienteForm').submit();
            // $('body').trigger('click');
            $('#opcionesModal').modal('hide');
            //Inicializar opciones del Modal
            $('#modalCrearGrupoCliente').modal({
                backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                show: false
            });

            $('#modalCrearGrupoCliente').find('.modal-title').empty().append('Editar: Grupo de Cliente');
            $('#modalCrearGrupoCliente').modal('show');

        }

    };
    //Función recargar.
    var recargar = function () {

        //Reload Grid
        tabla.grid_obj.setGridParam({
            url: phost() + st.segmento2 + '/ajax-listar',
            datatype: "json",
            colNames: [
                'Grupo de cliente',
                'Descripción',
                'Σ Crédito a favor',
                'Σ Saldo por cobrar',
                '',
                ''
            ],
            colModel: [
                {name: 'nombre', index: 'nombre', width: 50},
                {name: 'descripcion', index: 'descripcion', width: 60, sortable: false},
                {name: 'credito', index: 'credito', width: 30, sortable: false},
                {name: 'saldo', index: 'saldo', width: 30, sortable: false},
                {name: 'link', index: 'link', width: 30, align: "center", sortable: false, resizable: false, hidedlg: true},
                {name: 'options', index: 'options', hidedlg: true, hidden: true}
            ],
            mtype: "POST",
            postData: {
                erptkn: tkn,
            },
            height: "auto",
            autowidth: true,
            rowList: [10, 20, 50, 100],
            rowNum: 10,
            page: 1,
            pager: st.jqPager,
            loadtext: '<p>Cargando...',
            pgtext: "Página {0} de {1}",
            hoverrows: false,
            viewrecords: true,
            refresh: true,
            gridview: true,
            multiselect: multiselect,
            sortname: 'nombre',
            sortorder: "DESC",
        }).trigger('reloadGrid');

    };


    var muestra_tabla = function () {
        dom.jqGrid.jqGrid({
            url: phost() + st.segmento2 + '/ajax-listar',
            datatype: "json",
            colNames: [
                'Grupo de cliente',
                'Descripción',
                'Σ Crédito a favor',
                'Σ Saldo por cobrar',
                '',
                ''
            ],
            colModel: [
                {name: 'nombre', index: 'nombre', width: 50},
                {name: 'descripcion', index: 'descripcion', width: 60, sortable: false},
                {name: 'credito', index: 'credito', width: 30, sortable: false},
                {name: 'saldo', index: 'saldo', width: 30, sortable: false},
                {name: 'link', index: 'link', width: 30, align: "center", sortable: false, resizable: false, hidedlg: true},
                {name: 'options', index: 'options', hidedlg: true, hidden: true}
            ],
            mtype: "POST",
            postData: {
                erptkn: tkn,
            },
            height: "auto",
            autowidth: true,
            rowList: [10, 20, 50, 100],
            rowNum: 10,
            page: 1,
            pager: st.jqPager,
            loadtext: '<p>Cargando...',
            pgtext: "Página {0} de {1}",
            hoverrows: false,
            viewrecords: true,
            refresh: true,
            gridview: true,
            subGrid: true,
            //caption: "Custom Icons in Subgrid",
            multiselect: multiselect,
            sortname: 'nombre',
            sortorder: "DESC",
            beforeProcessing: function (data, status, xhr) {
                //Check Session
                if ($.isEmptyObject(data.session) == false) {
                    window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function () {//propiedadesGrid_cb
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find(st.jqGrid + "_cb, #jqgh_" + st.jqGrid + "_link").css("text-align", "center");
            },
            beforeRequest: function (data, status, xhr) {},
            loadComplete: function (data) {

                //check if isset data
                if (data['total'] == 0) {
                    $('#gbox_' + st.jqGrid).hide();
                    dom.noRecords.empty().append('No se encontraron Grupo de Clientes.').css({"color": "#868686", "padding": "30px 0 0"}).show();
                } else {
                    dom.noRecords.hide();
                    $('#gbox_' + st.jqGrid).show();
                }

                if (multiselect == true) {
                    //---------
                    // Cargar plugin jquery Sticky Objects
                    //----------
                    //add class to headers
                    dom.jqGrid.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");

                    //floating headers
                    $('#gridHeader').sticky({
                        getWidthFrom: '.ui-jqgrid-view',
                        className: 'jqgridHeader'
                    });

                    //Arreglar tamaño de TD de los checkboxes
                    $(st.jqGrid + "_cb").css("width", "50px");
                    $(st.jqGrid + " tbody tr").children().first("td").css("width", "50px");
                }

            },
            onSelectRow: function (id) {
                $(this).find('tr#' + id).removeClass('ui-state-highlight');
            },
            // define the icons in subgrid
            subGridOptions: {
                "plusicon": "ui-icon-triangle-1-e",
                "minusicon": "ui-icon-triangle-1-s",
                "openicon": "ui-icon-arrowreturn-1-e",
                //expand all rows on load
                "expandOnLoad": false
            },
            subGridRowExpanded: function (subgrid_id, row_id) {
                var subgrid_table_id, pager_id;
                subgrid_table_id = subgrid_id + "_t";
                var subGridId = $("#" + subgrid_table_id);
                pager_id = "p_" + subgrid_table_id;
                $("#" + subgrid_id).html("<table id='" + subgrid_table_id + "' class='scroll'></table><div id='" + pager_id + "' class='scroll'></div>");
                jQuery("#" + subgrid_table_id).jqGrid({
                    //url: "subgrid.php?q=2&id=" + row_id,
                    url: phost() + st.segmento2 + '/ajax-listar-clientes',
                    mtype: "POST",
                    datatype: "json",
                    colNames: ['', 'No. Cliente', 'Nombre', 'Teléfono', 'E-mail', 'Crédito a favor', 'Saldo por cobrar', '', ''],
                    colModel: [
                        {name: 'uuid', index: 'uuid', width: 30, hidedlg: true, hidden: true},
                        {name: 'codigo', index: 'codigo', width: 30, sortable: true},
                        {name: 'nombre', index: 'nombre', width: 70, sortable: true},
                        {name: 'telefono', index: 'telefono', width: 50, sortable: false},
                        {name: 'correo', index: 'correo', width: 65, sortable: false},
                        {name: 'credito', index: 'credito', width: 35, sortable: false},
                        {name: 'saldo', index: 'saldo', width: 35, sortable: false},
                        {name: 'options', index: 'options', width: 35},
                        {name: 'link', index: 'link', width: 50, align: "center", sortable: false, resizable: false, hidden: true, hidedlg: true},
                    ],
                    postData: {
                        erptkn: tkn,
                        id: row_id
                    },
                    height: "auto",
                    autowidth: true,
                    rowList: [10, 20, 50, 100],
                    rowNum: 10,
                    page: 1,
                    pager: pager_id,
                    loadtext: '<p>Cargando...',
                    hoverrows: false,
                    viewrecords: true,
                    refresh: true,
                    gridview: true,
                    // multiselect: true,
                    sortname: 'nombre',
                    sortorder: "ASC",
                    beforeProcessing: function (data, status, xhr) {
                        if ($.isEmptyObject(data.session) === false) {
                            window.location = phost() + "login?expired";
                        }
                        /*  console.log(data.rows[0].cell[5]);
                         var i, sumCredito = 0, sumSaldo = 0;
                         for (i = 0; i < data.rows.length; i++) {
                         
                         var credito = data.rows[i].cell[5];
                         var valorCredito = parseFloat(credito.substr(31));
                         //console.log(valorCredito.toFixed(2));
                         sumCredito = sumCredito + valorCredito;
                         
                         var saldo = data.rows[i].cell[6];
                         var valorSaldo = parseFloat(saldo.substr(30));
                         sumSaldo = sumSaldo + valorSaldo;
                         
                         
                         }
                         
                         jQuery('#tablaGrupoClientesGrid').jqGrid('setCell', row_id, 'credito', '<label class="totales-success">' + sumCredito.toFixed(2) + '</label>');
                         jQuery('#tablaGrupoClientesGrid').jqGrid('setCell', row_id, 'saldo', '<label class="totales-danger">' + sumSaldo.toFixed(2) + '</label>');
                         */
                    },
                    loadBeforeSend: function () {//propiedadesGrid_cb
                        $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                        $(this).closest("div.ui-jqgrid-view").find("#tablaClientesGrid_cb, #jqgh_tablaClientesGrid_link").css("text-align", "center");
                    },
                    loadComplete: function (data, status, xhr) {


                        if ($("#tablaClientesGrid").getGridParam('records') === 0) {
                            $('#gbox_' + "tablaClientesGrid").hide();
                            $('#' + "tablaClientesGrid" + 'NoRecords').empty().append('No se encontraron Clientes.').css({"color": "#868686", "padding": "30px 0 0"}).show();
                        } else {
                            $('#gbox_' + "tablaClientesGrid").show();
                            $('#' + "tablaClientesGrid" + 'NoRecords').empty();
                        }

                        //---------
                        // Cargar plugin jquery Sticky Objects
                        //----------
                        //add class to headers
                        $("#tablaClientesGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");
                        //floating headers
                        $('#gridHeader').sticky({
                            getWidthFrom: '.ui-jqgrid-view',
                            className: 'jqgridHeader'
                        });
                    },
                    onSelectRow: function (id) {
                        $(this).find('tr#' + id).removeClass('ui-state-highlight');
                    }
                });
                jQuery("#" + subgrid_table_id).jqGrid('navGrid', "#" + pager_id, {edit: false, add: false, del: false});
            }
        });
        //jQuery("#sg2").jqGrid('navGrid', '#psg2', {add: false, edit: false, del: false});
        dom.jqGrid.jqGrid('columnToggle');

        //-------------------------
        // Redimensionar Grid al cambiar tamaño de la ventanas.
        //-------------------------
        $(window).resizeEnd(function () {
            $(".ui-jqgrid").each(function () {
                var w = parseInt($(this).parent().width()) - 6;
                var tmpId = $(this).attr("id");
                var gId = tmpId.replace("gbox_", "");
                $("#" + gId).setGridWidth(w);
            });
        }
        );
    };

    var recargar_tabla = function (result, nombre_cliente, telefono, correo) {

        dom.jqGrid.setGridParam({
            url: phost() + st.segmento2 + '/ajax-listar',
            datatype: "json",
            mtype: "POST",
            postData: {
                ids: result,
                erptkn: tkn
            },
            subGridOptions: {
                //expand all rows on load
                "expandOnLoad": true
            },
            subGridRowExpanded: function (subgrid_id, row_id) {
                var subgrid_table_id, pager_id;
                subgrid_table_id = subgrid_id + "_t";
                var subGridId = $("#" + subgrid_table_id);
                pager_id = "p_" + subgrid_table_id;
                $("#" + subgrid_id).html("<table id='" + subgrid_table_id + "' class='scroll'></table><div id='" + pager_id + "' class='scroll'></div>");
                jQuery("#" + subgrid_table_id).jqGrid({
                    //url: "subgrid.php?q=2&id=" + row_id,
                    url: phost() + st.segmento2 + '/ajax-listar-clientes',
                    mtype: "POST",
                    datatype: "json",
                    colNames: ['', 'No. Cliente', 'Nombre', 'Teléfono', 'E-mail', 'Crédito a favor', 'Saldo por cobrar', '', ''],
                    colModel: [
                        {name: 'uuid', index: 'uuid', width: 30, hidedlg: true, hidden: true},
                        {name: 'codigo', index: 'codigo', width: 30, sortable: true},
                        {name: 'nombre', index: 'nombre', width: 70, sortable: true},
                        {name: 'telefono', index: 'telefono', width: 50, sortable: false},
                        {name: 'correo', index: 'correo', width: 65, sortable: false},
                        {name: 'credito', index: 'credito', width: 35, sortable: false},
                        {name: 'saldo', index: 'saldo', width: 35, sortable: false},
                        {name: 'options', index: 'options', width: 35},
                        {name: 'link', index: 'link', width: 50, align: "center", sortable: false, resizable: false, hidden: true, hidedlg: true},
                    ],
                    postData: {
                        nombre: nombre_cliente,
                        telefono: telefono,
                        correo: correo,
                        erptkn: tkn,
                        id: row_id
                    },
                    height: "auto",
                    autowidth: true,
                    rowList: [10, 20, 50, 100],
                    rowNum: 10,
                    page: 1,
                    pager: pager_id,
                    loadtext: '<p>Cargando...',
                    hoverrows: false,
                    viewrecords: true,
                    refresh: true,
                    gridview: true,
                    expandOnLoad: true,
                    // multiselect: true,
                    sortname: 'nombre',
                    sortorder: "ASC",
                    reloadOnExpand: true
                });
                jQuery("#" + subgrid_table_id).jqGrid('navGrid', "#" + pager_id, {edit: false, add: false, del: false});
            }
        }).trigger('reloadGrid');
    };
    // Función que inicializará los funciones decritas anteriormente.
    var initialize = function () {
        catchDom();
        suscribeEvents();
        muestra_tabla();
    };

    /* Retorna un objeto literal con el método init haciendo referencia a la
     función initialize. */
    return{
        init: initialize
    };
})();

//verificar si la url actual es contactos
//de lo contrario no mostrar multiselect del jqgrid
var multiselect = window.location.pathname.match(/consumos/g) ? true : false;


// Ejecutando el método "init" del módulo tabs.
tablaGrupoClientes.init();
