
var colectivePeople = ['vida', 'salud', 'accidente', 'accidentes'],
valid = 0,
sendIdividualForm,
setting = {},
ContVidaInd,
tablaTipo;


function getRamo() {
    $.ajax({
        type: "POST",
        dataType: "json",
        data: {
            ramo_id: ramo_id,
            erptkn: tkn
        },
        url: phost() + 'solicitudes/ajax_get_persona_colectivo',
        success: function (data)
        {
            isColective(data);
            OnloadFunction(valid, tablaTipo);

        }
    });
}

function isColective(data) {

    for (var i = colectivePeople.length - 1; i >= 0; i--) {

        var myWord = colectivePeople[i];
        if (new RegExp('\\b' + myWord + '\\b', "gi").test(data.nombre)) {
            valid++;
            tablaTipo = myWord;

        }
        if (new RegExp('\\b' + myWord + '\\b', "gi").test(data.descripcion)) {
            valid++;
            tablaTipo = myWord;


        }


    }
    if (tablaTipo == 'vida' || tablaTipo == "accidentes" || tablaTipo == "accidente") {

       
            if(tablaTipo == 'vida'){ //id_tipo_poliza == 1 &&

                $(".relaciondetalle_persona_vida_otros").addClass('hidden');
                $(".relaciondetalle_persona_vida_otros").attr('disabled',true);
                $(".relaciondetalle_persona_vida").removeClass('hidden');
                $(".relaciondetalle_persona_vida").attr('disabled',false);
                $("#suma_asegurada_persona").attr("data-rule-required", "false").attr('disabled',true);
            }

        
        setting = {
            nacionalidad: true,
            relacion: false,
            tipo_relacion:true,
            participacion: false,
            estatura: true,
            peso: true,
            treeview: true
        };

    } else if (tablaTipo == 'salud') {
        setting = {
            nacionalidad: false,
            relacion: false,
            tipo_relacion:false,
            participacion: true,
            estatura: true,
            peso: true,
            treeview: true
        };
    } else {
        setting = {
            nacionalidad: true,
            tipo_relacion:true,
            relacion: true,
            participacion: true,
            estatura: false,
            peso: false,
            treeview: false
        };
    }

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
$("#verCoberturas,#IndCoberturas").on('click', '.addCobertura', function(){
   $(".moneda").inputmask('currency',{
      prefix: "",
      autoUnmask : true,
      removeMaskOnSubmit: true
    });   
});

$('.relaciondetalle_persona_vida, .relaciondetalle_persona_vida_otros').change( function() {
    
    var nombre_ramo = $('#nombre_ramo').val();
    if(new RegExp('\\bvida\\b', "gi").test(nombre_ramo) || new RegExp('\\baccidente\\b', "gi").test(nombre_ramo) || new RegExp('\\baccidentes\\b', "gi").test(nombre_ramo) ){
        var relacion = $('.relaciondetalle_persona_vida').val(); 

        if(relacion == ''){
            relacion = $('relaciondetalle_persona_vida_otros').val(); 
        }

        if(relacion == "Principal"){
            $('#participacion_persona').attr('disabled',true);
            if (validavida == 1 && id_tipo_poliza == 2) {
                console.log("cambio princpial");
                $("#vigencia_vida_colectivo").show();
            }            
        }else{
            $('#participacion_persona').attr('disabled',false);
            if(validavida == 1 && id_tipo_poliza == 2){
                console.log("cambio princpial2");
                $("#vigencia_vida_colectivo").hide();
            }
        }
    }else{
        if (validavida == 1 && id_tipo_poliza == 2) {
            var relacion = $('.relaciondetalle_persona_vida').val(); 
            if(relacion == "Principal"){
                //$('#participacion_persona').attr('disabled',true);
                console.log("cambio princpial");
                $("#vigencia_vida_colectivo").show();           
            }else{
                //$('#participacion_persona').attr('disabled',false);
                console.log("cambio princpial2");
                $("#vigencia_vida_colectivo").hide();
            }
        }
    }
})

$(".comision_solicitud").inputmask('Regex', {regex: "^[0-9]{1,20}(\\.\\d{1,2})?$"});

function modalEstados(form){
    var estados = $('#estado').val();

    if(estados == "Aprobada"){
        //Init Modal
        $('#AprobarSolicitud').find('.modal-title').empty().append('Aprobar Solicitud');
        $('#AprobarSolicitud').find('.modal-body').empty().append('<div class="row"><div class="col-md-12 form-group"><label>Número de Póliza</label><br><input type="text" name="npoliza" id="npoliza" class="form-control" ></div></div><div class="row"><div class="col-md-12 form-group"><input type="hidden" name="ntipo" value="Cambio de Estado"><button class="btn btn-success massive" id="guardaraprobar" data-estado-anterior="'+estado+'" data-solicitud="'+numero_soliciud+'" data-id="'+solicitud_id+'" data-estado="Aprobada">Aprobar</button></div></div>');
        $('#AprobarSolicitud').find('.modal-footer').empty();
        $('#AprobarSolicitud').modal('show');
        
        $('#AprobarSolicitud').on('click', '.massive', function (e) {
           
            var motivo = $('#AprobarSolicitud').find('#npoliza').val();
            if (motivo != "") {

                var poliza = {campo: {numero: motivo}};
                var res = moduloSolicitudesPoliza.verificarPoliza(poliza);
                var conta = 1;
                var estado = $(this).attr("data-estado");
                var solicitud = $(this).attr("data-solicitud");
                var estado_anterior = $(this).attr("data-estado-anterior");
                var ids =  $(this).attr("data-id");

                res.done(function (resp) {

                    if (resp == 0) {

                        $('#AprobarSolicitud').modal('hide');
                        var inputs = $('#formClienteCrear :input');
                        var values = {};
                        inputs.each(function () {
                            if(this.name == "campovigencia[poliza_declarativa]"){
                                if ($(this).prop("checked") == true) {
                                    values[this.name] = "on";
                                }else{
                                    values[this.name] = "off";
                                }
                            }else if(this.name == "campoacreedores[]" || this.name == "campoacreedores_por[]" || this.name == "campoacreedores_mon[]" || this.name == "campoacreedores_ini[]" || this.name == "campoacreedores_fin[]" || this.name == "campoacreedores_id[]"){
                                var con = 0 ;
                                var nom = this.name;
                                $('input[name="'+this.name+'"]').each(function () {
                                    var x = $(this).val();
                                    var n = nom.split("[]");
                                    values[n[0]+"["+con+"]"] = x ;
                                    con++;              
                                });
                            }else{
                                values[this.name] = $(this).val();
                            }                            
                        });
                        tkn = values.erptkn;
                        
                        var guardarsolicitud = moduloSolicitudes.ajaxguardarsolicitud(values);
                        
                        guardarsolicitud.success (function(){
                            var datosbitacora = {campo: {estado: estado, estado_anterior: estado_anterior, tipo: 'Solicitud_aprobada', motivo: motivo, solicitud: solicitud, id: ids}};
                            var cambiobitacora = moduloSolicitudesBitacora.cambiarEstadoSolicitudesBitacora(datosbitacora);
                            cambiobitacora.done(function (response) {
                                toastr.success('Se ha Aprobado la solicitud correctamente.');
                                var inf = $.parseJSON(response);
                                if (inf.msg == "Ok") {
                                    //form.submit();
                                    location.href = phost() + 'polizas/editar/' + inf.uuid;
                                }
                            });
                        });
                        
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
            if (motivo != "") {
                var estado = $(this).attr("data-estado");
                var estado_anterior = $(this).attr("data-estado-anterior");
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


function OnloadFunction(valid, tablaTipo) {


    if (tablaTipo == "vida" || tablaTipo == "accidentes" || tablaTipo == "accidente") {

        $('.salud').hide();
        $('.montoPersona').remove();
    } else if (tablaTipo == "salud") {
        $('.vida').hide();
    } else {
        $('.persona').hide();
        $('.salud').hide();
    }

    if(id_tipo_poliza!==undefined){
        $('#personaInvidual').val(id_tipo_poliza);

    }

    var exonerado_imp = 0;

    $('#ramo_id').val(ramo_id);

    $('#selInteres,#asociadodetalle_persona,#cliente_seleccionado').select2();

    if ($("#nombre_padre").val() == "Salud") {
        $("#polizaDeclarativa").attr("checked", "");
    }

    $("#vigencia_desde").change(function () {
        var vig = $("#vigencia_desde").val();
        var now = new Date(vig);
        now.setDate(now.getDate() + 364);
        var dat = now.getDate();
        var mon = now.getMonth() + 1;
        var year = now.getFullYear();
        if (mon < 10) {
            mon = "0" + mon;
        }
        if (dat < 10) {
            dat = "0" + dat;
        }
        var fechafin = mon + "/" + dat + "/" + year;

        $("#vigencia_hasta").val(fechafin);

        $("#fecha_primer_pago").val(vig);
        $("#fecha_primerPago").val(vig);

        if (vig.indexOf('-') > -1) {
            var dat = vig.split('-');
            var dia = dat[2];
            var mes = dat[1];
            var anio = dat[0];
            vig = mes + '/' + dia + '/' + anio ;
        }

        $('.fechas_acreedores_inicio').each(function () {
            var inicio = $(this).val();
            var desde = new Date(vig);            

            if (inicio.indexOf('-') > -1) {
                var dat = inicio.split('-');
                var dia = dat[2];
                var mes = dat[1];
                var anio = dat[0];
                inicio = mes + '/' + dia + '/' + anio ;
            }

            if (inicio != "") {
                var ini = new Date(inicio);   
                if (ini < desde) {
                    $(this).val(vig);
                }
            }            
        });
        
    });

    $("#vigencia_hasta").change(function () {
        var vigini = $("#vigencia_desde").val();
        var vigfin = $("#vigencia_hasta").val();

        var ini = new Date(vigini);
        var fin = new Date(vigfin);

        if (ini > fin) {
            $("#vigencia_hasta").val(vigini);
            vigfin = vigini;
        }

        if (vigfin.indexOf('-') > -1) {
            var dat = vigfin.split('-');
            var dia = dat[2];
            var mes = dat[1];
            var anio = dat[0];
            vigfin = mes + '/' + dia + '/' + anio ;
        }

        $('.fechas_acreedores_fin').each(function () {
            var fin2 = $(this).val();
            var hasta = new Date(vigfin);

            if (fin2.indexOf('-') > -1) {
                var dat = fin2.split('-');
                var dia = dat[2];
                var mes = dat[1];
                var anio = dat[0];
                fin2 = mes + '/' + dia + '/' + anio ;
            }

            if (fin != "") {
                var fini = new Date(fin2);
                if (fini > hasta) {
                    $(this).val(vigfin);
                }
            }
            
        });

    });

    $("#fecha_primer_pago").change(function () {

        var vig = $("#fecha_primer_pago").val();
        $("#fecha_primerPago").val(vig);
    }); 


    $("#inpuesto_checkbox").change(function () {
        if ($("#inpuesto_checkbox")) {
        }
    });

    $(".ncli").change(function () {
        var paga = $(".ncli").val();
        $("#campopagador").val(paga);
    });

    $("#sumaaseguradadetalle_vehiculo, #primadetalle_vehiculo, #deducibledetalle_vehiculo, #deducibledetalle_articulo, #primadetalle_articulo, #sumaaseguradadetalle_articulo, #deducibledetalle_carga, #primadetalle_carga, #sumaaseguradadetalle_carga, #deducibledetalle_aereo, #primadetalle_aereo, #sumaaseguradadetalle_aereo, #deducibledetalle_maritimo, #primadetalle_maritimo, #sumaaseguradadetalle_maritimo, #deducibledetalle_proyecto, #primadetalle_proyecto, #sumaaseguradadetalle_proyecto, #deducibledetalle_ubicacion, #primadetalle_ubicacion, #sumaaseguradadetalle_ubicacion, #montodetalle_persona, #primadetalle_persona,#suma_asegurada_persona").inputmask('currency', {
        prefix: "",
        autoUnmask: true,
        removeMaskOnSubmit: true
    });

    

    $('#formClienteCrear').validate({
        rules: {
            campo: {
                validaPrima: true
            }
        },
        submitHandler: function (form) {

            var porcen = 0 ;
            var monto = 0;
            var suma = 0;

            //Inicio Cambio
            if (id_tipo_poliza == 1) {
                $('input[name="campoacreedores_por[]"]').each(function () {
                    var x = $(this).val();
                    if (x != "") {
                        porcen = parseFloat(porcen) + parseFloat(x) ;
                    }                
                });
                $('input[name="campoacreedores_mon[]"]').each(function () {
                    var x = $(this).val();
                    if (x != "") {
                        monto = parseFloat(monto) + parseFloat(x) ;
                    }                
                });
                var suma = $('input[name="campovigencia[suma_asegurada]"]').val();
                if (suma == "") {suma = 0;}
                suma = parseFloat(suma);
            }  
            //Fin Cambio          

            if (porcen <= 100 && monto <= suma ) {
                if (id_tipo_poliza == 1 ) {
                    if (id_tipo_int_asegurado == 1) {
                        if ($('#articulo').validate().form() == true) {
                            var inputs = $('#articulo :input');
                            var values = {};
                            inputs.each(function () {
                                values[this.name] = $(this).val();
                            });
                            tkn = values.erptkn;
                            var tipo = "articulo";
                            var guardar = Intereses.interes(values, tipo);
                            guardar.done(function (response) {
                                modalEstados(form);
                                //form.submit();
                            });
                            guardar.fail(function () {
                                toastr.error('Ha ocurrido un Error.');
                            });
                        } else {
                            toastr.error('Faltan campos por llenar del Interes Asegurado.');
                            window.location.href = "#divintereses";
                        }
                    } else if (id_tipo_int_asegurado == 2) {
                        if ($('#formCarga').validate().form() == true) {
                            var inputs = $('#formCarga :input');
                            var values = {};
                            inputs.each(function () {
                                values[this.name] = $(this).val();
                            });
                            tkn = values.erptkn;
                            var tipo = "carga";
                            var guardar = Intereses.interes(values, tipo);
                            guardar.done(function (response) {
                                modalEstados(form);
                                //form.submit();
                            });
                            guardar.fail(function () {
                                toastr.error('Ha ocurrido un Error.');
                            });
                        } else {
                            toastr.error('Faltan campos por llenar del Interes Asegurado.');
                            window.location.href = "#divintereses";
                        }
                    } else if (id_tipo_int_asegurado == 3) {
                        if ($('#formcasco_aereo').validate().form() == true) {
                            var inputs = $('#formcasco_aereo :input');
                            var values = {};
                            inputs.each(function () {
                                values[this.name] = $(this).val();
                            });
                            tkn = values.erptkn;
                            var tipo = "aereo";
                            var guardar = Intereses.interes(values, tipo);
                            guardar.done(function (response) {
                                modalEstados(form);
                                //form.submit();
                            });
                            guardar.fail(function () {
                                toastr.error('Ha ocurrido un Error.');
                            });
                        } else {
                            toastr.error('Faltan campos por llenar del Interes Asegurado.');
                            window.location.href = "#divintereses";
                        }
                    } else if (id_tipo_int_asegurado == 4) {
                        if ($('#formCasco_maritimo').validate().form() == true) {
                            var inputs = $('#formCasco_maritimo :input');
                            var values = {};
                            inputs.each(function () {
                                values[this.name] = $(this).val();
                            });
                            tkn = values.erptkn;
                            var tipo = "maritimo";
                            var guardar = Intereses.interes(values, tipo);
                            guardar.done(function (response) {
                                modalEstados(form);
                                //form.submit();
                            });
                            guardar.fail(function () {
                                toastr.error('Ha ocurrido un Error.');
                            });
                        } else {
                            toastr.error('Faltan campos por llenar del Interes Asegurado.');
                            window.location.href = "#divintereses";
                        }
                    } else if (id_tipo_int_asegurado == 5) {
                        if ($('#persona').validate().form() == true || ContVidaInd > 0) {
                            if(ContVidaInd == 0){
                                var inputs = $('#persona :input');
                                var values = {};

                                inputs.each(function () {
                                    values[this.name] = $(this).val();
                                });
                                //tkn = values.erptkn;
                                var tipo = "persona";
                                var guardar = Intereses.interes(values, tipo);
                                guardar.done(function (response) {
                                    modalEstados(form);
                                    //form.submit();
                                });
                                guardar.fail(function () {
                                    toastr.error('Ha ocurrido un Error.');
                                });
                            }else{
                                modalEstados(form);
                            }
                        } else {
                            window.location.href = "#divintereses";
                        }


                    } else if (id_tipo_int_asegurado == 6) {
                        if ($('#formProyecto_actividad').validate().form() == true) {
                            var inputs = $('#formProyecto_actividad :input');
                            var values = {};
                            inputs.each(function () {
                                values[this.name] = $(this).val();
                            });
                            tkn = values.erptkn;
                            var tipo = "proyecto";
                            var guardar = Intereses.interes(values, tipo);
                            guardar.done(function (response) {
                                modalEstados(form);
                                //form.submit();
                            });
                            guardar.fail(function () {
                                toastr.error('Ha ocurrido un Error.');
                            });
                        } else {
                            toastr.error('Faltan campos por llenar del Interes Asegurado.');
                            window.location.href = "#divintereses";
                        }
                    } else if (id_tipo_int_asegurado == 7) {
                        if ($('#formUbicacion').validate().form() == true) {
                            var inputs = $('#formUbicacion :input');
                            var values = {};
                            inputs.each(function () {
                                values[this.name] = $(this).val();
                            });
                            tkn = values.erptkn;
                            var tipo = "ubicacion";
                            var guardar = Intereses.interes(values, tipo);
                            guardar.done(function (response) {
                                modalEstados(form);
                                //form.submit();
                            });
                            guardar.fail(function () {
                                toastr.error('Ha ocurrido un Error.');
                            });
                        } else {
                            toastr.error('Faltan campos por llenar del Interes Asegurado.');
                            window.location.href = "#divintereses";
                        }
                    } else if (id_tipo_int_asegurado == 8) {
                        if ($('#vehiculo').validate().form() == true) {
                            var inputs = $('#vehiculo :input');
                            var values = {};
                            inputs.each(function () {
                                values[this.name] = $(this).val();
                            });
                            tkn = values.erptkn;
                            var tipo = "vehiculo";
                            var guardar = Intereses.interes(values, tipo);
                            guardar.done(function (response) {
                                modalEstados(form);
                                //form.submit();
                            });
                            guardar.fail(function () {
                                toastr.error('Ha ocurrido un Error.');
                            });
                        } else {
                            toastr.error('Faltan campos por llenar del Interes Asegurado.');
                            window.location.href = "#divintereses";
                        }
                    }
                } else if(vista == "editar"){
                    modalEstados(form);
                }else{
                    form.submit();
                }
                if($('#formClienteCrear').validate().form() == true) 
                    $('body').find('input[id="campo[guardar]"]').attr("disabled","disabled");
                else 
                    $('body').find('input[id="campo[guardar]"]').removeAttr("disabled");
                //
            }else{
                toastr.error("Acreedores: La sumatoria de porcentajes de cesión y/o el monto son mayores a la suma asegurada.");
            }
            
        }

    });

    jQuery.validator.addMethod('validaPrima', function (e) {
        try {
            if ($('#polizaDeclarativa').prop('checked')) {
                return true;
            }else{
                if (parseFloat($("#prima_anual").val())>0) {
                    return true;
                }else{
                    return false;
                }
            }
        }
        catch (e) {
            console.log(e);
        }
    }, 'El valor de la prima anual debe ser mayor a cero.');

    $('#prima_anual').rules(
        "add",{ 
            required: false, 
            validaPrima: true,
        }
    );

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

    if (uuid_solicitudes != 'undefined') {
        $('input[name="campo[uuid]').val(uuid_solicitudes);
        $('input[name="campo[id_solicitud]').val(solicitud_id);
        $('#id_solicitud').val(solicitud_id);

        var unico = $("#detalleunico").val();
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
                if(response != null){
                    $("#selInteres").val(response.interesestable_id);
                    $("#selInteres").trigger('change');
                    formularioCrear.getIntereses();
                }
               
            });
        }
    }

    if (editar != 'undefined') {
        $('#idSolicitud').val(solicitud_id);
        $('#formulario_tipo').attr('disabled', true);
        $('#formulario_tipo').empty();
        $('#formulario_tipo').append('<option>' + cliente.tipo_identificacion + '</option>');
        $('#cliente_seleccionado').empty();
        $('#cliente_seleccionado').append('<option value="' + cliente.id + '">' + cliente.nombre + '</option>');
        formularioCrear.getClienteSeleccionadoInfo();
        formularioCrear.getClienteCentroFacturable();
        $("#filtro-form").hide();
    }

    if (asegurada != 'undefined') {

        $('#aseguradoras').empty();
        $('#aseguradoras').append('<option value="' + asegurada[0].id + '">' + asegurada[0].nombre + '</option>');
    }

    if (plan != 'undefined' && plan != '') {

        $('#planes').empty();
        $('#planes').append('<option value="' + plan[0].id + '">' + plan[0].nombre + '</option>');

        $('input[name="campo[comision]').val(comision);
        if(permiso_editar){
           $('input[name="campo[comision]').prop('disabled',false); 
        }
        formularioCrear.getCoberturasPlanEditar();
        coberturasForm.setPlanValues();
        formularioCrear.getComisionesInfo();
    }

    if (vigencia == 'undefined') {

        $('#divpagadornombre').css('display', 'none');
    } else if (vigencia.tipo_pagador != 'asegurado') {

        $('#divpagadornombre').css('display', 'block');
        $('#divpgnombre').css('display', 'block');
        $('#divselpagador').css('display', 'none');
    } else if (vigencia.tipo_pagador == 'asegurado') {

        $('#divpagadornombre').css('display', 'block');
        $('#divpgnombre').css('display', 'none');
        $('#divselpagador').css('display', 'block');

    } else if (vigencia.tipo_pagador == 'otro') {
        $("#campopagador").removeAttr("disabled");
    } else {
        $("#campopagador").attr("disabled", true);
    }

    if (prima != 'undefined') {
        $('#prima_anual').val(prima.prima_anual);
        $('#impuesto').val(prima.impuesto);
        $('#otros').val(prima.otros);
        $('#descuentos').val(prima.descuentos);
        $('#total').val(prima.total);
    }
    if (participacion != 'undefined' && editar != 'undefined') {
        $('#crearParticipacion').empty();
        $('#editarParticipacion').removeClass('hidden');
        $('.docentregados').addClass('hidden');
        $('.chek_solicitudes').addClass('hidden');
        $('#cantidad').val($("div > #total_agentes_participantes").length);
        formularioCrear.sumatotal();
        
    }

    if (observaciones != 'undefined') {
        $('#observaciones_solicitudes').val(observaciones);
    }

    $('#imprimirSolicitudesLnk').click(function () {
        var id_solicitud = $('#idSolicitud').val();
        window.open('../imprimirSolicitud/' + id_solicitud);
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
    $(".guardarVehiculo, .guardarArticulo, .guardarCarga, .guardarAereo, .guardarMaritimo, .guardarPersona, .guardarProyecto, .guardarUbicacion").attr("type", "button").attr('disabled',true).val("Agregar");
    $("#articulo #cancelar, #formCarga #cancelar, #formcasco_aereo #cancelar, #formCasco_maritimo #cancelar, #persona #cancelar, #formProyecto_actividad #cancelar, #formUbicacion #cancelar, #vehiculo #cancelar").hide();

    if (id_tipo_poliza == 1 && valid == 0) {
        $(".botones").remove();
        $(".guardarVehiculo, .guardarArticulo, .guardarCarga, .guardarAereo, .guardarMaritimo, .guardarPersona, .guardarProyecto, .guardarUbicacion").hide();
        $(" #relaciondetalle_persona").attr("disabled", "disabled");
        $(" #asociadodetalle_persona").attr("disabled", "disabled");
        $(" #articuloTab, #cargaTab, #casco_aereoTab, #casco_maritimoTab , #personaTab , #proyecto_actividadTab , #ubicacionTab , #vehiculoTab").css("margin-bottom", "-31px");
    } else if (id_tipo_poliza == 2 || (valid > 0 && id_tipo_int_asegurado == 5)) {
        $(" .detalleinteres_articulo, .detalleinteres_carga, .detalleinteres_aereo, .detalleinteres_maritimo, .detalleinteres_proyecto, .detalleinteres_ubicacion, .detalleinteres_vehiculo, .detalleinteres_persona").show();
        $(" .tabladetalle_articulo, .tabladetalle_carga, .tabladetalle_aereo, .tabladetalle_maritimo, .tabladetalle_personas, .tabladetalle_proyecto, .tabladetalle_ubicacion, .tabladetalle_vehiculo").show();
        $(" #primadetalle_persona, #beneficiodetalle_persona, #montodetalle_persona").attr("data-rule-required", "true");
        $(" #sumaaseguradadetalle_articulo, #primadetalle_articulo, #sumaaseguradadetalle_carga, #primadetalle_carga, #sumaaseguradadetalle_aereo, #primadetalle_aereo, #sumaaseguradadetalle_maritimo, #primadetalle_maritimo, #sumaaseguradadetalle_proyecto, #primadetalle_proyecto, #sumaaseguradadetalle_ubicacion, #primadetalle_ubicacion, #sumaaseguradadetalle_vehiculo, #primadetalle_vehiculo").attr("data-rule-required", "true");
        if(id_tipo_poliza != 1){
            $(" #suma_asegurada").attr("data-rule-required", "false").attr("disabled", true);
        }
            

        $(" #prima_anual").attr("readonly", "readonly");

        $("#planes").change(function () {
            //formularioCrear.setPrimaNeta();
        });

    }

    var idint = [];

    $("#guardar_interes").click(function () {

        if ($('#guardarDocumentoIntereses').validate().form() == true) {
            var documentos = $('#guardarDocumentoIntereses :input');
        } else {
        }
    });

    $(".guardarCarga").click(function () {
        if ($('#formCarga').validate().form() == true) {
            var inputs = $('#formCarga :input');
            var values = {};
            inputs.each(function () {
                values[this.name] = $(this).val();
            });
            tkn = values.erptkn;
            var tipo = "carga";
            var guardar = Intereses.interes(values, tipo);
            guardar.done(function (response) {
                var x = response.split("&");
                if ($(".uuid_carga").val() == "") {
                    $(".uuid_carga").val(x[0]);
                }

                //Verifica que el Interes ya exista en la Tabla
                if (jQuery.inArray($(".uuid_carga").val(), idint) == -1) {
                    idint.push($(".uuid_carga").val());
                    toastr.success('Se ha guardado correctamente el interés ' + x[1] + '');
                } else {
                    toastr.error('El interés asegurado ya se encuentra en la lista.');
                }
                //Recarga Tabla
                tablaSolicitudesCarga.recargar();

                //Formulario se deja Limpio
                $("#selInteres").val('');
                $("#selInteres").trigger('change');
                formularioCrear.getIntereses();
                $("#certificadodetalle_carga, #sumaaseguradadetalle_carga, #deducibledetalle_carga").val("");

                //Obtener La prima Anual con la sumatoria de las primas netas
                var unico = $("#detalleunico").val();
                var camposi = {campo: unico};
                var obtener = Intereses.prima(camposi);
                obtener.done(function (resp) {
                    formularioCrear.getPrimaAnual(resp);
                });
            });
            guardar.fail(function () {
                toastr.error('Ha ocurrido un Error.');
            });
        } else {
            window.location.href = "#divintereses";
        }
    });


    $(".guardarArticulo").click(function () {
        if ($('#articulo').validate().form() == true) {
            var inputs = $('#articulo :input');
            var values = {};
            inputs.each(function () {
                values[this.name] = $(this).val();
            });
            tkn = values.erptkn;
            var tipo = "articulo";
            var guardar = Intereses.interes(values, tipo);
            guardar.done(function (response) {
                var x = response.split("&");
                if ($("#uuid_articulo").val() == "") {
                    $("#uuid_articulo").val(x[0]);
                }

                //Valida si e interes ya existe
                if (jQuery.inArray($("#uuid_articulo").val(), idint) == -1) {
                    idint.push($("#uuid_articulo").val());
                    toastr.success('Se ha guardado correctamente el interés ' + x[1] + '');
                } else {
                    toastr.error('El interés asegurado ya se encuentra en la lista.');
                }
                //Recarga Tabla
                tablaSolicitudesArticulo.recargar();

                //Formulario se deja Limpio
                $("#selInteres").val('');
                $("#selInteres").trigger('change');
                formularioCrear.getIntereses();
                $("#certificadodetalle_articulo, #sumaaseguradadetalle_articulo, #deducibledetalle_articulo").val("");

                //Se obtiene prima anual con sumatoria de primas netas
                var unico = $("#detalleunico").val();
                var camposi = {campo: unico};
                var obtener = Intereses.prima(camposi);
                obtener.done(function (resp) {
                    formularioCrear.getPrimaAnual(resp);
                });
            });
            guardar.fail(function () {
                toastr.error('Ha ocurrido un Error.');
            });
        } else {
            window.location.href = "#divintereses";
        }
    });

    $(".guardarAereo").click(function () {
        if ($('#formcasco_aereo').validate().form() == true) {
            var inputs = $('#formcasco_aereo :input');
            var values = {};
            inputs.each(function () {
                values[this.name] = $(this).val();
            });
            tkn = values.erptkn;
            var tipo = "aereo";
            var guardar = Intereses.interes(values, tipo);
            guardar.done(function (response) {
                var x = response.split("&");
                if ($(".uuid_aereo").val() == "") {
                    $(".uuid_aereo").val(x[0]);
                }

                //Verifica si el interes ya existe
                if (jQuery.inArray($(".uuid_aereo").val(), idint) == -1) {
                    idint.push($(".uuid_aereo").val());
                    toastr.success('Se ha guardado correctamente el interés ' + x[1] + '');
                } else {
                    toastr.error('El interés asegurado ya se encuentra en la lista.');
                }
                //Recarga la tabla
                tablaSolicitudesAereo.recargar();

                //Formulario se deja Limpio
                $("#selInteres").val('');
                $("#selInteres").trigger('change');
                formularioCrear.getIntereses();
                $("#certificadodetalle_aereo, #sumaaseguradadetalle_aereo, #deducibledetalle_aereo").val("");

                //Obtiene Prima Anual con sumatorias de prima neta
                var unico = $("#detalleunico").val();
                var camposi = {campo: unico};
                var obtener = Intereses.prima(camposi);
                obtener.done(function (resp) {
                    formularioCrear.getPrimaAnual(resp);
                });
            });
            guardar.fail(function () {
                toastr.error('Ha ocurrido un Error.');
            });
        } else {
            window.location.href = "#divintereses";
        }
    });

    $(".guardarMaritimo").click(function () {
        if ($('#formCasco_maritimo').validate().form() == true) {
            var inputs = $('#formCasco_maritimo :input');
            var values = {};
            inputs.each(function () {
                values[this.name] = $(this).val();
            });
            tkn = values.erptkn;
            var tipo = "maritimo";
            var guardar = Intereses.interes(values, tipo);
            guardar.done(function (response) {
                var x = response.split("&");
                if ($(".uuid_casco_maritimo").val() == "") {
                    $(".uuid_casco_maritimo").val(x[0]);
                }
                if ($(".serier").val() == "") {
                    $(".serier").val(x[1]);
                }

                //Verifica si el interes ya se ha agregado
                if (jQuery.inArray($(".uuid_casco_maritimo").val(), idint) == -1) {
                    idint.push($(".uuid_casco_maritimo").val());
                    toastr.success('Se ha guardado correctamente el interés ' + x[2] + '');
                } else {
                    toastr.error('El interés asegurado ya se encuentra en la lista.');
                }
                //Recarga Tabla
                tablaSolicitudesMaritimo.recargar();

                //Formulario se deja Limpio
                $("#selInteres").val('');
                $("#selInteres").trigger('change');
                formularioCrear.getIntereses();
                $("#certificadodetalle_maritimo, #sumaaseguradadetalle_maritimo, #primadetalle_maritimo, #deducibledetalle_maritimo").val("");

                //Obtiene prima anual con sumatorias de prima neta
                var unico = $("#detalleunico").val();
                var camposi = {campo: unico};
                var obtener = Intereses.prima(camposi);
                obtener.done(function (resp) {
                    formularioCrear.getPrimaAnual(resp);
                });
            });
            guardar.fail(function () {
                toastr.error('Ha ocurrido un Error.');
            });
        } else {
            window.location.href = "#divintereses";
        }
    });

    $('.guardarPersona').click(function () {

        var porcen = 0 ;
        var monto = 0;
        var suma = $('#suma_asegurada_persona').val();

        //Inicio Cambio
        if (id_tipo_poliza == 2) {
            $('input[name="campoacreedores_por[]"]').each(function () {
                var x = $(this).val();
                if (x != "") {
                    porcen = parseFloat(porcen) + parseFloat(x) ;
                }                
            });
            $('input[name="campoacreedores_mon[]"]').each(function () {
                var x = $(this).val();
                if (x != "") {
                    monto = parseFloat(monto) + parseFloat(x) ;
                }                
            });
            
            if (suma == "") {suma = 0;}
            suma = parseFloat(suma);
        }  
        //Fin Cambio         

        if (porcen <= 100 && monto <= suma ) {

            if ($('#persona').validate().form() == true ) {
                var inputs = $('#persona :input');
                var values = {};

                inputs.each(function () {
                    if(this.name == "campoacreedores[]" || this.name == "campoacreedores_por[]" || this.name == "campoacreedores_mon[]" || this.name == "campoacreedores_ini[]" || this.name == "campoacreedores_fin[]" || this.name == "campoacreedores_id[]"){
                        var con = 0 ;
                        var nom = this.name;
                        $('input[name="'+this.name+'"]').each(function () {
                            var x = $(this).val();
                            var n = nom.split("[]");
                            values[n[0]+"["+con+"]"] = x ;
                            con++;              
                        });
                    }else{
                        values[this.name] = $(this).val();
                    }
                });
                var tipo = "persona";

                console.log(values);

                var guardar = Intereses.interes(values, tipo);
                guardar.done(function (response) {
                    var x = response.split("&");
                    if ($(".uuid").val() == "") {
                        $(".uuid").val(x[0]);
                    }

                    if (jQuery.inArray($(".uuid").val(), idint) == -1) {
                        idint.push($(".uuid").val());
                        toastr.success('Se ha guardado correctamente el interés ' + x[1] + '');
                    } else {
                        msg = "";
                        if (x[2] == 'Exist') {
                            msg = "Elimine el interés asegurado antes de cambiar el tipo de relación"
                        } else {
                            msg = "El interés asegurado ya se encuentra en la lista.";
                        }
                        toastr.error(msg);
                    }

                    formularioCrear.getAsociado();
                    tablaSolicitudesPersonas.recargar();

                    //Formulario se deja Limpio
                    $("#selInteres,#tipo_relacion_persona,#asociadodetalle_persona,#relaciondetalle_persona,#beneficiodetalle_persona").val('');
                    $("#selInteres,#asociadodetalle_persona").trigger('change');
                    formularioCrear.getIntereses();
                    $("#certificadoPersona, #primadetalle_persona,#validar_editar,#edad, #montodetalle_persona,#participacion_persona,#suma_asegurada_persona").val("");
                    var unico = $("#detalleunico").val();
                    var camposi = {campo: unico, persona: 'interesPersona'};
                    var obtener = Intereses.prima(camposi);
                    obtener.done(function (resp) {
                        formularioCrear.getPrimaAnual(resp);
                    });

                    if (validavida == 1 && id_tipo_poliza == 2) {
                        $("#contenedoracreedores").remove();
                        $('#contacre').after('<div id="contenedoracreedores"><div class="file_tools_acreedores_adicionales row" id="a1"><div class="col-xs-12 col-sm-6 col-md-2 col-lg-2" style="margin-right: -5px"><input type="text" name="campoacreedores[]" id="acreedor_1" class="form-control"></div><div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon">%</span> <input type="text" name="campoacreedores_por[]" id="porcentajecesion_1" class="form-control porcentaje_cesion_acreedor" value="0"></div></div> <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon">$</span> <input type="text" name="campoacreedores_mon[]" id="montocesion_1" class="form-control monto_cesion_acreedor" value="0"></div></div> <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon"><i class="fa fa-calendar"></i></span> <input type="text" name="campoacreedores_ini[]" id="fechainicio_1" class="form-control fechas_acreedores_inicio"></div></div> <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon"><i class="fa fa-calendar"></i></span> <input type="text" name="campoacreedores_fin[]" id="fechafin_1" class="form-control fechas_acreedores_fin"></div></div><div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><button type="button" class="btn btn-default btn-block add_file_acreedores_adicionales" onclick="agregaracre()" style="float: left; width: 40px; margin-right:5px;" ><i class="fa fa-plus"></i></button><button type="button" style="float: left; width: 40px; margin-top:0px!important; display:none" id="del_acre" class="btn btn-default btn-block del_file_acreedores_adicionales" onclick="eliminaracre(1)"><i class="fa fa-trash"></i></button></div><input type="hidden" name="campoacreedores_id[]" value="0"></div><div id="agrega_acre"></div></div>');
                        counter_acre2 = 2;
                        formularioCrear.acreedores = [];
                        $("#vigencia_vida_colectivo").hide();

                        inicializaCamposAcreedor();

                    }  

                });
                guardar.fail(function () {
                    toastr.error('Ha ocurrido un Error.');
                });
            } else {
                window.location.href = "#divintereses";
            }
        }else{
            toastr.error("Acreedores: La sumatoria de porcentajes de cesión y/o el monto son mayores a la suma asegurada.");
        }
    });

    $(".guardarProyecto").click(function () {
        if ($('#formProyecto_actividad').validate().form() == true) {
            var inputs = $('#formProyecto_actividad :input');
            var values = {};
            inputs.each(function () {
                values[this.name] = $(this).val();
            });
            tkn = values.erptkn;
            var tipo = "proyecto";
            var guardar = Intereses.interes(values, tipo);
            guardar.done(function (response) {
                var x = response.split("&");
                if ($(".uuid_proyecto").val() == "") {
                    $(".uuid_proyecto").val(x[0]);
                }
                if ($(".no_ordenr").val() == "") {
                    $(".no_ordenr").val(x[1]);
                }

                if (jQuery.inArray($(".uuid_proyecto").val(), idint) == -1) {
                    idint.push($(".uuid_proyecto").val());
                    toastr.success('Se ha guardado correctamente el interés ' + x[2] + '');
                } else {
                    toastr.error('El interés asegurado ya se encuentra en la lista.');
                }

                tablaSolicitudesProyecto.recargar();

                //Formulario se deja Limpio
                $("#selInteres").val('');
                $("#selInteres").trigger('change');
                formularioCrear.getIntereses();
                $("#certificadodetalle_proyecto, #sumaaseguradadetalle_proyecto, #primadetalle_proyecto, #deducibledetalle_proyecto").val("");

                //Obtiene Prima Anual con sumatoria de prima neta
                var unico = $("#detalleunico").val();
                var camposi = {campo: unico};
                var obtener = Intereses.prima(camposi);
                obtener.done(function (resp) {
                    formularioCrear.getPrimaAnual(resp);
                });
            });
            guardar.fail(function () {
                toastr.error('Ha ocurrido un Error.');
            });
        } else {
            window.location.href = "#divintereses";
        }
    });

    $(".guardarUbicacion").click(function () {
        if ($('#formUbicacion').validate().form() == true) {
            var inputs = $('#formUbicacion :input');
            var values = {};
            inputs.each(function () {
                values[this.name] = $(this).val();
            });
            tkn = values.erptkn;
            var tipo = "ubicacion";
            var guardar = Intereses.interes(values, tipo);
            guardar.done(function (response) {
                var x = response.split("&");
                if ($(".uuid_ubicacion").val() == "") {
                    $(".uuid_ubicacion").val(x[0]);
                }
                if ($(".serier").val() == "") {
                    $(".serier").val(x[1]);
                }

                if (jQuery.inArray($(".uuid_ubicacion").val(), idint) == -1) {
                    idint.push($(".uuid_ubicacion").val());
                    toastr.success('Se ha guardado correctamente el interés ' + x[2] + '');
                } else {
                    toastr.error('El interés asegurado ya se encuentra en la lista.');
                }

                tablaSolicitudesUbicacion.recargar();

                //Formulario se deja Limpio
                $("#selInteres").val('');
                $("#selInteres").trigger('change');
                formularioCrear.getIntereses();
                $("#certificadodetalle_ubicacion, #sumaaseguradadetalle_ubicacion, #primadetalle_ubicacion, #deducibledetalle_ubicacion").val("");


                var unico = $("#detalleunico").val();
                var camposi = {campo: unico};
                var obtener = Intereses.prima(camposi);
                obtener.done(function (resp) {
                    formularioCrear.getPrimaAnual(resp);
                });
            });
            guardar.fail(function () {
                toastr.error('Ha ocurrido un Error.');
            });
        } else {
            window.location.href = "#divintereses";
        }
    });

    $(".guardarVehiculo").click(function () {
        if ($('#vehiculo').validate().form() == true) {
            var inputs = $('#vehiculo :input');
            var values = {};
            inputs.each(function () {
                values[this.name] = $(this).val();
            });
            tkn = values.erptkn;
            var tipo = "vehiculo";
            var guardar = Intereses.interes(values, tipo);
            guardar.done(function (response) {
                var x = response.split("&");
                if ($("#uuid_vehiculo").val() == "") {
                    $("#uuid_vehiculo").val(x[0]);
                }

                if (jQuery.inArray($("#uuid_vehiculo").val(), idint) == -1) {
                    idint.push($("#uuid_vehiculo").val());
                    toastr.success('Se ha guardado correctamente el interés ' + x[1] + '');
                } else {
                    toastr.error('El interés asegurado ya se encuentra en la lista.');
                }

                tablaSolicitudesVehiculo.recargar();

                //Formulario se deja Limpio
                $("#selInteres").val('');
                $("#selInteres").trigger('change');
                formularioCrear.getIntereses();
                $("#certificadodetalle_vehiculo, #sumaaseguradadetalle_vehiculo, #primadetalle_vehiculo, #deducibledetalle_vehiculo").val("");


                var unico = $("#detalleunico").val();
                var camposi = {campo: unico};
                var obtener = Intereses.prima(camposi);
                obtener.done(function (resp) {
                    formularioCrear.getPrimaAnual(resp);
                });
            });
            guardar.fail(function () {
                toastr.error('Ha ocurrido un Error.');
            });
        } else {
            window.location.href = "#divintereses";
        }
    });

    var Intereses = (function () {
        return {
            interes: function (parametros, tipo) {
                if (tipo == "persona") {
                    return $.post(phost() + "intereses_asegurados/guardar", $.extend({
                        erptkn: tkn
                    }, parametros));
                } else {
                    return $.post(phost() + "intereses_asegurados/guardar_" + tipo + "", $.extend({
                        erptkn: tkn
                    }, parametros));
                }

            },
            prima: function (parametros) {
                return $.post(phost() + "intereses_asegurados/get_detalle_prima", $.extend({
                    erptkn: tkn
                }, parametros));
            }
        };
    })();

    var txtTipo = '';
    switch (id_tipo_int_asegurado) {
        case 1:
        {
            txtTipo = 'tablaSolicitudesArticulo';
            break;
        }
        case 2:
        {
            txtTipo = 'tablaSolicitudesCarga';
            break;
        }
        case 3:
        {
            txtTipo = 'tablaSolicitudesAereo';
            break;
        }
        case 4:
        {
            txtTipo = 'tablaSolicitudesMaritimo';
            break;
        }
        case 5:
        {
            txtTipo = 'tablaSolicitudesPersona';
            break;
        }
        case 6:
        {
            txtTipo = 'tablaSolicitudesProyecto';
            break;
        }
        case 7:
        {
            txtTipo = 'tablaSolicitudesUbicacion';
            break;
        }
        case 8:
        {
            txtTipo = 'tablaSolicitudesVehiculo';
            break;
        }
    }

    $("#" + txtTipo).on("click", ".viewOptions", function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        var id = $(this).attr("data-id");
        var rowINFO = $("#" + txtTipo).getRowData(id);
        var options = rowINFO["link"];
        //Init boton de opciones
        $('#opcionesModalIntereses').find('.modal-title').empty().html('Opciones: ' + rowINFO["serie"] + '');
        $('#opcionesModalIntereses').find('.modal-body').empty().html(options);
        $('#opcionesModalIntereses').find('.modal-footer').empty();
        $('#opcionesModalIntereses').modal('show');
    });


}
if(estado !='Pendiente' && estado !='En Trámite' && estado != "undefined"){
    $(".guardarsolicitud").hide();
}

getRamo();

var DetallesAcreedores = (function () {
    return {
        cargaAcreedores: function (parametros) {
            return $.post(phost() + 'solicitudes/ajax_carga_acreedores_vida_colectivo', $.extend({
                erptkn: tkn
            }, parametros));
        }
    };
})();

function inicializaCamposAcreedor(){

    $("#contenedoracreedores").remove();
    $('#contacre').after('<div id="contenedoracreedores"><div class="file_tools_acreedores_adicionales row" id="a1"><div class="col-xs-12 col-sm-6 col-md-2 col-lg-2" style="margin-right: -5px"><input type="text" name="campoacreedores[]" id="acreedor_1" class="form-control"></div><div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon">%</span> <input type="text" name="campoacreedores_por[]" id="porcentajecesion_1" class="form-control porcentaje_cesion_acreedor" value="0"></div></div> <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon">$</span> <input type="text" name="campoacreedores_mon[]" id="montocesion_1" class="form-control monto_cesion_acreedor" value="0"></div></div> <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon"><i class="fa fa-calendar"></i></span> <input type="text" name="campoacreedores_ini[]" id="fechainicio_1" class="form-control fechas_acreedores_inicio"></div></div> <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><div class="input-group"><span class="input-group-addon"><i class="fa fa-calendar"></i></span> <input type="text" name="campoacreedores_fin[]" id="fechafin_1" class="form-control fechas_acreedores_fin"></div></div><div class="col-xs-12 col-sm-6 col-md-2 col-lg-2"><button type="button" class="btn btn-default btn-block add_file_acreedores_adicionales" onclick="agregaracre()" style="float: left; width: 40px; margin-right:5px;" ><i class="fa fa-plus"></i></button><button type="button" style="float: left; width: 40px; margin-top:0px!important; display:none" id="del_acre" class="btn btn-default btn-block del_file_acreedores_adicionales" onclick="eliminaracre(1)"><i class="fa fa-trash"></i></button></div><input type="hidden" name="campoacreedores_id[]" value="0"></div><div id="agrega_acre"></div></div>');
                            
    //Inicializa los campos
    $(".monto_cesion_acreedor").inputmask('currency',{ 
        prefix: "", 
        autoUnmask : true, 
        removeMaskOnSubmit: true 
    });

    $(".porcentaje_cesion_acreedor").inputmask('Regex', { regex: "^[1-9][0-9][.][0-9][0-9]?$|^100[.]00?$|^[0-9][.][0-9][0-9]$" });
    //$(".porcentaje_cesion_acreedor").inputmask('decimal',{min:0, max:100});

    $('.fechas_acreedores_inicio').each(function () {
        console.log("veces");
        var f = $(this).val();
        if ($(this).val() == "0000-00-00") {
            var now = new Date();
            now.setDate(now.getDate());
            var dat = now.getDate();
            var mon = now.getMonth() + 1;
            var year = now.getFullYear();
            if (mon < 10) {
                mon = "0" + mon;
            }
            if (dat < 10) {
                dat = "0" + dat;
            }
            var fe = mon + "/" + dat + "/" + year;
            $(this).val(fe);
            f = "";
        }
        $(this).daterangepicker({ 
         locale: { format: 'MM/DD/YYYY' },
         showDropdowns: true,
         defaultDate: '',
         singleDatePicker: true
     }).val(f);
    });
    $('.fechas_acreedores_fin').each(function () {
        console.log("veces2");
        var f = $(this).val();
        if ($(this).val() == "0000-00-00") {
            var now = new Date();
            now.setDate(now.getDate());
            var dat = now.getDate();
            var mon = now.getMonth() + 1;
            var year = now.getFullYear();
            if (mon < 10) {
                mon = "0" + mon;
            }
            if (dat < 10) {
                dat = "0" + dat;
            }
            var fe = mon + "/" + dat + "/" + year;
            $(this).val(fe);
            f = "";
        }
        $(this).daterangepicker({ 
         locale: { format: 'MM/DD/YYYY' },
         showDropdowns: true,
         defaultDate: '',
         singleDatePicker: true
     }).val(f);
    });

    $('#fechafin_1').daterangepicker({ 
     locale: { format: 'MM/DD/YYYY' },
     showDropdowns: true,
     defaultDate: '',
     singleDatePicker: true
 }).val('');

    $(".fechas_acreedores_inicio").change(function () {
        var vigini = $("#vigencia_desde").val();
        var vigfin = $("#vigencia_hasta").val();
        var actual = $(this).val();

        var id = $(this).attr("id");
        var x = id.split('_');
        var final = $("#fechafin_"+x[1]).val();

        /*if (vigini.indexOf('/') > -1) {
            var dat = vigini.split('/');
            var dia = dat[1];
            var mes = dat[0];
            var anio = dat[2];
            vigini = anio + '-' + mes + '-' + dia ;
        }
        if (vigfin.indexOf('/') > -1) {
            var dat = vigfin.split('/');
            var dia = dat[1];
            var mes = dat[0];
            var anio = dat[2];
            vigfin = anio + '-' + mes + '-' + dia ;
        }
        if (actual.indexOf('/') > -1) {
            var dat = actual.split('/');
            var dia = dat[0];
            var mes = dat[1];
            var anio = dat[2];
            actual = anio + '-' + mes + '-' + dia ;
        }*/

        var ini = new Date(vigini);
        var fin = new Date(vigfin);
        var act = new Date(actual);
        var fefin = new Date(final);

        if (act < ini) {
            $(this).val(vigini);
        }else if(act > fin){
            $(this).val(vigfin);
        }else if(final != "" && act > fefin){
            $(this).val(final);
        }
    });

    $(".fechas_acreedores_fin").change(function () {
        var vigini = $("#vigencia_desde").val();
        var vigfin = $("#vigencia_hasta").val();
        var actual = $(this).val();

        var id = $(this).attr("id");
        var x = id.split('_');
        var inicial = $("#fechainicio_"+x[1]).val();

        /*if (vigini.indexOf('/') > -1) {
            var dat = vigini.split('/');
            var dia = dat[1];
            var mes = dat[0];
            var anio = dat[2];
            vigini = anio + '-' + mes + '-' + dia ;
        }
        if (vigfin.indexOf('/') > -1) {
            var dat = vigfin.split('/');
            var dia = dat[1];
            var mes = dat[0];
            var anio = dat[2];
            vigfin = anio + '-' + mes + '-' + dia ;
        }*/

        var ini = new Date(vigini);
        var fin = new Date(vigfin);
        var act = new Date(actual);
        var feini = new Date(inicial);

        if (act < ini) {
            $(this).val(vigini);
        }else if(act > fin){
            $(this).val(vigfin);
        }else if(inicial != "" && act < feini){
            $(this).val(inicial);
        }
    });

    $(".monto_cesion_acreedor").keyup(function(){
        var id = $(this).attr("id");
        var x = id.split('_');
        var monto = $("#montocesion_"+x[1]).val();

        if (id_tipo_poliza == 1) {
            var sumaasegurada = $("#suma_asegurada").val();
        }else if(id_tipo_poliza == 2){
            var sumaasegurada = $("#suma_asegurada_persona").val();
        }        
        if (sumaasegurada == "") { sumaasegurada = 0;}
        var porcentaje = (monto * 100 )/(sumaasegurada);
        if (porcentaje > 100) { porcentaje = 100;}
        $("#porcentajecesion_"+x[1]).val(porcentaje);
    });

    $(".porcentaje_cesion_acreedor").keyup(function(){
        var id = $(this).attr("id");
        var x = id.split('_');
        var porcentaje = $("#porcentajecesion_"+x[1]).val();
        if (porcentaje == "") { porcentaje = 0;}
        if (id_tipo_poliza == 1) {
            var sumaasegurada = $("#suma_asegurada").val();
        }else if(id_tipo_poliza == 2){
            var sumaasegurada = $("#suma_asegurada_persona").val();
        } 
        var monto = (porcentaje * sumaasegurada )/(100);
        if (porcentaje>100) {
            $("#montocesion_"+x[1]).val(sumaasegurada);
        }else{
            $("#montocesion_"+x[1]).val(monto);
        }            
    });
    //------------------------
}
if (id_tipo_poliza == 1 ) $("#espac,.documentos_entregados").hide();
