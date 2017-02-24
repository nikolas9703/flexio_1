
var colectivePeople = ['vida', 'salud', 'accidente', 'accidentes'],
valid = 0,
sendIdividualForm,
setting = {},
tablaTipo;

//Oculta Campos de Acreedor para ciertos Intereses
if (id_tipo_int_asegurado==1 || id_tipo_int_asegurado==3 || id_tipo_int_asegurado==5) {
    $("#poliza_acre").hide();
}


//Funcion al Cambiar en Selector la Poliza y llenar Campos
function buscaPoliza(){
    formularioCrear.polizaInfoSelect();
}

if ($().chosen) {
    if ($(".chosen-filtro").attr("class") != undefined) {
        $(".chosen-filtro").chosen({
            width: '100%',
            disable_search: true,
            inherit_select_classes: true
        });
    }
}

function modalEstados(form){
    var estados = $('#estado').val();

    if(estados == "Aprobada"){
        //Init Modal
        $('#AprobarSolicitud').find('.modal-title').empty().append('Aprobar Solicitud');
        $('#AprobarSolicitud').find('.modal-body').empty().append('<div class="row"><div class="col-md-12 form-group"><label>Número de Póliza</label><br><input type="text" name="npoliza" id="npoliza" class="form-control" ></div></div><div class="row"><div class="col-md-12 form-group"><input type="hidden" name="ntipo" value="Cambio de Estado"><button class="btn btn-success massive" id="guardaraprobar" data-estado-anterior="'+estado+'" data-solicitud="'+numero_soliciud+'" data-id="'+solicitud_id+'" data-estado="Aprobada">Aprobar</button></div></div>');
        $('#AprobarSolicitud').find('.modal-footer').empty();
        $('#AprobarSolicitud').modal('show');
        
        var ids =  $(this).attr("data-id");

        $('#AprobarSolicitud').on('click', '.massive', function (e) {
           
            var motivo = $('#AprobarSolicitud').find('#npoliza').val();
            if (motivo != "") {

                var poliza = {campo: {numero: motivo}};
                var res = moduloSolicitudesPoliza.verificarPoliza(poliza);
                var conta = 1;
                var estado = $(this).attr("data-estado");
                var solicitud = $(this).attr("data-solicitud");
                var estado_anterior = $(this).attr("data-estado-anterior");
                
                res.done(function (resp) {

                    if (resp == 0) {

                        $('#AprobarSolicitud').modal('hide');
                        toastr.success('Se ha Aprobado la solicitud correctamente.');
                        form.submit();
                        var datosbitacora = {campo: {estado: estado, estado_anterior: estado_anterior, tipo: 'Cambio de Estado', motivo: motivo, solicitud: solicitud, id:ids}};
                        var cambiobitacora = moduloSolicitudesBitacora.cambiarEstadoSolicitudesBitacora(datosbitacora);
                        ids = "";
                            
                    } else {
                        toastr.error('Este Numero de Poliza ya existe en el Sistema. Ingrese otro.');
                    }
                });
            } else {
                toastr.warning('Debe ingresar el Numero de Poliza para Aprobar la Solicitud.');
            }
        });
    }else if(estados == "Anulada"){
        
        $('#AnularSolicitud').find('.modal-title').empty().append('Anular Solicitud');
        $('#AnularSolicitud').find('.modal-body').empty().append('<div class="row"><div class="col-md-6 form-group"><label>Solicitud</label><br><input type="text" name="nsolicitud" class="form-control" value="'+numero_soliciud+'" disabled=""></div><div class="col-md-6 form-group"><label>Cliente</label><br><input type="text" class="form-control" name="ncliente" value="'+cliente.nombre+'" disabled=""></div></div><div class="row"><div class="col-md-12 form-group"><label>Razón</label><br><textarea name="motivoanula" id="motivoanula" class="form-control"></textarea></div></div><div class="row"><div class="col-md-12 form-group"><input type="hidden" name="ntipo" value="Cambio de Estado"><input type="hidden" name="id_solicitud" class="form-control" value="'+solicitud_id+'" disabled=""><button class="btn btn-success massive" id="guardaranular" data-estado-anterior="'+estado+'" data-estado="Anulada">Guardar</button></div></div>');
        $('#AnularSolicitud').find('.modal-footer').empty();
        $('#AnularSolicitud').modal('show');

        $('#AnularSolicitud').on('click', '.massive', function (e) {

            var motivo = $('#AnularSolicitud').find('#motivoanula').val();
            var nsolicitud = $('#AnularSolicitud').find('input[name="nsolicitud"]').val();
            if (motivo != "") {
                var estado = $(this).attr("data-estado");
                var estado_anterior = $(this).attr("data-estado-anterior");
                var datos = {campo: {estado: estado, ids: ids}};
                
                $('#AnularSolicitud').modal('hide');
                var id_modificado = $('#AnularSolicitud').find('input[name="id_solicitud"]').val();
                toastr.success('Se ha Anulado la solicitud correctamente.');
                form.submit();
                var datosbitacora = {campo: {estado: estado, estado_anterior: estado_anterior, tipo: 'Cambio de Estado', motivo: motivo, solicitud: nsolicitud, id: id_modificado}};
                var cambiobitacora = moduloSolicitudesBitacora.cambiarEstadoSolicitudesBitacora(datosbitacora);
            } else {
                toastr.warning('Debe ingresar el Motivo para Anular la Solicitud.');
            }
        });
    }else if(estados == "Rechazada"){

        $('#RechazarSolicitud').find('.modal-title').empty().append('Rechazar Solicitud');
        $('#RechazarSolicitud').find('.modal-body').empty().append('<div class="row"><div class="col-md-6 form-group"><label>Solicitud</label><br><input type="text" name="nsolicitud" class="form-control" value="'+numero_soliciud+'" disabled=""></div><div class="col-md-6 form-group"><label>Cliente</label><br><input type="text" class="form-control" name="ncliente" value="'+cliente.nombre+'" disabled=""></div></div><div class="row"><div class="col-md-12 form-group"><label>Razón</label><br><textarea name="motivorechazar" id="motivorechazar" class="form-control"></textarea></div></div><div class="row"><div class="col-md-12 form-group"><input type="hidden" name="ntipo" value="Cambio de Estado"><input type="hidden" name="id_solicitud" class="form-control" value="'+solicitud_id+'" disabled=""><button class="btn btn-success massive" id="guardarrechazar" data-estado-anterior="'+estado+'" data-estado="Rechazada">Guardar</button></div></div>');
        $('#RechazarSolicitud').find('.modal-footer').empty();
        $('#RechazarSolicitud').modal('show');

        $('#RechazarSolicitud').on('click', '.massive', function (e) {

            var motivo = $('#RechazarSolicitud').find('#motivorechazar').val();
            var nsolicitud = $('#RechazarSolicitud').find('input[name="nsolicitud"]').val();
            console.log(nsolicitud);
            if (motivo != "") {
                var estado = $(this).attr("data-estado");
                var estado_anterior = $(this).attr("data-estado-anterior");
                console.log(estado_anterior);
                var id_solicitud = $('#RechazarSolicitud').find('input[name="id_solicitud"]').val();
                $('#RechazarSolicitud').modal('hide');
                toastr.success('Se ha Rechazado la solicitud correctamente.');
                form.submit();
                var datosbitacora = {campo: {estado: estado, estado_anterior: estado_anterior, tipo: 'Cambio de Estado', motivo: motivo, solicitud: nsolicitud, id: id_solicitud}};
                var cambiobitacora = moduloSolicitudesBitacora.cambiarEstadoSolicitudesBitacora(datosbitacora);
            } else {
                toastr.warning('Debe ingresar el Motivo para Rechazar la Solicitud.');
            }
        });

    }else{
        form.submit();
    }

}

