var moduloAjustadores = (function () {
    return {
        ajaxcambiarEstados: function (parametros) {
            return $.post(phost() + 'ajustadores/ajax_cambiar_estados', $.extend({
                erptkn: tkn
            }, parametros));
        },
        ajaxcambiarObtenerPoliticas: function () {
            return $.ajax({
                url: "ajustadores/obtener_politicas",
                dataType: "json"
            });
        },
        ajaxcambiarObtenerPoliticasGeneral: function () {
            return $.ajax({
                url: "ajustadores/obtener_politicas_general",
                dataType: "json"
            });
        },
        ajaxcambiarObtenerPoliticasGenerales: function () {
            return $.ajax({
                url: "ajustadores/obtener_politicasgenerales",
                dataType: "json"
            });
        }
    };
})();