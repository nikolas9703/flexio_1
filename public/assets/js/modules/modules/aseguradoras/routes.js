var moduloAseguradora = (function() {
	return {
		ajaxcambiarEstados:function(parametros){
			return $.post(phost() + 'aseguradoras/ajax_cambiar_estados', $.extend({
				erptkn: tkn
			}, parametros));
		},
		ajaxcambiarObtenerPoliticas:function(){
			return $.ajax({
				url: "aseguradoras/obtener_politicas",
				dataType: "json",
			});
		},
		ajaxcambiarObtenerPoliticasGeneral:function(){
			return $.ajax({
				url: "aseguradoras/obtener_politicas_general",
				dataType: "json",
			});
		},
                ajaxcambiarObtenerPoliticasGenerales:function(){
			return $.ajax({
				url: "aseguradoras/obtener_politicasgenerales",
				dataType: "json",
			});
		},
	};
})();

