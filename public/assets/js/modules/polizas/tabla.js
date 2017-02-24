$(document).ready(function(){
	var grid_obj = $("#PolizasGrid");
	
	$(function(){
		
		$("#exportarPolizasLnk").on("click", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();
			if($("#PolizasGrid").is(':visible') == true){
				//Exportar Seleccionados del jQgrid
				var ids = [];
				ids = grid_obj.jqGrid('getGridParam','selarrrow');
				
				//Verificar si hay seleccionados
				if(ids.length > 0){
					console.log(ids);
					$('#ids').val(ids);
					$('form#exportarPolizas').submit();
					$('body').trigger('click');
				}
	        }
		});
		
	});

});