// jshint esversion:6
const moduloCobros = class PopularFormularioCobros{

  constructor(formularioCobro,tipoSeleccionado){
    this.cobro = formularioCobro;
    this.tipoSeleccionado = tipoSeleccionado;
  }

  cliente(){
      var self = this.cobro;
      this.cobro.clientes = [{id:this.tipoSeleccionado.id,nombre:this.tipoSeleccionado.nombre}];
      this.cobro.formulario.cliente_id = this.tipoSeleccionado.id;
      this.cobro.formulario.saldo_pendiente = this.tipoSeleccionado.saldo_pendiente;
      this.cobro.formulario.credito = this.tipoSeleccionado.credito_favor;
      this.cobro.facturas = this.tipoSeleccionado.facturas;
      this.cobro.anticipo_cliente = this.tipoSeleccionado.anticipos_cliente;
      this.cobro.cache_credito = parseFloat(this.tipoSeleccionado.credito_favor);
      //var facturas = this.tipoSeleccionado.facturas;

      var facturas = this.buildfacturasForCliente(this.tipoSeleccionado.facturas);
      //logica de anticipos
      var anticipos =  [];
      if(_.has(this.tipoSeleccionado, 'anticipos')){
          anticipos =  this.tipoSeleccionado.anticipos_cliente;
      }


      if(anticipos.length ===0){
         this.cobro.filtar_metodo = true;
         this.cobro.formulario.credito = 0;
      }else{
         this.cobro.filtar_metodo = false;
         var cobros = facturas.map((fac)=>fac.cobros).reduce((a, b) => a.concat(b),[]);
         var metodosCobro = cobros.map((cob)=>cob.metodo_cobro).reduce((a, b) => a.concat(b),[]).filter((met)=> met.tipo_pago == 'credito_favor');
         var total_cobrado = _.sumBy(metodosCobro, function(met){
                 return parseFloat(met.total_pagado);
         });
         console.log(_.sumBy(anticipos,(ant)=> parseFloat(ant.monto)));
         console.log(_.sumBy(this.tipoSeleccionado.anticipos,(ant)=> parseFloat(ant.monto)));
         var total_credito = _.sumBy(anticipos,(ant)=> parseFloat(ant.monto)) -total_cobrado;
         if(total_credito === 0){
             this.cobro.filtar_metodo = true;
         }
         this.cobro.formulario.credito = total_credito;
     }
  }
  contrato_venta(){
      this.cobro.clientes = [{id:this.tipoSeleccionado.cliente.id,nombre:this.tipoSeleccionado.cliente.nombre}];
      this.cobro.formulario.cliente_id = this.tipoSeleccionado.cliente.id;
      this.cobro.formulario.saldo_pendiente = this.tipoSeleccionado.cliente.saldo_pendiente;
      this.cobro.formulario.credito = this.tipoSeleccionado.cliente.credito;
      this.cobro.facturas = this.tipoSeleccionado.facturas;

      //logica de anticipos
      var anticipos =  [];
      if(_.has(this.tipoSeleccionado, 'anticipos')){
          anticipos =  this.tipoSeleccionado.anticipos;
      }

      //var cobros = this.tipoSeleccionado.facturas.map((fac)=>fac.cobros).reduce((a, b) => a.concat(b),[]).filter((met)=> met.tipo_pago == 'credito_favor');
      if(anticipos.length ===0){
         this.cobro.filtar_metodo = true;
         this.cobro.formulario.credito = 0;
      }else{
         var cobros = this.tipoSeleccionado.facturas.map((fac)=>fac.cobros).reduce((a, b) => a.concat(b),[]);
         var metodosCobro = cobros.map(cob=> cob.metodo_cobro).reduce((a, b) => a.concat(b),[]).filter((met)=> met.tipo_pago == 'credito_favor');
         var total_cobrado = _.sumBy(metodosCobro, function(met){
                 return parseFloat(met.total_pagado);
         });
         var total_credito = _.sumBy(anticipos,(ant)=> parseFloat(ant.monto)) -total_cobrado;
          this.cobro.filtar_metodo = total_credito === 0?true:false;
         this.cobro.formulario.credito = total_credito;
     }
  }
  factura(){
      this.cobro.clientes = [{id:this.tipoSeleccionado.cliente.id,nombre:this.tipoSeleccionado.cliente.nombre}];
      this.cobro.formulario.cliente_id = this.tipoSeleccionado.cliente.id;
      this.cobro.formulario.saldo_pendiente = this.tipoSeleccionado.cliente.saldo_pendiente;
      //this.cobro.formulario.credito = this.tipoSeleccionado.cliente.credito;
      this.cobro.facturas = [{id:this.tipoSeleccionado.id,codigo:this.tipoSeleccionado.codigo,cobros:this.tipoSeleccionado.cobros,total:this.tipoSeleccionado.total,fecha_desde:this.tipoSeleccionado.fecha_desde,fecha_hasta:this.tipoSeleccionado.fecha_hasta, ordenes_ventas:this.tipoSeleccionado.ordenes_ventas}];
      // logica para ordenes anticipos.
      var ordenes_ventas =  this.tipoSeleccionado.ordenes_ventas;

      if(ordenes_ventas.length === 0){
          this.cobro.filtar_metodo = true;
          this.cobro.formulario.credito = 0;
      }
      if(ordenes_ventas.length > 0){
          var anticipos = _.head(ordenes_ventas).anticipos;
          if(anticipos.length ===0){
             this.cobro.filtar_metodo = true;
             this.cobro.formulario.credito = 0;
         }else{
             this.cobro.filtar_metodo = false;
             var cobros = this.tipoSeleccionado.cobros;
             var metodosCobro = cobros.map(cob=> cob.metodo_cobro).reduce((a, b) => a.concat(b),[]).filter((met)=> met.tipo_pago == 'credito_favor');
             //console.log(JSON.parse(JSON.stringify(metodosCobro)));
             var total_cobrado = _.sumBy(metodosCobro, function(met){
                     return parseFloat(met.total_pagado);
             });
             this.cobro.formulario.credito = _.sumBy(anticipos,(ant)=> parseFloat(ant.monto)) -total_cobrado;
         }
      }
      // si no tiene ordenes filtar metodos de cobros
      // si la orden de venta tiene anticipos setear el total para validarlo
      //con el total cobrado

  }
  orden_trabajo(){
      this.cobro.clientes = [{id:this.tipoSeleccionado.cliente.id,nombre:this.tipoSeleccionado.cliente.nombre}];
      this.cobro.formulario.cliente_id = this.tipoSeleccionado.cliente.id;
      this.cobro.formulario.saldo_pendiente = this.tipoSeleccionado.cliente.saldo_pendiente;
      this.cobro.formulario.credito = this.tipoSeleccionado.cliente.credito;
      this.cobro.facturas = this.tipoSeleccionado.facturas;
  }

  editar(){
     this.cobro.formEmpezable.empezable_type = this.tipoSeleccionado.empezable_type;
     this.cobro.formEmpezable.empezable_id = this.tipoSeleccionado.empezable_id;
     this.cobro.formEmpezable.aux_empezable_id = this.tipoSeleccionado.empezable_id;
     var context = this.cobro;
     var cliente = this.tipoSeleccionado.cliente;
     var facturas = this.tipoSeleccionado.factura_cobros;
     var cobro =  this.tipoSeleccionado;
     Vue.nextTick(function(){
         context.formulario.id = cobro.id;
         context.fecha_pago = cobro.fecha_pago;
         context.clientes = [{id:cliente.id,nombre:cliente.nombre}];
         context.formulario.estado = cobro.estado;
         context.estado_inicial = cobro.estado;
         context.formulario.cliente_id = cliente.id;
         context.formulario.saldo_pendiente = cliente.saldo_pendiente;
         context.formulario.credito = cliente.credito;
         context.facturas = facturas;
         //context.itemPago = [_.sumBy(cobro.cobros_facturas,(o)=> parseFloat(o.monto_pagado) )];
         context.montos = [_.sumBy(cobro.cobros_facturas,(o)=> parseFloat(o.monto_pagado) )];
         context.formulario.depositable_type = cobro.depositable_type;
         context.formulario.depositable_id = cobro.depositable_id;
         context.filas_metodo_cobro = cobro.metodo_cobro.map(function(value, i){
             var referencia = {};
             if(value.tipo_pago =="ach"){
                 referencia ={nombre_banco_ach:value.referencia.nombre_banco_ach,cuenta_cliente:value.referencia.cuenta_cliente};
             }
             if(value.tipo_pago=="cheque"){
                referencia = {numero_cheque:value.referencia.numero_cheque,nombre_banco_cheque:value.referencia.numero_cheque};
             }
             return {icon:i ===0?'fa fa-plus':'fa fa-trash', tipo_pago:value.tipo_pago,total_pagado:value.total_pagado,referencia:referencia};
         });
     });

  }
  buildfacturasForCliente (facturas){

    let objFacturas = [],anticipos=[],metodosCobro=[],cobros=[],total_cobrado=0,total_anticipos=0,cobrado_credito_favor = 0;

    for (let factura of facturas){

      if(_.has(factura, 'contratos') && factura.contratos.length > 0){

        anticipos = factura.contratos.map((cont)=> cont.anticipos).reduce((a, b) => a.concat(b),[]);
        total_anticipos = anticipos.reduce((ant, bef)=> ant.monto + bef.monto);
        if(anticipos.length > 0){
          /*cobros = factura.cobros.map((cob)=>cob).reduce((a, b) => a.concat(b),[]);
          metodosCobro = cobros.map((cob)=>cob.metodo_cobro).reduce((a, b) => a.concat(b),[]).filter((met)=> met.tipo_pago == 'credito_favor');
          total_cobrado = metodosCobro.reduce((bef, aft) => bef.total_pagado + aft.total_pagado);
          objFacturas.push(Object.assign(factura, {tipo:'anticipo',para_anticipo:(total_anticipos - total_cobrado) }));*/
          objFacturas.push(Object.assign(factura, {tipo:'anticipo',total_anticipo:total_anticipos}));
        }else{
          objFacturas.push(Object.assign(factura, {tipo:'normal'}));
        }

      }else if(_.has(factura, 'ordenes_ventas') && factura.ordenes_ventas.length > 0){
        anticipos = factura.ordenes_ventas.map((cont)=> cont.anticipos).reduce((a, b) => a.concat(b),[]);

        total_anticipos = _.sumBy(anticipos,(ant)=>ant.monto);
        if(anticipos.length > 0){
          /*cobros = factura.cobros.map((cob)=>cob).reduce((a, b) => a.concat(b),[]);
          metodosCobro = cobros.map((cob)=>cob.metodo_cobro).reduce((a, b) => a.concat(b),[]).filter((met)=> met.tipo_pago == 'credito_favor');
          total_cobrado = _.sumBy(metodosCobro, (mon)=>mon.total_pagado);
          objFacturas.push(Object.assign(factura, {tipo:'anticipo',para_anticipo:(total_anticipos - total_cobrado) }));*/
          objFacturas.push(Object.assign(factura, {tipo:'anticipo',total_anticipo:total_anticipos}));
        }else{
          objFacturas.push(Object.assign(factura, {tipo:'normal'}));
        }
      }else{
        objFacturas.push(Object.assign(factura, {tipo:'normal'}));
      }

    }
    return objFacturas;

  }
};


module.exports = {
  moduloCobrosInfo:moduloCobros
};
