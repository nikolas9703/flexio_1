var moduloEndoso = (function() {
	return {
		cambiarEstadoEndoso: function(parametros) {
			return $.post(phost() + 'endosos/ajax_cambiar_estado_endoso', $.extend({
				erptkn: tkn
			}, parametros));
		},
	};
})();