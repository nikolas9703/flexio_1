$(function () {
    var objFrom = {
        UbicacionForm: $('#formUbicacion'),
    };
    //jQuery Validate
    $('#formUbicacion').validate({
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
    //$('input[name="campo[nombre]').rules(
    $('#nombre_ubicacion').rules(
            "add", {required: true
            });
    //$('input[name="campo[direccion]').rules(
    $('#direccion_ubicacion').rules(
            "add", {required: true
            });

    $('input[name="campo[maquinaria]').rules(
            "add", {required: false,
                number: true,
                messages: {
                    number: 'Por favor, introduzca un número válido.'
                }
            });
    $('input[name="campo[inventario]').rules(
            "add", {required: false,
                number: true,
                messages: {
                    number: 'Por favor, introduzca un número válido.'
                }
            });
    $('input[name="campo[edif_mejoras]').rules(
            "add", {required: false,
                number: true,
                messages: {
                    number: 'Por favor, introduzca un número válido.'
                }
            });
    $('input[name="campo[edif_mejoras]').rules(
            "add", {required: false,
                number: true,
                messages: {
                    number: 'Por favor, introduzca un número válido.'
                }
            });
    $('input[name="campo[contenido]').rules(
            "add", {required: false,
                number: true,
                messages: {
                    number: 'Por favor, introduzca un número válido.'
                }
            });

    $("#edif_mejoras, #contenido, #maquinaria, #inventario").inputmask('currency',{
        prefix: "",
        autoUnmask : true,
        removeMaskOnSubmit: true
    });  

});

$(document).ready(function () {
   // $('.estado_ubicacion').attr('disabled', 'disabled');
    $("#porcentaje_acreedor_ubicacion").inputmask('integer', {min: 1, max: 100}).css("text-align", "left");
    if (vista === 'crear' && permiso_cambio_estado === 0) {
        $('.divestado').hide();
    }
    if (vista === 'editar' && permiso_editar === 0) {
        $('.guardarUbicacion').attr('disabled', true);
        $('.guardarUbicacion').hide();
        $('#nombre_ubicacion').attr('disabled', true);
        $('#direccion_ubicacion').attr('disabled', true);
        $('#edif_mejoras').attr('disabled', true);
        $('#contenido').attr('disabled', true);
        $('#maquinaria').attr('disabled', true);
        $('#inventario').attr('disabled', true);
        $('#acreedor_ubicacion').attr('disabled', true);
        $('#acreedor_ubicacion_opcional').attr('disabled', true);
        $('#porcentaje_acreedor').attr('disabled', true);
        $('#observaciones_ubicacion').attr('disabled', true);
        $('#estado_ubicacion').attr('disabled', true);

        //Verificar si tiene permisos para editar
        if (typeof permiso_editar !== 'undefined')
        {
            if (permiso_editar == '1') {
                setTimeout(function () {
                    $(".guardarUbicacion").prop('disabled', false);

                }, 1000);
            } else
            {
                $(".guardarUbicacion").hide();
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
    var counter = 2;
    $('#del_file_ubicacion').hide();
    $('#add_file_ubicacion').click(function () {

        $('#file_tools_ubicacion').before('<div class="file_upload_ubicacion row" id="fubicacion' + counter + '"><input name="nombre_documento[]" type="text" style="width: 300px!important; float: left;" class="form-control"><input name="file[]" class="form-control" style="width: 300px!important; float: left;" type="file"><br><br></div>');
        $('#del_file_ubicacion').fadeIn(0);
        counter++;
    });
    $('#del_file_ubicacion').click(function () {
        if (counter == 3) {
            $('#del_file_ubicacion').hide();
        }
        counter--;
        $('#fubicacion' + counter).remove();
    });
    var acreedor_ubicacion = $('.acreedor_ubicacion').val();
    if (acreedor_ubicacion === 'otro') {
        $('#acreedor_ubicacion_opcional').removeAttr('disabled');

    } else {
        $('#acreedor_ubicacion_opcional').attr('disabled', true);

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
//imprimir formulario de ubicacion
$('#imprimirLnk').click(function () {
    var id_ubicacion = $('.uuid_ubicacion').val();
    window.location.href = '../imprimirFormulario/' + id_ubicacion + '?tipo=7';
});
$('.acreedor_ubicacion').on("change", function () {


    var acreedor_ubicacion = $('.acreedor_ubicacion').val();
    if (acreedor_ubicacion === 'otro') {
        $('#acreedor_ubicacion_opcional').removeAttr('disabled');

    } else {
        $('#acreedor_ubicacion_opcional').attr('disabled', true);

    }
});