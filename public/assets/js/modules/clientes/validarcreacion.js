$('#formClienteCrear').validate({
        submitHandler: function (form) {
			$('input, select, input:text').prop('disabled', false);
			$('input:text,input:hidden, select:hidden, textarea, select').removeAttr('disabled');
			$('input,input:text,input:hidden, select:hidden, textarea, select').attr('disabled',false);
            $.post(phost() + 'clientes/existsIdentificacion', $('#formClienteCrear').serialize(), function(data){
                console.log(data);
                var respuesta = $.parseJSON(data);
                if(respuesta.existe){
					if(interes==='si')
					{
						$('select[name="campo[tipo_identificacion]"]').attr('disabled', 'disabled');
						$('input[name="campo[nombre]"]').attr('disabled', 'disabled');
						$('select[name="campo[toma_contacto_id]"]').attr('disabled', 'disabled');
						
						if(tipo_identificacion==='pasaporte')
						{
							$('input[name="campo[detalle_identificacion][pasaporte]"]').attr('disabled', 'disabled');
						}
						else
						{
							$('select[name="campo[detalle_identificacion][provincia]"]').attr('disabled', 'disabled');
							$('input[name="campo[detalle_identificacion][tomo]"]').attr('disabled', 'disabled');
							$('input[name="campo[detalle_identificacion][asiento]"]').attr('disabled', 'disabled');
						}
					}
                    toastr.error("Este numero de Identificación ya existe para esta empresa.");
					
                }else{  
					console.log('por aqui pasa');

					$(".iniramo").each(function(){
                        var id = $(this).attr("id");
                        var idx = id.split("_");
                        var valor = $(this).val();
                        var valorf = "";
                        $.each(valor,function(index, value){
                            valorf = valorf+value+",";
                        });
                        $("#ramos_h_"+idx[1]+"_"+idx[2]).val(valorf);
                        console.log(id, valorf);
                    });

					$('input, select, input:text').prop('disabled', false);
					$('input:text,input:hidden, select:hidden, textarea, select').removeAttr('disabled');
					$('input,input:text,input:hidden, select:hidden, textarea, select').attr('disabled',false);
                    form.submit();
                }                
            });
            
        }

    });
	
function Loaded(){
	$('select[name="campo[detalle_identificacion][letra]"]').val(letraVal);
	$('select[name="campo[detalle_identificacion][letra]"]').trigger('change');
	$('select[name="campo[detalle_identificacion][letra]"]').attr('disabled', 'disabled');
}

$(window).load(function(){
	if(interes==='si')
	{
		if(tipo_identificacion==='pasaporte')
		{
			$('input[name="campo[detalle_identificacion][pasaporte]"]').val(pasaporte);
			$('input[name="campo[detalle_identificacion][pasaporte]"]').attr('disabled', 'disabled');
		}
		else
		{
			$('select[name="campo[detalle_identificacion][provincia]"]').val(provinciaVal);
			$('select[name="campo[detalle_identificacion][provincia]"]').trigger('change');
			$('select[name="campo[detalle_identificacion][provincia]"]').attr('disabled', 'disabled');
			
			setInterval("Loaded()", 4000);
			
			$('input[name="campo[detalle_identificacion][tomo]"]').val(tomo);
			$('input[name="campo[detalle_identificacion][tomo]"]').attr('disabled', 'disabled');
			
			$('input[name="campo[detalle_identificacion][asiento]"]').val(asiento);
			$('input[name="campo[detalle_identificacion][asiento]"]').attr('disabled', 'disabled');
		}
		if(datos_interes.telefono_oficina!='')
		{
			$('input[name="telefonos[1][telefono]"]').val(datos_interes.telefono_oficina);
			$('select[name="telefonos[1][tipo]"]').val('trabajo');
			$('select[name="telefonos[1][tipo]"]').trigger('change');
		}
		
		if(datos_interes.direccion_laboral!='')
		{
			$('input[name="centro_facturacion[1][nombre]"]').val('Laboral');
			
			/*$('select[name="centro_facturacion[1][provincia_id]"]').attr('disabled', 'disabled');
			$('select[name="centro_facturacion[1][distrito_id]"]').attr('disabled', 'disabled');
			$('select[name="centro_facturacion[1][corregimiento_id]"]').attr('disabled', 'disabled');*/
			
			$('input[name="centro_facturacion[1][direccion]"]').val(datos_interes.direccion_laboral);
		}
	}
	//Quitar las opciones del select cuando estoy desde el controlador seguros
	var moduloInicial = localStorage.getItem("ms-selected");
	
	if(moduloInicial=='seguros')
	{
		$('select[name="campo[tipo_identificacion]"] option[value="cedula_nt"]').remove();
		$('select[name="campo[tipo_identificacion]"] option[value="ruc_nt"]').remove();
	}
	else 
	{
		$('select[name="campo[tipo_identificacion]"] option[value="pasaporte"]').remove();	
	}
	
});




$(document).ready(function(){
	if(interes==='si')
	{
		$('select[name="campo[tipo_identificacion]"]').attr('disabled', 'disabled');
		
		$('input[name="campo[nombre]"]').val(datos_interes.nombrePersona);
		$('input[name="campo[nombre]"]').attr('disabled', 'disabled');
		
		$('select[name="campo[toma_contacto_id]"]').val(6);
		$('select[name="campo[toma_contacto_id]"]').trigger('change');
		$('select[name="campo[toma_contacto_id]"]').attr('disabled', 'disabled');
		
		if(tipo_identificacion==='pasaporte')
		{
			$('select[name="campo[tipo_identificacion]"]').val('pasaporte');
			$('select[name="campo[tipo_identificacion]').trigger('change');
		}
		else
		{
			$('select[name="campo[tipo_identificacion]"]').val('cedula');
			$('select[name="campo[tipo_identificacion]').trigger('change');
		}
		
		if(datos_interes.telefono_residencial!='' && datos_interes.telefono_oficina!='')
		{
			$('#boton_telefono').trigger('click');
			
			$('input[name="telefonos[0][telefono]"]').val(datos_interes.telefono_residencial);
			$('select[name="telefonos[0][tipo]"]').val('residencial');
			$('select[name="telefonos[0][tipo]"]').trigger('change');
		}
		else if(datos_interes.telefono_residencial!='' && datos_interes.telefono_oficina=='')
		{
			$('input[name="telefonos[0][telefono]"]').val(datos_interes.telefono_residencial);
			$('select[name="telefonos[0][tipo]"]').val('residencial');
			$('select[name="telefonos[0][tipo]"]').trigger('change');
		}
		else if(datos_interes.telefono_residencial=='' && datos_interes.telefono_oficina!='')
		{
			$('input[name="telefonos[0][telefono]"]').val(datos_interes.telefono_oficina);
			$('select[name="telefonos[0][tipo]"]').val('trabajo');
			$('select[name="telefonos[0][tipo]"]').trigger('change');
		}
		$('input[name="correos[0][correo]"]').val(datos_interes.correo);
		
		if(datos_interes.direccion_residencial!='' && datos_interes.direccion_laboral!=='')
		{
			$('#boton_centrofact').trigger('click');
			
			$('input[name="centro_facturacion[0][nombre]"]').val('Residencial');

			$('input[name="centro_facturacion[0][direccion]"]').val(datos_interes.direccion_residencial);
		}
		
		else if(datos_interes.direccion_residencial!='' && datos_interes.direccion_laboral=='')
		{
			$('input[name="centro_facturacion[0][nombre]"]').val('Residencial');

			$('input[name="centro_facturacion[0][direccion]"]').val(datos_interes.direccion_residencial);
		}
		
	}
});