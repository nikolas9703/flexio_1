//Modulo
Array.prototype.move = function (old_index, new_index) {
    if (new_index >= this.length) {
        var k = new_index - this.length;
        while ((k--) + 1) {
            this.push(undefined);
        }
    }
    this.splice(new_index, 0, this.splice(old_index, 1)[0]);
};

var tablaSolicitudes = (function () {

    var url = 'solicitudes/ajax-listar',
	submenu = localStorage.getItem("ml-selected"),
	uuid = window.location.href.split("/").pop();
    var grid_id = "tablaSolicitudesGrid";
    var grid_obj = $("#tablaSolicitudesGrid");
    var opcionesModal = $('#opcionesModal');
    var opcionesModalAnular = $('#opcionesModalAnular');
    var opcionesModalAprobar = $('#opcionesModalAprobar');
    var opcionesModalRechazar = $('#opcionesModalRechazar');
    var botones = {
        opciones: ".viewOptions",
        editar: "",
        buscar: "#searchBtn",
        limpiar: "#clearBtn",
        modalstate: "span.estadoSolicitudes",
        opcionesmodalstate: "#cambio_estado_solicitud",
        exportar: "#exportarSolicitudesLnk",
        cambiarEstado: "#cambiarEstadoSolicitudesLnk",
        anularsolicitud: ".anular_solicitud",
        aprobarsolicitud: ".aprobar_solicitud",
        subir_archivos: ".subir_archivos_solicitudes",
    };
    var tabla = function () {
        //localstorage to get modulo seleccionado
        var modulo = '',
                setting = {modulo: submenu,
                    orderBy: 'fecha_creacion1',
                    moveTo: false,
                    sortorder: 'asc'
                };

        if (submenu == 'Clientes')
        {
            setting = {modulo: 'Clientes',
                orderBy: 'fecha_creacion1',
                moveTo: 7,
                sortorder: 'desc',
                cliente: true

            };
        }
        if (submenu == 'Aseguradoras')
        {
            setting = {modulo: 'Aseguradoras',
                orderBy: 'fecha_creacion1',
                moveTo: false,
                sortorder: 'desc'
            };
        }
        //var uuid = submenu!="Solicitudes" ? uuidModulo : false;
        //inicializar jqgrid
        var columNames = [
            {colname: '', hidden: true, index: 'id'},
            {colname: 'No. Solicitud', index: "numero", width: 50},
            {colname: 'Cliente', index: 'nombre_cliente', width: 70, hidden: setting.cliente},
            {colname: 'Aseguradora', index: 'aseguradora_id', width: 40},
            {colname: 'Ramo', index: 'ramo', width: 40},
            {colname: 'Tipo', index: 'id_tipo_poliza', width: 30},
            {colname: 'D&iacute;as transcurridos', index: 'created_at', width: 40, },
            {colname: 'Fecha de creaci&oacute;n', index: 'fecha_creacion', width: 40, moveTo: setting.moveTo},
            {colname: 'Usuario', index: 'usuario_id', width: 40},
            {colname: 'Estado', index: 'estado', width: 40},
            {colname: '', index: 'link', width: 50, sortable: false, resizable: false, hidedlg: true, align: "center"},
            {colname: '', index: 'options', hidedlg: true, hidden: true},
            {colname: '', index: 'modalstate', align: "center", hidedlg: true, hidden: true},
            {colname: '', index: 'modalanular', align: "center", hidedlg: true, hidden: true},
            {colname: '', index: 'modalaprobar', align: "center", hidedlg: true, hidden: true},
            {colname: '', index: 'modalarechazar', align: "center", hidedlg: true, hidden: true},
        ];
        customJQGrid(grid_obj, url, grid_id, modulo, uuid, columNames, setting);
        //Al redimensionar ventana
        $(window).resizeEnd(function () {
            tablaSolicitudes.redimensionar();
        });
    };
    //Inicializar Eventos de Botones
    var eventos = function () {

        //Boton de Buscar Colaborador
        $(botones.buscar).on("click", function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            buscarSolicitudes();
        });
        //Boton de Reiniciar jQgrid
        $(botones.limpiar).on("click", function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            recargar();
            limpiarCampos();
        });
        //Boton de Activar Colaborador
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
        //Boton de opciones
        grid_obj.on("click", botones.opciones, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            var id = $(this).attr("data-id");
            var rowINFO = $.extend({}, grid_obj.getRowData(id));
            var options = rowINFO.options;

            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.numero).text() + '');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
            opcionesModal.on('click', '#cambio_estado_solicitud', function (e) {
                e.preventDefault();
                e.returnValue = false;
                e.stopPropagation();
                var id = $(this).attr("data-id");

                var rowINFO = $.extend({}, grid_obj.getRowData(id));
                var options = rowINFO.modalstate;

                var estado = $(this).attr("data-estado");
                if (estado == "Rechazada" || estado == "Aprobada") {
                    opcionesModal.find('.modal-title').empty().append('Mensaje');
                    opcionesModal.find('.modal-body').empty().append("<button class='btn btn-block btn-outline btn-warning'><p>No Puede realizar</p> <p>el cambio de estado</p></button>");
                    opcionesModal.find('.modal-footer').empty();
                    opcionesModal.modal('show');
                } else {
                    //Init Modal
                    opcionesModal.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.numero).text() + '');
                    opcionesModal.find('.modal-body').empty().append(options);
                    opcionesModal.find('.modal-footer').empty();
                    opcionesModal.modal('show');
                    var ids = [rowINFO.id];
                    opcionesModal.on('click', '.massive', function (e) {
                        var estado = $(this).attr("data-estado");
                        var datos = {campo: {estado: estado, ids: ids}};
                        var cambio = moduloSolicitudes.cambiarEstadoSolicitudes(datos);
                        cambio.done(function (response) {
                            opcionesModal.modal('hide');
                            $("#mensaje").hide();
                            ids = "";
                            recargar();
                            toastr.success('Se ha efectuado Cambio de Estado correctamente.');
                        });
                        cambio.fail(function (response) {
                            toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
                        });
                    });

                }

            });
            //Boton subir documentos
            opcionesModal.on('click', '.subir_archivos_solicitudes', function (e) {
                e.preventDefault();
                e.returnValue = false;
                e.stopPropagation();
                opcionesModal.modal('hide');
                var id = $(this).attr("data-id");
                console.log(id);
                //Inicializar opciones del Modal
                $('#documentosModal').modal({
                    backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                    show: false
                });

                $('#documentosModal').modal('show');
                $('#id_solicitud').val(id);
            });
            
            opcionesModal.on('click','.rechazar_solicitud', function (e){
                    e.preventDefault();
                    e.returnValue = false;
                    e.stopPropagation();
                    var id = $(this).attr("data-id");
                    var solicitud = $(this).attr("data-solicitud");
                    var cliente = $(this).attr("data-cliente");
                    var rowINFO = $.extend({}, grid_obj.getRowData(id));
                    var options = rowINFO.modalarechazar;
                    //Init Modal
                    opcionesModal.modal('hide');
                    opcionesModalRechazar.find('.modal-title').empty().append('Rechazar Solicitud');
                    opcionesModalRechazar.find('.modal-body').empty().append(options);
                    opcionesModalRechazar.find('.modal-footer').empty();
                    opcionesModalRechazar.modal('show');
                    var razon = opcionesModalRechazar.find('#motivorechazar').val();
                    var ids = [rowINFO.id];


                    $("#motivorechazar").keyup(function () {

                    });
                    opcionesModalRechazar.on('click', '.massive', function (e) {

                        var motivo = opcionesModalRechazar.find('#motivorechazar').val();
                        var nsolicitud = opcionesModalRechazar.find('input[name="nsolicitud"]').val();
                        console.log(nsolicitud);
                        if (motivo != "") {
                            var estado = $(this).attr("data-estado");
                            var estado_anterior = $(this).attr("data-estado-anterior");
                            var datos = {campo: {estado: estado, ids: ids}};
                            var cambio = moduloSolicitudes.cambiarEstadoSolicitudes(datos);

                            cambio.done(function (response) {
                                opcionesModalRechazar.modal('hide');
                                $("#mensaje").hide();
                                
                                recargar();
                                toastr.success('Se ha Rechazado la solicitud correctamente.');
                                var datosbitacora = {campo: {estado: estado, estado_anterior: estado_anterior, tipo: 'Cambio de Estado', motivo: motivo, solicitud: nsolicitud, id: rowINFO.id}};
                                var cambiobitacora = moduloSolicitudesBitacora.cambiarEstadoSolicitudesBitacora(datosbitacora);
                                ids = "";
                                cambiobitacora.done(function (response) {

                                });
                            });
                            cambio.fail(function (response) {
                                toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
                            });
                        } else {
                            toastr.warning('Debe ingresar el Motivo para Rechazar la Solicitud.');
                        }
                    });

                });
            opcionesModal.on('click', '.anular_solicitud', function (e) {
                e.preventDefault();
                e.returnValue = false;
                e.stopPropagation();
                var id = $(this).attr("data-id");
                var solicitud = $(this).attr("data-solicitud");
                var cliente = $(this).attr("data-cliente");
                var rowINFO = $.extend({}, grid_obj.getRowData(id));
                var options = rowINFO.modalanular;
                //Init Modal
                opcionesModal.modal('hide');
                opcionesModalAnular.find('.modal-title').empty().append('Anular Solicitud');
                opcionesModalAnular.find('.modal-body').empty().append(options);
                opcionesModalAnular.find('.modal-footer').empty();
                opcionesModalAnular.modal('show');
                var razon = opcionesModalAnular.find('#motivoanula').val();
                var ids = [rowINFO.id];
                $("#motivoanula").keyup(function () {

                });
                opcionesModalAnular.on('click', '.massive', function (e) {

                    var motivo = opcionesModalAnular.find('#motivoanula').val();
                    var nsolicitud = opcionesModalAnular.find('input[name="nsolicitud"]').val();
                    if (motivo != "") {
                        var estado = $(this).attr("data-estado");
                        var estado_anterior = $(this).attr("data-estado-anterior");
                        var datos = {campo: {estado: estado, ids: ids}};
                        var cambio = moduloSolicitudes.cambiarEstadoSolicitudes(datos);

                        cambio.done(function (response) {
                            opcionesModalAnular.modal('hide');
                            var id_modificado = opcionesModalAnular.find('input[name="id_solicitud"]').val();
                            $("#mensaje").hide();
                            ids = "";
                            recargar();
                            toastr.success('Se ha Anulado la solicitud correctamente.');
                            var datosbitacora = {campo: {estado: estado, estado_anterior: estado_anterior, tipo: 'Cambio de Estado', motivo: motivo, solicitud: nsolicitud, id: id_modificado}};
                            var cambiobitacora = moduloSolicitudesBitacora.cambiarEstadoSolicitudesBitacora(datosbitacora);

                            cambiobitacora.done(function (response) {

                            });
                        });
                        cambio.fail(function (response) {
                            toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
                        });
                    } else {
                        toastr.warning('Debe ingresar el Motivo para Anular la Solicitud.');
                    }
                });
            });

            opcionesModal.on('click', '.aprobar_solicitud', function (e) {

                e.preventDefault();
                e.returnValue = false;
                e.stopPropagation();
                var id = $(this).attr("data-id");

                var solicitud = $(this).attr("data-solicitud");
                var cliente = $(this).attr("data-cliente");
                var rowINFO = $.extend({}, grid_obj.getRowData(id));
                var options = rowINFO.modalaprobar;
                //Init Modal
                opcionesModal.modal('hide');
                opcionesModalAprobar.find('.modal-title').empty().append('Aprobar Solicitud');
                opcionesModalAprobar.find('.modal-body').empty().append(options);
                opcionesModalAprobar.find('.modal-footer').empty();
                opcionesModalAprobar.modal('show');
                var ids = [rowINFO.id];

                opcionesModalAprobar.on('click', '.massive', function (e) {

                    var motivo = opcionesModalAprobar.find('#npoliza').val();
                    if (motivo != "") {

                        var poliza = {campo: {numero: motivo}};
                        var res = moduloSolicitudesPoliza.verificarPoliza(poliza);
                        var conta = 1;
                        var estado = $(this).attr("data-estado");
                        var solicitud = $(this).attr("data-solicitud");
                        var estado_anterior = $(this).attr("data-estado-anterior");
                        var id_modificado = $(this).attr("data-id");
                        console.log(estado+" "+solicitud+" "+estado_anterior+" "+id_modificado);
                        var datos = {campo: {estado: estado, ids: ids}};
                        res.done(function (resp) {

                            if (resp == 0) {
                                var cambio = moduloSolicitudes.cambiarEstadoSolicitudes(datos);

                                cambio.done(function (response) {
                                    opcionesModalAprobar.modal('hide');
                                    $("#mensaje").hide();
                                    ids = "";
                                    recargar();
                                    toastr.success('Se ha Aprobado la solicitud correctamente.');
                                    var datosbitacora = {campo: {estado: estado, estado_anterior: estado_anterior, tipo: 'Solicitud_aprobada', motivo: motivo, solicitud: solicitud, id: id_modificado}};
                                    var cambiobitacora = moduloSolicitudesBitacora.cambiarEstadoSolicitudesBitacora(datosbitacora);
                                
                                    cambiobitacora.done(function (response) {

                                        var inf = $.parseJSON(response);
                                        if (inf.msg == "Ok") {
                                            location.href = phost() + 'polizas/editar/' + inf.uuid;
                                        }

                                    });
                                });
                                cambio.fail(function (response) {
                                    toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
                                });
                            } else {
                                toastr.error('Este Numero de Poliza ya existe en el Sistema. Ingrese otro.');
                            }
                        });
                    } else {
                        toastr.warning('Debe ingresar el Numero de Poliza para Aprobar la Solicitud.');
                    }
                });
            });

        });

        //Bnoton de estado
        grid_obj.on("click", botones.modalstate, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            var id = $(this).attr("data-id");
            var rowINFO = $.extend({}, grid_obj.getRowData(id));
            var options = rowINFO.modalstate;

            var estado = $(this).attr("data-solicitudEstado");
            if (estado == "Rechazada" || estado == "Aprobada") {
                opcionesModal.find('.modal-title').empty().append('Mensaje');
                opcionesModal.find('.modal-body').empty().append("<button class='btn btn-block btn-outline btn-warning'><p>No Puede realizar</p> <p>el cambio de estado</p></button>");
                opcionesModal.find('.modal-footer').empty();
                opcionesModal.modal('show');

            } else {
                //Init Modal
                opcionesModal.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.numero).text() + '');
                opcionesModal.find('.modal-body').empty().append(options);
                opcionesModal.find('.modal-footer').empty();
                opcionesModal.modal('show');
                var ids = [rowINFO.id];
                var cont = 0;
                opcionesModal.on('click', '.massive', function (e) {
                    if (cont === 0) {
                        var estado_anterior = $(this).attr("data-estado-anterior");
                        var id_modificado = $(this).attr("data-id");
                        var estado = $(this).attr("data-estado");
                        var datos = {campo: {estado: estado, ids: ids}};
                        var cambio = moduloSolicitudes.cambiarEstadoSolicitudes(datos);
                        cambio.done(function (response) {
                            opcionesModal.modal('hide');
                            $("#mensaje").hide();
                            ids = "";
                            recargar();
                            toastr.success('Se ha efectuado Cambio de Estado correctamente.');
                            var datosbitacora = {campo: {estado: estado, estado_anterior: estado_anterior, tipo: 'Cambio de Estado', id: id_modificado}};
                            var cambiobitacora = moduloSolicitudesBitacora.cambiarEstadoSolicitudesBitacora(datosbitacora);
                            console.log(cambiobitacora);
                            cambiobitacora.done(function (response) {
                                console.log("En efecto esta es la funcion y si funcionó");
                            });
                        });
                        cambio.fail(function (response) {
                            toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
                        });
                    }
                    cont = 1;
                });
                var cont = 0;

                opcionesModal.on('click','.rechazar_solicitud', function (e){
                    if (cont === 0) {
                    e.preventDefault();
                    e.returnValue = false;
                    e.stopPropagation();
                    var id = $(this).attr("data-id");
                    var solicitud = $(this).attr("data-solicitud");
                    var cliente = $(this).attr("data-cliente");
                    var rowINFO = $.extend({}, grid_obj.getRowData(id));
                    var options = rowINFO.modalarechazar;
                    //Init Modal
                    opcionesModal.modal('hide');
                    opcionesModalRechazar.find('.modal-title').empty().append('Rechazar Solicitud');
                    opcionesModalRechazar.find('.modal-body').empty().append(options);
                    opcionesModalRechazar.find('.modal-footer').empty();
                    opcionesModalRechazar.modal('show');
                    var razon = opcionesModalRechazar.find('#motivorechazar').val();
                    var ids = [rowINFO.id];


                    $("#motivorechazar").keyup(function () {

                    });
                    opcionesModalRechazar.on('click', '.massive', function (e) {

                        var motivo = opcionesModalRechazar.find('#motivorechazar').val();
                        var nsolicitud = opcionesModalRechazar.find('input[name="nsolicitud"]').val();
                        console.log(nsolicitud);
                        if (motivo != "") {
                            var estado = $(this).attr("data-estado");
                            var estado_anterior = $(this).attr("data-estado-anterior");
                            var datos = {campo: {estado: estado, ids: ids}};
                            var cambio = moduloSolicitudes.cambiarEstadoSolicitudes(datos);

                            cambio.done(function (response) {
                                opcionesModalRechazar.modal('hide');
                                $("#mensaje").hide();
                                
                                recargar();
                                toastr.success('Se ha Rechazado la solicitud correctamente.');
                                var datosbitacora = {campo: {estado: estado, estado_anterior: estado_anterior, tipo: 'Cambio de Estado', motivo: motivo, solicitud: nsolicitud, id: rowINFO.id}};
                                var cambiobitacora = moduloSolicitudesBitacora.cambiarEstadoSolicitudesBitacora(datosbitacora);
                                ids = "";
                                cambiobitacora.done(function (response) {

                                });
                            });
                            cambio.fail(function (response) {
                                toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
                            });
                        } else {
                            toastr.warning('Debe ingresar el Motivo para Rechazar la Solicitud.');
                        }
                    });
                    }
                    cont = 1;

                });
                 var cont = 0;

                opcionesModal.on('click', '.anular_solicitud', function (e) {
                    if (cont === 0) {
                    e.preventDefault();
                    e.returnValue = false;
                    e.stopPropagation();
                    var id = $(this).attr("data-id");
                    var solicitud = $(this).attr("data-solicitud");
                    var cliente = $(this).attr("data-cliente");
                    var rowINFO = $.extend({}, grid_obj.getRowData(id));
                    var options = rowINFO.modalanular;
                    //Init Modal
                    opcionesModal.modal('hide');
                    opcionesModalAnular.find('.modal-title').empty().append('Anular Solicitud');
                    opcionesModalAnular.find('.modal-body').empty().append(options);
                    opcionesModalAnular.find('.modal-footer').empty();
                    opcionesModalAnular.modal('show');
                    var razon = opcionesModalAnular.find('#motivoanula').val();
                    var ids = [rowINFO.id];

                    $("#motivoanula").keyup(function () {

                    });
                    opcionesModalAnular.on('click', '.massive', function (e) {

                        var motivo = opcionesModalAnular.find('#motivoanula').val();
                        var nsolicitud = opcionesModalAnular.find('input[name="nsolicitud"]').val();
                        if (motivo != "") {
                            var estado = $(this).attr("data-estado");
                            var estado_anterior = $(this).attr("data-estado-anterior");
                            var datos = {campo: {estado: estado, ids: ids}};
                            var cambio = moduloSolicitudes.cambiarEstadoSolicitudes(datos);

                            cambio.done(function (response) {
                                opcionesModalAnular.modal('hide');
                                var id_modificado = opcionesModalAnular.find('input[name="id_solicitud"]').val();
                                $("#mensaje").hide();
                                ids = "";
                                recargar();
                                toastr.success('Se ha Anulado la solicitud correctamente.');
                                var datosbitacora = {campo: {estado: estado, estado_anterior: estado_anterior, tipo: 'Cambio de Estado', motivo: motivo, solicitud: nsolicitud, id: id_modificado}};
                                var cambiobitacora = moduloSolicitudesBitacora.cambiarEstadoSolicitudesBitacora(datosbitacora);

                                cambiobitacora.done(function (response) {

                                });
                            });
                            cambio.fail(function (response) {
                                toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
                            });
                        } else {
                            toastr.warning('Debe ingresar el Motivo para Anular la Solicitud.');
                        }
                    });
                    }
                    cont = 1;
                });
                var cont = 0;          
                opcionesModal.on('click', '.aprobar_solicitud', function (e) {
                    if (cont === 0) {
                    //("aqui");
                    e.preventDefault();
                    e.returnValue = false;
                    e.stopPropagation();
                    var id = $(this).attr("data-id");
                    //(id)
                    var solicitud = $(this).attr("data-solicitud");
                    var cliente = $(this).attr("data-cliente");
                    var rowINFO = $.extend({}, grid_obj.getRowData(id));
                    var options = rowINFO.modalaprobar;
                    //Init Modal
                    opcionesModal.modal('hide');
                    opcionesModalAprobar.find('.modal-title').empty().append('Aprobar Solicitud');
                    opcionesModalAprobar.find('.modal-body').empty().append(options);
                    opcionesModalAprobar.find('.modal-footer').empty();
                    opcionesModalAprobar.modal('show');
                    var ids = [rowINFO.id];
                    //(ids);
                    opcionesModalAprobar.on('click', '.massive', function (e) {

                        var motivo = opcionesModalAprobar.find('#npoliza').val();
                        if (motivo != "") {
                            //("aqui");
                            var poliza = {campo: {numero: motivo}};
                            var res = moduloSolicitudesPoliza.verificarPoliza(poliza);
                            var conta = 1;
                            var estado = $(this).attr("data-estado");
                            var solicitud = $(this).attr("data-solicitud");
                            var estado_anterior = $(this).attr("data-estado-anterior");
                            var id_modificado = $(this).attr("data-id");
                            console.log(estado+" "+solicitud+" "+estado_anterior+" "+id_modificado);
                            var datos = {campo: {estado: estado, ids: ids}};
                            res.done(function (resp) {
                                //(resp);
                                if (resp == 0) {
                                    var cambio = moduloSolicitudes.cambiarEstadoSolicitudes(datos);
                                    //(estado);
                                    cambio.done(function (response) {
                                        opcionesModalAprobar.modal('hide');
                                        $("#mensaje").hide();
                                        
                                        ids = "";
                                        recargar();
                                        toastr.success('Se ha Aprobado la solicitud correctamente.');
                                        var datosbitacora = {campo: {estado: estado, estado_anterior: estado_anterior, tipo: 'Solicitud_aprobada', motivo: motivo, solicitud: solicitud, id: id_modificado}};
                                        var cambiobitacora = moduloSolicitudesBitacora.cambiarEstadoSolicitudesBitacora(datosbitacora);
                                        //(cambiobitacora);
                                        cambiobitacora.done(function (response) {

                                            var inf = $.parseJSON(response);
                                            if (inf.msg == "Ok") {
                                                location.href = phost() + 'polizas/editar/' + inf.uuid;
                                            }

                                            //("Funciono");
                                        });
                                    });
                                    cambio.fail(function (response) {
                                        toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
                                    });
                                } else {
                                    toastr.error('Este Numero de Poliza ya existe en el Sistema. Ingrese otro.');
                                }
                            });
                        } else {
                            toastr.warning('Debe ingresar el Numero de Poliza para Aprobar la Solicitud.');
                        }
                    });
                     }
                    cont = 1;
                });
            }
        });
    };
    $(botones.exportar).on("click", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        if ($('#tabla,#tablaSolicitudes').is(':visible') === true) {
//Exportar Seleccionados del jQgrid
            var ids = [];
            ids = grid_obj.jqGrid('getGridParam', 'selarrrow');
            //Verificar si hay seleccionados
            if (ids.length > 0) {
                $('#ids').val(ids);
                $('form#exportarSolicitud').submit();
                $('body').trigger('click');
                //(ids);
            } else {
                alert("Seleccione un registro");
            }
        }
    });
    //Crear cambiar estado
    $(botones.cambiarEstado).on("click", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        if ($('#tabla').is(':visible') === true) {

//Exportar Seleccionados del jQgrid
            var ids = [],
                    statesValues = ["Aprobada", "Rechazada", "Anulada", "En Trámite", "Pendiente"],
                    //opciones,
                    estado,
                    //style,
                    button = "",
                    states = [];
            var ids_aprobados = 0;
            var ids_pendiente = 0;
            var ids_tramite = 0;
            var ids_aprobada = 0;
            var ids_rechazado = 0;
            var ids_anulado = 0;
            ids = grid_obj.jqGrid('getGridParam', 'selarrrow');

            //Verificar si hay seleccionados
            if (ids.length > 0) {

                ids_pendiente = _.filter(ids, function (fila) {
                    var infoFila = $.extend({}, grid_obj.getRowData(fila));
                    if ($(infoFila.estado).text() == 'Pendiente') {
                        return infoFila.id;
                    }
                });
                ids_tramite = _.filter(ids, function (fila) {
                    var infoFila = $.extend({}, grid_obj.getRowData(fila));
                    if ($(infoFila.estado).text() == 'En Trámite') {
                        return infoFila.id;
                    }
                });
                ids_aprobada = _.filter(ids, function (fila) {
                    var infoFila = $.extend({}, grid_obj.getRowData(fila));
                    if ($(infoFila.estado).text() == 'Aprobada') {
                        return infoFila.id;
                    }
                });
                ids_rechazado = _.filter(ids, function (fila) {
                    var infoFila = $.extend({}, grid_obj.getRowData(fila));
                    if ($(infoFila.estado).text() == 'Rechazada') {
                        return infoFila.id;
                    }
                });
                ids_anulado = _.filter(ids, function (fila) {
                    var infoFila = $.extend({}, grid_obj.getRowData(fila));
                    if ($(infoFila.estado).text() == 'Anulada') {
                        return infoFila.id;
                    }
                });

                if ((ids_pendiente.length > 0 && ids_tramite.length == 0 && ids_aprobada.length == 0 && ids_rechazado.length == 0 && ids_anulado.length == 0) || (ids_pendiente.length == 0 && ids_tramite.length > 0 && ids_aprobada.length == 0 && ids_rechazado.length == 0 && ids_anulado.length == 0) || (ids_pendiente.length == 0 && ids_tramite.length == 0 && ids_aprobada.length > 0 && ids_rechazado.length == 0 && ids_anulado.length == 0) || (ids_pendiente.length == 0 && ids_tramite.length == 0 && ids_aprobada.length == 0 && ids_rechazado.length > 0 && ids_anulado.length == 0) || (ids_pendiente.length == 0 && ids_tramite.length == 0 && ids_aprobada.length == 0 && ids_rechazado.length == 0 && ids_anulado.length > 0)) {
                    var politicas_general = moduloSolicitudes.ajaxcambiarObtenerPoliticasGeneral();
                    var permisos_generales = politicas_general.success(function (data) {
                        var perms_prueb=data;
                        var politicas = moduloSolicitudes.ajaxcambiarObtenerPoliticas();
                        var permisos1 = politicas.success(function (data) {
                            if (perms_prueb > 0) {
                                var permisos = [];
                                $.each(data, function (i, filename) {
                                    permisos.push(filename);
                                });

//                            alert(permisos.indexOf(22, 0));
                                if (permisos.indexOf(21, 0) != -1 || permisos.indexOf(22, 0) != -1 || permisos.indexOf(23, 0) != -1) {
                                    var titulo = 'Cambiar estado';
                                    if (ids_tramite.length > 0) {

                                        if (permisos.indexOf(21, 0) != -1)
                                        {
                                            button += "<button data-estado-anterior='En trámite' data-id='" + ids + "' data-estado='Rechazada' class='btn btn-block btn-outline btn-danger massive' >Rechazada</button>";
                                        } else
                                        {
                                            button += "<button data-estado-anterior='En trámite' data-id='" + ids + "' data-estado='Rechazada' class='btn btn-block btn-outline btn-danger massive' >Rechazada</button>";
                                        }
//                                }
                                    } else if (ids_anulado.length > 0) {

                                        if (permisos.indexOf(22, 0) != -1)
                                        {
                                            button += "<button data-estado-anterior='Anulada' data-id='" + ids + "' data-estado='Pendiente' class='btn btn-block btn-outline massive' style='border: blue 1px solid; color: #5bc0de;'>Pendiente</button>";
                                        } else {
                                            var modalview=1;
                                            $("#mensaje").show();
                                            $("#mensaje").html('<div id="mensaje" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Usted no tiene permisos para cambiar a este estado</div>');
                                        }
                                    }
                                    if (ids_aprobada.length > 0) {
                                        button += "<button class='btn btn-block btn-outline btn-warning'><p>No Puede realizar</p> <p>el cambio de estado</p></button>";
                                        titulo = 'Mensaje';
                                    } else if (ids_pendiente.length > 0) {
                                        button += "<button data-estado-anterior='Pendiente' data-id='" + ids + "' data-estado='En trámite' class='btn btn-block btn-outline massive' style='border: #F8AD46 1px solid; color: #F8AD46;'>En Trámite</button><button data-estado-anterior='Pendiente' data-id='" + ids + "' data-estado='Rechazada' class='btn btn-block btn-outline btn-danger massive'>Rechazada</button>";
                                    }

                                } else {
                                    titulo = 'Cambiar estado';
                                    if (ids_aprobada.length > 0) {
                                        button += "<button class='btn btn-block btn-outline btn-warning'><p>No Puede realizar</p> <p>el cambio de estado</p></button>";
                                        titulo = 'Mensaje';
                                    } else if (ids_tramite.length > 0) {
                                        button += "<button class='btn btn-block btn-outline btn-warning'><p>No Puede realizar</p> <p>el cambio de estado</p></button>";
                                        titulo = 'Mensaje';
                                    } else if (ids_anulado.length > 0) {
                                         var modalview=1;
                                            $("#mensaje").show();
                                            $("#mensaje").html('<div id="mensaje" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Usted no tiene permisos para cambiar a este estado</div>');
                                    } else if (ids_rechazado.length > 0) {
                                        button += "<button class='btn btn-block btn-outline btn-warning'><p>No Puede realizar</p> <p>el cambio de estado</p></button>";
                                        titulo = 'Mensaje';
                                    } else if (ids_pendiente.length > 0) {
                                        button += "<button data-estado-anterior='Pendiente' data-id='" + ids + "' data-estado='En trámite' class='btn btn-block btn-outline massive' id='en_tramite' style='border: #F8AD46 1px solid; color: #F8AD46;'>En trámite</button>";
                                    }

                                }
                            } else {
                                titulo = 'Cambiar estado';
                                if (ids_aprobada.length > 0) {
                                    button += "<button class='btn btn-block btn-outline btn-warning'><p>No Puede realizar</p> <p>el cambio de estado</p></button>";
                                    titulo = 'Mensaje';
                                } else if (ids_tramite.length > 0) {
                                    button += "<button class='btn btn-block btn-outline btn-warning'><p>No Puede realizar</p> <p>el cambio de estado</p></button>";
                                    titulo = 'Mensaje';
                                } else if (ids_anulado.length > 0) {
                                    button += "<button data-estado-anterior='Anulada' data-id='" + ids + "' data-estado='Pendiente' class='btn btn-block btn-outline massive' style='border: #5bc0de 1px solid; color: #5bc0de;'>Pendiente</button>";
                                } else if (ids_rechazado.length > 0) {
                                    button += "<button class='btn btn-block btn-outline btn-warning'><p>No Puede realizar</p> <p>el cambio de estado</p></button>";
                                    titulo = 'Mensaje';
                                } else if (ids_pendiente.length > 0) {
                                    button += "<button data-estado-anterior='Pendiente' data-id='" + ids + "' data-estado='En trámite' class='btn btn-block btn-outline massive' id='en_tramite' style='border: #F8AD46 1px solid; color: #F8AD46;'>En trámite</button>";
                                }
                            }
                            if(modalview!=1){
                            opcionesModal.find('.modal-title').empty().append(titulo);
                            opcionesModal.find('.modal-body').empty().append(button);
                            opcionesModal.find('.modal-footer').empty();
                            opcionesModal.modal('show');
                        } else {
                            opcionesModal.modal('hide');
                        }

                            $('#anulada').mouseover(function () {
                                $('#anulada').css('color', '#FFFFFF');
                                $('#anulada').css('background-color', 'black');
                            });

                            $('#anulada').mouseout(function () {
                                $('#anulada').css('color', 'black');
                                $('#anulada').css('background-color', '#FFFFFF');
                            });

                            $('#rechazada').mouseover(function () {
                                $('#rechazada').css('color', 'white');
                                $('#rechazada').css('background-color', 'red');
                            });

                            $('#rechazada').mouseout(function () {
                                $('#rechazada').css('color', 'red');
                                $('#rechazada').css('background-color', 'white');
                            });

                            $('#aprobada').mouseover(function () {
                                $('#aprobada').css('color', '#FFFFFF');
                                $('#aprobada').css('background-color', '#5cb85c');
                            });

                            $('#aprobada').mouseout(function () {
                                $('#aprobada').css('color', '#5cb85c');
                                $('#aprobada').css('background-color', '#FFFFFF');
                            });
                            $('#pendiente').mouseover(function () {
                                $('#pendiente').css('color', 'white');
                                $('#pendiente').css('background-color', 'blue');
                            });

                            $('#pendiente').mouseout(function () {
                                $('#pendiente').css('color', 'blue');
                                $('#pendiente').css('background-color', 'white');
                            });
                            $('#en_tramite').mouseover(function () {
                                $('#en_tramite').css('color', 'white');
                                $('#en_tramite').css('background-color', '#F8AD46');
                            });

                            $('#en_tramite').mouseout(function () {
                                $('#en_tramite').css('color', '#F8AD46');
                                $('#en_tramite').css('background-color', 'white');
                            });
                        });
                    });

                    //db state type emun
                    var cont = 0;
                    opcionesModal.on('click', '.massive', function (e) {
                        if (cont == 0) {
                            var estado = $(this).attr("data-estado");
                            var datos = {campo: {estado: estado, ids: ids}};
                            var cambio = moduloSolicitudes.cambiarEstadoSolicitudes(datos);
                            var estado_anterior = $(this).attr("data-estado-anterior");
                            //(estado);
                            cambio.done(function (response) {
                                opcionesModal.modal('hide');
                                $("#mensaje").hide();
                                recargar();
                                toastr.success('Se ha efectuado Cambio de Estado correctamente.');
                                for (var i = 0; i <= ids.length; i++) {
                                    var datosbitacora = {campo: {estado: estado, estado_anterior: estado_anterior, tipo: 'Cambio de Estado', id: ids[i]}};
                                    var cambiobitacora = moduloSolicitudesBitacora.cambiarEstadoSolicitudesBitacora(datosbitacora);
                                }
                                cambiobitacora.done(function (response) {
                                    console.log("Estados masivos ok");
                                });
                                cambio.fail(function (response) {
                                    toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
                                });
                            });
                        }
                        cont = 1;
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
    //Reload al jQgrid
    var recargar = function () {

        //Reload Grid
        grid_obj.setGridParam({
            url: phost() + url,
            datatype: "json",
            postData: {
                numero: '',
                cliente: '',
                aseguradora: '',
                ramo: '',
                tipo: '',
                fecha_creacion: '',
                usuario: '',
                estado: '',
                erptkn: tkn
            }
        }).trigger('reloadGrid');
    };
    //Buscar cargo en jQgrid
    var buscarSolicitudes = function () {

        var numero = $('#no_solicitud').val();
        var cliente = $('#cliente').val();
        var aseguradora = $('#aseguradora').val();
        var ramo = [];
        var ramo = $('.ramo').val();
        var tipo = $('#tipo_solicitud').val();        
        var inicio_creacion = $('#inicio_creacion').val();
        var fin_creacion = $('#fin_creacion').val();
        var usuario = $('#usuario').val();
        var estado = $('#estado_id').val();
        if (numero != "" || cliente != "" || aseguradora != "" || ramo != "" || tipo != "" || inicio_creacion != "" || fin_creacion != "" || usuario != "" || estado != "")
        {
            //Reload Grid
            grid_obj.setGridParam({
                url: phost() + url,
                datatype: "json",
                postData: {
                    numero: numero,
                    cliente: cliente,
                    aseguradora: aseguradora,
                    ramo: ramo,
                    tipo: tipo,
                    inicio_creacion: inicio_creacion,
                    fin_creacion: fin_creacion,
                    usuario: usuario,
                    estado: estado,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }
    };
    //Limpiar campos de busqueda
    var limpiarCampos = function () {
        $('#buscarSolicitudesForm').find('input[type="text"]').prop("value", "");
        $('#buscarSolicitudesForm').find('.chosen-select').val('').trigger('chosen:updated');
        $('#buscarSolicitudesForm').find('.ramo').val(' ').trigger('change.select2');
        $('#buscarSolicitudesForm').find('#fecha_creacion').prop("value", "");
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
tablaSolicitudes.init();

function customJQGrid(tableId, url, pagerId, modulo, uuid, columNames, setting) {
    return  tableId.jqGrid({
        url: phost() + url,
        datatype: "json",
        colNames: columNamesValues(columNames),
        colModel: columModel(columNames),
        mtype: "POST",
        postData: {
            erptkn: tkn,
            uuid: uuid,
            modulo: setting.modulo
        },
        height: "auto",
        autowidth: true,
        rowList: [10, 20, 50, 100],
        rowNum: 10,
        page: 1,
        pager: "#" + pagerId + "Pager",
        loadtext: '<p>Cargando...</p>',
        hoverrows: false,
        viewrecords: true,
        refresh: true,
        gridview: true,
        multiselect: true,
        sortname: setting.orderBy,
        sortorder: setting.sortorder,
        beforeProcessing: function (data, status, xhr) {
            //Check Session
            if ($.isEmptyObject(data.session) == false) {
                window.location = phost() + "login?expired";
            }
        },
        loadBeforeSend: function () {
            $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
            $(this).closest("div.ui-jqgrid-view").find("#tablaSolicitudesGrid_cb, #jqgh_tablaSolicitudesGrid_link").css("text-align", "center");
        },
        beforeRequest: function (data, status, xhr) {},
        loadComplete: function (data) {

            //check if isset data
            if (data['total'] == 0) {
                $('#gbox_' + pagerId).hide();
                $('#' + pagerId + 'NoRecords').empty().append('No se encontraron solicitudes.').css({"color": "#868686", "padding": "30px 0 0"}).show();
            } else {
                $('#' + pagerId + 'NoRecords').hide();
                $('#gbox_' + pagerId).show();
            }
        },
        onSelectRow: function (id) {
            $(this).find('tr#' + id).removeClass('ui-state-highlight');
        },
    });
}

function columNamesValues(tableColumns) {
    var columns = [],
            changePosition = [];
    for (var i = 0; i < tableColumns.length; i++) {
        var row = tableColumns[i];
        columns.push(row.colname);
        if (row.moveTo !== undefined && row.moveTo != false) {
            changePosition.push({start: i, end: row.moveTo - 1});

        }

    }

    if (changePosition.length) {
        for (var i = 0; i < changePosition.length; i++) {
            var index = changePosition[i];
            columns.move(index.start, index.end);
        }


    }
    return columns;
}
function columModel(tableColumns) {
    var columns = [];
    changePosition = [];
    for (var i in tableColumns) {
        var row = tableColumns[i];
        row.name = "name" in row === false ? row.index : row.name;
        if ("colname" in row) {

            columns.push(row);
        }
        if (row.moveTo !== undefined && row.moveTo !== false) {
            changePosition.push({start: parseInt(i), end: row.moveTo - 1});
        }

    }
    if (changePosition.length) {
        for (var i = 0; i < changePosition.length; i++) {
            var index = changePosition[i];
            columns.move(index.start, index.end);
        }

        console.log(columns);
    }
    return columns;
}

