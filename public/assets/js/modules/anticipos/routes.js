var moduloAnticipo = (function() {
	return {

		ajaxcambiarEstado:function(parametros){
			return $.post(phost() + 'anticipos/ajax-cambiar-estado', $.extend({
				erptkn: tkn
			}, parametros));
		},
		ajaxcambiarEstados:function(parametros){
			return $.post(phost() + 'anticipos/ajax-cambiar-estados', $.extend({
				erptkn: tkn
			}, parametros));
		},
	};
})();
