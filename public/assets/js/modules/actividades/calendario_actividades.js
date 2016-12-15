$(document).ready(function() {

 
    $('#calendario_actividades').fullCalendar({
        editable: true,
        eventLimit: true, // allow "more" link when too many events
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'agendaDay,agendaWeek,month',
            lang: 'es'
        },
        eventDurationEditable: false,
        timeFormat: 'H:mm',
        weekNumbers:true,
        events: {
            url: phost()+'actividades/ajax-actividades-source-calendario',
            data: {
                sord: "DESC",
                sidx: "act.id_actividad",
                erptkn: tkn,
                rows: 1000
            },
            type: "POST",
            error: function() {
                $('#script-warning').show();
            }
        },
        eventRender: function(calev, elt, view) {
         	
         	//Eastern Standard Time
        	
            var ntoday = new Date().getTime();
            
           //var date =new Date();
            //var  tomorrow= new Date(date.getTime() + 24 * 60 * 60 * 1000);
            var  tomorrow=  moment().endOf('day');
            var calendario =  calev.start  + (300 * 60 * 1000) ; 
            if(calev.completado == 0){
        	   
        	   if (calendario < ntoday){
                   elt.addClass("pasado");
                   elt.children().addClass("pasado");
               }
                else if(calendario > tomorrow){
                	elt.addClass("futuro");
                    elt.children().addClass("futuro");
                  
               }
                else if(calendario < tomorrow && calendario > ntoday){
               	 	elt.addClass("corriente");
                    elt.children().addClass("corriente");
               }
           }
           
        },
        loading: function(bool) {
            $('#loading').toggle(bool);
        },
        dayClick: function(date, jsEvent, view) {


            $('form#crearActividad').closest('form').find("input[type=text], textarea, select").val("");
            $('select[id*="uuid_sociedad"] option:not(:first)').remove();
            $('select[id*="uuid_contacto"] option:not(:first)').remove();
            $('select[id*="uuid_relacion"] option:not(:first)').remove();
 
             if( $('form#crearActividad').find('button[id="editarActividadBtn"]') ){
                 $('form#crearActividad').find('button[id="editarActividadBtn"]').parent().replaceWith('<div class="form-group col-xs-12 col-sm-6 col-md-6"><button id="guardarActividadBtn" class="btn btn-w-m btn-primary btn-block" type="button">Guardar</button></div>');
             }
            $('#crearActividadModal').modal('show');
            $('#uuid_asignado option[value="'+ uuid_usuario +'"]').prop('selected', uuid_usuario);
            
            
            $('form#crearActividad').find('input[id="campo[guardar]"]').parent().replaceWith('<div class="form-group col-xs-12 col-sm-6 col-md-6"><button id="guardarActividadBtn" class="btn btn-w-m btn-primary btn-block" type="button">Guardar</button></div>');
            $('form#crearActividad').find('a[id="cancelar"]').parent().replaceWith('<div class="form-group col-xs-12 col-sm-6 col-md-6"><button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button></div>');

            $('form#crearActividad').find('input[id="campo[fecha]"]').prop("value", date.format());

        },

        eventDrop: function( event, delta, revertFunc, jsEvent, ui, view ) {


            $.ajax({
                url: phost() + 'actividades/ver-actividad/'+event.id,
                data: {
                    uuid_actividad: event.id,
                    fecha:event.start.format("YYYY-MM-DD HH:mm:ss"),
                    erptkn:tkn
                },
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

                $('#calendario_actividades').fullCalendar( 'refetchEvents' );
                //Ocultar Ventana
                $('#crearActividadModal').modal('hide');
            });

        },


        eventClick: function(calEvent, jsEvent, view) {

            $.ajax({
                url: phost() + 'actividades/ajax-seleccionar-informacion-de-actividad',
                data: {
                    id_actividad:calEvent.id,
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

                console.log(json.results[0]);

                var uuid_sociedad=json.results[0].uuid_sociedad;
                var uuid_contacto=json.results[0].uuid_contacto;
                var uuid_relacion=json.results[0].uuid_relacion;
                var uuid_actividad=json.results[0].uuid_actividad;



               //$('#uuid_cliente').append('<option selected value="'+ json.results[0].uuid_cliente +'">'+ json.results[0].nombre_cliente +'</option>');
           	   $('form#crearActividad ').find('select[name*="campo[uuid_cliente]"]').find('option[value="'+  json.results[0].uuid_cliente +'"]').prop("selected", "selected");
	           	$(".chosen-select").chosen({
				    width: '100%'
				}).trigger('chosen:updated');

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
                
             	$('form#crearActividad').find('input[id="campo[guardar]"]').parent().replaceWith('<div class="form-group col-xs-12 col-sm-6 col-md-6"><button data-uuid="'+uuid_actividad+'"  id="editarActividadBtn" class="btn btn-w-m btn-primary btn-block" type="button">Guardar</button></div>');	

                if( $('form#crearActividad').find('button[id="guardarActividadBtn"]') ) {
                	 
                	$('form#crearActividad').find('button[id="guardarActividadBtn"]').parent().replaceWith('<div class="form-group col-xs-12 col-sm-6 col-md-6"><button data-uuid="'+uuid_actividad+'"  id="editarActividadBtn" class="btn btn-w-m btn-primary btn-block" type="button">Guardar</button></div>');	

                }
                else{
                 	$('form#crearActividad').find('input[id="campo[guardar]"]').parent().replaceWith('<div class="form-group col-xs-12 col-sm-6 col-md-6"><button data-uuid="'+uuid_actividad+'"  id="editarActividadBtn" class="btn btn-w-m btn-primary btn-block" type="button">Guardar</button></div>');	
                } 
                
                $('form#crearActividad').find('a[id="cancelar"]').parent().replaceWith('<div class="form-group col-xs-12 col-sm-6 col-md-6"><button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button></div>');


            });



            $('#crearActividadModal').modal('show');


        }
    });

    //Filtro de actividades
    $('.filtro_tipo').on('click',function(e) {

        e.preventDefault();

        $('#calendario_actividades').fullCalendar ('removeEvents');
        var uuid_tipo_actividad = $(this).data("uuid");

        $('#calendario_actividades').fullCalendar('removeEvents');

        var source={
            url: phost()+'actividades/ajax-actividades-source-calendario',
            data: {
            sord: "DESC",
                sidx: "act.id_actividad",
                erptkn: tkn,
                rows: 1000,
                uuid_tipo_actividad: uuid_tipo_actividad
        },
        type: "POST",
            error: function() {
            $('#script-warning').show();
        }
        };

        $('#calendario_actividades').fullCalendar('addEventSource', source);

    });

    $('#crearActividadModal').on("click", "#guardarActividadBtn", function(e){
    	
    	 
    	 $('#crearActividad').validate({
    		focusInvalid: true,
    		ignore: '',
    		wrapper: '',
    	});
    	
    	 if ($("#crearActividad").valid() != false) {
    		 	$('#uuid_tipo_actividad').rules("add",{ required: true, messages: { required: 'Introduzca el tipo.' } });
    		 
    		 	e.preventDefault();
    	        e.returnValue=false;
    	        e.stopPropagation();

    	        $.ajax({
    	            url: phost() + 'actividades/crear-actividad/',
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
    	            var mensaje = json.results[0] == true ? 'Se ha guardado satisfactoriamente la actividad.' : 'Hubo un error tratando de guardar la actividad';

    	            //Mostrar Mensaje
    	            mensaje_alerta(mensaje, class_mensaje);

    	            $('#calendario_actividades').fullCalendar( 'refetchEvents' );
    	            //Ocultar Ventana
    	            $('#crearActividadModal').modal('hide');
    	        });
    	 }
    	
        

    });



    $('#crearActividadModal').on("click", "#editarActividadBtn", function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();

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

            $('#calendario_actividades').fullCalendar( 'refetchEvents' );
            //Ocultar Ventana
            $('#crearActividadModal').modal('hide');
        });

    });

    $('.crearActBtn').on("click", function(e){




    $('form#crearActividad').closest('form').find("input[type=text], textarea, select").val("");

 	//Por defecto asignado a debe ser la persona logeada
 	$('#uuid_asignado option[value="'+ uuid_usuario +'"]').prop('selected', uuid_usuario);
 
   $('form#crearActividad').find('#uuid_tipo_actividad').val($(this).attr("data-uuid"));

    $('select[id*="uuid_sociedad"] option:not(:first)').remove();
    $('select[id*="uuid_contacto"] option:not(:first)').remove();
    $('select[id*="uuid_relacion"] option:not(:first)').remove();

    $('#crearActividadModal').modal('show');

    $('form#crearActividad').find('input[id="campo[guardar]"]').parent().replaceWith('<div class="form-group col-xs-12 col-sm-6 col-md-6"><button id="guardarActividadBtn" class="btn btn-w-m btn-primary btn-block" type="button">Guardar</button></div>');

    $('form#crearActividad').find('a[id="cancelar"]').parent().replaceWith('<div class="form-group col-xs-12 col-sm-6 col-md-6"><button id="closeModal" class="btn btn-w-m btn-default btn-block" type="button" data-dismiss="modal">Cancelar</button></div>');


    });



});