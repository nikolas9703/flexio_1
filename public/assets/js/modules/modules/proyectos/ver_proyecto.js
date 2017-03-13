$(function(){
 	
	//jQuery Validate
	$('#editarProyecto').validate({
		focusInvalid: true,
		ignore: '',
		wrapper: '',
	});
	
 
});

//Verificar si tiene permisos 
//de editar el fomrulario
if(permiso_editar_proyecto == "false"){
	$("#editarProyecto").find('select, input, button, textarea').prop("disabled", "disabled");
}