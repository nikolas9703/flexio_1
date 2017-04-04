var recibosCrear = {
    settings: {
        formulario: $('#recibosForm')
    },
    botones: {
        guardar: $('button.guardarFormBoton'), cancelar: $('button.cancelarFormBoton')
    },
    init: function () {
        this.inicializar_plugin();
        this.actualizar_chosen();
        this.eventos();
    }, inicializar_plugin: function () {

        this.settings.formulario.find('#transaccionesTable .chosen-select').each(function (index, element) {
            $('#' + this.id + ' option:first').val("");
        });

        this.settings.formulario.find('div.ibox').each(function (index, element) {
            var div = $(this).children();
            /*  if(index === 0){
             $(div[1]).css('display','none');
             }else if(index === 1){
             $(div[1]).css('display','block');
             } */
        });

        $("table#transaccionesTable").find('input.credito').each(function (index, element) {
            if (this.id.match(/debito/)) {
                $('#' + this.id).blur(function () {
                    if ($(this).val().length > 0) {
                        $("input.credito").attr('readonly', true);
                    } else if ($(this).val().length === 0) {
                        $("input.credito").attr('readonly', false);
                    }
                });

            }
            if (this.id.match(/credito/)) {

                $('#' + this.id).blur(function () {

                    $("input.debito").attr('readonly', true);

                });

            }

        });

        this.settings.formulario.find('.agregarTransaccionesBtn').tablaDinamica({

            afterAddRow: function (row) {
                $(row).find('input').inputmask();
                recibosCrear.actualizar_chosen();
                //$('input.debito').bind('input',recibosCrear.calcular_total_debito);
                $('input.credito').bind('input', recibosCrear.calcular_total_credito);
                if ($('.chekbox-incluir').is(':checked')) {
                    var nombre = $('#recibosForm').find('input[id*="campo[nombre]"]').val();
                    if (nombre !== "") {
                        $.each($('#recibosForm').find('#transaccionesTable input[id*="nombre"]'), function (i, campo) {
                            $(this).val(nombre);
                        });
                    }
                } else {
                    $.each($('#recibosForm').find('#transaccionesTable input[id*="nombre"]'), function (i, campo) {
                        //$(this).val("");
                    });
                }
                $(row).find('input.credito').each(function (index, element) {
                    $("#" + this.id).attr('readonly', false);


                });
            },
            afterDeleteRow: function (row) {

                recibosCrear.calcular_total_credito();
            }

        });

        $.validator.prototype.elements = function () {
            var validator = this,
                rulesCache = {};

            return $(this.currentForm)
                .find("input, select, textarea")
                .not(":submit, :reset, :image, [disabled]") // changed from: .not( ":submit, :reset, :image, [disabled], [readonly]" )
                .not(this.settings.ignore)
                .filter(function () {
                    if (!this.name && validator.settings.debug && window.console) {
                        console.error("%o has no name assigned", this);
                    }

                    if (this.name in rulesCache || !validator.objectLength($(this).rules())) {
                        return false;
                    }

                    rulesCache[this.name] = true;
                    return true;
                });
        };
        //Inicializar Validate en Formularios
        this.settings.formulario.validate({
            //focusInvalid: true,
            ignore: ':hidden:not(select)',
            wrapper: '',
            errorPlacement: function (error, element) {
                var clipro = $('#cliente_proveedor').val();
                if ($('#transaccionesTable').find('input[id*="nombre"]').length > 0 || $('#transaccionesTable').find('input[id*="cuenta_id"]').length > 0 || $('#transaccionesTable').find('input[id*="centro_id"]').length > 0 || element.attr("id") == "totalCredito") {
                    //error.appendTo( $("tfoot td:nth-child(1)") );
                    $("tfoot td:nth-child(1)").html(error);
                }

                else {
                    error.insertAfter(element);
                }
            }
        });

        /**
         * Init Bootstrap Calendar Plugin
         */
        this.settings.formulario.find('.fecha-tarea').daterangepicker({
            singleDatePicker: true,
            format: 'DD-MM-YYYY',
            showDropdowns: true,
            opens: "left",
            locale: {
                applyLabel: 'Seleccionar',
                cancelLabel: 'Cancelar',
                daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                firstDay: 1
            }
        });

        // $('input.debito').bind('input',recibosCrear.calcular_total_debito);
        $('input.credito').bind('input', recibosCrear.calcular_total_credito);
        recibosCrear.calcular_total_debito();
        recibosCrear.calcular_total_credito();
    },
    //Actualizar campos chosen
    actualizar_chosen: function () {
        this.settings.formulario.find('.chosen-select').chosen({
            width: '100%',
        }).trigger('chosen:updated').on('chosen:showing_dropdown', function (evt, params) {
            $(this).closest('div.table-responsive').css("overflow", "visible");
        }).on('chosen:hiding_dropdown', function (evt, params) {
            $(this).closest('div.table-responsive').css({'overflow-x': 'auto !important'});
        });
    },
    //Esta funcion calcula la columna "Monto de debito" de la tabla
    calcular_total_debito: function () {
        var total_monto = 0;


        $('#recibosForm').find('#totalDebito').prop("value", roundNumber(total_monto, 2));
    },
    calcular_total_credito: function () {
        var total_monto = 0;

        $.each($('#recibosForm').find('#transaccionesTable input[id*="credito"]'), function (i, campo) {
            if (this.value !== "" && isNumber(parseFloat(this.value))) {
                total_monto += parseFloat(this.value);
            }
        });
        $('#recibosForm').find('#totalCredito').prop("value", roundNumber(total_monto, 2));
    },
    eventos: function () {
        $('.chekbox-incluir').on('change', function () {
            if ($(this).is(':checked')) {
                var nombre = $('#recibosForm').find('input[id*="campo[nombre]"]').val();
                if (nombre !== "") {
                    $.each($('#recibosForm').find('#transaccionesTable input[id*="nombre"]'), function (i, campo) {
                        $(this).val(nombre);
                    });
                }
            } else {
                $.each($('#recibosForm').find('#transaccionesTable input[id*="nombre"]'), function (i, campo) {
                    $(this).val("");
                });
            }
        });
        this.botones.guardar.click(function (event) {
            event.preventDefault();
            var selfButton = this;

            if (recibosCrear.settings.formulario.valid() === true) {
                $(selfButton).unbind("click");
                $(selfButton).bind("click");
                var guardar = moduloEntradaManual.guardarEntradaManual(recibosCrear.settings.formulario);
                guardar.done(function (data) {
                    var respuesta = $.parseJSON(data);
                    if (respuesta.estado == 200) {
                        $("#mensaje_info").empty().html('<div id="success-alert" class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + respuesta.mensaje + '</div>');
                        window.location = respuesta.redireccionar;
                    }
                });

            }

        });
        this.botones.cancelar.click(function (event) {
            event.preventDefault();
            window.history.back();
        });
    }
};

$(document).ready(function () {
    $('#cancelarFormBoton').parent().parent().prepend('<div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>');
    $('#totalCredito').attr('name', 'total_credito');
    $('#totalCredito').attr('data-rule-required', "true");
    $('#transaccionesTable .chosen-select').attr('data-rule-required', "true");

    $('.guardar1').attr('disabled', 'disabled').click(function(){
        if($("#cliente_proveedor").val()==null || $("#cliente_proveedor").val()=="" ) {
            toastr.error("Debe seleccionar un Cliente/Proveedor");
            return false;
        }
    });



    $('#cliente_proveedor').empty().removeClass("chosen-filtro").addClass("form-control").select2({
        ajax: {
            url: phost() + "movimiento_monetario/ajax-cliente-proveedor",
            method: 'POST',
            dataType: 'json',
            delay: 200,
            cache: true,
            data: function (params) {

               if(typeof id_cliente_proveedor !="undefined")
                   return [{id:id_cliente_proveedor, text:selected_cliente_proveedor}];


                return {
                    cliente_proveedor: $('#categoria').val(),
                    q: params.term, // search term
                    page: params.page,
                    limit: 10,
                    erptkn: window.tkn
                };
            },
            processResults: function (data, params) {


                let resultsReturn = data.map(resp=> [{
                    'id': resp['id'],
                    'text': resp['nombre']
                }]).reduce((a, b) => a.concat(b), []);

                return {results: resultsReturn};
            },
            escapeMarkup: function (markup) {
                return markup;
            },
        }
    }).on("change", function () {
        $('.id_cliente_proveedor').val($(this).val());
        $('.guardar1').removeAttr('disabled', "disabled");

        $.post(phost() + "movimiento_monetario/ajax-cuenta-contable", {cliente_aseguradora: $('#categoria').val(), erptkn: window.tkn}, function(response){
            if(response.length > 0 ){
                $('#cuenta_id0').val(response[0].cuenta_id).trigger("chosen:updated");  
                $('#cuenta_id0').prop('disabled', true).trigger("chosen:updated");
            }
        });
       
    });
    //Inicializar Chosen plugin
    if ($().chosen) {
        if ($(".chosen-filtro").attr("class") != undefined) {
            $(".chosen-filtro").chosen({
                width: '100%',
                placeholder: "Seleccione",
                disable_search: true,
                inherit_select_classes: true
            });
        }
    }
    //Inicio select cliente o proveedor
    //Inicio select cliente o proveedor
    $("#categoria").change(function () {

        var cliente_proveedor = $('#categoria').val();
        $('.id_categoria').empty();
        $('.id_categoria').val(cliente_proveedor);

        $('.guardar1').attr('disabled', "disabled");
        if(typeof id_cliente_proveedor =="undefined")
            $('#cliente_proveedor').val("").trigger("change");
    });

    if(typeof id_cliente_proveedor !="undefined")
        setTimeout(function(){
            $('#categoria').val(cliente_proveedor).trigger("chosen:updated");
            $('#cliente_proveedor').val(id_cliente_proveedor).trigger("change");
        }, 1000);
    recibosCrear.init();

// Popular datos de formulario creado


//dar valor al checkbox
    $('.chekbox-incluir').val("0");

    setTimeout(function () {

        if (typeof cliente_proveedor != 'undefined') {

            //Seleccionar categoria
            //	$('#categoria').prop('disabled', 'disabled').find('option[value=' + cliente_proveedor + ']').attr('selected', 'selected');
            $('#categoria').trigger("chosen:updated");

            // $('#cancelarFormBoton').css('display', 'none');
            if (typeof retiros_id != 'undefined') {
                $('.guardar1').css('display', 'none');
            }
            $('.retiros_id').val(typeof retiros_id != 'undefined' ? retiros_id : "");


        }

        if (typeof selected_cliente_proveedor !== 'undefined') {
            //Seleccionar Departamento
            $('#cliente_proveedor').append("<option value='" + id_cliente_proveedor + "' selected>" + selected_cliente_proveedor + "</option>").prop('disabled', 'disabled');
            $('#cliente_proveedor').trigger("chosen:updated");
        }


        if (typeof narracion !== 'undefined') {

            //Seleccionar Departamento
            $('.narracion').val(narracion).prop('disabled', 'disabled');
            // $('.colaborador_id').val(colaborador_id);
        }

        //tipo recibir cuenta de banco

        if (typeof cuenta_id !== 'undefined') {

            //Seleccionar Departamento
            $('#cuenta_banco').prop('disabled', 'disabled').find('option[value=' + cuenta_id + ']').attr('selected', 'selected');
            // $('.colaborador_id').val(colaborador_id);
            $('#cuenta_banco').trigger("chosen:updated");


        }


        if (typeof incluir_narracion !== 'undefined') {

            if (incluir_narracion == '1') {
                //Seleccionar Departamento
                $('.chekbox-incluir').prop('checked', true).attr('disabled', true);
                // $('.colaborador_id').val(colaborador_id);
            } else {

                $('.chekbox-incluir').prop('checked', false).attr('disabled', true);

            }
        }

        if (typeof transacciones !== 'undefined') {

            $('#transacciones0').css('display', 'none');
            $.each($.parseJSON(cuentas_banco), function () {
                //   console.log(options_text);
                var t1 = '<tr id="1"><td><input type="text" name="transacciones[1][nombre]" value="' + this.nombre + '" class="form-control" disabled="disabled"></td><td class="cuenta_id"><select class="form-control" name="transacciones[1][cuenta_id]" id="cuenta_id1" disabled><option value="' + this.cuentas.id + '">' + this.cuentas.nombre + '</option></select></td><td><select name="transacciones[1][centro_id]" id="centro_id1" class="form-control" disabled><option value="' + this.centros.id + '">' + this.centros.nombre + '</option></select></td><td><div class="input-group"><span class="input-group-addon">$</span><input type="text" name="transacciones[0][credito]" value="' + this.credito + '" class="form-control credito" data-table-footer-sum-column="1" id="credito0" agrupador="transacciones" readonly></div></td>';
                $('#transaccionesTable').append(t1);

                $('#totalCredito').val(credito_total);
            });

            /*  var credito = $('#credito0').val();
             console.log(credito); */

        }

        //Transacciones
        /*    var t1 = '<tr id="1"><td><input type="text" name="transacciones[1][nombre]" value="" class="form-control" data-rule-required="true" id="nombre1" aria-required="true"></td><td class="cuenta_id"><select name="transacciones[1][cuenta_id]" id="cuenta_id1"></select></td><td><select name="transacciones[1][centro_id]" id="centro_id1"></select></td><td><div class="input-group"><span class="input-group-addon">$</span><input type="text" name="transacciones[0][credito]" value="45.00" class="form-control credito" data-table-footer-sum-column="1" id="credito0" agrupador="transacciones"></div></td>';

         $("#transaccionesTable").append(t1);   */


        if (typeof acreedor_id !== 'undefined') {

            //Seleccionar Departamento
            $('#acreedor_id').find('option[value=' + acreedor_id + ']').attr('selected', 'selected');
            // $('.colaborador_id').val(colaborador_id);
            $('#acreedor_id').trigger("chosen:updated");


        }

        //fecha inicio

        if (typeof fecha_inicio !== 'undefined') {

            //Seleccionar Departamento
            $('.fecha-inicio').val(fecha_inicio);
            // $('.colaborador_id').val(colaborador_id);
        }

        //cuenta pasivo

        if (typeof plan_contable_id !== 'undefined') {

            //Seleccionar Departamento
            $('#cuenta_pasivo').find('option[value=' + plan_contable_id + ']').attr('selected', 'selected');
            // $('.colaborador_id').val(colaborador_id);
            $('#cuenta_pasivo').trigger("chosen:updated");


        }


        //Numero referencia

        if (typeof no_referencia !== 'undefined') {

            //Seleccionar Departamento
            $('.no_referencia').val(no_referencia);
            // $('.colaborador_id').val(colaborador_id);
        }


        //monto total

        if (typeof monto_adeudado !== 'undefined') {
            //alert(monto_adeudado);
            //Seleccionar Departamento
            $('.monto_adeudado').removeAttr('data-inputmask').val(monto_adeudado);
            $('.monto_adeudado').attr('data-inputmask', '9{1,20}[.*{2}]');

            // $('.monto_adeudado').mask('9{1,20}[.*{2}]').val(monto_adeudado);
        }


        //monto ciclo

        if (typeof monto_ciclo !== 'undefined') {
            //alert(monto_ciclo);
            //Seleccionar Departamento
            $('.monto_ciclo').removeAttr('data-inputmask').val(monto_ciclo);
            $('.monto_ciclo').attr('data-inputmask', '9{1,20}[.*{2}]');
            // $('.colaborador_id').val(colaborador_id);
        }


        if (typeof porcentaje_capacidad !== 'undefined') {

            //Seleccionar Departamento
            $('.capacidad_respuesta').val(porcentaje_capacidad);
            // $('.colaborador_id').val(colaborador_id);
        }


        //estado

        if (typeof estado_id !== 'undefined') {

            //Seleccionar Departamento
            $('#estado_id').find('option[value=' + estado_id + ']').attr('selected', 'selected');
            // $('.colaborador_id').val(colaborador_id);
            $('#estado_id').trigger("chosen:updated");


        }

        //monto ciclo

        if (typeof detalle !== 'undefined') {

            //Seleccionar Departamento
            $('textarea[type="textarea"]').val(detalle);
            // $('.colaborador_id').val(colaborador_id);
        }

        //descuenta diciembre

        if (typeof descuento_diciembre !== 'undefined') {

            if (descuento_diciembre == '1') {
                //Seleccionar Departamento
                $('.descuenta_dic').prop('checked', true);
                // $('.colaborador_id').val(colaborador_id);
            } else {

                $('.descuenta_dic').prop('checked', false);

            }
        }

        //carta descuento

        if (typeof carta_descuento !== 'undefined') {

            if (carta_descuento == '1') {
                //Seleccionar Departamento
                $('.carta_descuento').prop('checked', true);
                // $('.colaborador_id').val(colaborador_id);
            } else {

                $('.carta_descuento').prop('checked', false);

            }
        }


    }, 800);


})
;





$('.chekbox-incluir').change(function () {
    if ($(this).is(':checked')) {
        $('.chekbox-incluir').val("1");
    } else {
        $('.chekbox-incluir').val("0");
    }
});


$('#permisosForm').append('<input type="hidden" name="campo[id_categoria]" class="id_categoria" style="display:none;" value="">');
$('#permisosForm').append('<input type="hidden" name="campo[id_cliente_proveedor]" class="id_cliente_proveedor" style="display:none;" value="">');

if(localStorage.getItem('ms-selected') == 'seguros'){


    $('div .filtro-formularios').find('select').each(function(){
        if($(this).attr('id') == "categoria"){
            console.log($(this).attr('id'));
            $(this).empty().append('<option value="">Seleccione</option><option value="2">Cliente</option><option value="3">Aseguradora</option>');/*.append('Cliente/Aseguradora'); */
        }
    });  

    if(vista == 'editar'){

        $('#categoria').prop('disabled','disabled').trigger("chosen:updated");
    }else if(vista == 'crear'){
        $('.guardar1').on('click',function(){
            $('#cuenta_id0').prop('disabled', false).trigger("chosen:updated");;
            $('#permisosForm').submit();
        })
    }

}else{
    $('.breadcrumb').empty();
}