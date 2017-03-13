$(function(){

	//$('input[name="campo[porcentaje_participacion]"]').val("0.00");

	//jQuery Validate
	$('#formAseguradoraCrearContacto').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '',
		submitHandler: function(form) {		
			//Habilitar campos ocultos
			//$('input:hidden, select:hidden, textarea').removeAttr('disabled');
			$('input[type="submit"]').prop('disabled','true');
			//Enviar el formulario
			form.submit();                        
		}
	});
	
	/*$('input[name="campo[nombre]').rules(
			 "add",{ required: true, 
				 	regex:'^[a-zA-ZáéíóúñÁÉÍÓÚ ]+$',
	});*/
	
	$('input[name="campo[cargo]').rules(
			 "add",{ required: false, 
				 	rgx:'^[a-zA-Z0-9áéíóúñÁÉÍÓÚ ]+$',
	});
	 
});

$.validator.addMethod(
        "regex",
        function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Campo es alfabetico."
);

$.validator.addMethod(
        "rgx",
        function(value, element, regexp) {
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