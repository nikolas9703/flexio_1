// jshint esversion:6
const moduloFactura = class PopularFormularioFactura{

  constructor(formularioFactura,tipoSeleccionado){
    this.factura = formularioFactura;
    this.tipoSeleccionado = tipoSeleccionado;
  }

  orden_venta(){
      let cliente = this.factura.catalogos.clientes.find((q)=> q.id == this.tipoSeleccionado.cliente_id);
      this.factura.formulario = {
          cliente_id: this.tipoSeleccionado.cliente_id,
          termino_pago: this.tipoSeleccionado.termino_pago,
          fecha_desde: this.tipoSeleccionado.fecha_desde,
          fecha_hasta: this.tipoSeleccionado.fecha_hasta,
          created_by: this.tipoSeleccionado.created_by,
          item_precio_id: this.tipoSeleccionado.item_precio_id,
          centro_contable_id: this.tipoSeleccionado.centro_contable_id,
          centro_facturable: typeof cliente != 'undefined' ? cliente.centro_facturable : [],
          credito_favor: typeof cliente != 'undefined' ? cliente.credito_favor : '',
          saldo_pendiente: typeof cliente != 'undefined' ? cliente.saldo_pendiente : '',
          estado: 'por_aprobar',
          observaciones: this.tipoSeleccionado.observaciones,
      };
      let context = this;
      this.factura.$nextTick(function(){
          context.factura.formulario.centro_facturacion_id = context.tipoSeleccionado.centro_facturacion_id;
          context.factura.$dispatch('OnArticulos',context.tipoSeleccionado.items);
      });
      this.factura.$store.dispatch('SET_ESTADO', 'por_aprobar');
  }

  contrato_venta(){
    let cliente = this.factura.catalogos.clientes.find((q)=> q.id == this.tipoSeleccionado.cliente_id);
      this.factura.formulario = {
          cliente_id: this.tipoSeleccionado.cliente_id,
          termino_pago: 'al_contado',
          fecha_desde: moment().format('DD/MM/YYYY'),
          fecha_hasta: moment().add(30,'days').format('DD/MM/YYYY'),
          created_by: window.usuario_id,
          item_precio_id: '',
          centro_contable_id: this.tipoSeleccionado.centro_contable_id,
          centro_facturable: typeof cliente != 'undefined' ? cliente.centro_facturable : [],
          centro_facturacion_id:'',
          credito_favor: typeof cliente != 'undefined' ? cliente.credito_favor : '',
          saldo_pendiente: typeof cliente != 'undefined' ? cliente.saldo_pendiente : '',
          estado: 'por_aprobar',
          observaciones: '',
      };
      this.factura.$store.dispatch('SET_ESTADO', 'por_aprobar');
  }
  orden_alquiler(){
      let cliente = this.factura.catalogos.clientes.find((q)=> q.id == this.tipoSeleccionado.cliente_id);
      if(_.isUndefined(cliente)){
          //toastr.error("el cliente esta Inactivo", "Cliente");
      }
      this.factura.formulario = {
          cliente_id: this.tipoSeleccionado.cliente_id,
          termino_pago: this.tipoSeleccionado.termino_pago,
          fecha_desde: this.tipoSeleccionado.fecha_desde,
          fecha_hasta: this.tipoSeleccionado.fecha_hasta,
          created_by: this.tipoSeleccionado.created_by,
          item_precio_id: this.tipoSeleccionado.item_precio_id,
          lista_precio_alquiler_id: this.tipoSeleccionado.lista_precio_alquiler_id,
          centro_contable_id: this.tipoSeleccionado.centro_contable_id,
          centro_facturable: typeof cliente != 'undefined' ? cliente.centro_facturable : [],
          credito_favor: typeof cliente != 'undefined' ? cliente.credito_favor : '',
          saldo_pendiente: typeof cliente != 'undefined' ? cliente.saldo_pendiente : '',
          estado: 'por_aprobar',
          observaciones: this.tipoSeleccionado.observaciones,
      };
      this.factura.$store.dispatch('SET_ESTADO', 'por_aprobar');
      let context = this;
      this.factura.$nextTick(function(){
          context.factura.formulario.centro_facturacion_id = context.tipoSeleccionado.centro_facturacion_id;
          if(context.tipoSeleccionado.items.length > 0){
              context.factura.$dispatch('OnArticulos',context.tipoSeleccionado.items);
          }
          if(Object.keys(context.tipoSeleccionado['items_alquiler']).length > 0){
              context.factura.$dispatch('OnArticulosAlquiler',context.tipoSeleccionado['items_alquiler']);
          }
      });
  }
  editar(){
      if(!_.isNull(this.tipoSeleccionado.empezable_type)){
         this.factura.formEmpezable.empezable_type = this.tipoSeleccionado.empezable_type;
         this.factura.formEmpezable.empezable_id = this.tipoSeleccionado.empezable_id;
         this.factura.formEmpezable.aux_empezable_id = this.tipoSeleccionado.empezable_id;
      }
      let cliente = this.factura.catalogos.clientes.find((q)=> q.id == this.tipoSeleccionado.cliente_id);
      this.factura.formulario = {
          id:this.tipoSeleccionado.id,
          cliente_id: this.tipoSeleccionado.cliente_id,
          termino_pago: this.tipoSeleccionado.termino_pago,
          fecha_desde: this.tipoSeleccionado.fecha_desde,
          fecha_hasta: this.tipoSeleccionado.fecha_hasta,
          created_by: this.tipoSeleccionado.created_by,
          item_precio_id: this.tipoSeleccionado.item_precio_id,
          centro_contable_id: this.tipoSeleccionado.centro_contable_id,
          centro_facturable: cliente.centro_facturable,
          credito_favor:cliente.credito_favor,
          saldo_pendiente:cliente.saldo_pendiente,
          estado: this.tipoSeleccionado.estado,
          comentario: this.tipoSeleccionado.comentario,
      };

      if(typeof this.tipoSeleccionado.lista_precio_alquiler_id != 'undefined'){
        this.factura.formulario['lista_precio_alquiler_id'] = this.tipoSeleccionado.lista_precio_alquiler_id;
      }
      
      this.factura.$store.dispatch('SET_ESTADO', this.tipoSeleccionado.estado);
      this.factura.estado_inicial = this.tipoSeleccionado.estado;
      let context = this;
      this.factura.$nextTick(function(){
          context.factura.formulario.centro_facturacion_id = context.tipoSeleccionado.centro_facturacion_id;
          context.factura.$dispatch('OnArticulos',context.tipoSeleccionado.items);
          context.factura.$dispatch('OnArticulosAlquiler',context.tipoSeleccionado.items_alquiler);
      });
  }

};

module.exports = {
  moduloPopularFormulario:moduloFactura
};
