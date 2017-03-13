$(document).ready(function () {


    $('#searchBtn').bind('click');


    $(function () {
		
		var opcionesModal = $('#opcionesModal');
        var grid = $("#AgentesGrid");
        grid.jqGrid({
            url: phost() + 'agentes/ajax-listar',
            datatype: "json",
            colNames: ['', 'Nombre', 'Cédula', 'Teléfono', 'E-mail', 'Participación ', 'Estado', '', ''],
            colModel: [
                {name: 'id', index: 'id', hidedlg: true, key: true, hidden: true},
                {name: 'nombre', index: 'nombre', sorttype: "text", sortable: true, width: 150},
                {name: 'identificacion', index: 'identificacion', sorttype: "identificacion", sortable: true, width: 90},
                {name: 'telefono', index: 'telefono', sorttype: "text", sortable: true, width: 90},
                {name: 'correo', index: 'correo', sorttype: "text", sortable: true, width: 150},
                {name: 'porcentaje_participacion', index: 'porcentaje_participacion', sorttype: "text", sortable: true, width: 90},
                {name: 'estado', index: 'estado', sorttype: "text", sortable: true, width: 90},
                {name: 'opciones', index: 'opciones', sortable: false, align: 'center'},
                {name: 'link', index: 'link', hidedlg: true, hidden: true}
            ],
            mtype: "POST",
            postData: {erptkn: tkn},
            height: "auto",
            autowidth: true,
            rowList: [10, 20, 50, 100],
            rowNum: 10,
            page: 1,
            pager: "#pager_agentes",
            loadtext: '<p>Cargando...</p>',
            hoverrows: false,
            viewrecords: true,
            refresh: true,
            gridview: true,
            multiselect: true,
            sortname: 'nombre',
            sortorder: "ASC",
            beforeRequest: function (data, status, xhr) {},
            loadComplete: function (data) {

                //check if isset data
                if (data.total == 0) {
                    $('#gbox_AgentesGrid').hide();
                    $('.NoRecordsAgente').empty().append('No se encontraron Agentes.').css({"color": "#868686", "padding": "30px 0 0"}).show();
                } else {
                    $('.NoRecordsAgente').hide();
                    $('#gbox_AgentesGrid').show();
                }

                //---------
                // Cargar plugin jquery Sticky Objects
                //----------
                //add class to headers
                $("#AgentesGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");
                $("#AgentesGrid").find('div.tree-wrap').children().removeClass('ui-icon');
                //floating headers
                $('#gridHeader').sticky({
                    getWidthFrom: '.ui-jqgrid-view',
                    className: 'jqgridHeader'
                });
            },
            onSelectRow: function (id) {
                $(this).find('tr#' + id).removeClass('ui-state-highlight');
            },
            loadBeforeSend: function () {//propiedadesGrid_cb
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find("#AgentesGrid_cb, #jqgh_AgentesGrid_cb").css("text-align", "center");
            },
        });
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

        $("#AgentesGrid").on("click", ".viewOptions", function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var id = $(this).attr("data-id");
            //console.log("id -"+id);
            var rowINFO = $("#AgentesGrid").getRowData(id);
            //console.log(rowINFO);
            var options = rowINFO["link"];
            //Init boton de opciones
            $('#opcionesModal').find('.modal-title').empty().html('Opciones: ' + rowINFO["nombre"] + '');
            $('#opcionesModal').find('.modal-body').empty().html(options);
            $('#opcionesModal').find('.modal-footer').empty();
            $('#opcionesModal').modal('show');
        });

        $("#exportarBtn").on("click", function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            if ($('#tabla').is(':visible') == true) {
                //Exportar Seleccionados del jQgrid
                var ids = [];
                ids = grid.jqGrid('getGridParam', 'selarrrow');

                //Verificar si hay seleccionados
                if (ids.length > 0) {
                    //console.log(ids);	
                    $('#ids').val(ids);
                    $('form#exportarAgentes').submit();
                    $('body').trigger('click');
                }
            }
        });

        var moduloAgentes = (function () {
            return {
                ajaxcambiarEstados: function (parametros) {
                    //console.log("parametros");
                    //console.log(parametros);
                    return $.post(phost() + 'agentes/ajax_cambiar_estados', $.extend({
                        erptkn: tkn
                    }, parametros));
                },
                ajaxcambiarObtenerPoliticas: function () {
                    return $.ajax({
                        url: "agentes/obtener_politicas",
                        dataType: "json"
                    });
                },
                ajaxcambiarObtenerPoliticasGeneral: function () {
                    return $.ajax({
                        url: "agentes/obtener_politicas_general",
                        dataType: "json"
                    });
                },
                ajaxcambiarObtenerPoliticasGenerales: function () {
                    return $.ajax({
                        url: "agentes/obtener_politicasgenerales",
                        dataType: "json"
                    });
                },
				cambiarAgentePrincipal:function(parametros){
					return $.post(phost() + 'agentes/ajax_cambiar_agente_principal', $.extend({
						erptkn: tkn,
						id:parametros,
					}, parametros));
				},
            };
        })();
		
		$("#opcionesModal").on("click", ".cambiarAgentePrincipal", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();	
			
			//Cerrar modal de opciones
			opcionesModal.modal('hide');
			var datosagente=moduloAgentes.cambiarAgentePrincipal($(this).attr("data-id"));
			
			var datos=datosagente.success(function (data) {
				$.each(data, function(i,filename) {
					recargar();
				});
			});	
		});

        $("#cambiarEstadosBtn").on("click", function (e) {

            var opcionesModal = $('#opcionesModal');

            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            $("#opcionesModal").on("click", ".activo", function () {

                //Seleccionados del jQgrid
                var ids = [];
                var ids_aprobados = 0;
                var ids_activos = 0;
                var ids_inactivos = 0;
                ids = grid.jqGrid('getGridParam', 'selarrrow');
                //console.log(ids);
                if (ids.length > 0) {
                    ids_aprobados = _.filter(ids, function (fila) {
                        var infoFila = $.extend({}, grid.getRowData(fila));
                        if ($(infoFila.estado).text() == 'Por Aprobar') {
                            return infoFila.id;
                        }
                    });
                    ids_activos = _.filter(ids, function (fila) {
                        var infoFila = $.extend({}, grid.getRowData(fila));
                        if ($(infoFila.estado).text() == 'Activo') {
                            return infoFila.id;
                        }
                    });
                    ids_inactivos = _.filter(ids, function (fila) {
                        var infoFila = $.extend({}, grid.getRowData(fila));
                        if ($(infoFila.estado).text() == 'Inactivo') {
                            return infoFila.id;
                        }
                    });
                }
                ;

                var politicas_general = moduloAgentes.ajaxcambiarObtenerPoliticasGeneral();
                var permisos_generales = politicas_general.success(function (data) {

                    if (data > 0)
                    {

                        var politicas = moduloAgentes.ajaxcambiarObtenerPoliticas();
                        var politicas_general_total = moduloAgentes.ajaxcambiarObtenerPoliticasGenerales();
                        var permisos_generales = politicas_general_total.success(function (data) {
                            var permisos_total = [];
                            $.each(data, function (i, filename) {
                                permisos_total.push(filename);

                            });
                            console.log(permisos_total.indexOf(16, 0) + " " + permisos_total.indexOf(17, 0) + " " + permisos_total.indexOf(18, 0) + " <br> ");

                            var permisos1 = politicas.success(function (data) {

                                var permisos = [];
                                $.each(data, function (i, filename) {
                                    permisos.push(filename);
                                });
                                console.log(permisos.indexOf(16, 0) + " " + permisos.indexOf(17, 0) + " " + permisos.indexOf(18, 0));
//                                if (permisos.indexOf(16, 0) != -1 || permisos.indexOf(17, 0) != -1 || permisos.indexOf(18, 0) != -1)
//                                {

                                if (ids_aprobados.length > 0)
                                {
                                    if (((permisos.indexOf(16, 0) == -1) && (permisos_total.indexOf(16, 0) == -1)) || (permisos.indexOf(16, 0) != -1))
                                {
                                        var datos = {campo: {estado: 'Activo', ids: ids_aprobados}};
                                        var cambio = moduloAgentes.ajaxcambiarEstados(datos);
                                        cambio.done(function (response) {
                                            var agentes = response;
                                            _.map(agentes, function (ant) {
                                                $("#AgentesGrid").jqGrid('setCell', ant.id, 'estado', ant.estado);
                                            });
                                            opcionesModal.modal('hide');
                                            $("#mensaje").hide();

                                            recargar();
                                        });
                                    } else {
                                        opcionesModal.modal('hide');
                                        $("#mensaje").show();
                                        $("#mensaje").html('<div id="mensaje" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Usted no tiene permisos para cambiar a este estado</div>');
                                    }
                                } else if (ids_inactivos.length > 0)
                                {
                                    var datos = {campo: {estado: 'Activo', ids: ids_inactivos}};

                                    if (((permisos.indexOf(18, 0) == -1) && (permisos_total.indexOf(18, 0) == -1)) || (permisos.indexOf(18, 0) != -1))
                                {
                                        //console.log(datos);							
                                        var cambio = moduloAgentes.ajaxcambiarEstados(datos);
                                        cambio.done(function (response) {
                                            //console.log(response);
                                            var agentes = response;
                                            _.map(agentes, function (ant) {
                                                $("#AgentesGrid").jqGrid('setCell', ant.id, 'estado', ant.estado);
                                            });
                                            opcionesModal.modal('hide');
                                            $("#mensaje").hide();

                                            recargar();
                                        });
                                    } else {
                                        opcionesModal.modal('hide');
                                        $("#mensaje").show();
                                        $("#mensaje").html('<div id="mensaje" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Usted no tiene permisos para cambiar a este estado</div>');
                                    }
                                }
//                                } else {
//                                    opcionesModal.modal('hide');
//                                    $("#mensaje").show();
//                                    $("#mensaje").html('<div id="mensaje" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Usted no tiene permisos para cambiar a este estado</div>');
//                                }
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
                            var cambio = moduloAgentes.ajaxcambiarEstados(datos);
                            cambio.done(function (response) {
                                var agentes = response;
                                _.map(agentes, function (ant) {
                                    $("#AgentesGrid").jqGrid('setCell', ant.id, 'estado', ant.estado);
                                });
                                opcionesModal.modal('hide');
                                $("#mensaje").hide();

                                recargar();
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
                ids = grid.jqGrid('getGridParam', 'selarrrow');
                if (ids.length > 0) {
                    ids_aprobados = _.filter(ids, function (fila) {
                        var infoFila = $.extend({}, grid.getRowData(fila));
                        if ($(infoFila.estado).text() == 'Por Aprobar') {
                            return infoFila.id;
                        }
                    });
                    ids_activos = _.filter(ids, function (fila) {
                        var infoFila = $.extend({}, grid.getRowData(fila));
                        if ($(infoFila.estado).text() == 'Activo') {
                            return infoFila.id;
                        }
                    });
                    ids_inactivos = _.filter(ids, function (fila) {
                        var infoFila = $.extend({}, grid.getRowData(fila));
                        if ($(infoFila.estado).text() == 'Inactivo') {
                            return infoFila.id;
                        }
                    });
                }
                ;

                var politicas_general = moduloAgentes.ajaxcambiarObtenerPoliticasGeneral();
                var permisos_generales = politicas_general.success(function (data) {

                    if (data > 0)
                    {
                        var politicas = moduloAgentes.ajaxcambiarObtenerPoliticas();
                        var politicas_general_total = moduloAgentes.ajaxcambiarObtenerPoliticasGenerales();
                        var permisos_generales = politicas_general_total.success(function (data) {
                            var permisos_total = [];
                            $.each(data, function (i, filename) {
                                permisos_total.push(filename);

                            });
                            console.log(permisos_total.indexOf(16, 0) + " " + permisos_total.indexOf(17, 0) + " " + permisos_total.indexOf(18, 0) + " <br> ");


                            var permisos1 = politicas.success(function (data) {
                                var permisos = [];
                                $.each(data, function (i, filename) {
                                    permisos.push(filename);
                                });
                                console.log(permisos.indexOf(16, 0) + " " + permisos.indexOf(17, 0) + " " + permisos.indexOf(18, 0));
//                            if (permisos.indexOf(16, 0) != -1 || permisos.indexOf(17, 0) != -1 || permisos.indexOf(18, 0) != -1)
//                            {
                                if (ids_activos.length > 0)
                                {
                                    if (((permisos.indexOf(17, 0) == -1) && (permisos_total.indexOf(17, 0) == -1)) || (permisos.indexOf(17, 0) != -1))
                                {
                                        var datos = {campo: {estado: 'Inactivo', ids: ids_activos}};
                                        var cambio = moduloAgentes.ajaxcambiarEstados(datos);
                                        cambio.done(function (response) {
                                            //console.log(response);				  							
                                            var agentes = response;
                                            _.map(agentes, function (ant) {
                                                $("#AgentesGrid").jqGrid('setCell', ant.id, 'estado', ant.estado);
                                            });
                                            opcionesModal.modal('hide');
                                            $("#mensaje").hide();

                                            recargar();
                                        });
                                    } else {
                                        opcionesModal.modal('hide');
                                        $("#mensaje").show();
                                        $("#mensaje").html('<div id="mensaje" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Usted no tiene permisos para cambiar a este estado</div>');
                                    }
                                }
//                            } else {
//                                opcionesModal.modal('hide');
//                                $("#mensaje").show();
//                                $("#mensaje").html('<div id="mensaje" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Usted no tiene permisos para cambiar a este estado</div>');
//                            }
                                return permisos;
                            });
                        });
                    } else
                    {
                        if (ids_activos.length > 0)
                        {
                            var datos = {campo: {estado: 'Inactivo', ids: ids_activos}};
                            var cambio = moduloAgentes.ajaxcambiarEstados(datos);
                            cambio.done(function (response) {
                                //console.log(response);
                                var agentes = response;
                                _.map(agentes, function (ant) {
                                    $("#AgentesGrid").jqGrid('setCell', ant.id, 'estado', ant.estado);
                                });
                                opcionesModal.modal('hide');
                                $("#mensaje").hide();

                                recargar();
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
            ids = grid.jqGrid('getGridParam', 'selarrrow');
            //console.log(ids);
            //console.log("bbbbb");
            if (ids.length > 0) {
                ids_aprobados = _.filter(ids, function (fila) {
                    var infoFila = $.extend({}, grid.getRowData(fila));
                    if ($(infoFila.estado).text() == 'Por Aprobar') {
                        return infoFila.id;
                    }
                });
                ids_activos = _.filter(ids, function (fila) {
                    var infoFila = $.extend({}, grid.getRowData(fila));
                    if ($(infoFila.estado).text() == 'Activo') {
                        return infoFila.id;
                    }
                });
                ids_inactivos = _.filter(ids, function (fila) {
                    var infoFila = $.extend({}, grid.getRowData(fila));
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
                    $("#mensaje").html('<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button> Los Agentes seleccionados tienen estados diferentes');
                }
            }
        });


    });



    $('#searchBtn').on("click", function (e) {

        var nombre = $('#nombre').val();
        var apellido = $('#apellido').val();
        var identificacion = $('#identificacion').val();
        var telefono = $('#telefono').val();
        var correo = $('#correo').val();
        if (nombre != "" || apellido != "" || identificacion != "" || telefono != "" || correo != "")
        {
            //Reload Grid
            $("#AgentesGrid").setGridParam({
                url: phost() + 'agentes/ajax-listar',
                datatype: "json",
                postData: {
                    nombre: nombre,
                    apellido: apellido,
                    identificacion: identificacion,
                    telefono: telefono,
                    correo: correo,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        } else {
            $("#AgentesGrid").setGridParam({
                url: phost() + 'agentes/ajax-listar',
                datatype: "json",
                postData: {
                    nombre: "",
                    apellido: "",
                    identificacion: "",
                    telefono: "",
                    correo: "",
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }
    });


});

var recargar = function () {

    //Reload Grid
    $("#AgentesGrid").setGridParam({
        url: phost() + 'agentes/ajax-listar',
        datatype: "json",
        postData: {
            nombre: '',
            apellido: '',
            identificacion: '',
            telefono: '',
            correo: '',
            erptkn: tkn
        }
    }).trigger('reloadGrid');
}

$('#clearBtn').on("click", function (e) {
    e.preventDefault();

    $("#AgentesGrid").setGridParam({
        url: phost() + 'agentes/ajax-listar',
        datatype: "json",
        postData: {
            nombre: '',
            apellido: '',
            identificacion: '',
            telefono: '',
            correo: '',
            erptkn: tkn
        }
    }).trigger('reloadGrid');

    //Reset Fields
    $('#nombre, #apellido, #identificacion, #telefono, #correo').val('');
});

