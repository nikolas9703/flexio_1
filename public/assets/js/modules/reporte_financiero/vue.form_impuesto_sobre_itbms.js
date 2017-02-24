Vue.directive('selecttwo', {
  twoWay: true,
  bind: function () {
    $(this.el).select2()
    .on("select2:select", function(e) {
      this.set($(this.el).val());
    }.bind(this));
  },
  update: function(nv, ov) {
    $(this.el).trigger("change");
  },
  unbind: function () {
    $(this.el).off().select2('destroy')
    }
});

var formularioImpuestoSobreItbms = Vue.extend({
  template:'#form_impuesto_sobre_itbms',
  data:function(){
    return {
      proveedores:[],
      reporte:{proveedor:'',fecha_desde:'',fecha_hasta:'',tipo:'impuestos_sobre_itbms', impuesto:'itbms'},
      botonDisabled:false
    };
  },
  ready:function(){
    var context = this;
    typeof proveedor_id != 'undefined' ? proveedor_id : '';
    var d = typeof proveedor_id != 'undefined' ? new Date(new Date().getFullYear(), 0, 1) : '';
    var fecha_hoy = moment(d).format('DD/MM/YYYY');    
    //iniciando proveedores select 2
    $('#proveedores').removeClass("chosen-filtro").addClass("form-control").select2({
        ajax: {
            url: phost() + "movimiento_monetario/ajax-cliente-proveedor",
            method: 'POST',
            dataType: 'json',
            delay: 200,
            cache: true,
            data: function (params) {
                return {
                    cliente_proveedor: 1,
                    q: params.term, // search term
                    page: params.page,
                    limit: 10,
                    erptkn: window.tkn
                };
            },
            processResults: function (data, params) {


                let resultsReturn = data.map(resp=> [{
                    'id': resp['id'],
                    'text': resp['nombre']
                }]).reduce((a, b) => a.concat(b), []);

                return {results: resultsReturn};
            },
            escapeMarkup: function (markup) {
                return markup;
            },
        }
    });
  },
  methods:{
    limpiar:function (){
      this.$resetValidation();
      this.reporte={mes:"",year:"",tipo:"impuestos_sobre_itbms", impuesto:"itbms"};
      $("#proveedores").val([]).trigger('change');
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
        reporteFinanciero.$set('reporte','reporte-impuesto-sobre-itbms');
      });
    }
  }
});
$('#exportarReporte').on("click", function(e){             
	e.preventDefault();
	e.returnValue=false;
	e.stopPropagation();
$('#pdf').val('');
});

$('#imprimirReporte').on("click", function(e){             
	e.preventDefault();
	e.returnValue=false;
	e.stopPropagation();
$('#pdf').val('1');
});