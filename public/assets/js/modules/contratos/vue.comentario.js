var adendaComentarios = Vue.extend({
  template:"#nota-comentario",
  props:['historial'],
  data:function(){
    return{
      moduloId:'',
      comentario:''
    };
  },
    ready:function(){
        if(vista == 'editar')
        {
            this.$set('moduloId',adenda.id);
            var comentarios = adenda.comentario.sort(function (a, b) {
                return a.id < b.id;
            });
            this.$set('historial',comentarios);
        }
    },
    methods:{
        limpiarEditor:function(e){
            e.preventDefault();
            e.stopPropagation();
            CKEDITOR.instances.tcomentario.setData('');
        },
        guardar_comentario:function(e){
            e.preventDefault();
            e.stopPropagation();
            
            var comentario = CKEDITOR.instances.tcomentario.getData();
            if(!_.isEmpty(comentario)){
                var context = this;
                $.ajax({
                    url: phost() + "contratos/ajax-guardar-comentario",
                    type:"POST",
                    data:{
                        erptkn:tkn,
                        comentario:comentario,
                        modelId:context.moduloId
                    },
                    dataType:"json",
                    success: function(response){
                        if(!_.isEmpty(response)){
                            context.$set('historial',response);
                            CKEDITOR.instances.tcomentario.setData('');
                        }
                    }
                });
            }
        }
  }
});
