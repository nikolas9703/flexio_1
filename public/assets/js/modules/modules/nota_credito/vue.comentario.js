var notaCreditoComentario = Vue.extend({
  template:"#nota-comentario",
  props:['historial'],
  data:function(){
    return{
      moduloId:'',
      comentario:''
    };
  },
  ready:function(){
    this.$set('moduloId',nota_credito.id);
  },
  methods:{
    limpiarEditor:function(){
      CKEDITOR.instances.tcomentario.setData('');
    },
    guardar_comentario:function(){

      var comentario = CKEDITOR.instances.tcomentario.getData();
      if(!_.isEmpty(comentario)){
        var context = this;
        var datos = {comentario:comentario, modelId:this.moduloId};
        this.$http.post({
          url: phost() + 'notas_creditos/ajax-guardar-comentario',
          method:'POST',
          data:$.extend({erptkn: tkn},datos)
        }).then(function(response){
          if(!_.isEmpty(response.data)){
           self.tablaError="";
           context.$set('historial',response.data);
           CKEDITOR.instances.tcomentario.setData('');
         }
        });
      }
    }
  }
});
