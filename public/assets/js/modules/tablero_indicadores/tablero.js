$(document).ready(function(){
	var parametros = {fecha:'este_mes',erptkn: tkn};
	graficaScoreClientes(parametros);
	top_oportunidades(parametros);
	//tabla_usuarios(parametros);
});
$(function(){
	
	//Cargar plugin SlimScroll para Notificaciones Recientes
	$('ul.lista-notificaciones-recientes').slimScroll({
	    //color: '#00f',
	    size: '10px',
	    height: '240px',
	    alwaysVisible: true
	});
	
	//Init Bootstrap Calendar Plugin
    $('.rango-fecha').daterangepicker({
        format: 'DD-MM-YYYY',
        showDropdowns: true,
        separator: ' hasta ',
        ranges: {
            'Hoy': [moment(), moment()],
            'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Ultimos 7 Dias': [moment().subtract(6, 'days'), moment()],
            'Ultimos 30 Dias': [moment().subtract(29, 'days'), moment()],
            'Este Mes': [moment().startOf('month'), moment().endOf('month')],
            'Ultimo Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: {
            applyLabel: 'Seleccionar',
            cancelLabel: 'Cancelar',
            fromLabel: 'Desde',
            toLabel: 'Hasta',
            customRangeLabel: 'Personalizar',
            daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi','Sa'],
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            firstDay: 1
        }
    }).on('apply.daterangepicker', function(ev, picker) {
    	//do something, like clearing an input
    	$('#fecha_filtro').prop("value", $('#fecha_filtro').val());
    });
	
    //Boton de Filtrar Indicadores
    $('#filtrarIndicadoresForm').on('click', '#filtrarIndicadoresBtn', function(e){
    	e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		cargar_oportunidades_ganadas_venta_alquiler();
		cargar_comisiones_ganadas_venta_alquiler();
		cargar_oportunidades_abiertas_venta_alquiler();
		cargar_cantidad_propiedades_venta_alquiler();
		cargar_notificaciones_recientes();
		cargar_actividades_pendientes();
		grafica_pipeline_ventas();
		grafica_oportunidades_alquiladas_vs_vendidas();
    });
    
    //Boton de Limpiar Filtro
    $('#filtrarIndicadoresForm').on('click', '#limpiarIndicadoresBtn', function(e){
    	e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();

		//Limpiar campos
		$('#filtrarIndicadoresForm').find('#fecha_filtro').val('');
		$('#filtrarIndicadoresForm').find('#uuid_usuario option:eq(0)').prop('selected', 'selected');
		
		cargar_oportunidades_ganadas_venta_alquiler();
		cargar_comisiones_ganadas_venta_alquiler();
		cargar_oportunidades_abiertas_venta_alquiler();
		cargar_cantidad_propiedades_venta_alquiler();
		cargar_notificaciones_recientes();
		cargar_actividades_pendientes();
		grafica_pipeline_ventas();
		grafica_oportunidades_alquiladas_vs_vendidas();
    });
    
	//Cargar mas notificaciones
	$('.notificaciones-recientes-wrapper').on('click', '#cargarNotificacionesBtn', function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		//Verificar si es valor numerico o si no es cero
		if(isNumber(parseInt($(this).attr('data-limit')))== false || parseInt($(this).attr('data-limit'))== 0){
			return false;
		}
		
		var button = this;
		
		//obtener limite y incrementarle 5
		var limit = isNumber(parseInt($(this).attr('data-limit'))) == true ? parseInt($(this).attr('data-limit')) + 5 : 5 + 5;
		
		//guardar lmite incrementado
		$(this).attr('data-limit', limit);
		
		//Mostrar Icono Loading
		$(this).empty().append('<i class="fa fa-circle-o-notch fa-spin"></i>');
		
		cargar_notificaciones_recientes();
	});
	
	//Quitarle el checkbox a las actividades que no estan
	//asignadas a este usuario.
	verificar_checkbox_actividades();
	
	//Evento: marcar las actividades como completada.
	$('.check-link').on('click', function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		var object =  this;
		var uuid_actividad = $(this).closest('li').attr('data-id-actividad');
		
		//Listar mas notificaciones si exiten mas registros
		$.ajax({
			url: phost() + 'actividades/ajax-toggle-actividad/0',
			data: {
				uuid_actividad: uuid_actividad,
				completada: 1,
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
			
			//si retorna true, quitar la actividad ya completada
			if(json.results[0] == true){
				$(object).closest('li').addClass('animated fadeOut');
				
				setTimeout(function(){
					$(object).closest('li').remove();
			    }, 500);
			}
		});
	});
	
	//Cargar mas actividades pendientes
	$('.actividades-pendientes-wrapper').on('click', '#cargarActividadesPendienteBtn', function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		
		//Verificar si es valor numerico o si no es cero
		if(isNumber(parseInt($(this).attr('data-limit')))== false || parseInt($(this).attr('data-limit'))== 0){
			return false;
		}
		
		var button = this;
				
		//obtener limite y incrementarle 5
		var limit = isNumber(parseInt($(this).attr('data-limit'))) == true ? parseInt($(this).attr('data-limit')) + 5 : 5 + 5;
		
		//guardar lmite incrementado
		$(this).attr('data-limit', limit);
		
		//Mostrar Icono Loading
		$(this).empty().append('<i class="fa fa-circle-o-notch fa-spin"></i>');
		
		setTimeout(function(){
			cargar_actividades_pendientes();
		}, 500);
	});
	
	grafica_pipeline_ventas();
	grafica_oportunidades_alquiladas_vs_vendidas();
});

