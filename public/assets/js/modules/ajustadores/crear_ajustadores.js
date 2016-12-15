$(function(){

	//$('input[name="campo[porcentaje_participacion]"]').val("0.00");

	//jQuery Validate
	$('#formAjustadores').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '',
		submitHandler: function(form) {		
			//Habilitar campos ocultos
			$('input:hidden, select:hidden, textarea').removeAttr('disabled');

			//Enviar el formulario
			form.submit();                        
		}
	});
 
	 $('input[name="campo[email]').rules(
			 "add",{ required: false, 
				 	email:true, 
				 	messages: { 
 					email:'Por favor, introduzca una dirección de email válida.' 
				 } 
			 });
	 
	 
});

$(function(){

	//$('input[name="campo[porcentaje_participacion]"]').val("0.00");

	//jQuery Validate
	$('#formulario_contacto').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '',
		submitHandler: function(form) {		
			//Habilitar campos ocultos
			$('input:hidden, select:hidden, textarea').removeAttr('disabled');

			//Enviar el formulario
			form.submit();                        
		}
	});
});

bluapp.controller("ContactosFormularioController", function($scope, $http){
    var objFrom = {
        contactosForm: $('#formulario_contacto'),
    };

    angular.element('#formulario_contacto').validate({
        submitHandler: function(form) {
            angular.element('#campo2[guardar]').attr('disabled', true);
            form.submit();
        }
    });

    $scope.inicializar = function(){
        objFrom.contactosForm.validate({
            ignore: '',
            wrapper: '',
        });
    };

    $scope.inicializar();
});