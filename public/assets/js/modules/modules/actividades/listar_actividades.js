 $(function() {
	
	 $('#barra').on("click", ".btn-bitbucket", function(e){
	 		e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
 			 var tipo	= ($(this).attr("data-tipo")=='todos')?'':$(this).attr("data-tipo");
 			 
			$("#actividadesGrid").setGridParam({
					url: phost() + 'actividades/ajax-listar-actividades',
					datatype: "json",
					postData: {
		                nombre_contacto: '',
		                cliente: '',
		                telefono: '',
		                email: '',
		                fecha: '',
		                estado: '',
		                tipo: tipo,
						erptkn: tkn
					}
				}).trigger('reloadGrid');
				
				 
			
	 });
	 
	 //Funcion para subir documentos desde las opciones de actividades
	 $('#optionsModal').on("click", "#subirArchivosBtn", function(e){
			
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			
			var uuid_actividad = $(this).attr("data-actividad");
			
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
				        	  uuid_relacion: uuid_actividad,
				        	  modulo: 'actividades' 
				          };
				    }
				     
			});
			  $('#input-dim-1').on('filebatchuploadcomplete', function(event, files, extra) {
					    $('#crearDocumentoModal').modal('hide');
						 //location.reload();
			});
			
		});
	$('#optionsModal').on("click", "#cancelarActividadBtn", function(e){
 		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var uuid_actividad	=  $(this).attr("data-actividad");
		
  		//Ventana de Confirmacion
		var footer_buttons = ['<div class="row">',
  		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
  		   		'<button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button>',
  		   '</div>',
  		   '<div class="form-group col-xs-12 col-sm-6 col-md-6">',
  		   		'<button id="eliminarActividad"  data-actividad="'+ uuid_actividad +'"  class="btn btn-w-m btn-success btn-block" type="button">Confirmar</button>',
  		   '</div>',
  		   '</div>'
  		].join('\n');
		
		var mensaje ='¿Esta seguro que desea cancelar esta actividad?';
   	    //Init boton de opciones
  		$('#optionsModal').find('.modal-title').empty().append('Confirme');
  		$('#optionsModal').find('.modal-body').empty().append(mensaje);
  		$('#optionsModal').find('.modal-footer').empty().append(footer_buttons);
   		$('#optionsModal').modal('show');
	});
	
	//Pasando cluientes a Naturales
	$('#optionsModal').on("click", "#eliminarActividad", function(e){
		//var uuid_actividad	=  $(this).attr("data-actividad");
 		var uuid_actividad = $(this).attr('data-actividad');
 		
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
	 
 		$.ajax({
			url: phost() + 'actividades/ajax-cancelar-actividad',
			data: {
				erptkn: tkn,
				uuid_actividad: uuid_actividad
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
				$("#actividadesGrid").setGridParam({
					url: phost() + 'actividades/ajax-listar-actividades',
					datatype: "json",
					postData: {
						nombre_contacto: '',
						cliente: '',
						telefono: '',
						email: '',
						fecha: '',
						estado: '',
						erptkn: tkn
					}
				}).trigger('reloadGrid');
 
			}
		});
		
		//$('#optionsModal').modal('show');
 		$('#optionsModal').modal('hide');
	});

     //Expotar Actividades a CSV
     $('#moduloOpciones ul').on("click", "#exportarActividadesBtn", function(e){
         e.preventDefault();
         e.returnValue=false;
         e.stopPropagation();
         
         exportarjQgrid();
      });
     
     function exportarjQgrid() {
 		//Exportar Seleccionados del jQgrid
 		var actividades = [];
 		
 		actividades = $("#actividadesGrid").jqGrid('getGridParam','selarrrow');
 		
 		var obj = new Object();
 		obj.count = actividades.length;
 	
 		if(obj.count) {
 			
 			obj.items = new Array();
 			
 			for(elem in actividades) {
 				//console.log(proyectos[elem]);
 				var actividad = $("#actividadesGrid").getRowData(actividades[elem]);
 				
 				 delete actividad['Tipo'];
                 delete actividad['Con'];
                 delete actividad['linkactividad'];
                 delete actividad['options'];
                 delete actividad['link'];
 				
 				//Push to array
 				obj.items.push(actividad);
 			}
 			
 			var json = JSON.stringify(obj);
 			var csvUrl = JSONToCSVConvertor(json);
 			var filename = 'actividades_'+ Date.now() +'.csv';
 			
 			//Ejecutar funcion para descargar archivo
 			downloadURL(csvUrl, filename);
 			
 			$('body').trigger('click');
 		} 
 	}
 	//Init Bootstrap Calendar Plugin
     $('.input-date').datepicker({
         autoclose:true,
         startView: 1,
         changeMonth: true,
         format: 'dd-M-yyyy',
         todayHighlight: true
     });
});
 