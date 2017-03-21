Vue.http.options.emulateJSON = true;
var formularioCrear = new Vue({
	el: ".wrapper-content",
	data:{
		informacionComisiones: [],
	},
	methods: {
		getInfoAgente: function(id_agente)
		{
			this.$http.post({
                url: phost() + 'honorarios_seguros/ajax_get_datos_agente',
                method: 'POST',
                data: {id_agente:id_agente,erptkn: tkn} 
				}).then(function (response) {
					if (_.has(response.data, 'session')) {
						window.location.assign(phost());
					}

					if (!_.isEmpty(response.data)) {
						if(response.data.tipo_identificacion=='natural')
						{
							$('.noPAS').show();
							$('.RUC').hide();
							$('.PAS').hide();
							
							$('#provincia_natural').val(response.data.provincia);
							$('#letra_natural').val(response.data.letra);
							$('#tomo_natural').val(response.data.tomo);
							$('#asiento_natural').val(response.data.asiento);
						}
						else if(response.data.tipo_identificacion=='juridico')
						{
							$('.noPAS').hide();
							$('.RUC').show();
							$('.PAS').hide();
							
							$('#tomo_ruc').val(response.data.tomo_ruc);
							$('#folio_ruc').val(response.data.folio_ruc);
							$('#asiento_ruc').val(response.data.asiento_ruc);
							$('#digito_ruc').val(response.data.digito_ruc);
						}
						else
						{
							$('.noPAS').hide();
							$('.RUC').hide();
							$('.PAS').show();
							
							$('#pasaporte').val(response.data.pasaporte);
						}
						
						$('#telefono').val(response.data.telefono);
						$('#correo').val(response.data.correo);
					}
				
				});
		},
		//Limpiar campos de busqueda
		limpiarCamposHonorarios: function () {

            $('#fecha_desde').val('');
            $('#fecha_hasta').val('');
			$('#agente').val('');
			$('#pasaporte').val('');
			$('#tomo_ruc').val('');
			$('#folio_ruc').val('');
			$('#asiento_ruc').val('');
			$('#digito_ruc').val('');
			$('#provincia_natural').val('');
			$('#letra_natural').val('');
			$('#tomo_natural').val('');
			$('#asiento_natural').val('');
			$('#telefono').val('');
			$('#correo').val('');
            $('.noPAS').hide();
			$('.RUC').hide();
			$('.PAS').hide();
			
		},
		getHonorarioGuardar: function()
		{
			//polula el segundo select del header
            var self = this;
            var id_agente = $('#agente').val();
            var fecha_inicio = $('#fecha_desde').val();
            var fecha_final = $('#fecha_hasta').val();
			var monto = $('#total_final').val();
			var comisiones = $('#total_com').val();
			
			$('#guardar_comision').prop('disabled','disabled');
			$('#procesar_comision').prop('disabled','disabled');
			
			var comisionespar_id=[];
			
			$("input[name='comisionpar_id[]']").map(function (index,dato) {
				if(dato.value>0)
				{
					comisionespar_id[index]=(dato.id);
				}
			}).get();
	
			if(id_agente!='')
			{
				this.$http.post({
                url: phost() + 'honorarios_seguros/guardar_honorario_proceso',
                method: 'POST',
                data: {codigo:codigo,id_agente:id_agente,fecha_inicio: fecha_inicio, fecha_final: fecha_final, comisionespar_id:comisionespar_id,monto:monto,comisiones:comisiones,erptkn: tkn} 
				}).then(function (response) {
					if (_.has(response.data, 'session')) {
						window.location.assign(phost());
					}

					if (!_.isEmpty(response.data)) {
						toastr.success('<b>Exito!</b> Se ha guardado correctamente');
						$('#tabla_remesas').addClass('hidden');
						window.location.href = phost() + "honorarios_seguros/listar";
					}
					
				});
			}
			else
			{
				toastr.error('El campo agente obligatorios');
				$('#tabla_comisiones').addClass('hidden');
			}
		},
		getHonorarios: function()
		{
			//polula el segundo select del header
            var self = this;
            var id_agente = $('#agente').val();
            var fecha_inicio = $('#fecha_desde').val();
            var fecha_final = $('#fecha_hasta').val();
	
			if(id_agente!='')
			{
				this.$http.post({
                url: phost() + 'honorarios_seguros/ajax_get_comisiones',
                method: 'POST',
                data: {codigo:codigo,id_agente:id_agente,fecha_inicio: fecha_inicio, fecha_final: fecha_final,erptkn: tkn} 
				}).then(function (response) {
					if (_.has(response.data, 'session')) {
						window.location.assign(phost());
					}

					if (!_.isEmpty(response.data)) {
						//console.log(response.data.inter);
						if(response.data.inter.length>1)
						{
							$('#tabla_comisiones').removeClass('hidden');
							self.$set('informacionComisiones', response.data.inter);
							
						}
						else
						{
							toastr.error('No existen comisiones para la busqueda realizada');
							$('#tabla_comisiones').addClass('hidden');
						}
					}
					
					$('#agente').val(id_agente).prop("selected","selected");
					$('#agente').trigger('change');
					$('#fecha_desde').val(fecha_inicio);
					$('#fecha_hasta').val(fecha_final);				
				});
			}
			else
			{
				toastr.error('El campo agente obligatorios');
				$('#tabla_comisiones').addClass('hidden');
			}
		},
		cancelar: function()
		{
			window.location.href = phost() + "honorarios_seguros/listar";
		},
		getHonorarioProcesar: function()
		{
			//polula el segundo select del header
            var self = this;
            var id_agente = $('#agente').val();
            var fecha_inicio = $('#fecha_desde').val();
            var fecha_final = $('#fecha_hasta').val();
			var monto = $('#total_final').val();
			var comisiones = $('#total_com').val();
			
			$('#guardar_comision').prop('disabled','disabled');
			$('#procesar_comision').prop('disabled','disabled');
			
			var comisionespar_id=[];
			
			$("input[name='comisionpar_id[]']").map(function (index,dato) {
				if(dato.value>0)
				{
					comisionespar_id[index]=(dato.id);
				}
			}).get();
	
			if(id_agente!='')
			{
				this.$http.post({
                url: phost() + 'honorarios_seguros/guardar_honorario_por_pagar',
                method: 'POST',
                data: {codigo:codigo,id_agente:id_agente,fecha_inicio: fecha_inicio, fecha_final: fecha_final, comisionespar_id:comisionespar_id,monto:monto,comisiones:comisiones,erptkn: tkn} 
				}).then(function (response) {
					if (_.has(response.data, 'session')) {
						window.location.assign(phost());
					}

					if (!_.isEmpty(response.data)) {
						toastr.success('<b>Exito!</b> Se ha guardado correctamente');
						$('#tabla_comisiones').addClass('hidden');
						window.location.href = phost() + "honorarios_seguros/listar";
					}
					
				});
			}
			else
			{
				toastr.error('El campo agente obligatorios');
				$('#tabla_comisiones').addClass('hidden');
			}
		},
        
	}
});