function verificar_checkbox_actividades()
{
	//Quitarle el checkbox a las actividades que no estan
	//asignadas a este usuario.
	$.each($('.lista-actividades-pendientes li'), function(i, li) {
		if($(this).attr('data-asignado') != uuid_usuario){
			$(this).find('a').remove();
		}
	});
}

function cargar_oportunidades_ganadas_venta_alquiler()
{
	var fecha_inicio = '';
	var fecha_fin = '';
	var rango_fecha =  $('#filtrarIndicadoresForm').find('#fecha_filtro').val();
	var uuid_usuario =  $('#filtrarIndicadoresForm').find('#uuid_usuario option:selected').val();
	
	if(rango_fecha != ""){
		var dates = rango_fecha.split(' hasta ');
		fecha_inicio = dates[0];
		fecha_fin = dates[1];
	}
	
	//Oportunidades Ganadas Venta
	$.ajax({
		url: phost() + 'oportunidades/ajax-seleccionar-monto-oportunidades-ganadas',
		data: {
			nombre_etapa: 'Ganado',
			tipo_transaccion: 'Venta',
			uuid_usuario: uuid_usuario,
			fecha_inicio: fecha_inicio,
			fecha_fin: fecha_fin,
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
			setTimeout(function(){
				$('.oportunidades-ganadas-wrapper').find('.widget-text-content').find('h3.venta').empty().append('$0.00');
			}, 200);
			return false;
		}
		
		var monto_venta = isNumber(json.results[0][0]["monto_total"]) && json.results[0][0]["monto_total"] > 0 ? json.results[0][0]["monto_total"] : '0.00';
		
		$('.oportunidades-ganadas-wrapper').find('.widget-text-content').find('h3.venta').empty().append('$'+ monto_venta);
	});
	
	//Oportunidades Ganadas Alquiler
	$.ajax({
		url: phost() + 'oportunidades/ajax-seleccionar-monto-oportunidades-ganadas',
		data: {
			nombre_etapa: 'Ganado',
			tipo_transaccion: 'Alquiler',
			uuid_usuario: uuid_usuario,
			fecha_inicio: fecha_inicio,
			fecha_fin: fecha_fin,
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
			setTimeout(function(){
				$('.oportunidades-ganadas-wrapper').find('.widget-text-content').find('h3.alquiler').empty().append('$0.00');
			}, 200);
			return false;
		}
		
		var monto_alquiler = isNumber(json.results[0][0]["monto_total"]) && json.results[0][0]["monto_total"] > 0 ? json.results[0][0]["monto_total"] : '0.00';
		
		$('.oportunidades-ganadas-wrapper').find('.widget-text-content').find('h3.alquiler').empty().append('$'+ monto_alquiler);
	});
}

