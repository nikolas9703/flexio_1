var formularioEstadoCuentaCliente = Vue.extend({
  template:'#form_estado_cuenta_cliente',
  data:function(){
    return {
      clientes:[],
      reporte:{cliente:'',fecha_desde:'',fecha_hasta:'',tipo:'estado_de_cuenta_de_cliente',centro_facturacion_id:''},
      botonDisabled:false,
      centros_facturables:[]
    };
  },
  ready:function(){
    var context = this;
    this.$http.post({
      url: phost() + 'reportes_financieros/ajax-formulario-datos',
      method:'POST',
      data:{erptkn: tkn,formulario:'estado_de_cuenta_de_cliente'}
    }).then(function(response){
      if(_.has(response.data, 'session')){
         window.location.assign(phost());
      }

      var catalogos = response.data;
      context.$set('clientes',catalogos.clientes);
    });

  },
  methods:{
    clienteSeleccionado:function(id){
      var cliente = _.find(this.clientes,function(query){
        return query.id == id;
      });
      this.$set('centros_facturables',cliente.centro_facturable || []);
      this.$set('reporte.centro_facturacion_id',"");
    },
    limpiar:function (){
      this.$resetValidation();
      this.reporte={ cliente:"",fecha_desde:"",fecha_hasta:"",centro_facturacion_id:"",tipo:"estado_de_cuenta_de_cliente"};
      reporteFinanciero.$set('dataReporte',[]);
    },
    generar_reporte:function(reporte){
      this.$validate(true);
      if (this.$validar.invalid) {
        return false;
      }
      reporteFinanciero.$set('dataReporte',[]);
      var data_reporte = reporte;
      var context = this;
      this.$http.post({
        url: phost() + 'reportes_financieros/ajax-generar-reporte',
        method:'POST',
        data:$.extend({erptkn: tkn}, data_reporte)
      }).then(function(response){
        if(_.has(response.data, 'session')){
           window.location.assign(phost());
        }
        var datos = response.data;
        reporteFinanciero.$set('dataReporte',[datos]);
        reporteFinanciero.$set('reporte','reporte-estado-cuenta-cliente');
      });
    }
  }
});
