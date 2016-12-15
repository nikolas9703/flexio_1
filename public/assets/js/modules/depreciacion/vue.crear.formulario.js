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
    datos:{centro_contable_id:'',categoria_id:''},
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
      this.datos = depreciacion;
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
        response.data.forEach(function(items){
          var monto_depreciado = parseFloat(items.valor_inicial) * (parseFloat(self.datos.porcentaje) / 100);
          items.categoria = params.categoria;
          items.categoria_id = params.categoria_id;
          items.porcentaje = self.datos.porcentaje;
          items.monto_depreciado = monto_depreciado;
          items.valor_actual =  (items.valor_inicial2 > 0) ? parseFloat(items.valor_inicial2) - monto_depreciado : parseFloat(items.valor_inicial) - monto_depreciado;
        });
         self.tablaError="";
         self.$set('articulos',response.data);
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
      var categoria_id = $("#categoria_id").val();
      //$("#categoria_id").val(categoria_id);
      var categoria = $("#categoria_id").select2('data')[0].text;
      //var categoria = $('#categoria_id option:selected').text();
      var params = {categoria_id: categoria_id, erptkn:tkn, categoria:categoria};
      this.getItemsActivosFijos(params);
  }

  }
});