function cargar_comisiones_ganadas_venta_alquiler()
{
	var fecha_inicio = '';
	var fecha_fin = '';
	var rango_fecha =  $('#filtrarIndicadoresForm').find('#fecha_filtro').val();
	var uuid_usuario =  $('#filtrarIndicadoresForm').find('#uuid_usuario option:selected').val();
	
	if(rango_fecha != ""){
		var dates = rango_fecha.split(' hasta ');
		fecha_inicio = dates[0];
		fecha_fin = dates[1];
	}
	
	//Comisiones Ganadas Venta
	$.ajax({
		url: phost() + 'oportunidades/ajax-seleccionar-monto-comisiones-ganadas',
		data: {
			tipo_transaccion: 'Venta',
			uuid_usuario: uuid_usuario,
			fecha_inicio: fecha_inicio,
			fecha_fin: fecha_fin,
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
			setTimeout(function(){
				$('.comisiones-ganadas-wrapper').find('.widget-text-content').find('h3.venta').empty().append('$0.00');
			}, 200);
			return false;
		}
		
		var monto_venta = isNumber(json.results[0][0]["monto_total_comision"]) && json.results[0][0]["monto_total_comision"] > 0 ? json.results[0][0]["monto_total_comision"] : '0.00';
		
		$('.comisiones-ganadas-wrapper').find('.widget-text-content').find('h3.venta').empty().append('$'+ monto_venta);
	});
	
	//Comisiones Ganadas Alquiler
	$.ajax({
		url: phost() + 'oportunidades/ajax-seleccionar-monto-comisiones-ganadas',
		data: {
			tipo_transaccion: 'Alquiler',
			uuid_usuario: uuid_usuario,
			fecha_inicio: fecha_inicio,
			fecha_fin: fecha_fin,
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
			setTimeout(function(){
				$('.oportunidades-ganadas-wrapper').find('.widget-text-content').find('h3.alquiler').empty().append('$0.00');
			}, 200);
			
			return false;
		}
		
		var monto_alquiler = isNumber(json.results[0][0]["monto_total_comision"]) && json.results[0][0]["monto_total_comision"] > 0 ? json.results[0][0]["monto_total_comision"] : '0.00';
		
		$('.oportunidades-ganadas-wrapper').find('.widget-text-content').find('h3.alquiler').empty().append('$'+ monto_alquiler);
	});
}

