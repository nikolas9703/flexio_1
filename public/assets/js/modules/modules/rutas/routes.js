var moduloRutas = (function() {
  return {
    guardarRutas:function(element){
      var parametros = $(element).serialize();
	  return $.post(phost() + 'catalogos/ajax_guardar_rutas', parametros);
    },
	seleccionarDistrito : function (parametros){
		return $.post(phost() + "catalogos/ajax_listar_distritos", $.extend({
		erptkn: tkn
		},parametros));
	},
	seleccionarCorregimiento : function (parametros){
		return $.post(phost() + "catalogos/ajax_listar_corregimientos", $.extend({
		erptkn: tkn
		},parametros));
	},
	ajaxcambiarEstados : function (parametros){
		return $.post(phost() + "catalogos/ajax_cambiar_estados_rutas", $.extend({
		erptkn: tkn
		},parametros));
	}
 };
})();
