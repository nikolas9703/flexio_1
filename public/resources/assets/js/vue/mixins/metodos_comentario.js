export default{
  methods:{
           limpiarEditor:function(){
            CKEDITOR.instances.tcomentario.setData('');
          },
          guardar_comentario:function(){

            var comentario = CKEDITOR.instances.tcomentario.getData();

            if(!_.isEmpty(comentario)){
                var context = this;
                $.ajax({
                    url: phost() + "comentarios/ajax_guardar",
                    type:"POST",
                    data:{
                        erptkn:tkn,
                        comentario:comentario,
                        modelId:context.registro_id,
                        modelo:context.modelo
                    },
                    dataType:"json",
                    success: function(response){
                        if(_.has(response,'errors')){
                          toastr.error(response.errors,'comentario');
                          return;
                        }
                            //context.$set('historial',response);
                            context.historial.push(response);
                            CKEDITOR.instances.tcomentario.setData('');
                    }
                });

            }
         }
}
}