function cargar_oportunidades_abiertas_venta_alquiler()
{
	var fecha_inicio = '';
	var fecha_fin = '';
	var rango_fecha =  $('#filtrarIndicadoresForm').find('#fecha_filtro').val();
	var uuid_usuario =  $('#filtrarIndicadoresForm').find('#uuid_usuario option:selected').val();
	
	if(rango_fecha != ""){
		var dates = rango_fecha.split(' hasta ');
		fecha_inicio = dates[0];
		fecha_fin = dates[1];
	}
	
	//Oportunidades Abiertas Venta
	$.ajax({
		url: phost() + 'oportunidades/ajax-seleccionar-monto-oportunidades-ganadas',
		data: {
			nombre_etapa: ["Prospecto", "Cotizaci�n", "Negociaci�n"],
			tipo_transaccion: 'Venta',
			uuid_usuario: uuid_usuario,
			fecha_inicio: fecha_inicio,
			fecha_fin: fecha_fin,
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
			setTimeout(function(){
				$('.oportunidades-abiertas-wrapper').find('.widget-text-content').find('h3.venta').empty().append('$0.00');
			}, 200);
			return false;
		}
		
		var monto_venta = isNumber(json.results[0][0]["monto_total"]) && json.results[0][0]["monto_total"] > 0 ? json.results[0][0]["monto_total"] : '0.00';
		
		$('.oportunidades-abiertas-wrapper').find('.widget-text-content').find('h3.venta').empty().append('$'+ monto_venta);
	});
	
	//Oportunidades Ganadas Alquiler
	$.ajax({
		url: phost() + 'oportunidades/ajax-seleccionar-monto-oportunidades-ganadas',
		data: {
			nombre_etapa: ["Prospecto", "Cotizaci�n", "Negociaci�n"],
			tipo_transaccion: 'Alquiler',
			uuid_usuario: uuid_usuario,
			fecha_inicio: fecha_inicio,
			fecha_fin: fecha_fin,
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
			setTimeout(function(){
				$('.oportunidades-abiertas-wrapper').find('.widget-text-content').find('h3.alquiler').empty().append('$0.00');
			}, 200);
			return false;
		}
		
		var monto_alquiler = isNumber(json.results[0][0]["monto_total"]) && json.results[0][0]["monto_total"] > 0 ? json.results[0][0]["monto_total"] : '0.00';
		
		$('.oportunidades-abiertas-wrapper').find('.widget-text-content').find('h3.alquiler').empty().append('$'+ monto_alquiler);
	});
}

function cargar_cantidad_propiedades_venta_alquiler()
{
	var fecha_inicio = '';
	var fecha_fin = '';
	var rango_fecha =  $('#filtrarIndicadoresForm').find('#fecha_filtro').val();
	var uuid_usuario =  $('#filtrarIndicadoresForm').find('#uuid_usuario option:selected').val();
	
	if(rango_fecha != ""){
		var dates = rango_fecha.split(' hasta ');
		fecha_inicio = dates[0];
		fecha_fin = dates[1];
	}
	
	//Comisiones Ganadas Venta
	$.ajax({
		url: phost() + 'propiedades/ajax-seleccionar-propiedades-disponibles',
		data: {
			tipo_transaccion: 'Venta',
			uuid_usuario: uuid_usuario,
			fecha_inicio: fecha_inicio,
			fecha_fin: fecha_fin,
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
			setTimeout(function(){
				$('.cantidad-propiedades-wrapper').find('.widget-text-content').find('h3.venta').empty().append('0');
			}, 200);
			return false;
		}
		
		var cantidad_propiedades = isNumber(json.results[0][0]["total_disponible"]) && json.results[0][0]["total_disponible"] > 0 ? json.results[0][0]["total_disponible"] : '0';
		
		$('.cantidad-propiedades-wrapper').find('.widget-text-content').find('h3.venta').empty().append(cantidad_propiedades);
	});
	
	//Comisiones Ganadas Alquiler
	$.ajax({
		url: phost() + 'propiedades/ajax-seleccionar-propiedades-disponibles',
		data: {
			tipo_transaccion: 'Alquiler',
			uuid_usuario: uuid_usuario,
			fecha_inicio: fecha_inicio,
			fecha_fin: fecha_fin,
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
			setTimeout(function(){
				$('.cantidad-propiedades-wrapper').find('.widget-text-content').find('h3.alquiler').empty().append('0');
			}, 200);
			
			return false;
		}
		
		var cantidad_propiedades = isNumber(json.results[0][0]["total_disponible"]) && json.results[0][0]["total_disponible"] > 0 ? json.results[0][0]["total_disponible"] : '0';
		
		$('.cantidad-propiedades-wrapper').find('.widget-text-content').find('h3.alquiler').empty().append(cantidad_propiedades);
	});
}

