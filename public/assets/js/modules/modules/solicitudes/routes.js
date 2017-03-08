var moduloSolicitudes = (function(){
  return {
      cambiarEstadoSolicitudes : function (parametros){
        return $.post(phost() + "solicitudes/ajax_cambiar_estado_solicitudes", $.extend({
          erptkn: tkn
        },parametros));
      },
       ajaxcambiarObtenerPoliticas: function () {
            return $.ajax({
                url: "solicitudes/obtener_politicas",
                dataType: "json",
            });
        },
        ajaxcambiarObtenerPoliticasGeneral: function () {
            return $.ajax({
                url: "solicitudes/obtener_politicas_general",
                dataType: "json",
            });
        },
        ajaxguardarsolicitud: function (parametros) {
          return $.post(phost() + "solicitudes/guardar", $.extend({
            erptkn: tkn
          },parametros));
        },

    };
})();

var moduloSolicitudesBitacora = (function(){
  return {
      cambiarEstadoSolicitudesBitacora : function (parametros){
        return $.post(phost() + "solicitudes/ajax_cambioestado_bitacora", $.extend({
          erptkn: tkn
        },parametros));
      },
    };
})();

var moduloSolicitudesPoliza = (function(){
  return {
      verificarPoliza : function (parametros){
        return $.post(phost() + "solicitudes/ajax_verifica_poliza", $.extend({
          erptkn: tkn
        },parametros));
      },
    };
})();
