// jshint esversion:6
// clase para saber si es compras o ventas
var AnticiposLocal = class AnticiposLocalStorage{

  constructor(){
    this.localstorage = window.localStorage;
  }
  get moduloPadre(){
    var padre = _.isEmpty(this.localstorage.getItem("ms-selected"))?'compras':this.localstorage.getItem("ms-selected");
    var aux = this.localstorage.getItem("ml-parent-selected");
    if(padre ==="compras" || aux==='Precio fijo con proveedores' || padre === "contratos"){
      return 'compras';
    }
    return  'ventas';
  }
  get tipoAnticipable(){
    if(this.moduloPadre ==='compras' || this.moduloPadre === 'contratos'){
      return 'proveedor';
    }
    return 'cliente';
  }
};

module.exports = {
  AnticiposLocalStorage:new AnticiposLocal()
};
