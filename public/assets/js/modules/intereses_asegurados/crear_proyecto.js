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
            $('input[name="campo[asignado_acreedor]').rules(
            "add", {required: false,
                number: true,
                messages: {
                    number: 'Por favor, introduzca un número válido.'
                }
            });
            

});
$(document).ready(function () {
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
    
});
$('.acreedor_proyecto').on("change", function () {
     var acreedor = $('.acreedor_proyecto').val();
});
$('.tipo_fianza').on("change", function () {

    var validar_tipo_fianza = $('.tipo_fianza').val();
    if (validar_tipo_fianza === 'propuesta') {
        $('.tipo_propuesta').removeAttr('disabled');

    } else {
        $('.tipo_propuesta').attr('disabled', true);
        
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
	$('#imprimirLnk').click(function(){
		var id_proyecto=$('.uuid_proyecto').val();
		window.location.href = '../imprimirFormulario/'+id_proyecto+'?tipo=6';	
	});