$(document).ready(function(){
	$('#fecha_desde').val('');
	$('#fecha_hasta').val('');
	$('#agente').val('');
	$('#pasaporte').val('');
	$('#tomo_ruc').val('');
	$('#folio_ruc').val('');
	$('#asiento_ruc').val('');
	$('#digito_ruc').val('');
	$('#provincia_natural').val('');
	$('#letra_natural').val('');
	$('#tomo_natural').val('');
	$('#asiento_natural').val('');
	$('#telefono').val('');
	$('#correo').val('');
	$('.noPAS').hide();
	$('.RUC').hide();
	$('.PAS').hide();
	
});

$("#imprimirHonorarioBtn").click(function(){
	
	window.open('../imprimirHonorarios/'+codigo+'/'+agente_id+'/'+fecha_desde+'/'+fecha_hasta); 
});

$(document).ready(function(){
	if(vista=='editar')
	{
		if(estado_honorario=='en_proceso')
		{
			$('#codigo_comision').val(codigo);
			$('#agente').val(agente_id).prop("selected","selected");
			formularioCrear.getInfoAgente(agente_id);
			
			if(fecha_desde!=='')
				$('#fecha_desde').val(fecha_desde);
			if(fecha_hasta!=='')
				$('#fecha_hasta').val(fecha_hasta);
			
			$('#botones_pagar').hide();
			$('#botones_proceso').show();
			$('#no_pago_div').hide();
			
			$('#actualizar').trigger('click');
		}
		else
		{
			$('#codigo_comision').val(codigo);
			$('#agente').val(agente_id).prop("selected","selected");
			formularioCrear.getInfoAgente(agente_id);
			
			$('#agente').prop("disabled","disabled");
			
			if(fecha_desde!=='')
				$('#fecha_desde').val(fecha_desde);
			if(fecha_hasta!=='')
				$('#fecha_hasta').val(fecha_hasta);
			
			$('#fecha_desde').prop("disabled","disabled");
			$('#fecha_hasta').prop("disabled","disabled");
			
			$('#botones_editar').hide();
			$('#botones_pagar').show();
			$('#botones_proceso').hide();
			$('#no_pago_div').show();
			$('#no_pago').val(no_pago);
			
			$('#actualizar').trigger('click');
			
		}
	}
	else
	{
		$('#botones_pagar').hide();
		$('#no_pago_div').hide();
	}
});
