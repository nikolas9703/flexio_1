var refacturarFormulario = new Vue({
    el:'#form_crear_facturas',
    data:{
        acceso:acceso === 1? true: false,
        vista: vista,
        datosFactura:{factura_id:'',cliente:'',termino_pago:'',saldo:'0.00',credito:'0.00',fecha_desde:'',fecha_hasta:'',vendedor:'',lista_precio:'',centro_contable_id:'',comentario:'', estado:'por_aprobar',formulario:'refactura', cuenta:''},
        lista_clientes:[],
        factura_compras: [],
        etapas:[],
        cuentas:[]
    },
    components:{
      'tabla-facturas-compras':tablaComponente
    },
    ready:function(){
      this.fetchClientes();
      if(this.vista ==='refacturar'){
          factura_compra.forEach(function(factura){
            factura.url = phost() + 'facturas_compras/ver/' +factura.uuid_factura;
          });
          this.factura_compras = factura_compra;
          this.cuentas = cuenta_tran;

          this.etapas = etapas;
          var self = this;
          Vue.nextTick(function () {
              self.datosFactura.termino_pago = 'al_contado';
              self.datosFactura.fecha_desde = moment().format('DD/MM/YYYY');
              self.datosFactura.fecha_hasta = moment().add(30, 'days').format('DD/MM/YYYY');
          });

      }else if(this.vista ==='refacturar_ver'){
           var refacturas = factura.refactura;

           refacturas.forEach(function(factura){
             factura.url = phost() + 'facturas_compras/ver/' +factura.uuid_factura;
           });
         
           this.factura_compras = refacturas;
           this.datosFactura.cliente = factura.cliente_id;
           this.datosFactura.termino_pago = factura.termino_pago;
           this.datosFactura.fecha_desde = factura.fecha_desde;
           this.datosFactura.fecha_hasta = factura.fecha_hasta;
           this.datosFactura.vendedor = factura.created_by;
           this.datosFactura.lista_precio = factura.item_precio_id;
           this.datosFactura.centro_contable_id = factura.centro_contable_id;
           this.datosFactura.comentario = factura.comentario;
           this.datosFactura.estado = factura.estado;
           this.datosFactura.saldo = factura.cliente.saldo_pendiente;
           this.datosFactura.credito = factura.cliente.credito;
           this.datosFactura.factura_id = factura.id;
           this.etapas = etapas;
           this.cuentas = cuenta_tran;
           this.datosFactura.cuenta = factura.cuenta;
      }
    },
    computed:{
       total_facturas:function(){
         var monto = _.sumBy(this.factura_compras,function(o) {return parseFloat(o.total);});
         return accounting.toFixed(monto,2);
       },
   },
    methods:{
        fetchClientes:function(){
            var clientes = moduloRefactura.getClientes();
            var self = this;
            clientes.done(function(data){
                self.lista_clientes  = data;
            });
        },
        clienteChange:function(cliente_id){
            if(!_.isEmpty(cliente_id)){
                var clienteActual = _.find(this.lista_clientes,function(query){
                    return query.id == cliente_id;
                });
                this.datosFactura.credito = clienteActual.credito;
                this.datosFactura.saldo = clienteActual.saldo;
            }
        },
        guardar:function(){
            $('#form_crear_facturas').validate({

                ignore: '',
                wrapper: '',
              submitHandler: function(form) {
                $("#total_pago").removeAttr("disabled");
                $("#cliente_id").removeAttr("disabled");
                $("#fecha_desde").removeAttr("disabled");
                $("#guardarBtn").prop("disabled",true);
                form.submit();
              }
            });
        }
    }
});
