var moduloContacto = (function() {
  return {
    asignarPrincipal: function(parametros) {
      return $.post(phost() + 'contactos/ajax-asignar-contacto-principal', $.extend({
        erptkn: tkn
      }, parametros));
    },
  };
  })();
