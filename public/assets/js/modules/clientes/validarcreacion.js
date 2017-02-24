$('#formClienteCrear').validate({
        submitHandler: function (form) {
            $.post(phost() + 'clientes/existsIdentificacion', $('#formClienteCrear').serialize(), function(data){
                console.log(data);
                var respuesta = $.parseJSON(data);
                if(respuesta.existe){
                    toastr.error("Este numero de Identificación ya existe para esta empresa.");
                }else{                    
                    form.submit();
                }                
            });
            
        }

    });

$(document).ready(function(){
	if(interes==='si')
	{
		$('select[name="campo[tipo_identificacion]').trigger('change');
		$('select[name="campo[tipo_identificacion]"]').attr('disabled', 'disabled');
		
		$('input[name="campo[nombre]"]').val(datos_interes.nombrePersona);
		$('input[name="campo[nombre]"]').attr('disabled', 'disabled');
		
		$('select[name="campo[toma_contacto_id]"]').val(6);
		$('select[name="campo[toma_contacto_id]"]').trigger('change');
		$('select[name="campo[toma_contacto_id]"]').attr('disabled', 'disabled');
		
		if(datos_interes.telefono_residencial!='' && datos_interes.telefono_oficina!='')
		{
			$('#boton_telefono').trigger('click');
			
			$('input[name="telefonos[0][telefono]"]').val(datos_interes.telefono_residencial);
			$('select[name="telefonos[0][tipo]"]').val('residencial');
			$('select[name="telefonos[0][tipo]"]').trigger('change');
			
			$('input[name="telefonos[1][telefono]"]').val(datos_interes.telefono_oficina);
			$('select[name="telefonos[1][tipo]"]').val('trabajo');
			$('select[name="telefonos[1][tipo]"]').trigger('change');
			
		}
		
		
		$('input[name="correos[0][correo]"]').val(datos_interes.correo);
		
		//$('#boton_telefono').trigger('click');
		
		
		/*$('#nombrePersona').attr('disabled', 'disabled');
		$('#formulario').attr('disabled', 'disabled');
		$('#telefono_residencial').val(telefono_cliente_residencial);
		$('#telefono_oficina').val(telefono_cliente_oficina);
		$('#correoPersona').val(correo_cliente);
		$('#direccion').val(direccion_residencial_cliente);
		$('#direccion_laboral').val(direccion_laboral_cliente);*/
		
		if(tipo_identificacion==='pasaporte')
		{
			$('select[name="campo[tipo_identificacion]"]').val('pasaporte');
			$("body").on("change", "#formClienteCrear", function () {
				$('input[name="campo[detalle_identificacion][pasaporte]"]').val(pasaporte);
				$('input[name="campo[detalle_identificacion][pasaporte]"]').attr('disabled', 'disabled');
			});
			
			
			
			
		}
		/*else
		{
			$('#provincia').trigger('click');
			
			$('#identificacion').val('cedula');
			$('#provincia').val(datos_cliente.detalle_identificacion.provincia);
			$('#letra').val(datos_cliente.detalle_identificacion.letra);
			$('#tomo').val(datos_cliente.detalle_identificacion.tomo);
			$('#asiento').val(datos_cliente.detalle_identificacion.asiento);
			
			$('#identificacion').attr('disabled', 'disabled');
			$('#provincia').attr('disabled', 'disabled');
			$('.provincia').attr('disabled', 'disabled');
			$('#letra').attr('disabled', 'disabled');
			$('#tomo').attr('disabled', 'disabled');
			$('#asiento').attr('disabled', 'disabled');
		}*/
		$('select[name="campo[tipo_identificacion]').trigger('change');
	}
});