function cargar_notificaciones_recientes()
{
	var fecha_inicio = '';
	var fecha_fin = '';
	var rango_fecha =  $('#filtrarIndicadoresForm').find('#fecha_filtro').val();
	var uuid_usuario =  $('#filtrarIndicadoresForm').find('#uuid_usuario option:selected').val();
	
	//obtener limite y incrementarle 5
	var limit = parseInt($('.notificaciones-recientes-wrapper').find('#cargarNotificacionesBtn').attr('data-limit'));
	
	if(rango_fecha != ""){
		var dates = rango_fecha.split(' hasta ');
		fecha_inicio = dates[0];
		fecha_fin = dates[1];
	}

	//Listar mas notificaciones si exiten mas registros
	$.ajax({
		url: phost() + 'tablero_indicadores/ajax-seleccionar-notificaciones-recientes',
		data: {
			limit: limit,
			uuid_usuario: uuid_usuario,
			fecha_inicio: fecha_inicio,
			fecha_fin: fecha_fin,
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
			
			//Limpiar lista de notjificaciones
			$('ul.lista-notificaciones-recientes').empty();
			
			setTimeout(function(){
				$('.notificaciones-recientes-wrapper').find('.ibox-content').find('h4').removeClass('hide');
				$('.notificaciones-recientes-wrapper').find('#cargarNotificacionesBtn').empty().hide();
			}, 200);
			return false;
		}
		
		$('.notificaciones-recientes-wrapper').find('.ibox-content').find('h4').addClass('hide');
		
		//Limpiar lista de notjificaciones
		$('ul.lista-notificaciones-recientes').empty();
		
		//recorrer arreglo de actualizaciones
		$.each(json.results[0], function(i, notificacion) {  
			
			var mensaje = notificacion["mensaje"] != "" ? notificacion["mensaje"] : "";
			var tiempo_transcurrido = notificacion["tiempo_transcurrido"] != "" ? notificacion["tiempo_transcurrido"] : "";
			var fecha = notificacion["fecha"] != "" ? notificacion["fecha"] : "";
			var first_item_class = i==0 ? 'fist-item' : '';
			
			var html = ['<li class="list-group-item '+ first_item_class +'">',
				'<span class="label label-info pull-right">'+ tiempo_transcurrido +'</span>',
				mensaje +'<br>',
				'<small>'+ fecha +'</small>',
			'</li>'].join('\n');

            //Append actividad html
			$('ul.lista-notificaciones-recientes').append(html);
		});
		
		//Actualizar label de conteo de notificaciones
		$('.label-conteo-notificacion').empty().append( json.results[0].length + ' Notificaciones ' );
		
		//mostrar texto de ver mas
		$('.notificaciones-recientes-wrapper').find('#cargarNotificacionesBtn').empty().show().append('<i class="fa fa-arrow-down"></i> Ver Mas');
	});
}

