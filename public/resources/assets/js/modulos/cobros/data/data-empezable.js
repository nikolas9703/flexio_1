var labelEmpezable = [
  {label:'Clientes',value:'cliente'},
  {label:'Contrato de venta',value:'contrato_venta'},
  {label:'Facturas',value:'factura'},
  {label:'Ordenes de trabajo',value:'orden_trabajo'}

];

var urls_catalogo = {
    'cliente':'cobros/catalogo_clientes_activo',
    'contrato_venta':'cobros/catalogo_contratos_ventas',
    'factura':'cobros/facturas',
    'orden_trabajo':'cobros/orden_trabajo'
};
var datos_del_header = {
  titulo:'Aplicar cobro a',
  configSelect2:{width:"100%",placeholder: "Seleccione"},
  categoria:labelEmpezable
};

module.exports = {
    urls_catalogo : urls_catalogo,
    info_header:datos_del_header
};
