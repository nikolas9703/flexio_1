var moduloReclamos = (function(){
  return {
      cambiarEstadoReclamos : function (parametros){        
        return $.post(phost() + "reclamos/ajax_cambiar_estado_reclamos", $.extend({
          erptkn: tkn
        },parametros));
      },
       ajaxcambiarObtenerPoliticas: function () {
            return $.ajax({
                url: "reclamos/obtener_politicas",
                dataType: "json",
            });
        },
        ajaxcambiarObtenerPoliticasGeneral: function () {
            return $.ajax({
                url: "reclamos/obtener_politicas_general",
                dataType: "json",
            });
        },

    };
})();

var moduloReclamosBitacora = (function(){
  return {
      cambiarEstadoReclamosBitacora : function (parametros){
        return $.post(phost() + "reclamos/ajax_cambioestado_bitacora", $.extend({
          erptkn: tkn
        },parametros));
      },
    };
})();

var moduloReclamosPoliza = (function(){
  return {
      verificarPoliza : function (parametros){
        return $.post(phost() + "reclamos/ajax_verifica_poliza", $.extend({
          erptkn: tkn
        },parametros));
      },
    };
})();