function cargar_actividades_pendientes()
{
	var fecha_inicio = '';
	var fecha_fin = '';
	var rango_fecha =  $('#filtrarIndicadoresForm').find('#fecha_filtro').val();
	var uuid_usuario =  $('#filtrarIndicadoresForm').find('#uuid_usuario option:selected').val();
	
	//obtener limite y incrementarle 5
	var limit = parseInt($('.actividades-pendientes-wrapper').find('#cargarActividadesPendienteBtn').attr('data-limit'));
	
	console.log(limit);

	
	if(rango_fecha != ""){
		var dates = rango_fecha.split(' hasta ');
		fecha_inicio = dates[0];
		fecha_fin = dates[1];
	}
	
	//Listar mas notificaciones si exiten mas registros
	$.ajax({
		url: phost() + 'actividades/ajax-seleccionar-actividades-pendientes',
		data: {
			limit: limit,
			uuid_usuario: uuid_usuario,
			fecha_inicio: fecha_inicio,
			fecha_fin: fecha_fin,
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
			//Limpiar lista de notjificaciones
			$('ul.lista-actividades-pendientes').empty();
			
			setTimeout(function(){
				$('.actividades-pendientes-wrapper').find('.ibox-content').find('h4').removeClass('hide');
				$('.actividades-pendientes-wrapper').find('#cargarActividadesPendienteBtn').empty().hide();
			}, 200);
			return false;
		}
		
		$('.actividades-pendientes-wrapper').find('.ibox-content').find('h4').addClass('hide');
		
		//Limpiar lista de notjificaciones
		$('ul.lista-actividades-pendientes').empty();
		
		//recorrer arreglo de actualizaciones
		$.each(json.results[0], function(i, actividad) {

    		//configurar momentjs a idioma espanol
    		moment.locale('es');
			
			var uuid_actividad = actividad["uuid_actividad"] != "" ? actividad["uuid_actividad"] : "";
			var asunto_actividad = actividad["asunto"] != "" ? actividad["asunto"] : "";
			var icono_actividad = actividad["icono"] != "" ? actividad["icono"] : "";
			var uuid_usuario_asignado = actividad["uuid_asignado"] != "" ? actividad["uuid_asignado"] : "";
			var fecha = actividad["fecha_creacion"] != "" ? actividad["fecha_creacion"] : "";
			var tiempo_transcurrido = fecha != "" ? moment(fecha).fromNow() : "";
			
			var html = ['<li data-id-actividad="'+ uuid_actividad +'" data-asignado="'+ uuid_usuario_asignado +'">',
				'<a class="check-link" href="#"><i class="fa fa-square-o"></i></a>',
				'<span class="label label-info pull-right">'+ tiempo_transcurrido +'</span>',
				'<span class="m-l-xs"><i class="fa '+ icono_actividad +'"></i> '+ asunto_actividad +'</span>',
			'</li>'].join('\n');

            //Append actividad html
			$('ul.lista-actividades-pendientes').append(html);
		});
		
		//Actualizar label de conteo de notificaciones
		$('.label-conteo-actividades-pendientes').empty().append( json.results[0].length + ' Actividad Pendiente' );
		
		setTimeout(function(){
			//Quitarle el checkbox a las actividades que no estan
			//asignadas a este usuario.
			verificar_checkbox_actividades();
		}, 150);
		
		//mostrar texto de ver mas
		$('.actividades-pendientes-wrapper').find('#cargarActividadesPendienteBtn').empty().show().append('<i class="fa fa-arrow-down"></i> Ver Mas');
	});
}

