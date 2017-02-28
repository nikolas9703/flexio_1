var labelEmpezable = [
  {label:'Ordenes Ventas',value:'orden_venta'},
  //{label:'Ordenes de Trabajo',value:'ordenes_trabajo'},
  {label:'Contrato de venta',value:'contrato_venta'},
  {label:'Orden de venta de alquiler',value:'orden_alquiler'}
];

var urls_catalogo = {
    'orden_venta':'facturas/ajax_catalogo_ordenes_ventas',
    //'ordenes_trabajo':'facturas/ajax_catalogo_ordenes_trabajo',
    'contrato_venta':'facturas/ajax_catalogo_contrato_ventas',
    'orden_alquiler':'facturas/ajax_catalogo_ordenes_alquiler'
};
var datos_del_header = {
  titulo:'Empezar factura desde',
  configSelect2:{width:"100%",placeholder: "Seleccione"},
  categoria:labelEmpezable
};

var tablasAlquiler = {
  'orden_alquiler':'tabla-cargos-alquiler',
  'contrato_alquiler':'tabla-ordenes-alquiler'
};

module.exports = {
    urls_catalogo : urls_catalogo,
    datos_del_header:datos_del_header,
    tablasAlquiler: tablasAlquiler
};
