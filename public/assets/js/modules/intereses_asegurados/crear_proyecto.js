$(function () {
    var objFrom = {
        proyecto_actividadForm: $('#formProyecto_actividad'),
    };
    //jQuery Validate
    $('#formProyecto_actividad').validate({
        focusInvalid: true,
        ignore: '',
        wrapper: '',
        submitHandler: function (form) {
            //Habilitar campos ocultos
            $('input:hidden, select:hidden, textarea').removeAttr('disabled');
            $('input[type="submit"]').prop('disabled', 'true');
            //Enviar el formulario
            form.submit();
        }
    });

    $('input[name="campo[nombre_proyecto]').rules(
            "add", {required: true
            });
    $('input[name="campo[no_orden]').rules(
            "add", {required: true,
                rgx: '^[a-zA-Z0-9áéíóúñÁÉÍÓÚ ]+$'
            });
    $('input[name="campo[contratista]').rules(
            "add", {required: false,
                rgx: '^[a-zA-Z0-9áéíóúñÁÉÍÓÚ ]+$'
            });
    $('input[name="campo[representante_legal]').rules(
            "add", {required: false,
                rgx: '^[a-zA-Z0-9áéíóúñÁÉÍÓÚ ]+$'
            });
    $('input[name="campo[duracion]').rules(
            "add", {required: false,
                rgx: '^[a-zA-Z0-9áéíóúñÁÉÍÓÚ ]+$'
            });
    $('input[name="campo[monto_afianzado]').rules(
            "add", {required: false,
                number: true,
                messages: {
                    number: 'Por favor, introduzca un número válido.'
                }
            });

    $(".monto_proyecto").inputmask('currency',{
        prefix: "",
        autoUnmask : true,
        removeMaskOnSubmit: true
    });  


});
$(document).ready(function () {
    //$('.estado_proyecto').attr('disabled', 'disabled');
    $("#monto_afianzado").inputmask('integer', {min: 1, max: 100}).css("text-align", "left");
    $("#asignado_acreedor").inputmask('integer', {min: 1, max: 100}).css("text-align", "left");
    var validar_tipo_fianza = $('.tipo_fianza').val();
    var validez_fianza_pr = $('.validez_fianza_pr').val();
    if (validar_tipo_fianza === 'propuesta') {
        $('.tipo_propuesta').removeAttr('disabled');
    }
    if (validez_fianza_pr === 'otro') {
        $('.validez_fianza_opcional').removeAttr('disabled');
    }
    var tipo_propuesta = $('.tipo_propuesta').val();
    if (tipo_propuesta === 'otro') {
        $('.tipo_propuesta_opcional').removeAttr('disabled');

    }
     var acreedor_proyecto = $('.acreedor_proyecto').val();
    if (acreedor_proyecto === 'otro') {
        $('.acreedor_opcional_proyecto').removeAttr('disabled');

    }
    var counter = 2;
    $('#del_file_proyecto').hide();
    $('#add_file_proyecto').click(function () {

        $('#file_tools_proyecto').before('<div class="file_upload_proyecto row" id="fproyecto' + counter + '"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"><br><br></div>');
        $('#del_file_proyecto').fadeIn(0);
        counter++;
    });
    $('#del_file_proyecto').click(function () {
        if (counter == 3) {
            $('#del_file_proyecto').hide();
        }
        counter--;
        $('#fproyecto' + counter).remove();
    });


    if (vista === 'crear' && permiso_cambio_estado === 0) {
        $('.divestado_proyecto').hide();
    }
    if (vista === 'editar' && permiso_editar === 0) {
        $('.guardarProyecto').attr('disabled', true);
        $('.guardarProyecto').hide();
        $('#nombre_proyecto').attr('disabled', true);
        $('#contratista_proyecto').attr('disabled', true);
        $('#representante_legal_proyecto').attr('disabled', true);
        $('#fecha_concurso').attr('disabled', true);
        $('#no_orden_proyecto').attr('disabled', true);
        $('#duracion_proyecto').attr('disabled', true);
        $('#fecha').attr('disabled', true);
        $('#tipo_fianza').attr('disabled', true);
        $('#monto').attr('disabled', true);
        $('#monto_afianzado').attr('disabled', true);
        $('#asignado_acreedor').attr('disabled', true);
        $('#ubicacion_proyecto').attr('disabled', true);
        $('#acreedor_pr').attr('disabled', true);
        $('#acreedor_opcional').attr('disabled', true);
        $('#validez_fianza_pr').attr('disabled', true);
        $('#validez_fianza_opcional').attr('disabled', true);
        $('#observaciones_proyecto').attr('disabled', true);
        $('#estado_proyecto').attr('disabled', true);

        //Verificar si tiene permisos para editar
        if (typeof permiso_editar !== 'undefined')
        {
            if (permiso_editar == '1') {
                setTimeout(function () {
                    $(".guardarProyecto").prop('disabled', false);

                }, 1000);
            } else
            {
                $(".guardarProyecto").hide();
            }
        }
    }
    //Documentos Modal
    $('#subirDocumentoLnk').click(function (e) {
        e.preventDefault();
        e.returnValue = false;
        e.stopPropagation();

        //Inicializar opciones del Modal
        $('#documentosModal').modal({
            backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
            show: false
        });

        $('#documentosModal').modal('show');
    });

});
$('.acreedor_proyecto').on("change", function () {
    var acreedor_proyecto = $('.acreedor_proyecto').val();
    if (acreedor_proyecto === 'otro') {
        $('.acreedor_opcional_proyecto').removeAttr('disabled');

    } else {
        $('.acreedor_opcional_proyecto').attr('disabled', true);
         $(".acreedor_opcional_proyecto").val('');

    }
});
$('.tipo_fianza').on("change", function () {

    var validar_tipo_fianza = $('.tipo_fianza').val();
    if (validar_tipo_fianza === 'propuesta') {
        $('.tipo_propuesta').removeAttr('disabled');

    } else {
        $('.tipo_propuesta').attr('disabled', true);
        $('.tipo_propuesta_opcional').attr('disabled', true);
        $(".tipo_propuesta").val('');
        $(".tipo_propuesta_opcional").val('');

    }
});
$('.tipo_propuesta').on("change", function () {

    var tipo_propuesta = $('.tipo_propuesta').val();
    if (tipo_propuesta === 'otro') {
        $('.tipo_propuesta_opcional').removeAttr('disabled');

    } else {
        $('.tipo_propuesta_opcional').attr('disabled', true);
        $(".tipo_propuesta_opcional").val('');

    }
});
$('.validez_fianza_pr').on("change", function () {

    var validez_fianza_pr = $('.validez_fianza_pr').val();
    if (validez_fianza_pr === 'otro') {
        $('.validez_fianza_opcional').removeAttr('disabled');

    } else {
        $('.validez_fianza_opcional').attr('disabled', true);
        $(".validez_fianza_opcional").val('');
    }
});
$.validator.addMethod(
        "regex",
        function (value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Campo es alfabetico."
        );

$.validator.addMethod(
        "rgx",
        function (value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Campo es alfanumerico."
        );
//imprimir formulario de proyecto_actividad
$('#imprimirLnk').click(function () {
    var id_proyecto = $('.uuid_proyecto').val();
    window.location.href = '../imprimirFormulario/' + id_proyecto + '?tipo=6';
});