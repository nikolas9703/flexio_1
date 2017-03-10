$(document).ready(function(){
	$("#comentario_reclamo").ckeditor({
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
		window.location.href = phost() + 'reclamos/bitacora/'+uuid;
	});
});

function limpiarEditor(){
	$("#comentario_solicitud").val('');
}

function guardar_comentario(){

	var comentario = $("#comentario_reclamo").val();
	var n_reclamo = $("#n_reclamo").val();
	if(!_.isEmpty(comentario)){
		var context = this;
		$.ajax({
			url: phost() + "reclamos/ajax_guardar_comentario",
			type:"POST",
			data:{
				erptkn:tkn,
				comentario: comentario,
				n_reclamo: n_reclamo
			},
			success: function(response){
                                    $("#comentario_reclamo").val('');
					historial();
			}
		});
	}
       
}
function historial(){
	var n_reclamo = $("#n_reclamo").val();
	$.ajax({
		url: phost() + "reclamos/ajax_carga_comentarios",
		type:"post",
		data:{
			erptkn:tkn,
			n_reclamo: n_reclamo
		},
		dataType:"html",
		success: function(res){
			$("#historial_comentario").html(res);
		}
	});
}
 