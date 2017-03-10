$(document).ready(function() {	
	var fecha = $('input[name="options"]:checked').val();
	var parametros = {fecha:fecha,erptkn: tkn};
	var oportunidades = moduloPerfil.getOportinudades(parametros);
	var actividades = moduloPerfil.getActividades(parametros);
	var agentes = moduloPerfil.getAgentes(parametros);
$('input[type=radio][name=options]').change(function(){
		 var fecha = this.value;
		 var parametros = {fecha:fecha,erptkn: tkn};
		 var oportunidades = moduloPerfil.getOportinudades(parametros);
		 var actividades = moduloPerfil.getActividades(parametros);
		 var agentes = moduloPerfil.getAgentes(parametros);
		 
});	
$("#masOpciones").on("click", ".viewOptions", function(e){
		e.preventDefault();
		e.returnValue=false;
		e.stopPropagation();
		var id_usuario = $(this).attr("data-usuario");
		var rowINFO = $("#usuariosGrid").getRowData(id_usuario);
	    //var nombre_usuario = rowINFO["nombre_agencia"];
	    var options = rowINFO["options"];

	    //Init boton de opciones
		//$('#optionsModal').find('.modal-title').empty().append('Opciones ('+ nombre_agencia +'):');
		$('#optionsModal').find('.modal-body').empty().append(options);
		$('#optionsModal').find('.modal-footer').empty();
		$('#optionsModal').modal('show');
	});
});