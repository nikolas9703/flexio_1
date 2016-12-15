// jshint esversion:6
const moduloReferencia = class ModuloReferenciaUrl{

  constructor(padreVue,empezable){
    this.padrevue = padreVue;
    this.empezable = empezable;
  }

   orden_compra(){
     var self = this.padrevue;
      this.padrevue.$nextTick(function(){
        self.header_empezable.empezable_type = 'orden_compra';
        self.header_empezable.aux_empezable_id =  self.referencia.desde.orden_compra;
       $("#empezable_type").prop('disabled',true);
     });
   }
   subcontrato(){
     var self = this.padrevue;
     this.padrevue.$nextTick(function(){
         self.header_empezable.empezable_type = 'subcontrato';
         self.header_empezable.aux_empezable_id = self.referencia.desde.subcontrato;
       $("#empezable_type").prop('disabled',true);
     });
   }

   proveedor(){
     this.padrevue.aplicable(_.toString(this.padrevue.referencia.desde.proveedor));
     this.padrevue.$nextTick(function(){
       $("#empezable_type").prop('disabled',true);
     });
   }

   orden_venta() {
     var self = this.padrevue;
      this.padrevue.$nextTick(function(){
        self.header_empezable.empezable_type = 'orden_venta';
        self.header_empezable.aux_empezable_id =  self.referencia.desde.orden_venta;
        self.header_empezable.empezable_id = self.referencia.desde.orden_venta;
       $("#empezable_type").prop('disabled',true);
     });
   }
   contrato(){
     var self = this.padrevue;
     this.padrevue.$nextTick(function(){
         self.header_empezable.empezable_type = 'contrato';
         self.header_empezable.aux_empezable_id = self.referencia.desde.contrato;
       $("#empezable_type").prop('disabled',true);
     });
   }
   cliente(){
     this.padrevue.aplicable(_.toString(this.padrevue.referencia.desde.cliente));
     this.padrevue.$nextTick(function(){
       $("#empezable_type").prop('disabled',true);
     });
   }
};

module.exports = {
  ModuloReferenciaUrl:moduloReferencia
};
