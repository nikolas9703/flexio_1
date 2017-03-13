$(function () {

    //$('input[name="campo[porcentaje_participacion]"]').val("0.00");

    //jQuery Validate
    $('#formAjustadoresCrear').validate({
        focusInvalid: true,
        ignore: '',
        wrapper: '',
        submitHandler: function (form) {
            $('input[type="submit"]').prop('disabled', 'true');
            //Habilitar campos ocultos
            //$('input:hidden, select:hidden, textarea').removeAttr('disabled');

            //Enviar el formulario
            form.submit();
        }
    });

    $('input[name="campo[folio]').rules(
            "add", {required: false,
                regex: '^[a-zA-Z0-9áéíóúñ ]+$',
            });
    $('input[name="campo[tomo]').rules(
            "add", {required: false,
                regex: '^[a-zA-Z0-9áéíóúñ ]+$',
            });
    $('input[name="campo[asiento]').rules(
            "add", {required: false,
                regex: '^[a-zA-Z0-9áéíóúñ ]+$',
            });
    $('.identificacion').on("change", function () {
        var validar_identificacion = $('.identificacion').val();
        if (validar_identificacion === 'Juridico') {
            $('input[name="campo[tomo_j]').rules(
                    "add", {required: true,
                    });
            $('input[name="campo[folio]').rules(
                    "add", {required: true,
                    });
            $('input[name="campo[asiento_j]').rules(
                    "add", {required: true,
                    });
            $('input[name="campo[digverificador]').rules(
                    "add", {required: true,
                    });
            $('input[name="campo[pasaporte]').rules(
                    "add", {required: false,
                    });
            $('select[name="campo[provincia]').rules(
                    "add", {required: false,
                    });
            $('select[name="campo[letras]').rules(
                    "add", {required: false,
                    });
            $('input[name="campo[tomo]').rules(
                    "add", {required: false,
                    });
            $('input[name="campo[asiento]').rules(
                    "add", {required: false,
                    });

        } else if (validar_identificacion === 'Natural') {
            $('.letras').on("change", function () {
                var letras = $('.letras').val();
                if (letras === 'PAS') {
                    $('input[name="campo[pasaporte]').rules(
                            "add", {required: true,
                            });
                    $('select[name="campo[provincia]').rules(
                            "add", {required: false,
                            });
                    $('select[name="campo[letras]').rules(
                            "add", {required: false,
                            });
                    $('input[name="campo[tomo]').rules(
                            "add", {required: false,
                            });
                    $('input[name="campo[asiento]').rules(
                            "add", {required: false,
                            });
                    $('input[name="campo[tomo_j]').rules(
                            "add", {required: false,
                            });
                    $('input[name="campo[folio]').rules(
                            "add", {required: false,
                            });
                    $('input[name="campo[asiento_j]').rules(
                            "add", {required: false,
                            });
                    $('input[name="campo[digverificador]').rules(
                            "add", {required: false,
                            });
                } else {
                    $('input[name="campo[pasaporte]').rules(
                            "add", {required: false,
                            });
                    $('select[name="campo[provincia]').rules(
                            "add", {required: true,
                            });
                    $('select[name="campo[letras]').rules(
                            "add", {required: true,
                            });
                    $('input[name="campo[tomo]').rules(
                            "add", {required: true,
                            });
                    $('input[name="campo[asiento]').rules(
                            "add", {required: true,
                            });
                    $('input[name="campo[tomo_j]').rules(
                            "add", {required: false,
                            });
                    $('input[name="campo[folio]').rules(
                            "add", {required: false,
                            });
                    $('input[name="campo[asiento_j]').rules(
                            "add", {required: false,
                            });
                    $('input[name="campo[digverificador]').rules(
                            "add", {required: false,
                            });
                }
            });
        }
    });
//    $('input[name="campo[nombre]').rules(
//            "add", {required: true,
//                rgx: '^[a-zA-ZáéíóúñÁÉÍÓÚÑ ]+$',
//            });

});

