$(document).ready(function(){
	var grid_obj = $("#PolizasGrid");
	
	$(function(){
		
		$("#exportarPolizasLnk").on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			if($("#PolizasGrid").is(':visible') == true){
				//Exportar Seleccionados del jQgrid
				var ids = [];
				ids = grid_obj.jqGrid('getGridParam','selarrrow');
				
				//Verificar si hay seleccionados
				if(ids.length > 0){
					console.log(ids);
					$('#ids').val(ids);
					$('form#exportarPolizas').submit();
					$('body').trigger('click');
				}
	        }
		});


		$('#agendarCobroLnk').on('click',function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			if ($('#tabla').is(':visible') === true) {

				var ids = [];
				var clientes = [];
				var i = 0;
				var cont = 0;
				ids = grid_obj.jqGrid('getGridParam', 'selarrrow');

				if (ids.length > 0) {

					console.log(ids);
					_.filter(ids, function (fila) {
	                    var infoFila = $.extend({}, grid_obj.getRowData(fila));
	                    console.log(infoFila);
	                    clientes[i] = infoFila.cliente;
	                    if(i >= 1){
	                    	if(clientes[0] != clientes[i]){
	                    		cont++;
	                    	}else{
	                    		cont=0;
	                    	}
	                    }
	                    i++;
	                });
	                if(cont == 0){
						$.post(phost() + 'polizas/ajax-get-cliente',{id_poliza: ids[0], erptkn: tkn}, function(response){
	                		if(response.cliente != 0){

	                			var id_poliza = ids;
	                			var id_cliente = response.cliente;
							   	var datos = {id_poliza:id_poliza,modo_cobro:'masivo',id_cliente: id_cliente};
							   	var cobros_agendados = moduloPolizas.AgendarCobro(datos);
								cobros_agendados.done(function (data) {
									if(data.cobro_agendado == 1){
										console.log("HOLAAAAAAAAAAAAAAAAA");
										toastr.error('Las polizas tiene un cobro agendado <p> número: <a href="'+phost()+'cobros_seguros/ver/'+data.datos_cobro.uuid_cobro+'?mod=polizas">'+data.datos_cobro.numero_cobro+'</a> fecha de creación '+data.datos_cobro.fecha_cobro+'</p>');
									}else if(data.cobro_agendado == 2){
										console.log(data);
	                					window.open('../cobros_seguros/crear?mod=polizas&idPoliza='+response.cliente+'&ids='+ids ,"_self");
									}else if(data.cobro_agendado == 3){
										console.log("No tiene facturas");
										toastr.error('Las polizas no tiene facturas en estado por cobrar o cobrado parcial o el saldo por cobrar es 0.00');
									}
								});
	                		}
	                	});
	                }else{
						opcionesModal.find('.modal-title').empty().append('Mensaje');
                    	opcionesModal.find('.modal-body').empty().append("<button class='btn btn-block btn-outline btn-danger'><p>Los registros</p> <p>no tienen el mismo cliente <i class='fa fa-exclamation-triangle'></p></button>");
                    	opcionesModal.find('.modal-footer').empty();
                    	opcionesModal.modal('show');
                	}

				}else{
					opcionesModal.find('.modal-title').empty().append('Mensaje');
                	opcionesModal.find('.modal-body').empty().append("<button class='btn btn-block btn-outline btn-warning'>seleccione algún registro <i class='fa fa-check'></button>");
                	opcionesModal.find('.modal-footer').empty();
                	opcionesModal.modal('show');
				}


			}


		});

		
	});

});