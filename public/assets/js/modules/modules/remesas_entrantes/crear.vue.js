Vue.http.options.emulateJSON = true;
var formularioCrear = new Vue({
	el: ".wrapper-content",
	data:{
		informacionRemesas: [],
		informacionRemesas2: [],
	},
	methods: {
        getRemesas: function () {
			
            //polula el segundo select del header
            var self = this;
            var id_aseguradora = $('#aseguradora').val();
            var fecha_inicio = $('#fecha_desde').val();
            var fecha_final = $('#fecha_hasta').val();
            var id_ramos = $('#ramos').val();
	
			//console.log(id_ramos);
			
			if(id_aseguradora!='' && id_ramos!="" && id_aseguradora!=null && id_ramos!=null)
			{
				this.$http.post({
                url: phost() + 'remesas_entrantes/ajax_get_remesa_entrantes',
                method: 'POST',
                data: {codigo_remesa:codigo,id_aseguradora: id_aseguradora, fecha_inicio: fecha_inicio, fecha_final: fecha_final, id_ramos: id_ramos, erptkn: tkn} 
				}).then(function (response) {
					if (_.has(response.data, 'session')) {
						window.location.assign(phost());
					}

					if (!_.isEmpty(response.data)) {
						//console.log(response.data.length);
						if(response.data.inter.length>0)
						{
							$('#tabla_remesas_procesadas').addClass('hidden');
							$('#tabla_remesas').removeClass('hidden');
							self.$set('informacionRemesas', response.data.inter);
							
						}
						else
						{
							toastr.error('No existen facturas para la busqueda realizada');
							$('#tabla_remesas_procesadas').addClass('hidden');
							$('#tabla_remesas').addClass('hidden');
						}
					}
					
					$('#aseguradora').val(id_aseguradora).prop("selected","selected");
					$('#fecha_desde').val(fecha_inicio);
					$('#fecha_hasta').val(fecha_final);
					$('#ramos').val(id_ramos);					
				});
			}
			else
			{
				toastr.error('Los campos aseguradora y ramos son obligatorios');
				$('#tabla_remesas_procesadas').addClass('hidden');
				$('#tabla_remesas').addClass('hidden');
			}
        },
		//Limpiar campos de busqueda
		limpiarCamposRemesa: function () {
			
			if(vista != "editar"){
				$('#aseguradora').val('');
			}
			
            $('#fecha_desde').val('');
            $('#fecha_hasta').val('');
            $('#ramos').val('');
			$('#ramos').trigger('chosen:updated');
			
			this.getRemesas();
		},
		
		getFormatoMonto: function(id){
			if(incremento==0)
			{
				console.log('');
				$('#'+id+'').inputmask('currency',{
					prefix: "",
					autoUnmask : true,
					removeMaskOnSubmit: true
				});
				incremento++;
			}
			
		},
		
		//saber el monto de la remesa
		valorMonto: function(id,monto_inicial){
			var monto_final=$('#'+id+'').val();
			
			if(parseFloat(monto_final)>parseFloat(monto_inicial))
			{
				$('#'+id+'').val(parseFloat(monto_inicial));
			}
			
			var valor_final=0;
			$("input[name='monto[]']").map(function (index,dato) {
				valor_final+=parseFloat(dato.value);
			}).get();
			
			var monto_total=0;
			var monto=0;
			$('input[name="factura_id[]"]:checked').map(function(index,dato) {
				monto=$('#'+dato.value+'').val();
				monto_total+=parseFloat(monto);
			});
			
			var total_final=0;
			total_final=(parseFloat(monto_total) + parseFloat(valor_final)).toFixed(2);
			
			$("#monto_total_final").html(total_final);
				
		},
		
		ValorMontoChequeado: function(){
			var valor_final=0;
			$("input[name='monto[]']").map(function (index,dato) {
				valor_final+=parseFloat(dato.value);
			}).get();
			
			var monto_total=0;
			var monto=0;
			$('input[name="factura_id[]"]:checked').map(function(index,dato) {
				monto=$('#'+dato.value+'').val();
				monto_total+=parseFloat(monto);
			});
			var total_final=0;
			total_final=(parseFloat(monto_total) + parseFloat(valor_final)).toFixed(2);
			
			$("#monto_total_final").html(total_final);
		}, 
		
		//saber el monto de la remesa
		valorComisionPagada: function(id,com_esperada){
			var com_pagada=$('#comision_pagada_'+id+'').val();
			
			if(com_pagada=='')
			{
				com_pagada=parseFloat(0);
			}
			
			if(parseFloat(com_pagada).toFixed(2)!==parseFloat(com_esperada).toFixed(2))
			{
				$('#pagada_'+id+'').addClass('comisionDiferente');
				$('#pagada_'+id+'').removeClass('comisionIgual');
				
				$('#fecha_'+id+'').addClass('comisionDiferente');
				$('#fecha_'+id+'').removeClass('comisionIgual');
				
				$('#ramo_'+id+'').addClass('comisionDiferente');
				$('#ramo_'+id+'').removeClass('comisionIgual');
				
				$('#cliente_'+id+'').addClass('comisionDiferente');
				$('#cliente_'+id+'').removeClass('comisionIgual');
				
				$('#prima_'+id+'').addClass('comisionDiferente');
				$('#prima_'+id+'').removeClass('comisionIgual');
				
				$('#monto_'+id+'').addClass('comisionDiferente');
				$('#monto_'+id+'').removeClass('comisionIgual');
				
				$('#comision_pagada_'+id+'').addClass('comisionDiferente');
				$('#comision_pagada_'+id+'').removeClass('comisionIgual');
				
				$('#por_comision_'+id+'').addClass('comisionDiferente');
				$('#por_comision_'+id+'').removeClass('comisionIgual');
				
				$('#con_esperada_'+id+'').addClass('comisionDiferente');
				$('#con_esperada_'+id+'').removeClass('comisionIgual');
				
				$('#com_descontada_'+id+'').addClass('comisionDiferente');
				$('#com_descontada_'+id+'').removeClass('comisionIgual');
				
				$('#sob_comison_'+id+'').addClass('comisionDiferente');
				$('#sob_comison_'+id+'').removeClass('comisionIgual');
				
				$('#sob_esperada_'+id+'').addClass('comisionDiferente');
				$('#sob_esperada_'+id+'').removeClass('comisionIgual');
				
				$('#com_sob_descontada_'+id+'').addClass('comisionDiferente');
				$('#com_sob_descontada_'+id+'').removeClass('comisionIgual');
			}
			else
			{
				$('#pagada_'+id+'').addClass('comisionIgual');
				$('#pagada_'+id+'').removeClass('comisionDiferente');
				
				$('#fecha_'+id+'').addClass('comisionIgual');
				$('#fecha_'+id+'').removeClass('comisionDiferente');
				
				$('#ramo_'+id+'').addClass('comisionIgual');
				$('#ramo_'+id+'').removeClass('comisionDiferente');
				
				$('#cliente_'+id+'').addClass('comisionIgual');
				$('#cliente_'+id+'').removeClass('comisionDiferente');
				
				$('#prima_'+id+'').addClass('comisionIgual');
				$('#prima_'+id+'').removeClass('comisionDiferente');
				
				$('#monto_'+id+'').addClass('comisionIgual');
				$('#monto_'+id+'').removeClass('comisionDiferente');
				
				$('#por_comision_'+id+'').addClass('comisionIgual');
				$('#por_comision_'+id+'').removeClass('comisionDiferente');
				
				$('#con_esperada_'+id+'').addClass('comisionIgual');
				$('#con_esperada_'+id+'').removeClass('comisionDiferente');
				
				$('#com_descontada_'+id+'').addClass('comisionIgual');
				$('#com_descontada_'+id+'').removeClass('comisionDiferente');
				
				$('#sob_comison_'+id+'').addClass('comisionIgual');
				$('#sob_comison_'+id+'').removeClass('comisionDiferente');
				
				$('#sob_esperada_'+id+'').addClass('comisionIgual');
				$('#sob_esperada_'+id+'').removeClass('comisionDiferente');
				
				$('#com_sob_descontada_'+id+'').addClass('comisionIgual');
				$('#com_sob_descontada_'+id+'').removeClass('comisionDiferente');
				
				$('#comision_pagada_'+id+'').addClass('comisionIgual');
				$('#comision_pagada_'+id+'').removeClass('comisionDiferente');
			}
			
			var valor_final=parseFloat(0);
			var valor;
			$("input[name='com_pagada[]']").map(function (index,dato) {
				if(dato.value=='')
					valor=0;
				else
					valor=dato.value;
				valor_final+=parseFloat(valor);
			}).get();
			
			
			$("#com_paga_final_final").html('$'+ valor_final.toFixed(2));
			
			$("#com_final_comision").val(valor_final.toFixed(2));
			
			var com_final_esperada=parseFloat($('#com_final_esperada').val());
			
			if(parseFloat(com_final_esperada).toFixed(2)!=parseFloat(valor_final).toFixed(2))
			{
				$("#com_paga_final_final").addClass('comisionDiferente');
				$("#com_paga_final_final").removeClass('comisionIgual');
				
				$('#finales_1').addClass('comisionDiferente');
				$('#finales_1').removeClass('comisionIgual');
				
				$('#finales_2').addClass('comisionDiferente');
				$('#finales_2').removeClass('comisionIgual');
				
				$('#finales_2').addClass('comisionDiferente');
				$('#finales_2').removeClass('comisionIgual');
				
				$('#finales_3').addClass('comisionDiferente');
				$('#finales_3').removeClass('comisionIgual');
				
				$('#finales_4').addClass('comisionDiferente');
				$('#finales_4').removeClass('comisionIgual');
				
				$('#finales_5').addClass('comisionDiferente');
				$('#finales_5').removeClass('comisionIgual');
				
				$('#finales_6').addClass('comisionDiferente');
				$('#finales_6').removeClass('comisionIgual');
				
				$('#finales_7').addClass('comisionDiferente');
				$('#finales_7').removeClass('comisionIgual');
			}
			else
			{
				$("#com_paga_final_final").addClass('comisionIgual');
				$("#com_paga_final_final").removeClass('comisionDiferente');
				
				$('#finales_1').addClass('comisionIgual');
				$('#finales_1').removeClass('comisionDiferente');
				
				$('#finales_2').addClass('comisionIgual');
				$('#finales_2').removeClass('comisionDiferente');
				
				$('#finales_2').addClass('comisionIgual');
				$('#finales_2').removeClass('comisionDiferente');
				
				$('#finales_3').addClass('comisionIgual');
				$('#finales_3').removeClass('comisionDiferente');
				
				$('#finales_4').addClass('comisionIgual');
				$('#finales_4').removeClass('comisionDiferente');
				
				$('#finales_5').addClass('comisionIgual');
				$('#finales_5').removeClass('comisionDiferente');
				
				$('#finales_6').addClass('comisionIgual');
				$('#finales_6').removeClass('comisionDiferente');
				
				$('#finales_7').addClass('comisionIgual');
				$('#finales_7').removeClass('comisionDiferente');
				
				$("#com_paga_final_final").addClass('comisionIgual');
				$("#com_paga_final_final").addClass('comisionDiferente');
			}
				
		},
		
		getRemesasProcesadasCancelar: function(){
			
			remesa_uuid=$('#uuid_remesa').val();
			console.log(remesa_uuid);
			
			if(estado_remesa=='en_proceso')
				window.location.assign(phost() + 'remesas_entrantes/editar/' + remesa_uuid);
			else 
				window.location.assign(phost() + 'remesas_entrantes/listar/');
			/*else{
				window.location.assign(phost() + 'remesas_entrantes/editar/' + remesa_uuid);
			}*/
			/*if(vista != "editar"){
				$('#aseguradora').prop("disabled",false);
				$('#aseguradora').prop("disabled",false);
				$('#fecha_desde').prop("disabled",false);
				$('#fecha_hasta').prop("disabled",false);
				$('#ramos').prop("disabled",false);
				
				$('#clearBtn').removeClass('hidden');
				$('#actualizar').removeClass('hidden');
				
				$('#actualizar').trigger('click');
			}
			else{
				window.location.assign(phost() + 'remesas_entrantes/listar');
			}*/
		},
		
		getRemesasCancelarPrincipal: function(){
			window.location.assign(phost() + 'remesas_entrantes/listar/');
		},
		
		getRemesasProcesadas: function () {
            //polula el segundo select del header
			$("#procesar_remesa").attr('disabled',true);
			$("#procesar_remesa").prop('disabled',true);
			
            var self = this;
			
			var id_ramos = $('#ramos').val();
			var fecha_inicio = $('#fecha_desde').val();
            var fecha_final = $('#fecha_hasta').val();
			
			var monto=[];
			var facturas_id=[];
			var id_monto=[];
			
			var aseguradora_id;
			aseguradora_id=$('#aseguradora').val();
			$("input[name='monto[]']").map(function (index,dato) {
				if(dato.value>0)
				{
					id_monto[index]=(dato.id);
					facturas_id[index]=(dato.id);
					monto[index]=parseFloat((dato.value)).toFixed(2);
					
				}
			}).get();
			
			var facturas_chequeadas = [];
			//recorremos todos los checkbox seleccionados con .each
			$('input[name="factura_id[]"]:checked').map(function(index,dato) {
				facturas_chequeadas[index]=(dato.value);
			});
			
			if( (estado_remesa=='en_proceso' || estado_remesa=='')  ){

				if(facturas_id.length==0 && facturas_chequeadas.length==0 && vista=='crear'){

					$("#procesar_remesa").attr('disabled',false);
					$("#procesar_remesa").prop('disabled',false);
					$("#guardar_remesa").attr('disabled',false);

					this.$http.post({
		                url: phost() + 'remesas_entrantes/ajax_get_remesas_sin_cobros',
		                method: 'POST',
		                data: {fecha_desde:fecha_inicio,fecha_hasta:fecha_final,ramos_id:id_ramos,codigo_remesa:codigo,aseguradora_id:aseguradora_id,erptkn: tkn} 
		            }).then(function (response) {
		                if (_.has(response.data, 'session')) {
		                    window.location.assign(phost());
		                }
		                if (!_.isEmpty(response.data)) {
							if(vista=='crear'){
								console.log(response.data.uuid);
								window.location.assign(phost() + 'remesas_entrantes/editar/'+ response.data.uuid);
							}
		                }
		            });
					//toastr.error('Por favor selecione una factura con estado cobro completado o coloque un monto mayor a cero para el restos de facturas');
					//return false;
				}
			}else{
            
	            this.$http.post({
	                url: phost() + 'remesas_entrantes/ajax_get_remesa_entrantes_procesar',
	                method: 'POST',
	                data: {fecha_desde:fecha_inicio,fecha_hasta:fecha_final,ramos_id: id_ramos,codigo_remesa:codigo,aseguradora_id:aseguradora_id,facturas_id:facturas_id,facturas_chequeadas:facturas_chequeadas,montos:monto,id_monto:id_monto,erptkn: tkn} 
	            }).then(function (response) {
	                if (_.has(response.data, 'session')) {
	                    window.location.assign(phost());
	                }

	                if (!_.isEmpty(response.data)) {
						console.log(response.data);
						if(vista=='crear'){
							window.location.assign(phost() + 'remesas_entrantes/editar/'+ response.data.uuid);
						}
						
						if(response.data.consulta == 0 && vista == "crear"){
							var mensaje_error = 1;
							window.location.assign(phost() + 'remesas_entrantes/listar/'+mensaje_error);
						}else{
							$('#tabla_remesas').addClass('hidden');
	                    	$('#tabla_remesas_procesadas').removeClass('hidden');
							self.$set('informacionRemesas', '');
							self.$set('informacionRemesas', response.data.inter);
						}
	                }
					
					$('#aseguradora').prop("disabled",true);
					$('#aseguradora').prop("disabled",true);
					//$('#fecha_desde').prop("disabled",true);
					//$('#fecha_hasta').prop("disabled",true);
					//$('#ramos').prop("disabled",true);
					//$('#clearBtn').addClass('hidden');
					//$('#actualizar').addClass('hidden');
						
	            });
			}
        },
		getRemesasProcesadasGuardar: function () {
			
            //polula el segundo select del header
			var aseguradora_id;
			var monto=[];
			var facturas_id=[];
			var id_monto=[];
			
			var fecha_desde;
			var fecha_hasta;
			var ramos_id;
			
			fecha_desde=$('#fecha_desde').val();
            fecha_hasta=$('#fecha_hasta').val();
            ramos_id=$('#ramos').val();
			
			aseguradora_id=$('#aseguradora').val();
			
			$("input[name='monto[]']").map(function (index,dato) {
				monto[index]=parseFloat((dato.value)).toFixed(2);
				id_monto[index]=(dato.id);
				facturas_id[index]=(dato.id);
			}).get();
			
			var facturas_chequeadas = [];
			//recorremos todos los checkbox seleccionados con .each
			/*$('input[name="factura_id[]"]:checked').map(function(index,dato) {
				facturas_chequeadas[index]=(dato.value);
			});*/
			
			$('input[name="factura_id[]"]').map(function(index,dato) {
				facturas_chequeadas[index]=(dato.value);
			});
			
			/*if(facturas_id.length==0 && facturas_chequeadas.length==0){
				alert('Por favor selecione una factura con estado cobro completado o coloque un monto mayor a cero para el restos de facturas');
				return false;
			}*/
            
            this.$http.post({
                url: phost() + 'remesas_entrantes/ajax_get_remesa_entrantes_borrador',
                method: 'POST',
                data: {codigo_remesa:codigo,fecha_desde:fecha_desde,fecha_hasta:fecha_hasta,ramos_id:ramos_id,aseguradora_id:aseguradora_id,facturas_id:facturas_id,facturas_chequeadas:facturas_chequeadas,montos:monto,id_monto:id_monto,erptkn: tkn} 
            }).then(function (response) {
               window.location.href = phost() + "remesas_entrantes/listar";
            });
        },
        getActualizarComisiones: function(){

        	var self = this;

        	var fecha_inicial = $('#fecha_desde').val();
        	var fecha_final = $('#fecha_hasta').val();
        	var ramos = $('#ramos').val();
        	var no_recibo = $('#no_recibo').val();
        	

        	this.$http.post({
                url: phost() + 'remesas_entrantes/ajax_actualizar_comisiones',
                method: 'POST',
                data: {id_remesa:id_remesa_entrante, estado_remesa:estado_remesa, aseguradora_id:aseguradora_id, fecha_inicial:fecha_inicial, fecha_final:fecha_final, ramos:ramos, no_recibo:no_recibo, erptkn: tkn} 
            }).then(function (response) {

                if (_.has(response.data, 'session')) {
                    window.location.assign(phost());
                }

                if (!_.isEmpty(response.data)) {
					console.log(response.data);
					if(response.data.consulta == 0){
						$('#tabla_remesas_procesadas').addClass('hidden');
						toastr.error('No hay comisiones con las opciones de busqueda');
					}else{
						$('#tabla_remesas_procesadas').removeClass('hidden');
						self.$set('informacionRemesas', '');
						self.$set('informacionRemesas', response.data.inter);
					}
                }
            });
        },
        getLimpiarComisiones:function(){

        	$('#fecha_desde').val('');
        	$('#fecha_hasta').val('');
        	$('#ramos').val('').trigger("chosen:updated");
        	$('#no_recibo').val('');
        	$('#monto_recibo').val('');
        	$('#tabla_remesas_procesadas').addClass('hidden');
        }
		
	}
});

