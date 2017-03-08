var moduloContabilidad = (function() {
  return {
    crearPlan: function() {
      return $.post(phost() + 'contabilidad/ajax-cargar-plan-contable', {
        erptkn: tkn
      });
    },
    listarCuenta: function(parametros) {
      return $.post(phost() + 'contabilidad/ajax-listar-cuentas', $.extend({
        erptkn: tkn
      }, parametros));
    },
    getCodigo: function(parametros) {
      return $.post(phost() + 'contabilidad/ajax-codigo', $.extend({
        erptkn: tkn
      }, parametros));
    },
    guardarCuenta: function(element) {
      var parametros = $(element).serialize();
      return $.post(phost() + 'contabilidad/ajax-guardarCuenta', parametros);
    },
    cambiarEstadoCentroContable: function(parametros) {
      return $.post(phost() + 'contabilidad/ajax-cambiar-estado-centro-contable', $.extend({
        erptkn: tkn
      }, parametros));
    },
    cambiarEstadoCuentaContable: function(parametros) {
      return $.post(phost() + 'contabilidad/ajax-cambiar-estado-cuenta-contable', $.extend({
        erptkn: tkn
      }, parametros));
    },
    crearCentroContable: function(element) {
      var parametros = $(element).serialize();
      return $.post(phost() + 'contabilidad/ajax-guardarCentro', parametros);
    },
    getCentro: function(parametros) {
      return $.post(phost() + 'contabilidad/ajax-buscar-centro', $.extend({
        erptkn: tkn
      }, parametros));
    },
    getCuenta:function(parametros){
      return $.post(phost() + 'contabilidad/ajax-buscar-cuenta', $.extend({
        erptkn: tkn
      }, parametros));
    },
    guardarImpuesto:function(element){
      var parametros = $(element).serialize();
      return $.post(phost() + 'contabilidad/ajax-guardar-impuesto', parametros);
    },
    cambiarEstadoImpuesto:function(parametros){
      return $.post(phost() + 'contabilidad/ajax-cambiar-estado-impuesto', $.extend({
        erptkn: tkn
      }, parametros));
    },
    getListaCentro:function(){
      return $.post(phost() + 'contabilidad/ajax-lista-centros-contables', {
        erptkn: tkn
      });
    }
  };
})();
