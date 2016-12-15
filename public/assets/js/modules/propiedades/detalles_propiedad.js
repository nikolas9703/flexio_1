$(function(){
	//-------------------------
	// Boton de opciones
	//-------------------------
	$("#oportunidades").on("click", ".viewOptionsGrid", function(e){
 		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var options = $('#menu'+this.id).html();

 	    //Init boton de opciones
		//$('#optionsModal').find('.modal-title').empty().append('Opciones ('+ nombre_agencia +'):');
		$('#optionsModal').find('.modal-body').empty().append(options);
		$('#optionsModal').find('.modal-footer').empty();
		$('#optionsModal').modal('show');
	});
	
	$('#comentario_propiedad').on('keypress', function(e){
		if(e.which == 13) {
			
			var comentario = $(this).val();

			//Si no ha introducido ningun comentario: no guardar
			if(comentario == ""){
				return false;
			}
			
			$('.feed-activity-list').empty().append('<span style="color:#43B762;"><i class="fa fa-circle-o-notch fa-spin"></i> Cargando...</span>');
			
			//Actualizar Etapa Oportunidad
	    	$.ajax({
	    		url: phost() + 'propiedades/ajax-guardar-comentario-propiedad',
				data: {
					uuid_propiedad: uuid_propiedad,
					comentario: comentario,
					erptkn: tkn
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
				
				//limpiar campo
				$('#comentario_propiedad').val('');
				
				//Mostrar Mensaje
				mensaje_alerta(json.results[0]['mensaje'], $class_mensaje);
 				//cargar actividades
				cargar_actualizaciones();
			 
			});
			
	    }
	});

	
	 $('#moduloOpciones ul').on("click", "#editarPropiedadBtn", function(e){
		
 		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var url =  phost() + 'propiedades/ver-propiedad/'+uuid_propiedad; 
		$(location).attr('href',url);
   	}); 
 	
     var updatingChart = $(".updating-chart").peity("line", { fill: '#1ab394',stroke:'#169c81', width: 64 })

    setInterval(function() {
         var random = Math.round(Math.random() * 10)
         var values = updatingChart.text().split(",")
         values.shift()
         values.push(random)

         updatingChart
             .text(values.join(","))
             .change()
     }, 1000);
     
      //Funcion para reajustar el ancho de la tabla en los tabs
      $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
  		e.target // newly activated tab
  		e.relatedTarget // previous active tab
  		setTimeout(function(){
  			resizeJqGrid();
  		}, 300);
  	}); 
      
     
   	//Abrir ventana de Crear Actividad
  	 $("#actividades").on("click", "#crearActividad", function(e){
  		
   		e.preventDefault();
  		e.returnValue=false;
  		e.stopPropagation();
   		
   		//Desabilitar (Disable) campos en el modal de Crear Actividad
   	    $('#crearActividadModal').find('#uuid_clienteBtn, #uuid_sociedad, #uuid_contacto, #relacionado_con, #uuid_relacion').prop("disabled", "disabled").find('option:eq(0)').prop("selected", "selected");
   	    $('form#crearActividad').find('#uuid_sociedad').append('<option value="" selected="selected">Seleccione</option>');
   	    $('form#crearActividad').find('#uuid_contacto').append('<option value="" selected="selected">Seleccione</option>');
   		$('form#crearActividad').find('select[name="campo[uuid_cliente]"]').prop("disabled", "disabled");
   		setTimeout(function(){
            $(".chosen-select").chosen({
                width: '100%'
            }).trigger('chosen:updated');
        }, 500);
   		
    	
   		//Limpiar formulario por si acaso
 	    $('form#crearActividad').find('textarea[id="campo[apuntes]"]').empty();
	    $('form#crearActividad').find('input[id="campo[asunto]"], input[id="campo[fecha]"], input[id="campo[duracion]"]').prop("value", "");
	    $('form#crearActividad').find('#uuid_tipo_actividad option:eq(0), #uuid_asignado option:eq(0)').prop("selected", "selected"); 
	    $('form#crearActividad').find('textarea[id="campo[apuntes]"]').each(function() {
	        var name = $(this).attr('name');
	        CKEDITOR.instances[name].setData('');
	    });
	    
   	    //Llenar Datos en formulario segun la propiedad
   		var id_propiedad = $(this).attr("data-propiedad");
   		$('form#crearActividad').removeAttr("action");
	    $('form#crearActividad').find("#relacionado_con option:contains('Propiedades')").prop("selected", "selected");
	    $('form#crearActividad').find('#relacionado_con').attr("disabled", "disabled"); 
	    $('form#crearActividad').find('#uuid_relacion').empty().append('<option value="'+id_propiedad+'" selected="selected">'+nombre_propiedad+'</option>');
	    $('form#crearActividad').find('#cancelar, input[id="campo[guardar]"]').parent().remove();
		
	    //Por defecto asignado a debe ser la uuid_usuario logeada
		$('form#crearActividad').find('select[name*="campo[uuid_asignado]"] option[value="'+ uuid_usuario +'"]').prop('selected', 'selected');
  	  
  		$('#optionsModal').modal('hide');
  		$('#crearActividadModal').modal('show');
 	});
  	
  	/**
	 * 
	 * Listado de Nombres Comerciales dependiendo del cliente
	 * 
 	 */

	 $('form#crearActividad').on('change', 'select[name="campo[uuid_cliente]"]', function(e){
 
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var uuid_cliente = this.value;
   	 	$.ajax({
			url: phost() + 'clientes/ajax-listar-comerciales',
 			data: {
 				uuid_cliente: uuid_cliente,
	            erptkn: tkn
			},
			type: "POST",
			dataType: "json",
			cache: false,
		}).done(function(json) {

			  //Check Session
	        if( $.isEmptyObject(json.session) == false){
	            window.location = phost() + "login?expired";
	        }

	        //If json object is not empty.
	        if( $.isEmptyObject(json.results[0]) == false ){

	            $('#uuid_sociedad').empty().append('<option value="">Seleccione</option>').removeAttr('disabled');
	            $.each(json.results[0], function(i, result){
 	                $('#uuid_sociedad').append('<option value="'+ result['uuid_sociedad'] +'">'+ result['nombre_comercial'] +'</option>');
 	            });
	        }else{

	            $('#uuid_sociedad').empty().append('<option value="">Seleccione</option>').prop('disabled', 'disabled');
	        }
			 
		});	 
	}); 
	/**
	 * 
	 * Listado de Nombres de contactos dependiendo del contacto
	 * 
 	 */

 	$('form#crearActividad').on('change', 'select[name="campo[uuid_sociedad]"]', function(e){
 
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var uuid_sociedad = this.value;
   	 	$.ajax({
			url: phost() + 'clientes/ajax-listar-contactos-comerciales',
 			data: {
 				uuid_sociedad: uuid_sociedad,
	            erptkn: tkn
			},
			type: "POST",
			dataType: "json",
			cache: false,
		}).done(function(json) {

			  //Check Session
	        if( $.isEmptyObject(json.session) == false){
	            window.location = phost() + "login?expired";
	        }

	        //If json object is not empty.
	        if( $.isEmptyObject(json.results[0]) == false ){

	            $('#uuid_contacto').empty().append('<option value="">Seleccione</option>').removeAttr('disabled');
	            $.each(json.results[0], function(i, result){
 	                $('#uuid_contacto').append('<option value="'+ result['uuid_contacto'] +'">'+ result['****'] +'</option>');
 	            });
	        }else{

	            $('#uuid_contacto').empty().append('<option value="">Seleccione</option>').prop('disabled', 'disabled');
	        }
			 
		});	 
	}); 
  	
	//Guardar formulario de Actividad
	//Pasando cluientes a Naturales
	 $('#crearActividadModal').on("click", "#guardarActividadBtn", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		$('form#crearActividad').validate({
    		focusInvalid: true,
    		ignore: '',
    		wrapper: '',
    	});
		 $('form#crearActividad').find('select[id*="uuid_tipo_actividad"]').rules(
				 "add",{
					 required: true, 
					 messages: { required: 'Introduzca el tipo.' } 
		});
     	 if ($("form#crearActividad").valid() != false) {
     		 
     	 
     		 $('form#crearActividad').find('input:disabled, select:disabled').removeAttr("disabled");
		
			$.ajax({
				url: phost() + 'actividades/crear-actividad',
				data: $('form#crearActividad').serialize(),
				type: "POST",
				dataType: "json",
				cache: false,
			}).done(function(json) {
	 			 
				 if( $.isEmptyObject(json.session) == false){
					window.location = phost() + "login?expired";
				}
				
				//If json object is empty.
				if($.isEmptyObject(json.results) == true){
					return false;
				}
				
			 
				 
				 var class_mensaje = json.results[0] == true ? 'alert-success' : 'alert-danger';
				var mensaje = json.results[0] == true ? 'Se ha creado satisfactoriamente la actividad.' : 'Hubo un error tratando de crear la actividad';
				
				//Mostrar Mensaje
				mensaje_alerta(mensaje, class_mensaje); 
				//Ocultar Ventana
	 			$('#crearActividadModal').modal('hide');
	 			
	 			 location.reload();
			});
     	 }
	}); 
  	
   //Abrir ventana de Crear Actividad
	 $("#documentos").on("click", "#crearDocumento", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
 		 $('#optionsModal').modal('hide');
		$('#crearDocumentoModal').modal('show');
	});
	 
	 
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
 			 location.reload();
		});
	 
	 
 	 
});
//Cargar actualizaciones de la oportunidad seleccionada
function cargar_actualizaciones()
{
	 
	//Actualizar Etapa Oportunidad
	$.ajax({
		url: phost() + 'propiedades/ajax-listar-actualizaciones',
		data: {
			uuid_propiedad: uuid_propiedad,
			erptkn: tkn
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
		
		//Verificar si hay comentarios que mostrar
		$('.feed-activity-list').empty();

		//configurar momentjs a idioma espa�ol
		moment.locale('es');
		
		//recorrer arreglo de actualizaciones
		$.each(json.results[0], function(i,actividad) {       
			
			var tipo_actividad = actividad["tipo_actividad"] != "" ? actividad["tipo_actividad"] : " ";
			var nombre_oportunidad = (actividad["nombre"] != "" &&  actividad["nombre"]!= null) ? actividad["nombre"] : "";
			
 			var uuid_cliente = actividad["uuid_cliente"] != "" ? actividad["uuid_cliente"] : "";
			var nombre_cliente = actividad["nombre_cliente"] != "" ? actividad["nombre_cliente"] : "";
			var tipo_cliente = (actividad["tipo_cliente"] != "" &&  actividad["tipo_cliente"]!= null) ? actividad["tipo_cliente"] : " ";
			var creado_por = actividad["creado_por"] != "" ? actividad["creado_por"] : "";
			var nombre_contacto = actividad["nombre_contacto"] != "" ? actividad["nombre_contacto"] : "";
			var imagen_perfil_usuario_asignado = actividad["imagen_perfil_usuario_asignado"] != "" ? 'public/uploads/'+ actividad["imagen_perfil_usuario_asignado"] : "public/uploads/usuarios/18382257171431644103.png";
			var descripcion_actividad = actividad["descripcion"] != "" ? actividad["descripcion"] : "";
			var fecha_creacion = actividad["fecha_creacion"] != "" ? actividad["fecha_creacion"] : "";
			var cliente = '';
  			//Verificar Tipo de Cliente Para Armar el enlace hacia El perfil de cliente.
			
  				if(tipo_cliente.match(/juridico/g)){
  					
 					//Agregar el contacto del cliente juridico
 					cliente = nombre_contacto != "" && $.trim(nombre_contacto) != "" ? 'con <b>'+ nombre_contacto +'</b> ' : '';
 					cliente += nombre_cliente != "" && permiso_ver_cliente_juridico == 'true' ? 'de <a href="'+ phost() + 'clientes/ver-cliente-juridico/'+ uuid_cliente +'"><b>'+ nombre_cliente +'</b></a>' : "de "+ nombre_cliente;
					console.log("Enrr");
					
					
				}
				else if(tipo_cliente.match(/natural/g)){
				
					//Verificar si tiene permiso
					  cliente = nombre_cliente != "" && permiso_ver_cliente_natural == 'true' ? 'de <a href="'+ phost() + 'clientes/ver-cliente-natural/'+ uuid_cliente +'"><b>'+ nombre_cliente +'</b></a>' : "de "+ nombre_cliente;
				}
			 
 			
			//Verificar la preposicion segun
			//el nombre de tipo de actividad
 			if(tipo_actividad.match(/comentario/g)){
 				tipo_actividad = "un "+ tipo_actividad;
			}else{
 				tipo_actividad = "una "+ tipo_actividad;
 			}
 			
			 var html = ['<div class="feed-element">',
				'<img alt="perfil" class="img-circle pull-left" src="'+ phost() + imagen_perfil_usuario_asignado +'">',
				'<div class="media-body ">',
				'<small class="pull-right">'+ moment(fecha_creacion).fromNow() +'</small>',
 				'<strong>'+ creado_por +'</strong> a&ntilde;adio '+ tipo_actividad +' a la propiedad <strong>'+ nombre_oportunidad +'</strong> '+ cliente +'.<br>',
            	'<small class="text-muted">'+ moment(fecha_creacion).format("DD/MM/YY HH:mm") +'</small>',
				'<div class="well">'+ descripcion_actividad +'</div>',
					'<div class="pull-right"></div>',
				'</div>',
			'</div>'].join('\n'); 

            //Append actividad html
			$('.feed-activity-list').append(html);
		});
		
	});
}
function resizeJqGrid()
{
	$(".ui-jqgrid").each(function(){
		var w = parseInt( $(this).parent().width()) - 6;
		var tmpId = $(this).attr("id");
		var gId = tmpId.replace("gbox_","");
		$("#"+gId).setGridWidth(w);
	});
} 