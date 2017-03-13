Vue.http.options.emulateJSON = true;
//Vue.http.headers.common['erptknckie_secure'] = tkn;
//Vue.http.headers.common['HTTP_X_REQUESTED_WITH'] = true;
Vue.filter('redondeo',{
  read:function(val){
    return accounting.toFixed(val, 2);
  },
  write: function(val,oldVal){
    return isNaN(val) ? 0 : accounting.toFixed(parseFloat(val),2);
  }
});
var depreciacionFormulario = new Vue({
  el:'#depreciacion_crear',
   mixins: [guardar],
  data:{
    acceso: acceso === 1? true : false,
    vista: vista,
    tablaError:'',
    tiposdeitem : window.tiposdeitem,
    datos:{ centro_contable_id:'',
            categoria_id:'',
            porcentaje:'',
            cuenta_id_debito: '',
            cuenta_id_credito: '',
            tipo_item: '',
            referencia: ''
           },
    catalogo_cuentas_transaccionales: window.catalogo_cuentas_transaccionales,
    articulos:[],
    mensaje:"",
    disableDevolucion:false,
    botonDisabled:false
  },
  components:{
    'devolucion-productos':tablaComponenteDepreciacion
  },
  ready:function(){
    if(this.vista==='ver'){
      
      this.datos.centro_contable_id = depreciacion.centro_contable_id;
      this.datos.categoria_id = depreciacion.categoria_id;
      this.datos.cuenta_id_credito = depreciacion.cuenta_id_credito;
      this.datos.cuenta_id_debito = depreciacion.cuenta_id_debito;
      this.datos.tipo_item = depreciacion.tipo_item;
      this.datos.referencia = depreciacion.referencia;
      this.datos.porcentaje = depreciacion.porcentaje;
      depreciacion.items.forEach(function(items){
        items.descripcion = items.items_activo_fijo.descripcion;
        items.nombre = items.items_activo_fijo.nombre;
        items.codigo = items.items_activo_fijo.codigo;
        items.categoria = depreciacion.categoria_item.nombre;
        items.categoria_id = depreciacion.categoria_item.id;
      });
      this.articulos = depreciacion.items;
    }
  },
  methods:{
    getItemsActivosFijos:function(params){
      var self = this;
      this.$http.post({
        url: phost() + 'depreciacion_activos_fijos/ajax-items-activos-fijos',
        method:'POST',
        data:params
      }).then(function(response){
        if(!_.isEmpty(response.data)){
            var articulos =
            _.filter(response.data, function(o){
                return parseFloat(o.valor_inicial2) >= 0;
            });
          articulos.forEach(function(items){
          var monto_depreciado = parseFloat(items.valor_inicial) * (parseFloat(parseFloat(porcentaje.value)) / 100);
          items.categoria = params.categoria;
          items.categoria_id = params.categoria_id;
          items.porcentaje = parseFloat(porcentaje.value);
          items.monto_depreciado = monto_depreciado;
          //items.valor_actual =  (items.valor_inicial2 > 0) ? parseFloat(items.valor_inicial2) - monto_depreciado : parseFloat(items.valor_inicial) - monto_depreciado;
          //case #989 cuando el item no tiene depreciacion valor actual debe ser igual al valor inicial
          items.valor_actual = (items.valor_inicial2 > 0) ? items.valor_inicial2 : items.valor_inicial;
        });
         self.tablaError="";
         self.articulos = articulos;
         //self.$set('articulos',articulos);
       }else{
         self.tablaError="La categoria no tiene items con seriales";
       }
      });

    },
    actualizar:function(e){
      this.$validate(true);
      e.preventDefault();
      if(this.$validation1.invalid) {
        e.preventDefault();
        console.log('falso');
        return false;
      }
    //  $("#categoria_id").trigger('change');
    //  using vue method
      //var categoria_id = $("#categoria_id").val();
      //$("#categoria_id").val(categoria_id);
      //var categoria = $("#categoria_id").select2('data')[0].text;
      //var categoria = $('#categoria_id option:selected').text();
      var params = 
          {
          categoria_id: categoria_id.value,
          tipo_item: tipo_item.value,
          erptkn:tkn, 
          categoria: categoria_id[categoria_id.selectedIndex].innerText
          };
      this.getItemsActivosFijos(params);
  }

  },
  watch:{
      'datos.categoria_id': function(val, oldVal) {
          var context = this;
          if(categoria_id[categoria_id.selectedIndex].getAttribute('porcentaje')!=0){
                porcentaje.value = categoria_id[categoria_id.selectedIndex].getAttribute('porcentaje');
            }
          if(categoria_id[categoria_id.selectedIndex].getAttribute('cuenta_id')!=0){
              context.datos.cuenta_id_debito = categoria_id[categoria_id.selectedIndex].getAttribute('cuenta_id');
          }
      }
  }
 
});