function cambiaAjustador(valor){
    formularioCrear.contactosAjustador(valor);
}
function cambiaContacto(valor){
    formularioCrear.telefonoContacto(valor);
}


var pantalla = $('.intereses_modal');
pantalla.css('display', 'block');
$('#verModalIntereses').find('.modal-title').empty().html('Buscar Interés Asegurado ');
$('#verModalIntereses').find('.modal-body').empty().html(pantalla);
$('#verModalIntereses').find('.modal-footer').empty();


$(document).ready(function () {

    if (tablaTipo == "vida" || tablaTipo == "accidentes" || tablaTipo == "accidente") {
        console.log(tablaTipo);
        $('.salud').hide();
    } else if (tablaTipo == "salud") {
        console.log(tablaTipo);
        $('.vida').hide();
    } else {
        $('.persona').hide();
        $('.salud').hide();
    }
    if (tablaTipo == 'vida' || tablaTipo == "accidentes" || tablaTipo == "accidente") {

        setting = {
            nacionalidad: true,
            relacion: false,
            participacion: false,
            estatura: true,
            peso: true,
            treeview: true
        };

    } else if (tablaTipo == 'salud') {
        setting = {
            nacionalidad: false,
            relacion: false,
            participacion: true,
            estatura: true,
            peso: true,
            treeview: true
        };
    } else {
        setting = {
            nacionalidad: true,
            relacion: true,
            participacion: true,
            estatura: false,
            peso: false,
            treeview: false
        };
    }


    var exonerado_imp = 0;

    $('#ramo_id').val(ramo_id);

    
    $('#poliza_seleccionado').select2();
    if (formularioCrear.polizaid != "") {
        $(".filtro-formularios").hide();
        formularioCrear.getPolizaSeleccionadoInfo();
    }
    

    if ($("#nombre_padre").val() == "Salud") {
        $("#polizaDeclarativa").attr("checked", "");
    }

    

    $("#reclamante").change(function () {
        var recla = $("#reclamante").val();

        if (recla == "otros") {
            $("#reclamante_otros").show();
            $("#reclamante_otro").attr("data-rule-required", "true");
        }else{
            $("#reclamante_otros").hide();
            $("#reclamante_otro").removeAttr("data-rule-required");
        }
    });

        

    $('#formReclamosCrear').validate({
        submitHandler: function (form) {
            $.post(phost() + 'reclamos/existsIdentificacion', $('#formReclamosCrear').serialize(), function(data){
                //console.log(data);
                var respuesta = $.parseJSON(data);
                if(respuesta.existe){
                    toastr.error("Este numero de reclamo ya existe, por favor intente con otro numero de caso.");
                }else{
                    if (id_tipo_int_asegurado == 8) {
                        var accid = 0;
                        $('input[name="campoaccidente[]').each(function( index ) {
                            if (this.checked == true) {
                                accid++;
                            }                    
                        });
                        if (accid>0) {
                            $("#uuid_vehiculo, #chasis, #unidad, #marca, #modelo, #placa, #ano, #motor, #color, #capacidad, #operador, #extras, #valor_extras, #porcentaje_acreedor, #observaciones_vehiculo, #uso, .condicion_vehiculo, .acreedor, .estado ").attr("disabled", false);
                            $("#fecha_reclamo")removeAttr("disabled");
                            form.submit();
                        }else{
                            toastr.warning("Debe seleccionar un tipo de accidente.");
                        }
                    }else{
                        $(".uuid_carga, #nombre, #clase_equipo, #marca, #modelo, #anio_articulo, #numero_serie, .valor_articulo, #observaciones_articulo, #id_condicion, .estado_articulo").attr("disabled", false);
                        $(".uuid_carga, #no_liquidacion, #fecha_despacho, #fecha_arribo, #fecha_arribo, #detalle, #origen, #destino, .valor_mercancia, #acreedor_carga_opcional, #tipo_obligacion_opcional, #observaciones_carga, .tipo_empaque, .acreedor_carga, #acreedor_carga_opcional, .tipo_obligacion, #tipo_obligacion_opcional, .condicion_envio, .medio_transporte, .estado_carga").attr("disabled", false);
                        $(".uuid_aereo, #serie_aereo, #marca_aereo, #modelo_aereo, #matricula_aereo, #valor_aereo, #pasajeros_aereo, #tripulacion_a, #observaciones_aereo, .estado_aereo").attr("disabled", false);
                        $(".uuid_casco_maritimo, #serie_maritimo, .serier, #marca_maritimo, #nombre_embarcacion, .porcentaje_acreedor_maritimo, #valor_maritimo, #pasajeros_maritimo, #observaciones_maritimo, .tipo_maritimo, .acreedor_maritimo, .estado_casco").attr("disabled", false);
                        $("#nombrePersona, #identificacion, #pasaporte, #provincia, #letra, #tomo, #asiento, .noPAS, .PAS, #fecha_nacimiento, #edad, #estado_civil, #nacionalidad, #sexo, #estatura, #peso, #telefono_residencial, #telefono_oficina, #direccion, #direccion_laboral, #observacionesPersona, #estadoPersona, #idPersona, #correoPersona, #telefono_residencial_check, #telefono_oficina_check, #direccion_residencial_check, #direccion_laboral_check").attr("disabled", false);
                        $(".uuid_proyecto, #nombre_proyecto, #contratista_proyecto, #representante_legal_proyecto, #fecha_concurso, #no_orden_proyecto, .no_ordenr, #duracion_proyecto, .fecha_proyecto, .monto_proyecto, #monto_afianzado, #asignado_acreedor, #ubicacion_proyecto, #acreedor_opcional, #validez_fianza_opcional, #observaciones_proyecto, .tipo_fianza, .tipo_propuesta, .acreedor_proyecto, .validez_fianza_pr, .estado_proyecto").attr("disabled", false);
                        $(".uuid_ubicacion, #nombre_ubicacion, #direccion_ubicacion, .serier, #edif_mejoras, #contenido, #maquinaria, #inventario, #acreedor_ubicacion_opcional, #porcentaje_acreedor_ubicacion, #observaciones_ubicacion, #acreedor_ubicacion, .estado_ubicacion").attr("disabled", false);
                        $("#fecha_reclamo")removeAttr("disabled");
                        form.submit();
                    } 
                }                
            });
            
        }

    });
    

    ///***************************************************

    var InteresesEditar = (function () {
        return {
            actualizaunico: function (parametros) {
                return $.post(phost() + "intereses_asegurados/update_unico", $.extend({
                    erptkn: tkn
                }, parametros));
            }
        };
    })();

    if (uuid_reclamos != 'undefined') {
        $('input[name="camporeclamo[uuid]').val(uuid_reclamos);
        //$('input[name="campo[id_solicitud]').val(solicitud_id);
        //$('#id_solicitud').val(solicitud_id);

        /*var unico = $("#detalleunico").val();
        var val = {campos: {id_solicitudes: solicitud_id, detalle_unico: unico}};
        var actualiza = InteresesEditar.actualizaunico(val);
        actualiza.done(function (response) {
            if (id_tipo_int_asegurado == 1) {
                tablaSolicitudesArticulo.recargar();
            } else if (id_tipo_int_asegurado == 2) {
                tablaSolicitudesCarga.recargar();
            } else if (id_tipo_int_asegurado == 3) {
                tablaSolicitudesAereo.recargar();
            } else if (id_tipo_int_asegurado == 4) {
                tablaSolicitudesMaritimo.recargar();
            } else if (id_tipo_int_asegurado == 5) {
                tablaSolicitudesPersonas.recargar();
            } else if (id_tipo_int_asegurado == 6) {
                tablaSolicitudesProyecto.recargar();
            } else if (id_tipo_int_asegurado == 7) {
                tablaSolicitudesUbicacion.recargar();
            } else if (id_tipo_int_asegurado == 8) {
                tablaSolicitudesVehiculo.recargar();
            }
        });

        var InteresesAct = (function () {
            return {
                obtenerInteres: function (parametros) {
                    return $.post(phost() + "intereses_asegurados/obtenerInteres", $.extend({
                        erptkn: tkn
                    }, parametros));
                }
            };
        })();

        if (id_tipo_poliza == 1) {
            var val2 = {campos: {id_solicitudes: solicitud_id}};
            var obtenerint = InteresesAct.obtenerInteres(val2);

            obtenerint.done(function (response) {
                console.log(response);
                if(response != null){
                    $("#selInteres").val(response.interesestable_id);
                    $("#selInteres").trigger('change');
                    formularioCrear.getIntereses();
                }
               
            });
        }*/
    }

    if (editar != 'undefined') {
        $('#idReclamo').val(id_reclamo);
        /*$('#formulario_tipo').attr('disabled', true);
        $('#formulario_tipo').empty();
        $('#formulario_tipo').append('<option>' + cliente.tipo_identificacion + '</option>');
        $('#cliente_seleccionado').empty();
        $('#cliente_seleccionado').append('<option value="' + cliente.id + '">' + cliente.nombre + '</option>');
        formularioCrear.getClienteSeleccionadoInfo();
        formularioCrear.getClienteCentroFacturable();
        $("#filtro-form").hide();*/
    }


    

    $('#imprimirReclamosLnk').click(function () {
        var id_reclamo = $('#idReclamo').val();
        window.open('../imprimirReclamos/' + id_reclamo);
    });
    ///***************************************************

    //Documentos Modal
    $('#subirDocumento').click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        //Inicializar opciones del Modal
        $('#documentosModalEditar').modal({
            backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
            //show: false
        });
        $('.docentregados').removeClass('hidden');
        $('#documentosModalEditar').modal('show');
        //$('#id_solicitud').val(id);
    });

    var counter = 2;
    $('#del_file_solicitud').hide();
    $('#add_file_solicitud').click(function () {

        $('#file_tools_solicitud').before('<div class="file_upload_solicitud row" id="fsolicitud' + counter + '"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"><br><br></div>');
        $('#del_file_solicitud').fadeIn(0);
        counter++;
    });
    $('#del_file_solicitud').click(function () {
        if (counter == 3) {
            $('#del_file_solicitud').hide();
        }
        counter--;
        $('#fsolicitud' + counter).remove();
    });



    $("#articulo, #formCarga, #formcasco_aereo, #formCasco_maritimo, #persona, #formProyecto_actividad, #formUbicacion, #vehiculo").attr('action', '' + window.location.href + '');
    $("#articulo #cancelar, #formCarga #cancelar, #formcasco_aereo #cancelar, #formCasco_maritimo #cancelar, #persona #cancelar, #formProyecto_actividad #cancelar, #formUbicacion #cancelar, #vehiculo #cancelar").remove();    
    $(".botones").remove();
    $(".guardarVehiculo, .guardarArticulo, .guardarCarga, .guardarAereo, .guardarMaritimo, .guardarPersona, .guardarProyecto, .guardarUbicacion").remove();
    $(" .detalleinteres_articulo, .detalleinteres_carga, .detalleinteres_aereo, .detalleinteres_maritimo, .detalleinteres_proyecto, .detalleinteres_ubicacion, .detalleinteres_vehiculo, .detalleinteres_persona").remove();

    switch (id_tipo_int_asegurado) {
        case 1:
        {
            $("#etiqueta1FormArticulo").remove();
            $("#etiqueta2FormArticulo").remove();
            break;
        }
        case 2:
        {
            break;
        }
        case 3:
        {
            break;
        }
        case 4:
        {
            break;
        }
        case 5:
        {
            break;
        }
        case 6:
        {
            break;
        }
        case 7:
        {
            break;
        }
        case 8:
        {
            break;
        }
    }

    $.extend($.validator.messages, {
        required: "Este campo es obligatorio.",
        email: "Por favor ingrese una dirección de correo válida."
    });

    $("#doc_entregados").hide();
    $("#espac").remove();
    if (id_tipo_int_asegurado == 4) {
        $(".divestado_casco").hide();
    }else if (id_tipo_int_asegurado == 7) {
        $(".divestado_ubicacion").hide();
    }else if (id_tipo_int_asegurado == 6) {
        $(".divestado_proyecto").hide();
    }else{
        $(".divestado").hide();
    }

    if (id_tipo_int_asegurado==8) {
        $("#detallereclamo_todos").remove();
    }else{
        $("#detallereclamo_vehiculo").remove();
    }


    $("#campotipointeres").val(id_tipo_int_asegurado);

    $("#verModalIntereses .modal-lg").width((window.innerWidth)-50);

    $(".guardarsolicitud").remove();

});