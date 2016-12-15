var moduloNodeAjax = (function(){
	return{
		getNotificaciones:function(parametros){
		  return $.post(phost() +'oportunidades/ajax-notificaciones', $.extend({erptkn: tkn}, parametros));
		},
    marcarLeido:function(parametros){
        return $.post(phost() +'oportunidades/ajax-marcar-leido', $.extend({erptkn: tkn}, parametros));
    }
	};
})();
