var comentarioTraslados = new Vue({
    el:"#rootApp",
    data:{
        config:{vista:'editar'},
        comentarios:[],
        modelo:'',
        id:''
    },
    ready(){
        this.modelo = 'Flexio\\Modulo\\Traslados\\Models\\Traslados2';
        this.id = traslados_id;
        this.comentarios = coment_traslados;
    },
    components:{
        'vista_comments':require('./../../vue/components/comentario.vue')
    }
});