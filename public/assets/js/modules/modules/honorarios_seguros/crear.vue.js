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
                data: {id_agente:id_agente,fecha_inicio: fecha_inicio, fecha_final: fecha_final, comisionespar_id:comisionespar_id,erptkn: tkn} 
				}).then(function (response) {
					if (_.has(response.data, 'session')) {
						window.location.assign(phost());
					}

					if (!_.isEmpty(response.data)) {
						//console.log(response.data.length);
						if(response.data.inter.length>0)
						{
							//window.location.href = phost() + "honorarios_seguros/listar";
							console.log(response.data.inter);
						}
						else
						{
							toastr.error('No existen comisiones para la busqueda realizada');
							$('#tabla_remesas').addClass('hidden');
						}
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
                data: {id_agente:id_agente,fecha_inicio: fecha_inicio, fecha_final: fecha_final,erptkn: tkn} 
				}).then(function (response) {
					if (_.has(response.data, 'session')) {
						window.location.assign(phost());
					}

					if (!_.isEmpty(response.data)) {
						//console.log(response.data.length);
						if(response.data.inter.length>0)
						{
							$('#tabla_comisiones').removeClass('hidden');
							self.$set('informacionComisiones', response.data.inter);
							
						}
						else
						{
							toastr.error('No existen comisiones para la busqueda realizada');
							$('#tabla_remesas').addClass('hidden');
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
		}
        
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

$("#imprimirRemesaBtn").click(function(){
	
	window.open('../imprimirRemesasProcesadas/'+codigo+'/'+aseguradora_id+'/'+fecha_desde+'/'+fecha_hasta); 
});
