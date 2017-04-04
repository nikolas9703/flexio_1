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

var tablaReclamos = (function () {

    var url = 'reclamos/ajax_listar',
	submenu = localStorage.getItem("ml-selected"),
	uuid = window.location.href.split("/").pop();
    var grid_id = "tablaReclamosGrid";
    var grid_obj = $("#tablaReclamosGrid");
    var opcionesModal = $('#opcionesModal');
    var opcionesModalAnulado = $('#opcionesModalAnulado');
    var opcionesModalCerrado = $('#opcionesModalCerrado');
    var botones = {
        opciones: ".viewOptions",
        editar: "",
        buscar: "#searchBtn",
        limpiar: "#clearBtn",
        modalstate: "span.estadoReclamos",
        opcionesmodalstate: "#cambio_estado_reclamos",
        exportar: "#exportarReclamosLnk",
        cambiarEstado: "#cambiarEstadoReclamosLnk",
        anularreclamos: ".anular_reclamos",
        aprobarreclamos: ".aprobar_reclamos",
        subir_archivos: ".subir_archivos_reclamos",
    };
    var tabla = function () {
        //localstorage to get modulo seleccionado
        var modulo = '',
                setting = {modulo: submenu,
                    orderBy: 'rec_reclamos.fecha',
                    moveTo: false,
                    sortorder: 'desc'
                };

        
        //var uuid = submenu!="Reclamos" ? uuidModulo : false;
        //inicializar jqgrid
        var columNames = [
            {colname: '', hidden: true, index: 'id'},
            {colname: 'No. Reclamo', index: "recnumero", width: 50},
            {colname: 'No. Póliza', index: 'polnumero', width: 50, hidden: setting.cliente},
            {colname: 'Ramo', index: 'pol_polizas.ramo', width: 40},
            {colname: 'No. Caso', index: 'rec_reclamos.numero_caso', width: 40},
            //{colname: 'Asegurado', index: 'seg_aseguradoras.nombre', width: 40},
            {colname: 'Cliente', index: 'clinombre', width: 40},
            {colname: 'Fecha registro', index: 'rec_reclamos.fecha', width: 50},
            {colname: 'Fecha siniestro', index: 'rec_reclamos.fecha_siniestro', width: 50, },
            {colname: 'Usuario', index: 'usunombre', width: 40, moveTo: setting.moveTo},
            {colname: 'Actualización', index: 'rec_reclamos.updated_at', width: 40},
            {colname: 'Fecha_seguimiento', index: 'rec_reclamos.fecha_seguimiento', width: 40, align: "center"},
            {colname: 'Estado', index: 'estado', width: 40},
            {colname: '', index: 'link', width: 50, sortable: false, resizable: false, hidedlg: true, align: "center"},
            {colname: '', index: 'options', hidedlg: true, hidden: true},
            {colname: '', index: 'modalstate', align: "center", hidedlg: true, hidden: true},
            {colname: '', index: 'modalanulado', align: "center", hidedlg: true, hidden: true},
            {colname: '', index: 'modalcerrado', align: "center", hidedlg: true, hidden: true},
        ];
        customJQGrid(grid_obj, url, grid_id, modulo, uuid, columNames, setting);
        //Al redimensionar ventana
        $(window).resizeEnd(function () {
            tablaReclamos.redimensionar();
        });
    };
    //Inicializar Eventos de Botones
    var eventos = function () {

        //Boton de Buscar Colaborador
        $(botones.buscar).on("click", function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            buscarReclamos();
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
            opcionesModal.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.recnumero).text() + '');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
            opcionesModal.on('click', '#cambio_estado_reclamos', function (e) {
                e.preventDefault();
                e.returnValue = false;
                e.stopPropagation();
                var id = $(this).attr("data-id");

                var rowINFO = $.extend({}, grid_obj.getRowData(id));
                var options = rowINFO.modalstate;

                var estado = $(this).attr("data-estado");
                if (estado == "Cerrado" || estado == "Anulado") {
                    toastr.error("Usted no puede efectuar este cambio de estado.");
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
                        var cambio = moduloReclamos.cambiarEstadoReclamos(datos);
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

        });

        //Bnoton de estado
        grid_obj.on("click", botones.modalstate, function (e) {
            if (permiso_estado == 1) {
                e.preventDefault();
                e.returnValue = false;
                e.stopPropagation();
                var id = $(this).attr("data-id");
                var rowINFO = $.extend({}, grid_obj.getRowData(id));
                var options = rowINFO.modalstate;
                var optionsanular = rowINFO.modalanulado;
                var optionscerrar = rowINFO.modalcerrado;

                var estado = $(this).attr("data-estado");
                console.log(estado);
                if (estado == "Cerrado" || estado == "Anulado") {
                    /*opcionesModal.find('.modal-title').empty().append('Mensaje');
                    opcionesModal.find('.modal-body').empty().append("<button class='btn btn-block btn-outline btn-warning'><p>No Puede realizar</p> <p>el cambio de estado</p></button>");
                    opcionesModal.find('.modal-footer').empty();
                    opcionesModal.modal('show');*/
                    toastr.error("Usted no puede realizar el cambio de estado.");
                } else {
                    //Init Modal
                    opcionesModal.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.recnumero).text() + '');
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

                            if (estado == "Cerrado" || estado == "Anulado") {
                                opcionesModal.modal('hide');
                                if (estado == "Cerrado") {                                     
                                    opcionesModalCerrado.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.recnumero).text() + '');
                                    opcionesModalCerrado.find('.modal-body').empty().append(optionscerrar);
                                    opcionesModalCerrado.find('.modal-footer').empty();
                                    opcionesModalCerrado.modal('show'); 

                                    opcionesModalCerrado.on('click', '.massive', function (e) {
                                        var motivo = opcionesModalCerrado.find('#motivocerrar').val();
                                        var nreclamo = opcionesModalCerrado.find('input[name="nreclamo"]').val();
                                        var idreclamo = opcionesModalCerrado.find('input[name="id_reclamo"]').val();
                                        if (motivo != "") {
                                            var estado = $(this).attr("data-estado");
                                            var estado_anterior = $(this).attr("data-estado-anterior");
                                            var datos = {campo: {estado: estado, ids: [idreclamo]}};
                                            var cambio = moduloReclamos.cambiarEstadoReclamos(datos);

                                            cambio.done(function (response) {
                                                opcionesModalCerrado.modal('hide');
                                                var id_modificado = opcionesModalCerrado.find('input[name="id_reclamo"]').val();
                                                $("#mensaje").hide();
                                                ids = [];
                                                recargar();
                                                toastr.success('Se ha cerrado el reclamo correctamente.');
                                                var datosbitacora = {campo: {estado: estado, estado_anterior: estado_anterior, tipo: 'Cambio de Estado', motivo: motivo, reclamo: nreclamo, id: id_modificado}};
                                                var cambiobitacora = moduloReclamosBitacora.cambiarEstadoReclamosBitacora(datosbitacora);

                                                cambiobitacora.done(function (response) {

                                                });
                                            });
                                            cambio.fail(function (response) {
                                                toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
                                            });
                                        } else {
                                            toastr.warning('Debe ingresar el Motivo para Cerrar el reclamo.');
                                        }
                                    }); 
                                }
                                if (estado == "Anulado") { 
                                    opcionesModalAnulado.find('.modal-title').empty().append('Opciones: ' + $(rowINFO.recnumero).text() + '');
                                    opcionesModalAnulado.find('.modal-body').empty().append(optionsanular);
                                    opcionesModalAnulado.find('.modal-footer').empty();
                                    opcionesModalAnulado.modal('show');

                                    opcionesModalAnulado.on('click', '.massive', function (e) {
                                        var motivo = opcionesModalAnulado.find('#motivoanula').val();
                                        var nreclamo = opcionesModalAnulado.find('input[name="nreclamo"]').val();
                                        var idreclamo = opcionesModalAnulado.find('input[name="id_reclamo"]').val();
                                        if (motivo != "") {
                                            var estado = $(this).attr("data-estado");
                                            var estado_anterior = $(this).attr("data-estado-anterior");
                                            var datos = {campo: {estado: estado, ids: [idreclamo]}};
                                            var cambio = moduloReclamos.cambiarEstadoReclamos(datos);

                                            cambio.done(function (response) {
                                                opcionesModalAnulado.modal('hide');
                                                var id_modificado = opcionesModalAnulado.find('input[name="id_reclamo"]').val();
                                                $("#mensaje").hide();
                                                ids = [];
                                                recargar();
                                                toastr.success('Se ha Anulado el reclamo correctamente.');
                                                var datosbitacora = {campo: {estado: estado, estado_anterior: estado_anterior, tipo: 'Cambio de Estado', motivo: motivo, reclamo: nreclamo, id: id_modificado}};
                                                var cambiobitacora = moduloReclamosBitacora.cambiarEstadoReclamosBitacora(datosbitacora);

                                                cambiobitacora.done(function (response) {

                                                });
                                            });
                                            cambio.fail(function (response) {
                                                toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
                                            });
                                        } else {
                                            toastr.warning('Debe ingresar el Motivo para Anular el reclamo.');
                                        }
                                    }); 
                                }


                            }else{
                                var cambio = moduloReclamos.cambiarEstadoReclamos(datos);
                                cambio.done(function (response) {
                                    opcionesModal.modal('hide');
                                    $("#mensaje").hide();
                                    ids = [];
                                    recargar();
                                    toastr.success('Se ha efectuado Cambio de Estado correctamente.');
                                    /*var datosbitacora = {campo: {estado: estado, estado_anterior: estado_anterior, tipo: 'Cambio de Estado', id: id_modificado}};
                                    var cambiobitacora = moduloReclamosBitacora.cambiarEstadoReclamosBitacora(datosbitacora);
                                    console.log(cambiobitacora);
                                    cambiobitacora.done(function (response) {
                                        console.log("En efecto esta es la funcion y si funcionó");
                                    });*/
                                });
                                cambio.fail(function (response) {
                                    toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
                                });
                            }
                        }
                        cont = 1;
                    });                
                    
                }
            }else{
                toastr.error("Usted no tiene permiso para cambiar estados.");
            }            
        });
    };
    


    $(botones.exportar).on("click", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();
        if ($('#tabla,#tablaReclamos').is(':visible') === true) {
            //Exportar Seleccionados del jQgrid
            var ids = [];
            ids = grid_obj.jqGrid('getGridParam', 'selarrrow');
            //Verificar si hay seleccionados
            if (ids.length > 0) {
                $('#ids').val(ids);
                $('form#exportarReclamos').submit();
                $('body').trigger('click');
                //(ids);
            } else {
                toastr.warning("Seleccione un registro para exportar.");
            }
        }
    });

    //Crear cambiar estado
    $(botones.cambiarEstado).on("click", function (e) {
        if (permiso_estado == 1) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            if ($('#tabla').is(':visible') === true) {

                //Exportar Seleccionados del jQgrid
                var ids = [],
                    statesValues = ["En analisis", "En pago", "Pendiente doc.", "Legal", "Cerrado", "Anulado"],
                    //opciones,
                    estado,
                    //style,
                    button = "",
                    states = [];
                var ids_analisis = 0;
                var ids_pago = 0;
                var ids_pendiente = 0;
                var ids_legal = 0;
                var ids_anulado = 0;
                var ids_cerrado = 0;
                ids = grid_obj.jqGrid('getGridParam', 'selarrrow');

                //Verificar si hay seleccionados
                if (ids.length > 0) {

                    ids_analisis = _.filter(ids, function (fila) {
                        var infoFila = $.extend({}, grid_obj.getRowData(fila));
                        if ($(infoFila.estado).text() == 'En Analisis') {
                            estado = $(infoFila.estado).text();
                            return infoFila.id;
                        }
                    });
                    ids_legal = _.filter(ids, function (fila) {
                        var infoFila = $.extend({}, grid_obj.getRowData(fila));
                        if ($(infoFila.estado).text() == 'Legal') {
                            estado = $(infoFila.estado).text();
                            return infoFila.id;
                        }
                    });
                    ids_pendiente = _.filter(ids, function (fila) {
                        var infoFila = $.extend({}, grid_obj.getRowData(fila));
                        if ($(infoFila.estado).text() == 'Pendiente Doc.') {
                            estado = $(infoFila.estado).text();
                            return infoFila.id;
                        }
                    });
                    ids_pago = _.filter(ids, function (fila) {
                        var infoFila = $.extend({}, grid_obj.getRowData(fila));
                        if ($(infoFila.estado).text() == 'En Pago') {
                            estado = $(infoFila.estado).text();
                            return infoFila.id;
                        }
                    });
                    ids_cerrado = _.filter(ids, function (fila) {
                        var infoFila = $.extend({}, grid_obj.getRowData(fila));
                        if ($(infoFila.estado).text() == 'Cerrado') {
                            estado = $(infoFila.estado).text();
                            return infoFila.id;
                        }
                    });
                    ids_anulado = _.filter(ids, function (fila) {
                        var infoFila = $.extend({}, grid_obj.getRowData(fila));
                        if ($(infoFila.estado).text() == 'Anulado') {
                            estado = $(infoFila.estado).text();
                            return infoFila.id;
                        }
                    });

                    if ((ids_pendiente.length > 0 && ids_pago.length == 0 && ids_cerrado.length == 0 && ids_anulado.length == 0 && ids_legal.length == 0 && ids_analisis.length == 0) || (ids_pendiente.length == 0 && ids_pago.length > 0 && ids_cerrado.length == 0 && ids_anulado.length == 0 && ids_legal.length == 0 && ids_analisis.length == 0 ) || (ids_pendiente.length == 0 && ids_pago.length == 0 && ids_cerrado.length > 0 && ids_anulado.length == 0 && ids_legal.length == 0 && ids_analisis.length == 0 ) || (ids_pendiente.length == 0 && ids_pago.length == 0 && ids_cerrado.length == 0 && ids_anulado.length > 0 && ids_legal.length == 0 && ids_analisis.length == 0 ) || (ids_pendiente.length == 0 && ids_pago.length == 0 && ids_cerrado.length == 0 && ids_anulado.length == 0 && ids_legal.length > 0 && ids_analisis.length == 0 ) || (ids_pendiente.length == 0 && ids_pago.length == 0 && ids_cerrado.length == 0 && ids_anulado.length == 0 && ids_legal.length == 0 && ids_analisis.length > 0 )) {
                        var politicas_general = moduloReclamos.ajaxcambiarObtenerPoliticasGeneral();
                        var permisos_generales = politicas_general.success(function (data) {
                            var perms_prueb=data;
                            var politicas = moduloReclamos.ajaxcambiarObtenerPoliticas();
                            var permisos1 = politicas.success(function (data) {
                                if (perms_prueb > 0) {
                                    var permisos = [];
                                    $.each(data, function (i, filename) {
                                        permisos.push(filename);
                                    });

    //                            alert(permisos.indexOf(22, 0));
                                    if (permisos.indexOf(21, 0) != -1 || permisos.indexOf(22, 0) != -1 || permisos.indexOf(23, 0) != -1) {
                                        var titulo = 'Cambiar estado';
                                        if (ids_pago.length > 0) {

                                            if (permisos.indexOf(21, 0) != -1)
                                            {
                                                button += "<button data-estado-anterior='En trámite' data-id='" + ids + "' data-estado='Rechazada' class='btn btn-block btn-outline btn-danger massive' >Rechazada</button>";
                                            } else
                                            {
                                                button += "<button data-estado-anterior='En trámite' data-id='" + ids + "' data-estado='Rechazada' class='btn btn-block btn-outline btn-danger massive' >Rechazada</button>";
                                            }
    //                                }
                                        } 
                                        if (ids_cerrado.length > 0) {
                                            console.log("aqui1");
                                            button += "<button class='btn btn-block btn-outline btn-warning'><p>No Puede realizar</p> <p>el cambio de estado</p></button>";
                                            titulo = 'Mensaje';
                                        } else if (ids_pendiente.length > 0) {
                                            button += "<button data-estado-anterior='Pendiente' data-id='" + ids + "' data-estado='En trámite' class='btn btn-block btn-outline massive' style='border: #F8AD46 1px solid; color: #F8AD46;'>En Trámite</button><button data-estado-anterior='Pendiente' data-id='" + ids + "' data-estado='Rechazada' class='btn btn-block btn-outline btn-danger massive'>Rechazada</button>";
                                        }

                                    } else {
                                        titulo = 'Cambiar estado';
                                        if (ids_cerrado.length > 0) {
                                            console.log("aqui2");
                                            button += "<button class='btn btn-block btn-outline btn-warning'><p>No Puede realizar</p> <p>el cambio de estado</p></button>";
                                            titulo = 'Mensaje';
                                        } else if (ids_pago.length > 0) {
                                            console.log("aqui4");
                                            button += "<button class='btn btn-block btn-outline btn-warning'><p>No Puede realizar</p> <p>el cambio de estado</p></button>";
                                            titulo = 'Mensaje';
                                        }else if (ids_anulado.length > 0) {
                                            console.log("aqui5");
                                            button += "<button class='btn btn-block btn-outline btn-warning'><p>No Puede realizar</p> <p>el cambio de estado</p></button>";
                                            titulo = 'Mensaje';
                                        } else if (ids_pendiente.length > 0) {
                                            button += "<button data-estado-anterior='Pendiente' data-id='" + ids + "' data-estado='En trámite' class='btn btn-block btn-outline massive' id='en_tramite' style='border: #F8AD46 1px solid; color: #F8AD46;'>En trámite</button>";
                                        }

                                    }
                                } else {
                                    //CAMBIAR eSTADO SIN TENER POLITICAS
                                    titulo = 'Cambiar estado';
                                    var buttonlegal = "<button data-estado-anterior='"+estado+"' data-id='" + ids + "' data-estado='Legal' class='btn btn-block btn-outline massive' id='legal' style='background-color: #F8AD46; color: white;'>Legal</button>";
                                    var buttonanalisis = "<button data-estado-anterior='"+estado+"' data-id='" + ids + "' data-estado='En analisis' class='btn btn-block btn-outline massive' id='en_analisis' style='background-color: #5bc0de; color: white;'>En analisis</button>";
                                    var buttonpago = "<button data-estado-anterior='"+estado+"' data-id='" + ids + "' data-estado='En pago' class='btn btn-block btn-outline massive' id='en_pago' style='background-color: blue; color: white;'>En pago</button>";
                                    var buttonpendiente = "<button data-estado-anterior='"+estado+"' data-id='" + ids + "' data-estado='Pendiente doc.' class='btn btn-block btn-outline massive' id='pendiente_documentacion' style='background-color: gold; color: white;'>Pendiente doc.</button>";
                                    var buttoncerrado = "<button data-estado-anterior='"+estado+"' data-id='" + ids + "' data-estado='Cerrado' class='btn btn-block btn-outline massive' id='cerrado' style='background-color: #5cb85c; color: white;'>Cerrado</button>";
                                    var buttonanulado = "<button data-estado-anterior='"+estado+"' data-id='" + ids + "' data-estado='Anulado' class='btn btn-block btn-outline massive' id='anulado' style='background-color: #000000; color: white;'>Anulado</button>";
                                    if (ids_cerrado.length > 0) {                                        
                                        button += buttonlegal+buttonanalisis+buttonpago+buttonpendiente+buttonanulado;                    
                                    } else if (ids_pago.length > 0) {
                                        button += buttonlegal+buttonanalisis+buttonpendiente+buttoncerrado+buttonanulado;  
                                    } else if (ids_anulado.length > 0) {
                                        button += buttonlegal+buttonanalisis+buttonpago+buttonpendiente+buttoncerrado;
                                    } else if (ids_pendiente.length > 0) {
                                        button += buttonlegal+buttonanalisis+buttonpago+buttoncerrado+buttonanulado;
                                    } else if (ids_legal.length > 0) {
                                        button += buttonanalisis+buttonpago+buttonpendiente+buttoncerrado+buttonanulado;
                                    } else if (ids_analisis.length > 0) {
                                        button += buttonlegal+buttonpago+buttonpendiente+buttoncerrado+buttonanulado;
                                    }
                                    
                                }
                                opcionesModal.find('.modal-title').empty().append(titulo);
                                opcionesModal.find('.modal-body').empty().append(button);
                                opcionesModal.find('.modal-footer').empty();
                                opcionesModal.modal('show');
                            });
                        });

                        //db state type emun
                        var cont = 0;
                        opcionesModal.on('click', '.massive', function (e) {
                            if (cont == 0) {
                                var estado = $(this).attr("data-estado");
                                var datos = {campo: {estado: estado, ids: ids}};
                                var cambio = moduloReclamos.cambiarEstadoReclamos(datos);
                                var estado_anterior = $(this).attr("data-estado-anterior");
                                
                                cambio.done(function (response) {
                                    opcionesModal.modal('hide');
                                    $("#mensaje").hide();
                                    recargar();
                                    toastr.success('Se ha efectuado Cambio de Estado correctamente.');
                                    /*for (var i = 0; i <= ids.length; i++) {
                                        var datosbitacora = {campo: {estado: estado, estado_anterior: estado_anterior, tipo: 'Cambio de Estado', id: ids[i]}};
                                        var cambiobitacora = moduloReclamosBitacora.cambiarEstadoReclamosBitacora(datosbitacora);
                                    }
                                    cambiobitacora.done(function (response) {
                                        console.log("Estados masivos ok");
                                    });*/
                                    cambio.fail(function (response) {
                                        toastr.error('No se pudo efectuar el Cambio de Estado correctamente.');
                                    });
                                });
                            }
                            cont = 1;
                        });


                    } else {
                        toastr.warning("Los registros no tienen el mismo estado.");
                    }

                } else {
                    toastr.warning("Seleccione algún registro.");
                }
            }
        }else{
            toastr.error("Usted no tiene permisos para cambiar estados.");
        }       

    });
    //Reload al jQgrid
    var recargar = function () {

        //Reload Grid
        grid_obj.setGridParam({
            url: phost() + url,
            datatype: "json",
            postData: {
                no_poliza: '',
                no_caso: '',
                no_certificado: '',
                cliente: '',
                fecha_inicio: '',
                fecha_fin: '',
                aseguradora: '',
                ramo: '',
                usuario: '',
                estado: '',
                erptkn: tkn
            }
        }).trigger('reloadGrid');
    };
    //Buscar cargo en jQgrid
    var buscarReclamos = function () {
        
        var no_poliza = $('#no_poliza').val();
        var no_caso = $('#no_caso').val();
        var no_certificado = $('#no_certificado').val();
        var cliente = $('#cliente').val();
        var aseguradora = $('#aseguradora').val();
        var ramo = [];
        var ramo = $('.ramo').val();        
        var inicio_creacion = $('#inicio_creacion').val();
        var fin_creacion = $('#fin_creacion').val();
        var usuario = $('#usuario').val();
        var estado = $('#estado_id').val();

        

        if (no_poliza != "" || no_caso != "" || no_certificado != "" || cliente != "" || aseguradora != "" || ramo != "" || inicio_creacion != "" || fin_creacion != "" || usuario != "" || estado != "")
        {
            //Reload Grid
            grid_obj.setGridParam({
                url: phost() + url,
                datatype: "json",
                postData: {
                    no_poliza: no_poliza,
                    no_caso: no_caso,
                    no_certificado: no_certificado,
                    cliente: cliente,
                    fecha_inicio: inicio_creacion,
                    fecha_fin: fin_creacion,
                    aseguradora: aseguradora,
                    ramo: ramo,
                    usuario: usuario,
                    fecha_seguimiento: '',
                    estado: estado,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }
    };
    //Limpiar campos de busqueda
    var limpiarCampos = function () {
        console.log("limpiar");
        $('#buscarReclamosForm').find('input[type="text"]').prop("value", "");
        $('#buscarReclamosForm').find('.chosen-select').val('').trigger('chosen:updated');
        $('#buscarReclamosForm').find('.ramo').val(' ').trigger('change.select2');
        $('#buscarReclamosForm').find('#fecha_creacion').prop("value", "");
        recargar();
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
tablaReclamos.init();

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
            $(this).closest("div.ui-jqgrid-view").find("#tablaReclamosGrid_cb, #jqgh_tablaReclamosGrid_link").css("text-align", "center");
        },
        beforeRequest: function (data, status, xhr) {},
        loadComplete: function (data) {

            //check if isset data
            if (data['total'] == 0) {
                $('#gbox_' + pagerId).hide();
                $('#' + pagerId + 'NoRecords').empty().append('No se encontraron Reclamos.').css({"color": "#868686", "padding": "30px 0 0"}).show();
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

