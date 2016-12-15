var settingsEmpresa = (function() {
	return {
		listarRoles:function(parametros){
			return $.post(phost() + 'empresa/cambio', $.extend({
				erptkn: tkn
			}, parametros));
		},

  };
})();
