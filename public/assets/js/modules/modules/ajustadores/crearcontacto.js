$(function () {
    //jQuery Validate
    $('#formAjustadoresCrearContacto').validate({
        focusInvalid: true,
        ignore: '',
        wrapper: '',
        submitHandler: function (form) {
            $('input[type="submit"]').prop('disabled','true');
            //Habilitar campos ocultos
            //$('input:hidden, select:hidden, textarea').removeAttr('disabled');

            //Enviar el formulario
            form.submit();
        }
    });

    $('input[name="campo[nombre]').rules(
            "add", {required: true
            });
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
 $('input[name="campo[imprimirContacto]').on("click",function() {
     var id_contacto=$('input[name="campo[uuid]').val();
	  console.log(id_contacto);
	 window.location.href = '../imprimirContacto/'+id_contacto;	
 });
 
$('#impresioncontacto').hide();