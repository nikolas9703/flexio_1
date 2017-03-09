$(document).ready(function(){
	//$("#tcomentario").ckeditor({
	$("#tcomentario").ckeditor({
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
		window.location.href = phost() + 'polizas/bitacora/'+uuid;
	});
	
});

function limpiarEditor(){
	$("#tcomentario").val('');
}

function guardar_comentario(){

	var comentario = $("#tcomentario").val();
	var nid_poliza = $("#nid_poliza").val();
	if(!_.isEmpty(comentario)){
		var context = this;
		$.ajax({
			url: phost() + "polizas/ajax_guardar_comentario",
			type:"post",
			data:{
				erptkn:tkn,
				comentario:comentario,
				nid_poliza: nid_poliza
			},
			success: function(res){
				$("#tcomentario").val('');
				historial();
			}
		});
	}
}

function historial(){
	var nid_poliza = $("#nid_poliza").val();
	$.ajax({
		url: phost() + "polizas/ajax_carga_comentarios_poliza",
		type:"post",
		data:{
			erptkn:tkn,
			id_poliza:nid_poliza
		},
		dataType:"html",
		success: function(res){
			$("#historial_comentario").html(res);
		}
	});
}
