Vue.http.options.emulateJSON = true;
var formCentrosFacturable = new Vue({
  el:"#vue-centros-facturables",
  data:{
    cliente_centros:[{id:'',nombre:''}],
    puedeEliminar: false
  },
  ready:function(){
    if(vista ==='ver'){
      if(lista_facturacion.length > 0){
        this.$set('cliente_centros',lista_facturacion);
      }
    }
  },
  methods:{
    addFilas:function(event){
      this.cliente_centros.push({id:'',nombre:''});
    },
    deleteFilas:function(index,id){
      this.centrosConRelaciones(index,id);
    },

    centrosConRelaciones:function(index,id){

      if(_.isNumber(id)){
        var centro = {centro_facturacion_id:id};
        var context = this;
        this.$http.post({
          url: phost() + 'clientes/ajax-centro-facturable',
          method:'POST',
          data:$.extend({erptkn: tkn}, centro)
        }).then(function(response){
          if(_.has(response.data, 'session')){
             window.location.assign(phost());
          }
          if(response.data){
            toastr.info("el centro de facturaci&oacute;n no se puede eliminar");
            context.$set('puedeEliminar',false);
          }else{
            context.cliente_centros.splice(index, 1);
          }

        });
      }else{
        this.cliente_centros.splice(index, 1);
      }

    }

  }

});
