$(function() {

 
   $(".editar-actividad-pipeline").on("click",function() {

        $.ajax({
            url: phost() + 'actividades/ajax-seleccionar-informacion-de-actividad',
            data: {
                id_actividad:$(this).attr("data-uuid-actividad"),
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

 
            var uuid_sociedad=json.results[0].uuid_sociedad;
            var uuid_contacto=json.results[0].uuid_contacto;
            var uuid_relacion=json.results[0].uuid_relacion;
            var uuid_actividad=json.results[0].uuid_actividad;


   		    
   		    $('#crearActividadModal').find('#uuid_clienteBtn').prop("disabled", "disabled").find('option:eq(0)').prop("selected", "selected");
      	    $('form#crearActividad').find('select[name="campo[uuid_cliente]"]').append('<option value="'+  json.results[0].uuid_cliente +'" selected="selected">'+ json.results[0].nombre_cliente +'</option>').prop("disabled", "disabled");

     		setTimeout(function(){
    	           $(".chosen-select").chosen({
    	               width: '100%'
    	           }).trigger('chosen:updated');
    			}, 500);  

            $.ajax({
                url: phost() + 'oportunidades/ajax-seleccionar-cliente-sociedades',
                data: {
                    uuid_cliente: json.results[0].uuid_cliente,
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
                        if(uuid_sociedad==result['uuid_sociedad']){
                            $('#uuid_sociedad').append('<option selected value="'+ result['uuid_sociedad'] +'">'+ result['nombre_comercial'] +'</option>');
                        }
                        else{
                            $('#uuid_sociedad').append('<option value="'+ result['uuid_sociedad'] +'">'+ result['nombre_comercial'] +'</option>');
                        }

                    });
                }else{

                    $('#uuid_sociedad').empty().append('<option value="">Seleccione</option>').prop('disabled', 'disabled');
                }

            });



            $.ajax({
                url: phost() + 'oportunidades/ajax-seleccionar-cliente-contactos',
                data: {
                    uuid_cliente: json.results[0].uuid_cliente,
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
                        if(uuid_contacto==result['uuid_contacto']){
                            $('#uuid_contacto').append('<option selected value="'+ result['uuid_contacto'] +'">'+ result['nombre_contacto'] +'</option>');
                        }
                        else{
                            $('#uuid_contacto').append('<option value="'+ result['uuid_contacto'] +'">'+ result['nombre_contacto'] +'</option>');
                        }
                    });
                }else{

                    $('#uuid_contacto').empty().append('<option value="">Seleccione</option>').prop('disabled', 'disabled');
                }

            });


            $.ajax({
                url: phost() + 'actividades/ajax-listar-elementos-modulo',
                data: {
                    id_cat: json.results[0].relacionado_con,
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

                    $('#uuid_relacion').empty().append('<option value="">Seleccione</option>').removeAttr('disabled');
                    $.each(json.results[0], function(i, result){
                        if(uuid_relacion==result['uuid']){
                            $('#uuid_relacion').append('<option selected value="'+ result['uuid'] +'">'+ result['etiqueta'] +'</option>');
                        }
                        else{
                            $('#uuid_relacion').append('<option value="'+ result['uuid'] +'">'+ result['etiqueta'] +'</option>');
                        }
                    });
                }else{

                    $('#uuid_relacion').empty().append('<option value="">Seleccione</option>').prop('disabled', 'disabled');
                }

            });

            $('#relacionado_con').val(json.results[0].relacionado_con);
            $('input[id*="asunto"]').val(json.results[0].asunto);
            $('#uuid_tipo_actividad').val(json.results[0].uuid_tipo_actividad);
            $('input[id*="fecha"]').val(json.results[0].fecha);
            $('input[id*="duracion"]').val(json.results[0].duracion);
            $('textarea[id*="apuntes"]').val(json.results[0].apuntes);
            $('select[id*="uuid_asignado"]').val(json.results[0].uuid_asignado);
            if(json.results[0].completada==1){
                $('input[id*="completada"]').click();
            }

            $('form#crearActividad').find('input[id*="guardar"]').parent().replaceWith('<div class="form-group col-xs-12 col-sm-6 col-md-6"><button data-uuid="'+uuid_actividad+'" id="editarActividadBtn" class="btn btn-w-m btn-primary btn-block" type="button">Guardar</button></div>');
            $('form#crearActividad').find('a[id="cancelar"]').parent().replaceWith('<div class="form-group col-xs-12 col-sm-6 col-md-6"><button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button></div>');


        });


    	$('#optionsModal').modal('hide');
        $('#crearActividadModal').modal('show');


    });
  

	//Seleccionar boton filtro mes default
	$('#filtroGroupBtns').find('#mes').addClass('active');
	
	//Al cargar la pagina
	//Calcular la suma de los montos
	//por etapa
	calcular_actividades_mes();
    calcular_estado_actividades();

	//Poner todas los div del mismo tama�o
	$('.widget-content').matchHeight({
		byRow: false,
	    property: 'height'
	});
	
    var element = '[class*="d-colummActividades"]';
    var handle = ".ibox-content";
    var connect = '[class*=d-colummActividades]';
    $(element).sortable({
    	helper: "clone",
    	handle: handle,
        connectWith: connect,
        containment: $('.d-colummActividades').closest('div.row'),
        tolerance: 'pointer',
        forcePlaceholderSize: true,
        opacity: 0.8,
        cursor: "move",
     }).bind('sortstart', function(e, ui) {
	    /*
	    This event is triggered when the user starts sorting and the DOM position has not yet changed.

	    ui.item contains the current dragged element.
	    ui.startparent contains the element that the dragged item comes from
	    */
    	 
    	 //Remover la animacion al hacer drag and drop
    	 $('.crm-widget').removeClass('animated fadeInUp');
    	 
     }).bind('sortstop', function(e, ui) {
         /*
         This event is triggered when the user stops sorting. The DOM position may have changed.

         ui.item contains the element that was dragged.
         ui.startparent contains the element that the dragged item came from.
         */
    	 
         var mes_anterior = $(this).attr('data-mes');
         var mes_actual = $(ui.item).parent().attr('data-mes');
         var uuid_actividad = $(ui.item).attr('data-uuid-actividad');

    	 //Calcular Montos
        calcular_actividades_mes();
        calcular_estado_actividades();
    	 
    	 //Actualizar Etapa Oportunidad
    	$.ajax({
    		url: phost() + 'actividades/ajax-actualizar-mes-actividad',
			data: {
                mes_actual: mes_actual,
                uuid_actividad: uuid_actividad,
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
			if(json['results'][0] == true){
				location.reload();
			}
		});
     });
    
    //Botones de Filtro por Fechas
    //Dia / Semana / Mes
   /* $('#filtroGroupBtns').on('click', 'a', function(e){
    	console.log("Any");
    	e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		//Remover clase de activo
		$('#filtroGroupBtns a').removeClass('active');
		
		//Activar el boton presionado
		$('#filtroGroupBtns').find('a[id="'+ this.id +'"]').addClass('active');
		
		//Filtrar Oportunidades
		//filtrar_oportunidades_por_periodo();
    });
    */
    //Filtro por tipo de Actividad
    $('#moduloOpciones').on("click", '.dropdown-menu a', function(e){
    	e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
    	
		//Filtrar Oportunidades
		var tipo_actividad = this.id;
		
		//Recorrer las etapas
		$.each($('.d-colummActividades'), function(i, _etapa_){
			//Recorrer todas las oportunidades
			$.each($(this).find('.crm-widget'), function(i, etapa_){
				$(this).attr('data-actividad') == tipo_actividad ? ( $(this).hasClass('fadeOutDown') == true ? $(this).removeClass('animated fadeOutDown').addClass('animated fadeInUp') : '' ) : $(this).removeClass('animated fadeInUp').addClass('animated fadeOutDown');
			});
		});
		
		//Cerrar Dropdown
		$('body').trigger('click');
    });
    
    //Abrir ventana de Crear Actividad
    $(".content-widget").on("click", "a.crearActividadBtn", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		//Obtener Informacion de la Oportunidad
		var id_oportunidad 		= $(this).closest('.crm-widget').attr("data-oportunidad");
	    var id_cliente 			= $(this).closest('.crm-widget').attr("data-id-cliente");
	    var id_sociedad 		= $(this).closest('.crm-widget').attr("data-id-sociedad");
	    var id_contacto 		= $(this).closest('.crm-widget').attr("data-id-contacto");
	    var nombre_sociedad 	= $(this).closest('.crm-widget').attr("data-nombre-comercial");
	    var nombre_contacto 	= $(this).closest('.crm-widget').attr("data-nombre-contacto");
	    var nombre_oportunidad 	= $(this).closest('.crm-widget').attr("data-nombre-oportunidad");
	    
	    //Informacion de la Actividad
	    var uui_actividad = $(this).attr("data-id-actividad");
	    var asunto 	= $(this).attr("data-asunto");
	    var uuid_tipo_actividad = $(this).attr("data-id-tipo-actividad");
	    var apuntes = $(this).attr("data-apuntes");
	    var fecha_actividad = $(this).attr("data-fecha-actividad");
	    var duracion = $(this).attr("data-duracion");
	    var id_asignado = $(this).attr("data-id-asignado");
	    
	    //Desabilitar campos en el modal de Crear Actividad
		$('#crearActividadModal').find('#uuid_cliente, #uuid_clienteBtn, #uuid_sociedad, #uuid_contacto, #relacionado_con, #uuid_relacion').prop("disabled", "disabled").find('option:eq(0)').prop("selected", "selected");
		
	    //Limpiar formulario
	    $('form#crearActividad').find('textarea[id="campo[apuntes]"]').empty();
	    $('form#crearActividad').find('input[id="campo[asunto]"], input[id="campo[fecha]"], input[id="campo[duracion]"]').prop("value", "");
	    $('form#crearActividad').find('#uuid_tipo_actividad option:eq(0), #uuid_asignado option:eq(0)').prop("selected", "selected"); 

	    //Verificar si existe el campo uuid_actividad
	    if($('form#crearActividad').find('#uuid_actividad').attr('id') == undefined){
	    	$('form#crearActividad').append('<input type="hidden" id="uuid_actividad" name="uuid_actividad" value="'+ uui_actividad +'" />');
	    }else{
	    	$('form#crearActividad').find('#uuid_actividad').prop("value", uui_actividad);
	    }
	    
	    //Llenar Datos de la oportunidad seleccionada
    	$('form#crearActividad').removeAttr("action");
    	$('form#crearActividad').find('#uuid_cliente option[value="'+ id_cliente +'"]').prop("selected", "selected"); 
    	$('form#crearActividad').find('#uuid_sociedad').empty().append('<option value="'+ id_sociedad +'" selected="selected">'+ nombre_sociedad +'</option>');
    	$('form#crearActividad').find('#uuid_contacto').empty().append('<option value="'+ id_contacto +'" selected="selected">'+ nombre_contacto +'</option>');
    	$('form#crearActividad').find("#relacionado_con option:contains('Oportunidades')").prop("selected", "selected");
    	$('form#crearActividad').find('#uuid_relacion').empty().append('<option value="'+ id_oportunidad +'" selected="selected">'+ nombre_oportunidad +'</option>');
    	$('form#crearActividad').find('input[id="campo[asunto]"]').prop("value", asunto);
    	$('form#crearActividad').find('#uuid_tipo_actividad option[value="'+ uuid_tipo_actividad +'"]').prop("selected", "selected");
    	$('form#crearActividad').find('textarea[id="campo[apuntes]"]').empty().append(apuntes).each(function() {
	        var name = $(this).attr('name');
	        CKEDITOR.instances[name].setData(apuntes);
	    });
    	$('form#crearActividad').find('input[id="campo[fecha]"]').prop("value", fecha_actividad);
    	$('form#crearActividad').find('input[id="campo[duracion]"]').prop("value", duracion);
    	$('form#crearActividad').find('#uuid_asignado option[value="'+ id_asignado +'"]').prop("selected", "selected");
    	$('form#crearActividad').find('#cancelar, input[id="campo[guardar]"]').parent().remove();
		
		$('#optionsModal').modal('hide');
		$('#crearActividadModal').modal('show');
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
		 console.log($("form#crearActividad").valid() );
		 
     	 if ($("form#crearActividad").valid() != false) {
	     		$('form#crearActividad').find('input:disabled, select:disabled').removeAttr("disabled");
	    		
	    		var uuid_actividad = $('form#crearActividad').find('#uuid_actividad').val();
	    		
	    		$.ajax({
	    			url: phost() + 'actividades/ver-actividad/'+ uuid_actividad,
	    			data: $('form#crearActividad').serialize(),
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
	    			
	    			var class_mensaje = json.results[0] == true ? 'alert-success' : 'alert-danger';
	    			var mensaje = json.results[0] == true ? 'Se ha modificado satisfactoriamente la actividad.' : 'Hubo un error tratando de modificar la actividad';
	    			
	    			//Mostrar Mensaje
	    			mensaje_alerta(mensaje, class_mensaje);
	
	    			//Ocultar Ventana
	    			$('#crearActividadModal').modal('hide');
	    		});
     	 }
		
		
		
		
	});
    
    $('[data-toggle="tooltip"]').tooltip({
        selector: "[data-toggle=tooltip]",
        container: "body"
    });

});

