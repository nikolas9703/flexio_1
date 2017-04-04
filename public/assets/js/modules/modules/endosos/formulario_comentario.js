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
		//window.location.href = phost() + 'polizas/bitacora/'+uuid;
	});
	
});

function limpiarEditor(){
	$("#tcomentario").val('');
}

function guardar_comentario(){
	$('#formPolizasCrear').submit(function(){
        return false;
    });
	var comentario = $("#tcomentario").val();
	var nid_endoso = $("#nid_endoso").val();
	if(!_.isEmpty(comentario)){
		var context = this;
		$.ajax({
			url: phost() + "endosos/ajax_guardar_comentario",
			type:"post",
			data:{
				erptkn:tkn,
				comentario:comentario,
				nid_endoso: nid_endoso
			},
			success: function(res){
				$("#tcomentario").val('');
				historial();
			}
		});
	}
}

function historial(){
	var nid_endoso = $("#nid_endoso").val();
	$.ajax({
		url: phost() + "endosos/ajax_carga_comentarios_poliza",
		type:"post",
		data:{
			erptkn:tkn,
			id_endoso:nid_endoso
		},
		dataType:"html",
		success: function(res){
			$("#historial_comentario").html(res);
		}
	});
}
