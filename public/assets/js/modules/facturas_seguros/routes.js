var moduloRefactura = (function() {
  return {
    getClientes:function(){
        return $.post(phost() + 'facturas/ajax-cliente-info', {
          erptkn: tkn
        });
    },
};
})();