function calcular_actividades_mes()
{
	$.each($('.d-colummActividades'), function(i, _etapa_){

		var mes = $(this).attr('data-mes');
		var total = 0;
		$.each($(this).find('.crm-widget'), function(i, etapa_){
			total++;
		});
		
		$('#mes-'+ mes).find('.total-actividades-mes').empty().append(total);
	});
}

function calcular_estado_actividades()
{
    $.each($('.d-colummActividades'), function(i, _etapa_){

        var mes = $(this).attr('data-mes');
        var planned = 0;
        var past = 0;
        $.each($(this).find('.planned-act'), function(i, etapa_){
            planned++;
        });

        $.each($(this).find('.past-act'), function(i, etapa_){
            past++;
        });

        var s;

        if(planned>0){
            if(planned==1){
            s='';
            }
            else{
            s='s';
            }
            $('#mes-'+ mes).find('.planificadas').empty().append(planned+" Planificada"+s);
        }
        if(past>0){
            if(past==1){
            s='';
            }
            else{
            s='s';
            }
            $('#mes-'+ mes).find('.atrasadas').empty().append(past+ " Atrasada"+s);
        }
    });
}

function filtrar_oportunidades_por_periodo()
{
	//obtener cual es el periodo que se quiere filtrar
	var periodo = $('#filtroGroupBtns a.active').attr('id');
 		//Recorrer las etapas
		$.each($('.d-colummActividades'), function(i, _etapa_){
			
			//Recorrer todas las oportunidades
			$.each($(this).find('.crm-widget'), function(i, etapa_){
				
				if( periodo == "mes" ){
					
					//Ocultar los que no sean del mes en curso
					moment().month() == moment($(this).attr('data-fecha')).month() == true ? ( $(this).hasClass('fadeOutDown') == true ? $(this).removeClass('animated fadeOutDown').addClass('animated fadeInUp') : '' ) : $(this).removeClass('animated fadeInUp').addClass('animated fadeOutDown');
				
				}else if( periodo == "semana" ){

					//Ocultar los que no sean de la semana en curso
					moment().week() == moment($(this).attr('data-fecha')).week() == true ? ( $(this).hasClass('fadeOutDown') == true ? $(this).removeClass('animated fadeOutDown').addClass('animated fadeInUp') : '' ) : $(this).removeClass('animated fadeInUp').addClass('animated fadeOutDown');
				
				}else if( periodo == "dia" ){
					
					//Mostrar solo los del dia de hoy
					moment().format("D-M-YY") == moment($(this).attr('data-fecha')).format("D-M-YY") ? ( $(this).hasClass('fadeOutDown') == true ? $(this).removeClass('animated fadeOutDown').addClass('animated fadeInUp') : '' ) : $(this).removeClass('animated fadeInUp').addClass('animated fadeOutDown');
				}
			});
		});
		
		//Cambiar texto de label
	 	$('.ibox').find('span.filtro-periodo').empty().append(ucWords(periodo));
	 
 	
 }

