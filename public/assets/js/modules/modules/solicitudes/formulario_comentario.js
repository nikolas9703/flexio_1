$(document).ready(function(){
	$("#comentario_solicitud").ckeditor({
		toolbar :
		[
			{ name: 'basicstyles', items : [ 'Bold','Italic' ] },
			{ name: 'paragraph', items : [ 'NumberedList','BulletedList' ] }
		],
		uiColor : '#F5F5F5'
	});
         $('#verHistorial').css({'right':($('#moduloOpciones').width()+35),'top':8,'position':'absolute'});
	
	$('#verHistorial').click(function(e){
		var uuid = window.location.href.split("/").pop();
		window.location.href = phost() + 'solicitudes/bitacora/'+uuid;
	});
});

function limpiarEditor(){
	$("#comentario_solicitud").val('');
}

function guardar_comentario(){

	var comentario = $("#comentario_solicitud").val();
	var n_solicitud = $("#n_solicitud").val();
	if(!_.isEmpty(comentario)){
		var context = this;
		$.ajax({
			url: phost() + "solicitudes/ajax_guardar_comentario",
			type:"POST",
			data:{
				erptkn:tkn,
				comentario: comentario,
				n_solicitud: n_solicitud
			},
			success: function(response){
                                    $("#comentario_solicitud").val('');
					historial();
			}
		});
	}
       
}
function historial(){
	var n_solicitud = $("#n_solicitud").val();
	$.ajax({
		url: phost() + "solicitudes/ajax_carga_comentarios",
		type:"post",
		data:{
			erptkn:tkn,
			n_solicitud: n_solicitud
		},
		dataType:"html",
		success: function(res){
			$("#historial_comentario").html(res);
		}
	});
}
 