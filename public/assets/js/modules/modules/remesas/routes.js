var moduloRemesa = (function() {
	return {
		cambiarEstadoRemesa: function(parametros) {
			return $.post(phost() + 'remesas/ajax_cambiar_estado_remesa', $.extend({
				erptkn: tkn
			}, parametros));
		},
		 ajaxcambiarObtenerPoliticas: function () {
            return $.ajax({
                erptkn: tkn,
                url: phost() +"intereses_asegurados/obtener_politicas",
                dataType: "json",
            });
        },
        ajaxcambiarObtenerPoliticasGeneral: function () {
            return $.ajax({
                erptkn: tkn,
                url: phost() +"intereses_asegurados/obtener_politicas_general",
                dataType: "json",
            });
        },
	};
})();