$('#crearActividadModal').on("click", "#editarActividadBtn", function(e){
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
	 console.log($("form#crearActividad").valid() );
	 
 	 if ($("form#crearActividad").valid() != false) {
 		$.ajax({
 	        url: phost() + 'actividades/ver-actividad/'+$(this).attr("data-uuid"),
 	        data: $('form#crearActividad').serialize()+ '&uuid_actividad=' + $(this).attr("data-uuid"),
 	        type: "POST",
 	        dataType: "json",
 	        cache: false,
 	        erptkn: tkn

 	    }).done(function(json) {

 	        //Check Session
 	        if( $.isEmptyObject(json.session) == false){
 	            window.location = phost() + "login?expired";
 	        }

 	        //If json object is empty.
 	        if($.isEmptyObject(json.results) == true){
 	            return false;
 	        }

 	        var class_mensaje = json.results[0] == true ? 'alert-success' : 'alert-danger';
 	        var mensaje = json.results[0] == true ? 'Se ha guardado satisfactoriamente la actividad.' : 'Hubo un error tratando de guardar la actividad';

 	        //Mostrar Mensaje
 	        mensaje_alerta(mensaje, class_mensaje);

 	        //Ocultar Ventana
 	        $('#crearActividadModal').modal('hide');
 	        location.reload();
 	    });
 		 
 	 }
    

});