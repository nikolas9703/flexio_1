 $(function() {
	
	//Expotar Cliente a CSV
	$('#moduloOpciones ul').on("click", "#exportarProyectoBtn", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var proyectos = [];
  
		if($('#tabla').is(':visible') == true){
			
				//Exportar Seleccionados del jQgrid
				proyectos = $("#proyectosGrid").jqGrid('getGridParam','selarrrow');
				
				var obj = new Object();
				obj.count = proyectos.length;
			
				if(obj.count) {
					
					obj.items = new Array();
					
					for(elem in proyectos) {
 						var proyecto = $("#proyectosGrid").getRowData(proyectos[elem]);
						
						//Remove objects from associative array
						delete proyecto['Disponibles'];
						delete proyecto['nombre_clear'];
						delete proyecto['options'];
						delete proyecto['link'];
						delete proyecto['link_seleccionar'];
						
						//Push to array
						obj.items.push(proyecto);
					}
					
					var json = JSON.stringify(obj);
					var csvUrl = JSONToCSVConvertor(json);
					var filename = 'proyectos_'+ Date.now() +'.csv';
					
					//Ejecutar funcion para descargar archivo
					downloadURL(csvUrl, filename);
					
					$('body').trigger('click');
			} 
 
		}
		else{
 			
			$("#iconGrid").find('input[type="checkbox"]:checked').filter(function(){
				proyectos.push(this.value);
			});
 		 
 			//Verificar si ha seleccionado algun proyecto
			if(proyectos.length==0){
				return false;
			}
 			//Convertir array a srting separado por guion
			var proyectos_string = proyectos.join('-');
		
 			//Armar url
			var url = phost() + 'proyectos/ajax-exportar/'+ proyectos_string;
			 
			downloadURL(url);
			//Ejecutar funcion pa
		}
  	});
	
	
	 $('#optionsModal').on("click", "#subirArchivosBtn", function(e){
			
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			var uuid_proyecto = $(this).attr("data-proyecto");
			
			$('#optionsModal').modal('hide');
			$('#crearDocumentoModal').modal('show');
				 
				 
			 $("#input-dim-1").fileinput({
				    uploadUrl:  phost() + "documentos/ajax-subir-archivos",
				    allowedFileExtensions: null,
				    minImageWidth: 50,
				    minImageHeight: 50,
				    uploadAsync: true,
				    maxFileSize: 500,
				    language: 'es',
				    uploadExtraData: function() {
				          return {
				        	  erptkn: tkn,
				        	  uuid_relacion: uuid_proyecto,
				        	  modulo: 'proyectos' 
				          };
				    }
				     
			});
			  $('#input-dim-1').on('filebatchuploadcomplete', function(event, files, extra) {
					    $('#crearDocumentoModal').modal('hide');
						 //location.reload();
			});
			
		});
	 
	
	//Alerta para eliminar proyectos
	$('#moduloOpciones ul').on("click", "#eliminarProyectoBtn", function(e){
		
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var proyectos = [];
		
		if($('#tabla').is(':visible') == true){
				proyectos = $("#proyectosGrid").jqGrid('getGridParam','selarrrow');
		}else{
			
			$("#iconGrid").find('input[type="checkbox"]:checked').filter(function(){
				proyectos.push(this.value);
			});
  		}
		
		//var proyectos = $("#proyectosGrid").jqGrid('getGridParam','selarrrow');
		if(proyectos.length==0){
			return false;
		}
		var mensaje = '¿Esta seguro que desea eliminar este Proyecto?';
		if(proyectos.length > 1){
			var mensaje = '¿Esta seguro que desea eliminar estos Proyectos?';
		}
 		var footer_buttons = ['<div class="row">',
   		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
   		   		'<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>',
   		   '</div>',
   		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
   		   		'<button id="eliminarProyectoBtn" class="btn btn-w-m btn-success btn-block" type="button">Confirmar</button>',
   		   '</div>',
   		   '</div>'
   		].join('\n');
   	    
   	    //Init boton de opciones
   		$('#optionsModal').find('.modal-title').empty().append('Confirme');
   		$('#optionsModal').find('.modal-body').empty().append(mensaje);
   		$('#optionsModal').find('.modal-footer').empty().append(footer_buttons);
   		$('#optionsModal').modal('show');
	});
	
	//Accion que eliminana Proyectos
	$('#optionsModal').on("click", "#eliminarProyectoBtn", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var proyectos = [];
		
		if($('#tabla').is(':visible') == true){
				proyectos = $("#proyectosGrid").jqGrid('getGridParam','selarrrow');
		}else{
			
			$("#iconGrid").find('input[type="checkbox"]:checked').filter(function(){
				proyectos.push(this.value);
			});
  		}
		 
 		$.ajax({
			url: phost() + 'proyectos/eliminar',
			data: {
				erptkn: tkn,
				id_proyectos: proyectos
			},
			type: "POST",
			dataType: "json",
			cache: false,
		}).done(function(json) {

			//Check Session
			if( $.isEmptyObject(json.session) == false){
				window.location = phost() + "login?expired";
			}
			
			//If json object is empty.
			if($.isEmptyObject(json.results[0]) == true){
				return false;
			}
			
			$class_mensaje = json.results[0]['respuesta'] == true ? 'alert-success' : 'alert-danger';
			
			//Mostrar Mensaje
			mensaje_alerta(json.results[0]['mensaje'], $class_mensaje);
			
			//Recargar grid si la respuesta es true
			if(json.results[0]['respuesta'] == true)
			{
				//Recargar Grid
				$("#proyectosGrid").setGridParam({
					url: phost() + 'proyectos/ajax-listar-proyectos',
					datatype: "json",
					postData: {
						nombre: '',
						compania: '',
						telefono: '',
						correo: '',
						erptkn: tkn
					}
				}).trigger('reloadGrid');
				
				if($('#tabla').is(':visible') != true)
					location.reload();
 
			}
		});
	    
	    //Ocultar ventana
	    $('#optionsModal').modal('hide');
	});
	
});