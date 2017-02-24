var moduloIntereses = (function () {
    return {
        cambiarEstadoIntereses: function (parametros) {
            return $.post(phost() + 'intereses_asegurados/ajax_cambiar_estado_intereses', $.extend({
                erptkn: tkn
            }, parametros));
        },
        ajaxcambiarObtenerPoliticas: function () {
            return $.ajax({
                erptkn: tkn,
                url: "intereses_asegurados/obtener_politicas",
                dataType: "json",
            });
        },
        ajaxcambiarObtenerPoliticasGeneral: function () {
            return $.ajax({
                erptkn: tkn,
                url: "intereses_asegurados/obtener_politicas_general",
                dataType: "json",
            });
        },
        obtenerDetalleAsociado: function (parametros) {
            return $.post(phost() + 'intereses_asegurados/get_detalle_asociado', $.extend({
                erptkn: tkn
            }, parametros));
        },
    };
})();