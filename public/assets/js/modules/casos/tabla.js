$(function(){
     if(typeof uuid_cliente_casos === 'undefined'){ //Es x que no viene de Clientes o Actividades y debe estarblanco la variable
        var id_cliente = '';
    }
    else{
         var id_cliente = uuid_cliente_casos;
    }
     
 	var multiselect = window.location.pathname.match(/casos/g) ? true : false;
 	
     //Init Casos Grid
	$("#casosGrid").jqGrid({
	   	url: phost() + 'casos/ajax-listar-casos',
	   	datatype: "json",
	   	colNames:[
			'N&uacute;mero',
			'Asunto',
			'Cliente',
			'Fecha de Creaci&oacute;n',
			'Tipo',
			'Estado',
			'Asignado a',
            'Acci&oacute;n',
            '',
			'Acci&oacute;n',
		],
	   	colModel:[
            {name:'Numero', index:'cas.id_caso', width:30, sortable:true},
            {name:'Asunto', index:'cas.asunto', width:30, sortable:true},
            {name:'Cliente', index:'cl.nombre', width:30, sortable:true},
            {name:'Fecha', index:'cas.fecha_cracion', width:30, sortable:true},
            {name:'Tipo', index:'cat_tp.etiqueta', width:30, sortable:true},
            {name:'Estado', index:'cat_es.etiqueta', width:30, sortable:true},
            {name:'Asignado a', index:'usr.nombre', width:30, sortable:true},
            {name:'link', index:'link', width:30, align:"center", sortable:false, resizable:false, hidedlg:true},
            {name:'options', index:'options', hidedlg:true, hidden: true},
			{name:'linkcaso', index:'linkcaso', width:30, align:"center", sortable:false, resizable:false, hidedlg:true, hidden: true},
	   	],
		mtype: "POST",
	   	postData: {
            uuid_cliente: id_cliente,
	   		erptkn: tkn
        },
        firstsortorder: 'desc',
		height: "auto",
		autowidth: true,
		rowList: [10, 20,50,100],
		rowNum: 10,
		page: 1,
		pager: "#pagerCasos",
		loadtext: '<p>Cargando...',
		hoverrows: false,
	    viewrecords: true,
	    refresh: true,
	    gridview: true,
	    multiselect: multiselect,
	    sortname: 'cas.fecha_cracion',
	    sortorder: "ASC",
	    beforeProcessing: function(data, status, xhr){
	    	//Check Session
			if( $.isEmptyObject(data.session) == false){
				window.location = phost() + "login?expired";
			}
	    },
 
	    loadBeforeSend: function () {//propiedadesGrid_cb
	    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
	    	$(this).closest("div.ui-jqgrid-view").find("#casosGrid_cb, #jqgh_casosGrid_link").css("text-align", "center");
 		    //$(this).closest("div.ui-jqgrid-view").find("table.table table-striped ui-jqgrid-btable>tbody>tr>td").css("text-align", "right");
 
	    }, 
	    beforeRequest: function(data, status, xhr){},
            loadComplete: function(data){
			
                //check if isset data
                if( data['total'] == 0 ){
                        $('#gbox_casosGrid').hide();
                        $('.NoRecordsCasos').empty().append('No se encontraron casos.').css({"color":"#868686","padding":"30px 0 0"}).show();
                }
                else{
                        $('.NoRecordsCasos').hide();
                        $('#gbox_casosGrid').show();
                }

                //---------
                // Cargar plugin jquery Sticky Objects
                //----------
                //add class to headers
                
                if(multiselect == true)
    			{
                	$("#casosGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");

                    //floating headers
                    $('#gridHeader').sticky({
                        getWidthFrom: '.ui-jqgrid-view', 
                        className:'jqgridHeader'
                    });

                    //Arreglar tamaÃ±o de TD de los checkboxes
                    $("#casosGrid_cb").css("width","50px");
                    $("#casosGrid tbody tr").children().first("td").css("width","50px");

    			}
                                
                //Mostrar asunto al abrir el modal
                $(".viewOptions").on("click" ,function(){
                    var i=0;
                    var asunto = "";
                    $(this).parent().parent().find("td").each(function(){
                        if(i==2)
                        {
                            asunto = $(this).html();
                        }
                        i++;
                    });
                    $(".modal-title").html("Opciones: "+asunto);
                });
            },
            onSelectRow: function(id){
                    $(this).find('tr#'+ id).removeClass('ui-state-highlight');
            },
	});

    $("#casosGrid").jqGrid('columnToggle');
	
	//-------------------------
	// Redimensioanr Grid al cambiar tamaño de la ventanas.
	//-------------------------
	$(window).resizeEnd(function() {
		$(".ui-jqgrid").each(function(){
			var w = parseInt( $(this).parent().width()) - 6;
			var tmpId = $(this).attr("id");
			var gId = tmpId.replace("gbox_","");
			$("#"+gId).setGridWidth(w);
		});
	});
	/**
     * Verificar si existe en dropdown #moduloOpciones
     * el enlace de Crear Contacto.
     * 
     * Pantalla: Editar Cliente Natural
     * Modulo: Clientes
     */
    if($.isEmptyObject($('#moduloOpciones ul li').find('a[id="crearCasoLnk"]')) == false)
    {
      	//Mostrar Panel de Crear Casos
		$('#moduloOpciones ul').on("click", "#crearCasoLnk", function(e){     

   			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
 			
  			
 			//Ocultar Panel de Datos del Cliente
			$('.editarFormularioClientes, .editarFormularioClientesNaturales').addClass('hide');
			
			//mostrar formulario de editar oportunidad
			$('#sub-panel-grid-modulos').find('.panel-heading').find('.sub-panel-dropdown-contenido').find('a[href*="crearCasos"]').trigger('click');
			$('#sub-panel-grid-modulos').find('.panel-heading').find('.sub-panel-dropdown-contenido').find('a[href*="tablaCasos"]').closest('li.dropdown').find('a[data-toggle="dropdown"]').trigger('click');
			
  			setTimeout(function(){
 				//Desabilitar dropdown de cliente
				$('form#crearCaso').find('select[name="campo[uuid_cliente]"]').attr("disabled", "disabled").find('option[value="'+ id_cliente +'"]').prop("selected", "selected");
 				$(".chosen-select").chosen({
				    width: '100%'
				}).trigger('chosen:updated');
 			}, 300);
			
  			//Campos por defecto al crear el caso
			$('form#crearCaso').find('select[name*="campo[id_asignado]"] option[value="'+ uuid_usuario  +'"]').prop('selected', 'selected');
  			$('form#crearCaso').find('button [id="uuid_clienteBtn"]').attr("disabled", "disabled");
 			$('form#crearCaso').find('#uuid_clienteBtn').addClass("disabled").attr("disabled", "disabled");
 			
			//Pasar focus al body
			$('body').click();   
		});
    }
  
    
    //Asignamer el caso
    $("#optionsModal").on("click", "#asignarmeCaso", function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        
        var uuid_caso = $(this).attr('data-caso');
        //asignarme($id_caso
        $.ajax({
			url: phost() + 'casos/ajax-asignarme-caso',
			data: {
				uuid_caso: uuid_caso,
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
			if($.isEmptyObject(json.results) == true){
				return false;
			}
			
			//Mostrar Mensaje
			$class_mensaje = json.results[0]['respuesta'] == true ? 'alert-success' : 'alert-danger';
			mensaje_alerta(json.results[0]['mensaje'], $class_mensaje);
			
			 
	        $("#casosGrid").setGridParam({
	            url: phost() + 'casos/ajax-listar-casos',
	            datatype: "json",
	            postData: {
 	                erptkn: tkn
	            }
	        }).trigger('reloadGrid');
			
		});
        
        $('#optionsModal').modal('hide');
    });
    
 
	//Abrir ventana de Crear Actividad
	$("#optionsModal").on("click", "#crearActividad", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		//Desabilitar campos en el modal de Crear Actividad
 		$('#crearActividadModal').find(' #uuid_clienteBtn , #uuid_contacto, #uuid_sociedad, #relacionado_con, #uuid_relacion').find('option:eq(0)').prop("selected", "selected");

 		  //Limpiar formulario por si acaso
 	    $('form#crearActividad').find('textarea[id="campo[apuntes]"]').empty();
	    $('form#crearActividad').find('input[id="campo[asunto]"], input[id="campo[fecha]"], input[id="campo[duracion]"]').prop("value", "");

	    $('form#crearActividad').find('#uuid_tipo_actividad option:eq(0), #uuid_asignado option:eq(0)').prop("selected", "selected"); 
	    $('form#crearActividad').find('textarea[id="campo[apuntes]"]').val();
		
	    //llenar Datos de la oportunidad seleccionada
		var uuid_caso = $(this).attr("data-caso");
		
	    
 	    //setTimeout(function(){
	  		$('#optionsModal').modal('hide');
			$('#crearActividadModal').modal({
				keyboard: false,
				backdrop: 'static'
			}).modal('show');
 	    //}, 1000);
 	    
 	    //Al mostrar contenido de modal
		$('#crearActividadModal').on('shown.bs.modal', function (e) {
			
 			$('form#crearActividad').find('#uuid_oportunidad').attr("disabled", "disabled").find('option[value=""]').prop("selected", "selected");
			$('form#crearActividad').find('#uuid_tipo_actividad1').attr("checked", "checked");
			$('form#crearActividad').find('#uuid_tipo_actividad1').closest('label').addClass('active');
			$('form#crearActividad').find('select[name*="campo[uuid_asignado]"] option[value="'+ uuid_usuario +'"]').prop('selected', 'selected');
			$('#hidden_uuid_oportunidad').val(uuid_caso);
			$('form#crearActividad').find('#cancelar, input[id="campo[guardar]"]').parent().remove();
		
			$('form#crearActividad').find(".chosen-select").chosen({
				width: '100%'
			}).trigger('chosen:updated');
		});
 	});
	
	
	//Guardar formulario de Actividad
	//Pasando cluientes a Naturales
	$('#crearActividadModal').on("click", "#guardarActividadBtn", function(e){
		
 		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		 
 		if($('#crearActividadModal').find('#crearActividad').validate().form() == true)
		{
 			//Habilitar campos ocultos
  			$("form#crearActividad").find('input:hidden, input:disabled, select:disabled, select:hidden, textarea').removeAttr('disabled');
			var uuid_caso = $('#hidden_uuid_oportunidad').val();
			
			
			 $('#crearActividadModal').modal('hide');
			 
				var mensaje =  'Se ha creado la actividad satisfactoriamente.' ;
				var class_mensaje =   'alert-success' ;
				//Mostrar Mensaje
				mensaje_alerta(mensaje, class_mensaje);
				
 			  $.ajax({
					url: phost() + 'actividades/ajax-crear-actividad-modal',
					data: $('form#crearActividad').serialize()+'&campo[modulo_relacion]=casos&campo[uuid_relacion]='+uuid_caso,
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
					var mensaje = json.results[0] == true ? 'Se ha creado la actividad satisfactoriamente.' : 'Hubo un error tratando de crear la actividad.';
					
					//Mostrar Mensaje
					mensaje_alerta(mensaje, class_mensaje);
  					
 					 
				 });
			 
		} 
	});
	
    if(multiselect == false){
    	
     	//Abrir ventana de Crear Actividad
        $("#optionsModal").on("click", "#crearActividadCasoClientes", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();
            
            
             
            if($(this).attr('href') == "#"){

            	var uuid_caso = $(this).attr('data-caso');
            	 
                //ocultar vista de cliente
                $('.editarFormularioClientes, .editarFormularioClientesNaturales').addClass('hide');

                //mostrar formulario de editar contacto
                $('#sub-panel-grid-modulos').find('.panel-heading').find('.sub-panel-dropdown-contenido').find('a[href*="crearActividades"]').trigger('click');
    			$('#sub-panel-grid-modulos').find('.panel-heading').find('.sub-panel-dropdown-contenido').find('a[href*="tablaActividades"]').closest('li.dropdown').find('a[data-toggle="dropdown"]').trigger('click');

                //ocultar modal
                $('#optionsModal').modal('hide');
            	
        		 
       		 	$('form#crearActividad').find('input[id="campo[guardar]"]').attr("data-caso", uuid_caso);
       		 	$('form#crearActividad').find('#uuid_oportunidad').attr("disabled", "disabled").find('optio option:eq(0)').prop("selected", "selected");
				
				$(".chosen-select").chosen({
				    width: '100%'
				}).trigger('chosen:updated');
				
				
       		 	/*$('form#crearActividad').find('#uuid_oportunidad option:eq(0)').attr("disabled", "disabled").prop("selected", "selected");
				
				$(".chosen-select").chosen({
				    width: '100%'
				}).trigger('chosen:updated');
				
				*/
       		//	$('form#crearActividad').find('#uuid_oportunidad').attr("disabled", "disabled").find('option[value="'+ uuid_oportunidad +'"]').prop("selected", "selected");
 	
       		/* $('#crearActividad').attr("disabled", "disabled").find('#uuid_oportunidad option:eq(0)').prop("selected", "selected");
				$(".chosen-select").chosen({
				    width: '100%'
				}).trigger('chosen:updated');*/
				
              //  popular_detalle_actividad_caso(uuid_caso); 
             }
        });
    	
    	//Abrir ventana de Crear Contacto
        $("#optionsModal").on("click", "#verCaso", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();
            
            
             
            if($(this).attr('href') == "#"){

            	var uuid_caso = $(this).attr('data-caso');
            	 
                //ocultar vista de cliente
                $('.editarFormularioClientes, .editarFormularioClientesNaturales').addClass('hide');

                //mostrar formulario de editar contacto
                $('#sub-panel-grid-modulos').find('.panel-heading').find('.sub-panel-dropdown-contenido').find('a[href*="editarCasos"]').trigger('click');

                //ocultar modal
                $('#optionsModal').modal('hide');

                popular_detalle_caso(uuid_caso); 
             }
        });
    }
  
 
    
	//-------------------------
	// Boton de opciones
	//-------------------------
	$("#casosGrid").on("click", ".viewOptions", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		var caso = $(this).attr("data-caso");
		var rowINFO = $("#casosGrid").getRowData(caso);
	    var options = rowINFO["options"];

	    //Init boton de opciones
		//$('#optionsModal').find('.modal-title').empty().append('Opciones ('+ nombre_agencia +'):');
		$('#optionsModal').find('.modal-body').empty().append(options);
		$('#optionsModal').find('.modal-footer').empty();
		$('#optionsModal').modal('show');
	});

});