$.validator.addMethod(
        "regex",
        function (value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Campo es alfanúmerico."
        );

$.validator.addMethod(
        "rgx",
        function (value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Campo es alfabetico."
        );
$(document).ready(function () {
    var iden = $('.identificacion').val();
    var pspt = $('.letras').val();
    if (iden === 'Juridico') {
        $('.natural').hide();
        $('.juridico').show();
    } else if (iden === 'Natural') {
        $('.juridico').hide();
        $('.natural').show();
    } else {
        $(".natural").hide();
        $(".juridico").hide();
    }
    if (pspt === 'PAS') {
        $(".provincia").prop("disabled", true);
        $(".provincia").val('');
        $('.PAS').show();
        $('.tomo').hide();
        $('.asiento').hide();
    } else {
        $(".provincia").prop("disabled", false);
        $('.tomo').show();
        $('.asiento').show();
        $('.PAS').hide();
        localStorage.setItem("letras", pspt);
    }
});
$('.identificacion').on("change", function () {
    var letra = $(this).val();
    if (letra === 'Juridico') {
        $('.natural').hide();
        $('.juridico').show();
        $(".pasaporte").val('');
        $(".tomo_n").val('');
        $(".asiento_n").val('');
        $(".pasaporte").val('');
        $(".provincia").val('');
        $(".letras").val('');
        localStorage.removeItem('tomo_n');
        localStorage.removeItem('asiento_n');
        localStorage.removeItem('pasaporte');
        localStorage.removeItem('provincia');
        localStorage.removeItem('letras');
        localStorage.setItem("identificacion", letra);
    } else if (letra === 'Natural') {
        $('.juridico').hide();
        $('.natural').show();
        $(".tomo_j").val('');
        $(".asiento_j").val('');
        $(".folio").val('');
        $(".asietno").val('');
        $(".digverificador").val('');
        localStorage.removeItem('tomo_j');
        localStorage.removeItem('asiento_j');
        localStorage.removeItem('folio');
        localStorage.removeItem('asietno');
        localStorage.removeItem('digverificador');
        localStorage.setItem("identificacion", letra);
    } else {
        $('.juridico').hide();
        $('.natural').hide();
        $(".folio").val('');
        $(".tomo_j").val('');
        $(".asiento_j").val('');
        $(".digverificador").val('');
        localStorage.removeItem('folio');
        localStorage.removeItem('tomo_j');
        localStorage.removeItem('asiento_j');
        localStorage.removeItem('digverificador');
    }
});
$('.provincia').on("change", function () {
    var letra = $(this).val();
    localStorage.setItem("provincia", letra);
});
$('.letras').on("change", function () {
    var letra = $(this).val();
    if (letra == "0" || letra == "N" || letra == "PE" || letra == 'PI' || letra == 'E') {
        $(".provincia").prop("disabled", false);
        $('.tomo').show();
        $('.asiento').show();
        $('.PAS').hide();
        $(".pasaporte").val('');
        localStorage.removeItem('pasaporte');
        localStorage.setItem("letras", letra);
    } else {
        $(".provincia").prop("disabled", true);
        $(".provincia").val('');
        $('.PAS').show();
        $('.tomo').hide();
        $('.asiento').hide();
        localStorage.setItem("letras", letra);
        $(".tomo_n").val('');
        $(".asiento_n").val('');
        localStorage.removeItem('tomo_n');
        localStorage.removeItem('asiento_n');
    }
});

$(".nombre").keyup(function (e) {
    var nombre;
    nombre = $(".nombre").val();
    localStorage.setItem("nombre", nombre);
});
$(".identificacion").keyup(function (e) {
    var identificacion;
    identificacion = $(".identificacion").val();
    localStorage.setItem("identificacion", identificacion);
});
$(".tomo_j").keyup(function (e) {
    var tomo_j;
    tomo_j = $(".tomo_j").val();
    localStorage.setItem("tomo_j", tomo_j);
});
$(".tomo_n").keyup(function (e) {
    var tomo_n;
    tomo_n = $(".tomo_n").val();
    localStorage.setItem("tomo_n", tomo_n);
});
$(".folio").keyup(function (e) {
    var folio;
    folio = $(".folio").val();
    localStorage.setItem("folio", folio);
});
$(".asiento_j").keyup(function (e) {
    var asiento_j;
    asiento_j = $(".asiento_j").val();
    localStorage.setItem("asiento_j", asiento_j);
});
$(".asiento_n").keyup(function (e) {
    var asiento_n;
    asiento_n = $(".asiento_n").val();
    localStorage.setItem("asiento_n", asiento_n);
});
$(".digverificador").keyup(function (e) {
    var digverificador;
    digverificador = $(".digverificador").val();
    localStorage.setItem("digverificador", digverificador);
});
$(".pasaporte").keyup(function (e) {
    var pasaporte;
    pasaporte = $(".pasaporte").val();
    localStorage.setItem("pasaporte", pasaporte);
});
$(".telefono").keyup(function (e) {
    var telefono;
    telefono = $(".telefono").val();
    localStorage.setItem("telefono", telefono);
});
$(".email").keyup(function (e) {
    var email;
    email = $(".email").val();
    localStorage.setItem("email", email);
});
$(".email").keyup(function (e) {
    var email;
    email = $(".email").val();
    localStorage.setItem("email", email);
});
$(".direccion").keyup(function (e) {
    var direccion;
    direccion = $(".direccion").val();
    localStorage.setItem("direccion", direccion);
});
$('.estado').on("change", function () {
    var estado = $(this).val();
    localStorage.setItem("estado", estado);
});
if (localStorage.nombre != undefined) {
    var nombre = localStorage.getItem("nombre");
    $(".nombre").val(nombre);
}
if (localStorage.identificacion != undefined) {
    var identificacion = localStorage.getItem("identificacion");
    $(".identificacion").val(identificacion);
}

if (localStorage.folio != undefined) {
    var folio = localStorage.getItem("folio");
    $(".folio").val(folio);
}
if (localStorage.digverificador != undefined) {
    var digverificador = localStorage.getItem("digverificador");
    $(".digverificador").val(digverificador);
}
if (localStorage.provincia != undefined) {
    var provincia = localStorage.getItem("provincia");
    $(".provincia").val(provincia);
}
if (localStorage.letras != undefined) {
    var letras = localStorage.getItem("letras");
    $(".letras").val(letras);
}
if (localStorage.tomo_n != undefined) {
    var tomo_n = localStorage.getItem("tomo_n");
    $(".tomo_n").val(tomo_n);
}
if (localStorage.tomo_j != undefined) {
    var tomo_j = localStorage.getItem("tomo_j");
    $(".tomo_j").val(tomo_j);
}
if (localStorage.asiento_n != undefined) {
    var asiento_n = localStorage.getItem("asiento_n");
    $(".asiento_n").val(asiento_n);
}
if (localStorage.asiento_j != undefined) {
    var asiento_j = localStorage.getItem("asiento_j");
    $(".asiento_j").val(asiento_j);
}
if (localStorage.pasaporte != undefined) {
    var pasaporte = localStorage.getItem("pasaporte");
    $(".pasaporte").val(pasaporte);
}
if (localStorage.telefono != undefined) {
    var telefono = localStorage.getItem("telefono");
    $(".telefono").val(telefono);
}
if (localStorage.email != undefined) {
    var email = localStorage.getItem("email");
    $(".email").val(email);
}
if (localStorage.direccion != undefined) {
    var direccion = localStorage.getItem("direccion");
    $(".direccion").val(direccion);
}
if (localStorage.estado != undefined) {
    var estado = localStorage.getItem("estado");
    $(".estado").val(estado);
}
$('select[name="campo[estado]"').attr('disabled', 'disabled');
$("#cancelar").click(function () {
    localStorage.removeItem('tomo_n');
    localStorage.removeItem('asiento_n');
    localStorage.removeItem('pasaporte');
    localStorage.removeItem('provincia');
    localStorage.removeItem('letras');
    localStorage.removeItem('folio');
    localStorage.removeItem('tomo_j');
    localStorage.removeItem('asiento_j');
    localStorage.removeItem('digverificador');
    localStorage.removeItem('nombre');
    localStorage.removeItem('telefono');
    localStorage.removeItem('email');
    localStorage.removeItem('direccion');
    localStorage.removeItem('estado');
    localStorage.removeItem('identificacion');
    localStorage.removeItem('letras');
});
$('#agregarContactoBtn').on("click", function () {
    var id_ajustadores = $('input[name="campo[uuid]').val();
    window.location.href = '../agregarcontacto/' + id_ajustadores + '?opt=2';
});
$("#formulariocontacto").hide();