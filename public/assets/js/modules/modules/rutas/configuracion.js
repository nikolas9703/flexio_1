bluapp.controller("configRutasController", function ($scope, $http) {

    var vista = {
        formRuta: $('#crearRutasForm')
    };
    var botonModal = {
        editar: 'a.editarImpuestoBtn',
        cambiarEstado: 'a.cambiarEstadoImpuestoBtn'
    };


    $scope.guardarRuta = function (ruta) {

        $scope.ruta = angular.copy(ruta);
        var formValidado = vista.formRuta.validate();
		

        if (formValidado.form() === true) {
            //$(selfButton).unbind("click");
            var guardar = moduloRutas.guardarRutas(vista.formRuta);
            guardar.done(function (data) {
                var respuesta = $.parseJSON(data);
                if (respuesta.estado == 200) {
                    $("#mensaje_info_ruta").empty().html('<div id="success-alert" class="alert alert-' + respuesta.clase + '"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + respuesta.mensaje + '</div>');
                    $('html, body').animate({
                        scrollTop: $("#mensaje_info").offset().top
                    }, 500);
                    if(respuesta.clase != "danger"){
						tablaRutas.recargar();								
                        
						$('#nombre1_ruta').val('');
						$('#nombremensajero_ruta').val('');	
						$('#id_ruta').val('');	
						$('#provincia_ruta').val('');
						$('#distrito_ruta').val('');	
						$('#corregimiento_ruta').val('');	
						$('#boton_actualizar').hide();
						$('#boton_guardar').show();
                    }
                }
            });

	}
};
    $scope.cancelarRuta = function (e) {
		$('#nombre1_ruta').val('');
		$('#nombremensajero_ruta').val('');	
		$('#id_ruta').val('');	
		$('#provincia_ruta').val('');
		$('#distrito_ruta').val('');	
		$('#corregimiento_ruta').val('');	
		$('#has-error').hide();
		$("#mensaje_info_ruta").empty().html('');
		
		$('#boton_actualizar').hide();
		$('#boton_guardar').show();
		
		vista.formRuta.trigger('reset');
		
		var form = $('#crearRutasForm').validate({
        });
        form.resetForm();
	};

});

$('#boton_actualizar').hide();
