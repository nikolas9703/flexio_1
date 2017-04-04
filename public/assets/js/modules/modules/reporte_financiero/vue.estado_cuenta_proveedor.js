Vue.directive('datepicker', {
  bind: function () {
    var vm = this.vm;
    var key = this.expression;
    var context = this;
    $(this.el).datepicker({
      dateFormat: "dd/mm/yy",
      onSelect: function (date) {
        vm.$set(key, date);
      },
      onClose: function( selectedDate ) {
        if(context.el.id ==='fecha_desde'){
           $("#fecha_hasta").datepicker( "option", "minDate", selectedDate );
        }
        if(context.el.id ==='fecha_hasta'){
          $("#fecha_desde").datepicker( "option", "maxDate", selectedDate );
        }
      }
    });

  },
  update: function (val) {
    $(this.el).datepicker('setDate', val);
  }
});
var formularioEstadoCuentaProveedor = Vue.extend({
  template:'#estado_cuenta_proveedor',
  data:function(){
    return {
      proveedores:[],
      reporte:{proveedor:'',fecha_desde:'',fecha_hasta:'',tipo:'estado_cuenta_proveedor'},
      botonDisabled:false
    };
  },
  ready:function(){
    var context = this;
    typeof proveedor_id != 'undefined' ? proveedor_id : '';
    var d = typeof proveedor_id != 'undefined' ? new Date(new Date().getFullYear(), 0, 1) : '';
    var fecha_hoy = moment(d).format('DD/MM/YYYY');
    this.$http.post({
      url: phost() + 'reportes_financieros/ajax-formulario-datos',
      method:'POST',
      data:{erptkn: tkn,formulario:'estado_cuenta_proveedor'}
    }).then(function(response){
      var catalogos = response.data;
      context.$set('proveedores',catalogos.provedores);
      context.$set('reporte.proveedor',proveedor_id);
      context.$set('reporte.fecha_desde',fecha_hoy);
    });
  },
  methods:{
    limpiar:function (){
      this.$resetValidation();
      this.reporte={ proveedor:"",fecha_desde:"",fecha_hasta:"",tipo:"estado_cuenta_proveedor"};
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
        reporteFinanciero.$set('reporte','reporte-estado-cuenta-proveedor');
      });
    }
  }
});
