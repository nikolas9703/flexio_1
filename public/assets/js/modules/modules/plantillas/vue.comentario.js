var anexoComentarios = Vue.extend({
    template:"#nota-comentario",
    props:['historial'],
    data:function(){
        return{
            moduloId:'',
            comentario:''
        };
    },
    ready:function(){
        if(vista == 'ver')
        {
            this.$set('moduloId',id_plant_detalle);
            var comentarios = coment;
            // console.log(comentarios);
            this.$set('historial',comentarios);
        }
    },
    methods:{
        limpiarEditor:function(){
            CKEDITOR.instances.tcomentario.setData('');
        },
        guardar_comentario:function(){

            var comentario = CKEDITOR.instances.tcomentario.getData();

            if(!_.isEmpty(comentario)){
                var context = this;
                $.ajax({
                    url: phost() + "plantillas/ajax-guardar-comentario",
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
                           // console.log(context.historial);
                            CKEDITOR.instances.tcomentario.setData('');
                        }
                    }
                });

            }
        }
    }
});
