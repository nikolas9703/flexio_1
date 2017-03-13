  //Modulo Tabla de Cargos
var constructor = (function(){
   //eliminarContructorBtn
	
	var formulario = $('#editarAcumulado');
	var opcionesModal = $('#opcionesModal');

	var campos = function(){
		$(formulario).validate({
			focusInvalid: true,
			ignore: '',
			wrapper: '',
 		});
		$(formulario).find('#cuenta_pasivo_id').rules("add",{ required: true});
    	$(formulario).find('select[name="tipo_acumulado"], select[name="cuenta_pasivo_id"], select[name="estado_id"], select[class="form-control operador_id"], select[class="form-control tipo_calculo_uno"], select[class="form-control tipo_calculo_dos"]').chosen({width: '100%'}).trigger('chosen:updated');
     	$(formulario).find('#fecha_corte').daterangepicker({
    			startDate: fecha_corte,
	    		singleDatePicker: true,
	    		showDropdowns: true,
	    		opens: "left",
 		        locale: {
	        	format: 'DD/MM/YYYY',
	        	applyLabel: 'Seleccionar',
	            cancelLabel: 'Cancelar',
	        	daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
	            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
	            firstDay: 1
	        }
	    }); 
    	 
 				//Init Tabla Dinamica Plugin
				$("#contructor_expresiones").find('.agregarContructor').tablaDinamica({
					 
					afterAddRow: function(row){
 						
				    	$(formulario).find('select[class="form-control operador_id"], select[class="form-control tipo_calculo_uno"], select[class="form-control tipo_calculo_dos"]').chosen({width: '100%'}).trigger('chosen:updated');

 					},
					afterDeleteRow: function(){
						
  					},
					onClickDeleteBtn: function(tabla_id, row){
						 
						var scope = this;
						
 						$(row).addClass('alert-warning');
						
 						opcionesModal.on('hidden.bs.modal', function (e) {
 							$(row).removeClass('alert-warning');
						});
						 
						opcionesModal.find('.modal-title').empty().append('Confirme');
					    opcionesModal.find('.modal-body').empty().append('&#191;Esta seguro que desea eliminar?');
					    opcionesModal.find('.modal-footer').empty()
						.append('<button id="closeModal" class="btn btn-w-m btn-default" type="button" data-dismiss="modal">Cancelar</button>')
						.append(
							$('<button class="btn btn-w-m btn-danger" type="button" />').append('Eliminar').click(function(e){
								e.preventDefault();
								e.returnValue=false;
								e.stopPropagation();
								
								var response = {};
								var id = $(row).find('input:hidden[name*="[id]"]').val();
 						 
								response = ajax('configuracion_planilla/ajax-eliminar-constructor-acumulado', {id: id});
 								response.done(function(json){

						            //Check Session
									if( $.isEmptyObject(json.session) == false){
										window.location = phost() + "login?expired";
									}
						            
									//If json object is empty.
									if($.isEmptyObject(json) == true){
										return false;
									}
									
									//Mostrar Mensaje
									if(json.response == "" || json.response == undefined || json.response == 0){
										toastr.error(json.mensaje);
									}else{
										toastr.success(json.mensaje);
									}
									
									//Eliminar fila de la tabla
									scope.deleterow(row);
									
									//Ocultar Modal
									opcionesModal.modal('hide');
									
						        }).fail(function(xhr, textStatus, errorThrown) {
									//mensaje error
									toastr.error('Hubo un error al tratar de eliminar.');
								})
							})
						);
					    opcionesModal.modal('show');
					}
				});
		
   };
   var ajax = function(url, parametros){
		
		if(parametros == "" || url == ""){
			return false;
		}
		
		return $.ajax({
			url: phost() + url,
			data: $.extend({erptkn: tkn}, parametros),
			type: "POST",
			dataType: "json",
			cache: false,
		});
	};

 	 
	return{	    
		init: function() {
  			campos();
		},
		redimensionar: function(){
			//Al redimensionar ventana
			$(".ui-jqgrid").each(function(){
				var w = parseInt( $(this).parent().width()) - 6;
				var tmpId = $(this).attr("id");
				var gId = tmpId.replace("gbox_","");
				$("#"+gId).setGridWidth(w);
			});
		},
 	};
})();

constructor.init();






		
		
	