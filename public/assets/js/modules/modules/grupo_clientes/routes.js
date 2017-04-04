var moduloGrupoClientes = (function() {
  return {
 
    listarGrupos: function(parametros) {
      return $.post(phost() + 'grupo_clientes/ajax-listar', $.extend({
        erptkn: tkn
      }, parametros));
    },
    guardarGrupo: function(element) {
      var parametros = $(element).serialize();
      return $.post(phost() + 'grupo_clientes/ajax-guardar', parametros);
    },
    crearGrupoCliente: function(element) {
      var parametros = $(element).serialize();
      return $.post(phost() + 'grupo_clientes/ajax-guardar', parametros);
    },
    getGrupo:function(parametros){
      return $.post(phost() + 'grupo_clientes/ajax-buscar-grupo', $.extend({
        erptkn: tkn
      }, parametros));
    },
    getListaGrupo:function(){
      return $.post(phost() + 'grupo_clientes/index', {
        erptkn: tkn
      });
    }
  };
})();