function popular_detalle_caso(uuid_caso)
{
    if(uuid_caso == ""){
        return false;
    }

    $.ajax({
        url: phost() + 'casos/ajax-seleccionar-caso',
        data: {
            erptkn: tkn,
            uuid_caso: uuid_caso
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
         //Limpiar formulario
        $('form#editarCaso').find('input[type="text"], input[type="checkbox"], textarea').prop("value", "");
        
        //poner uuid_contacto en el boton de guardar
        $('form#editarCaso').find('input[id="campo[guardar]"]').attr("data-caso", uuid_caso);
        
        var uuid_cliente = json.results[0]["uuid_cliente"] != "" ? json.results[0]["uuid_cliente"] : "";
        var fecha_cracion = json.results[0]["fecha_cracion"] != "" ? json.results[0]["fecha_cracion"] : "";
        var fecha_modificacion = json.results[0]["fecha_modificacion"] != "" ? json.results[0]["fecha_modificacion"] : "";
        var id_estado = json.results[0]["id_estado"] != "" ? json.results[0]["id_estado"] : "";
        var asunto = json.results[0]["asunto"] != "" ? json.results[0]["asunto"] : "";
        var id_tipo = json.results[0]["id_tipo"] != "" ? json.results[0]["id_tipo"] : "";
        var resolucion = json.results[0]["resolucion"] != "" ? json.results[0]["resolucion"] : "";
        var comentarios = json.results[0]["comentarios"] != "" ? json.results[0]["comentarios"] : "";
        var descripcion = json.results[0]["descripcion"] != "" ? json.results[0]["descripcion"] : "";
        var id_asignado = json.results[0]["id_asignado"] != "" ? json.results[0]["id_asignado"] : "";
 
        
        setTimeout(function(){
				//Desabilitar dropdown de cliente
			$('form#editarCaso').find('select[name="campo[uuid_cliente]"]').attr("disabled", "disabled").find('option[value="'+ uuid_cliente +'"]').prop("selected", "selected");
				$(".chosen-select").chosen({
			    width: '100%'
			}).trigger('chosen:updated');
			}, 300);
        $('form#editarCaso').find('#uuid_clienteBtn').addClass("disabled").attr("disabled", "disabled");
        $('form#editarCaso').find('input[id="campo[fecha_cracion]"]').prop("value", fecha_cracion);
        $('form#editarCaso').find('input[id="campo[fecha_modificacion]"]').prop("value", fecha_modificacion);
        $('form#editarCaso').find('select[name="campo[id_estado]"] option[value="'+ id_estado +'"]').attr('selected', 'selected');
        $('form#editarCaso').find('input[id="campo[asunto]"]').prop("value", asunto);
        $('form#editarCaso').find('select[name="campo[id_tipo]"] option[value="'+ id_tipo +'"]').attr('selected', 'selected');
        $('form#editarCaso').find('textarea[name="campo[resolucion]"]').val(resolucion);
        $('form#editarCaso').find('textarea[name="campo[comentarios]"]').val(comentarios);
        $('form#editarCaso').find('textarea[name="campo[descripcion]"]').val(descripcion);
        $('form#editarCaso').find('select[name="campo[id_asignado]"] option[value="'+ id_asignado +'"]').attr('selected', 'selected');
 
    });
}
  
