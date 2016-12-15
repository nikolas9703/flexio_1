var comentarioBodegas = new Vue({
    el:"#rootApp",
    data:{
        config:{vista:'editar'},
        comentarios:[],
        modelo:'',
        id:''
    },
    ready(){
        this.modelo = 'Flexio\\Modulo\\Bodegas\\Models\\Bodegas2';
        this.id = bodega_id;
        this.comentarios = bodega_coment;
    },
    components:{
        'vista_comments':require('./../../vue/components/comentario.vue')
    }
});