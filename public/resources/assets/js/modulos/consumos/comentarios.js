var comentarioConsumos = new Vue({
    el:"#rootApp",
    data:{
        config:{vista:'editar'},
        comentarios:[],
        modelo:'',
        id:''
    },
    ready(){
        this.modelo = 'Flexio\\Modulo\\Consumos\\Models\\Consumos2';
        this.id = consumos_id;
        this.comentarios = coment_consumos;
    },
    components:{
        'vista_comments':require('./../../vue/components/comentario.vue')
    }
});