function grafica_pipeline_ventas()
{
	var fecha_inicio = '';
	var fecha_fin = '';
	var rango_fecha =  $('#filtrarIndicadoresForm').find('#fecha_filtro').val();
	var uuid_usuario =  $('#filtrarIndicadoresForm').find('#uuid_usuario option:selected').val();
	
	if(rango_fecha != ""){
		var dates = rango_fecha.split(' hasta ');
		fecha_inicio = dates[0];
		fecha_fin = dates[1];
	}
	
	$.ajax({
        url: phost() + 'oportunidades/ajax-pipeline-ventas',
        data: {
        	uuid_usuario: uuid_usuario,
        	fecha_inicio: fecha_inicio,
			fecha_fin: fecha_fin, 
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

    	$('#containerChart').highcharts({
    		chart: {
                renderTo: 'container',
                type: 'bar'
            },
            xAxis: {
                categories: ['Prospecto', 'Cotizacion', 'Negociacion', 'Perdido', 'Ganado']
            },
            yAxis: {
                min: 0,
                title: {
                    text: ''
                }
            },
            title: {
                text: ''
            },
            colors: [
                '#FFA349',
                '#0069AC',
                '#00C24F',
                '#BF8F00',
                '#00B4B4',
                '#DF00A7',
                '#FFDA2F',
                '#738F00',
                '#690079',
                '#49A5FF',
                '#FF8E8E',
                '#FF4B4B',
            ],
            legend: {
                reversed: true
            },
            plotOptions: {
                series: {
                    stacking: 'normal'
                }
            },
            credits: {
                enabled: false
            },
            series: json
        });
    });
}

function grafica_oportunidades_alquiladas_vs_vendidas()
{
	var fecha_inicio = '';
	var fecha_fin = '';
	var rango_fecha =  $('#filtrarIndicadoresForm').find('#fecha_filtro').val();
	var uuid_usuario =  $('#filtrarIndicadoresForm').find('#uuid_usuario option:selected').val();
	
	if(rango_fecha != ""){
		var dates = rango_fecha.split(' hasta ');
		fecha_inicio = dates[0];
		fecha_fin = dates[1];
	}
	
	$.ajax({
        url: phost() + 'oportunidades/ajax-oportunidades-vendidas-vs-alquiladas',
        data: {
        	uuid_usuario: uuid_usuario,
        	fecha_inicio: fecha_inicio,
			fecha_fin: fecha_fin,
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
        
		$('#graficaOportunidadesVendidasAlquiladas').highcharts({
	        chart: {
	            type: 'line'
	        },
	        title: {
	            text: ''
	        },
	        subtitle: {
	            text: ''
	        },
	        xAxis: {
	            categories: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
	        },
	        yAxis: {
	            title: {
	                text: ''
	            }
	        },
	        colors: [
                 '#B61600',
                 '#007FC1',
            ],
            legend: {
                reversed: true
            },
	        plotOptions: {
	            line: {
	                dataLabels: {
	                    enabled: true
	                },
	                enableMouseTracking: true
	            }
	        },
	        credits: {
                enabled: false
            },
	        series: json
	    });
    });
}

function graficaScoreClientes(parametros){
 var grafica = moduloGraficas.getClientesScore(parametros);
 grafica.done(function(data){
	 //setea la grafica
	 total = $.parseJSON(data).results[0];

	 var gaugeOptions = {

        chart: {
            type: 'solidgauge'
        },
				credits: {
          enabled: false
        },
        title: "Clientes Score",

        pane: {
            center: ['50%', '85%'],
            size: '140%',
            startAngle: -90,
            endAngle: 90,
            background: {
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#FDDFD0',
                innerRadius: '60%',
                outerRadius: '100%',
                shape: 'arc'
            }
        },

        tooltip: {
            enabled: false
        },

        // the value axis
        yAxis: {
            stops: [
                [0.9, '#ED2228'] // red
            ],
            lineWidth: 0,
            minorTickInterval: null,
            tickPixelInterval: 400,
            tickWidth: 0,
						tickPositions: [],
            title: {
                y: 10
            },
            labels: {
                y: 16
            }
        },

        plotOptions: {
            solidgauge: {
                dataLabels: {
                    y: -75,
                    borderWidth: 0,
                    useHTML: true
                }
            }
        }
    };
		//render de la grafica
		$('#container-clientes-score').highcharts(Highcharts.merge(gaugeOptions, {
        yAxis: {
            min: 0,
            max: 100,
            title: {
                text: ''
            }
        },

        series: [{
            name: 'Score',
            data: [parseInt(total.score)],
            dataLabels: {
                format: '<div style="text-align:center"><span style="font-size:25px;color:' +
                    ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y} %</span>'
            }
        }]

    }));
		//fin del render
 });
}
function top_oportunidades(parametros){
	var grafica = moduloGraficas.getTopOportunidad(parametros);
	grafica.done(function(data){
		puntajes = $.parseJSON(data).results[0];
		var categoria = [];
		var datapuntaje = [];
		$.each(puntajes,function(key, value ){
			categoria.push(value.nombre + ' - pts '+ value.puntaje_total);
			datapuntaje.push(parseFloat(value.percentagerank));
		});
		$('#top-oportunidades').highcharts({
        chart: {
          type: 'bar'
        },
				title: {
            text: ''
        },
				xAxis: {
            categories: categoria
        },
				yAxis: {
            min: 0,
            title: {
                text: ''
            }
        },
				credits: {
            enabled: false
        },
				plotOptions: {
            bar: {
                pointPadding: 0.2,
                borderWidth: 0,
								dataLabels: {
                    enabled: true,
										format: '{point.y}%'
                }
            }
        },
				series:[{
					name:"Score de Puntajes",
					data:datapuntaje
				}]
		 });
	});

}
function tabla_usuarios(parametros){
  	var agentes = moduloPerfil.getAgentes(parametros);
}
