var moduloPrecio = (function() {
    return {
        asignarPrincipal: function(parametros) {
            return $.post(phost() + 'catalogos_inventario/ajax-select-precio', $.extend({
              erptkn: tkn
            }, parametros));
        },
    };
})();
