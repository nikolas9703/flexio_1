  //Modulo Tabla de Cargos
var constructor = (function(){
   //eliminarContructorBtn
	
	var formulario = $('#editarDeduccion');
	var opcionesModal = $('#opcionesModal');

	var campos = function(){
		$(formulario).validate({
			focusInvalid: true,
			ignore: '',
			wrapper: '',
 		});
		$(formulario).find('#cuenta_pasivo_id').rules("add",{ required: true});
		
		//$(formulario).find('select[name="tipo_acumulado"], select[name="cuenta_pasivo_id"], select[name="estado_id"], select[class="form-control operador_id"], select[class="form-control tipo_calculo_uno"], select[class="form-control tipo_calculo_dos"]').chosen({width: '100%'}).trigger('chosen:updated');
    	$(formulario).find('select[name="cuenta_pasivo_id"],select[class="form-control cuando_id"], select[class="form-control operador_id"], select[class="form-control aplicar"] ').chosen({width: '100%'}).trigger('chosen:updated');
 
   
     	
    	$("#div_rata_colaborador").find(".btn:first-child").text(colaborador_simbolo);
     	$("#div_rata_patrono").find(".btn:first-child").text(patrono_simbolo);
 		 
    	 if(colaborador_simbolo == 'Monto' ){
			 formulario.find("#rata_simbolo").text('$');
		}
		else{
			 formulario.find("#rata_simbolo").text('%');
		}
    	
    	if(patrono_simbolo == 'Monto' ){
			 formulario.find("#rata_simbolo_patrono").text('$');
		}
		else{
			 formulario.find("#rata_simbolo_patrono").text('%');
		}
    	 
    	 
    //	formulario.find('#rata_colaborador').prop('readonly',true);
	//	formulario.find('#rata_patrono').prop('readonly',true);
		
    	 formulario.find("#rata_colaborador_ul li a").click(function(){
			 formulario.find('#rata_colaborador').prop('readonly',false);
 			 var name = this.name;
			 if(name == 'monto' ){
				 formulario.find("#rata_simbolo").text('$');
			 }
			 else{
				 formulario.find("#rata_simbolo").text('%');
			 }
						
			 $("#div_rata_colaborador").find(".btn:first-child").text($(this).text());
			 formulario.find("#rata_colaborador_tipo").val($(this).text());
			 
		 }); 
		 
		 formulario.find("#rata_patrono_ul li a").click(function(){
			 
			 formulario.find('#rata_patrono').prop('readonly',false);
 			 var name = this.name;
 			 
 			 if(name == 'monto' ){
				 formulario.find("#rata_simbolo_patrono").text('$');
			 }
			 else{
				 formulario.find("#rata_simbolo_patrono").text('%');
			 }
						
			 $("#div_rata_patrono").find(".btn:first-child").text($(this).text());
			 formulario.find("#rata_patrono_tipo").val($(this).text());

		 }); 
		
    	 
    	 
 				//Init Tabla Dinamica Plugin
				$("#contructor_expresiones").find('.agregarContructor').tablaDinamica({
					 
					afterAddRow: function(row){
 						
				    	$(formulario).find('select[class="form-control cuando_id"], select[class="form-control operador_id"],  select[class="form-control aplicar"] ').chosen({width: '100%'}).trigger('chosen:updated');

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






		
		
	