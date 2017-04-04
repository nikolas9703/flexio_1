 $(function() {
	
	//Expotar Cliente a CSV
	$('#moduloOpciones ul').on("click", "#exportarPropiedadBtn", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
 		if( $("#tabla").size() == 0){ //Si la accion es desde el view de Proyectos
			exportarjQgrid();
		}
		else{ //Si la accion es desde la Lista de Propiedades
			if($('#tabla').is(':visible') == true){
				exportarjQgrid();
 			}else{ //Si se está exportando desde el Grid
 				exportarGrid();
	 		}
		}
   	});
	
	function exportarjQgrid() {
		//Exportar Seleccionados del jQgrid
		var propiedades = [];
		
		propiedades = $("#propiedadesGrid").jqGrid('getGridParam','selarrrow');
		
		var obj = new Object();
		obj.count = propiedades.length;
	
		if(obj.count) {
			
			obj.items = new Array();
			
			for(elem in propiedades) {
				//console.log(proyectos[elem]);
				var propiedad = $("#propiedadesGrid").getRowData(propiedades[elem]);
				
				//Remove objects from associative array
				delete propiedad['nombre_clear'];
				delete propiedad['options'];
				delete propiedad['link'];
				
				//Push to array
				obj.items.push(propiedad);
			}
			
			var json = JSON.stringify(obj);
			var csvUrl = JSONToCSVConvertor(json);
			var filename = 'propiedades_'+ Date.now() +'.csv';
			
			//Ejecutar funcion para descargar archivo
			downloadURL(csvUrl, filename);
			
			$('body').trigger('click');
		} 
	}
	 function exportarGrid(){
		 
		 var propiedades = [];
		 
		 $("#iconGrid").find('input[type="checkbox"]:checked').filter(function(){
			 propiedades.push(this.value);
			});
		 
			//Verificar si ha seleccionado algun proyecto
			if(propiedades.length==0){
				return false;
			}
			//Convertir array a srting separado por guion
			var propiedades_string = propiedades.join('-');
		
			//Armar url
			var url = phost() + 'propiedades/ajax-exportar/'+ propiedades_string;
			 
			downloadURL(url);
	}
	 
	
	//Alerta para eliminar proyectos
	$('#moduloOpciones ul').on("click", "#eliminarPropiedadBtn", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var propiedades = [];
		
	 
		if( $("#tabla").size() == 0){ //Si la accion es desde el view de Proyectos
			propiedades = $("#propiedadesGrid").jqGrid('getGridParam','selarrrow');
		}
		else{ //Si la accion es desde la Lista de Propiedades
			if($('#tabla').is(':visible') == true){
				propiedades = $("#propiedadesGrid").jqGrid('getGridParam','selarrrow');
 			}else{ //Si se está exportando desde el Grid
 				$("#iconGrid").find('input[type="checkbox"]:checked').filter(function(){
 					propiedades.push(this.value);
 				});
	 		}
		}
		
 		if(propiedades.length==0){
			return false;
		}
		var mensaje = (propiedades.length > 1)?'¿Esta seguro que desea eliminar estas Propiedades?':'¿Esta seguro que desea eliminar esta Propiedad?';
		
 		var footer_buttons = ['<div class="row">',
   		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
   		   		'<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>',
   		   '</div>',
   		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
   		   		'<button id="eliminarPropiedadBtn" class="btn btn-w-m btn-success btn-block" type="button">Confirmar</button>',
   		   '</div>',
   		   '</div>'
   		].join('\n');
   	    
   	    //Init boton de opciones
   		$('#optionsModal').find('.modal-title').empty().append('Confirme');
   		$('#optionsModal').find('.modal-body').empty().append(mensaje);
   		$('#optionsModal').find('.modal-footer').empty().append(footer_buttons);
   		$('#optionsModal').modal('show');
	});
	 $('#optionsModal').on("click", "#subirArchivosBtn", function(e){
		
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var uuid_propiedad = $(this).attr("data-propiedad");
		
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
			        	  uuid_relacion: uuid_propiedad,
			        	  modulo: 'propiedades' 
			          };
			    }
			     
		});
		  $('#input-dim-1').on('filebatchuploadcomplete', function(event, files, extra) {
				    $('#crearDocumentoModal').modal('hide');
					 //location.reload();
		});
		
	});
	//Accion que elimina Proyectos
	$('#optionsModal').on("click", "#eliminarPropiedadBtn", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		 
 		
		var propiedades = [];
		
 		if( $("#tabla").size() == 0){ //Si la accion es desde el view de Proyectos
			propiedades = $("#propiedadesGrid").jqGrid('getGridParam','selarrrow');
		}
		else{ //Si la accion es desde la Lista de Propiedades
			if($('#tabla').is(':visible') == true){
				propiedades = $("#propiedadesGrid").jqGrid('getGridParam','selarrrow');
 			}else{ //Si se está exportando desde el Grid
 				$("#iconGrid").find('input[type="checkbox"]:checked').filter(function(){
 					propiedades.push(this.value);
 				});
	 		}
		}
		

		$.ajax({
			url: phost() + 'propiedades/eliminar',
			data: {
				erptkn: tkn,
				id_propiedades: propiedades
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
				$("#propiedadesGrid").setGridParam({
					url: phost() + 'propiedades/ajax-listar-propiedades',
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
