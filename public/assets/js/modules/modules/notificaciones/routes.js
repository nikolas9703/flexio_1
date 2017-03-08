/**
 * Created by Ivan Cubilla on 2/11/16.
 */
var moduloNotificacion = (function() {
    return {
        guardar:function(element){
            var parametros = $(element).serialize();
            return $.post(phost() + 'notificaciones/ajax-guardar', parametros);
        },
        transaccion:function(element){
            var parametros = $(element).serialize();
            return $.post(phost() + 'notificaciones/ajax-transaccion', parametros);
        },
    };
})();