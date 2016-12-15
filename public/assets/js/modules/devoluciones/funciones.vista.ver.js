var moduloDevoluciones = (function() {
  return {
    vistaVer: function() {
      devolucionFormulario.$set('devolucionHeader.tipo',factura);
      devolucionFormulario.$set('listaTipo',[{uuid:devolucion.facturas.uuid_factura}]);
      devolucionFormulario.$set('devolucionHeader.tipo',factura);
      //this.devolucionHeader.uuid = devolucion.facturas.uuid_factura;
    },

  };
})();