$(document).ready(function(){
	
	$('#datos_por_liquidar').hide();
	
	$("body").on("click", "#formRemesaEntranteCrear", function () {
		$(".formatomoneda").inputmask('currency',{
			prefix: "",
			autoUnmask : true,
			removeMaskOnSubmit: true
		});
	});
	
	$("body").on("click", "#formRemesaEntranteProcesadasCrear", function () {
		$(".formatomoneda").inputmask('currency',{
			prefix: "",
			autoUnmask : true,
			removeMaskOnSubmit: true
		});
	});
	
	$("#selectAll").click( function(){
		if($(this).is(':checked')) {
			$(".checkboxcompletos").prop('checked', true);
		}
		else
		{
			$(".checkboxcompletos").prop('checked', false);
		}

	});
	
	$("#selectAllPorLiquidar").click( function(){
		if($(this).is(':checked')) {
			$(".checkboxcompletos").prop('checked', true);
		}
		else
		{
			$(".checkboxcompletos").prop('checked', false);
		}

	});
	

    if(vista == "editar" && borrador=='si'){
		if(ver==1)
		{
			$("body").on("click", "#formRemesaEntranteProcesadasCrear", function () {
				$(".formatomoneda").attr('disabled',true);
			});
		}
		$('#aseguradora').prop("disabled",true);
		$('#codigo_remesa_procesado').val(codigo);
		$('#aseguradora').val(aseguradora_id).prop("selected","selected");;
		
		if(fecha_desde!=='')
			$('#fecha_desde').val(fecha_desde);
		if(fecha_hasta!=='')
			$('#fecha_hasta').val(fecha_hasta);
        var prueba = ramos_id.split(",");
        $('#ramos').val(prueba).trigger("chosen:updated");
		
	   $('#codigo_remesa').val(codigo);
	   
		$('#actualizar').trigger('click'); 
    }
	
	if(vista == "editar" && borrador=='no'){
		$('#datos_por_liquidar').show();
		
		if(no_recibo!="")
		{
			//$('#no_recibo').append('<option value="'+ no_recibo +'" selected="selected">'+nombre_recibo+'</option>');
			$('#no_recibo').val(no_recibo).prop("selected","selected");
			$('#monto_recibo').val(parseFloat((monto_recibo)).toFixed(2));
			
			$('#no_recibo_guardar').val(no_recibo);
		}
		
		if(ver==1)
		{
			$("body").on("click", "#formRemesaEntranteProcesadasCrear", function () {
				$(".com_pagada").attr('disabled',true);
			});
		}
		$('#codigo_remesa_procesado').val(codigo);
		$('#aseguradora').val(aseguradora_id).prop("selected","selected");
		
		if(fecha_desde!=='')
			$('#fecha_desde').val(fecha_desde);
		if(fecha_hasta!=='')
			$('#fecha_hasta').val(fecha_hasta);
        var prueba = ramos_id.split(",");
        $('#ramos').val(prueba).trigger("chosen:updated");

		$('#procesar_remesa').trigger('click');
		
		if(estado_remesa=='liquidada')
		{
			$('#clasecancelar').removeClass('col-sm-8');
			$('#clasecancelar').removeClass('col-md-8');
			$('#clasecancelar').removeClass('col-lg-6');
			
			$('#clasecancelar').addClass('col-sm-12');
			$('#clasecancelar').addClass('col-md-12');
			$('#clasecancelar').addClass('col-lg-10');
			
			$('#guardar_remesa_pro').addClass('hidden');
			$('#liquidar_remesa').addClass('hidden');
			$("body").on("click", "#formRemesaEntranteProcesadasCrear", function () {
				$(".com_pagada").attr('disabled',true);
			});
		}
		else
		{
			$('#clasecancelar').addClass('col-sm-8');
			$('#clasecancelar').addClass('col-md-8');
			$('#clasecancelar').addClass('col-lg-6');
			
			$('#clasecancelar').removeClass('col-sm-12');
			$('#clasecancelar').removeClass('col-md-12');
			$('#clasecancelar').removeClass('col-lg-10');
			
			$('#guardar_remesa').removeClass('hidden');
			$('#liquidar_remesa').removeClass('hidden');
			$("body").on("click", "#formRemesaEntranteProcesadasCrear", function () {
				$(".com_pagada").attr('disabled',false);
			});
		}
    }
	
	$("#liquidar_remesa").unbind().click(function(){
		
		var valor_final=$('#com_final_comision').val();
		
		var valor_recibo=$('#monto_recibo').val();
		
		if(parseFloat((valor_final)).toFixed(2) !== parseFloat((valor_recibo)).toFixed(2))
		{
			toastr.error('No se pueden liquidar la remesa, el valor de total de las comisiones debe ser igual al monto del recibo');
			
			return false;
		}
		else
			return true;
	});
	
	$("#guardar_remesa_pro").unbind().click(function(){
		
		if(vista == "editar" && borrador=='no'){
			var recibo=$('#no_recibo_guardar').val();
			if(recibo=="")
			{
				$('#no_recibo_guardar').val(no_recibo);
			}
		}
		
		return true;
	});
	
	$("#eliminarRemesaBtn").unbind().click(function(){
		var facturas_chequeadas = [];
		//recorremos todos los checkbox seleccionados con .each
		$('input[name="factura_check_id[]"]:checked').map(function(index,dato) {
			facturas_chequeadas[index]=(dato.value);
		});
		
		var facturas_no_chequeadas = [];
		//recorremos todos los checkbox seleccionados con .each
		$('input[name="factura_check_id[]"]:unchecked').map(function(index,dato) {
			facturas_no_chequeadas[index]=(dato.value);
		});
		
		if(facturas_no_chequeadas.length==0){
			toastr.error('No se pueden eliminar todas las comisiones de la remesa, debe dejar por lo menos una');
			return false;
		}else{
			if(facturas_chequeadas.length>0){				
				var remesa=$('#codigo_remesa_procesado').val();
				  $.post(phost()+'remesas_entrantes/ajax_get_eliminar_comisiones', {comisiones: facturas_chequeadas, remesa: remesa, erptkn: window.tkn}, function(response){
					console.log(response);
					formularioCrear.getRemesas();
					formularioCrear.getRemesasProcesadas();
						
				});
			}else{
				toastr.error('Debe seleccionar por lo menos una comision para poder eliminarla');
				return false;
			}
		}
	});	
});
	
$("#imprimirRemesaBtn").click(function(){
    fecha_desde = fecha_desde.replace("/", "-").replace("/", "-");
    fecha_hasta = fecha_hasta.replace("/", "-").replace("/", "-");
	
	window.open('../imprimirRemesasProcesadas/'+codigo+'/'+aseguradora_id+'/'+fecha_desde+'/'+fecha_hasta); 
});

$("#no_recibo").unbind().change(function(){
		var recibo=$('#no_recibo').val();
		$.post(phost()+'remesas_entrantes/ajax_get_datos_mov_dinero', {recibo: recibo, erptkn: window.tkn}, function(response){
			console.log(response);
			var dato=$.parseJSON(response);
			$('#monto_recibo').val(dato);
			$('#no_recibo_guardar').val(recibo);
		});
});	