// jshint esversion:6
import {AnticiposLocalStorage} from './clases/anticipo-local-storage';

var labelAnticiposEmprezable = [];

if(AnticiposLocalStorage.moduloPadre ==='compras'){
  labelAnticiposEmprezable=[
    {label:'Orden de Compra',value:'orden_compra'},{label:'Subcontrato',value:'subcontrato'}
  ];
}else{
  labelAnticiposEmprezable=[
    {label:'Orden de Venta',value:'orden_venta'},{label:'Contrato',value:'contrato'}
  ];
}

var urls_catalogo = {};
if(AnticiposLocalStorage.moduloPadre ==='compras'){
    var urls_catalogo = {
        'orden_compra':'ajax_catalogo/catalogo_ordenes_por_facturar',
        'subcontrato':'ajax_catalogo/catalogo_subcontratos_compras'
    };
}else{
    var urls_catalogo = {
        'orden_venta':'ajax_catalogo/catalogo_ordenes_ventas_por_facturar',
        'contrato':'ajax_catalogo/catalogo_contratos_ventas'
    };
}
//export default urls_catalogo;
var datos_del_header = {
  titulo:'Aplicar anticipo a',
  configSelect2:{width:"100%",placeholder: "Seleccione"},
  categoria:labelAnticiposEmprezable
};
module.exports = {
    urls_catalogo : urls_catalogo,
    info_header:datos_del_header
};
