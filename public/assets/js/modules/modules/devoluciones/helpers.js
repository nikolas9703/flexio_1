var moduloDevoluciones = (function() {
  return {
    empezarDesde: function(parametros) {
      return $.post(phost() + 'facturas/ajax_getFacturasDevoluciones',  $.extend({
        erptkn: tkn
      }, parametros));
    },
    filtrar: function(lista, uuid) {
      var seleccionado = _.find(lista,function(item){
        return item.uuid_factura == uuid;
      });
      devolucionFormulario.$set('datosDevolucion.cliente_id',seleccionado.cliente_id);
      devolucionFormulario.$set('datosDevolucion.fecha_factura',seleccionado.fecha_desde);
      devolucionFormulario.$set('datosDevolucion.saldo',seleccionado.cliente.saldo_pendiente);
      devolucionFormulario.$set('datosDevolucion.credito',seleccionado.cliente.credito_favor);
      devolucionFormulario.$set('datosDevolucion.centro_contable_id',seleccionado.centro_contable_id);
      devolucionFormulario.$set('datosDevolucion.bodega_id',seleccionado.bodega_id);
      devolucionFormulario.$set('datosDevolucion.created_by',seleccionado.created_by);
      devolucionFormulario.$set('datosDevolucion.estado','por_aprobar');
      devolucionFormulario.$set('datosDevolucion.factura_id',seleccionado.id);
      seleccionado.items.forEach(function(items){
        items.precio_total = 0;
        items.id='';
      });
      devolucionFormulario.$set('articulos',seleccionado.items);
      if(_.isEmpty(seleccionado.items)){
        devolucionFormulario.$set('botonDisabled',true);
        devolucionFormulario.$set('mensajeError','La factura no tiene Items asociada');
      }else{
        devolucionFormulario.$set('botonDisabled',false);
        devolucionFormulario.$set('mensajeError','');
      }

      setTimeout(function() {
      $(".select2").select2({ theme: "bootstrap", width:"100%" });
      devolucionFormulario.$set('disableDevolucion',true);
       }, 300);
    },


  };
})();
