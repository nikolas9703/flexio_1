var comentarioEntradas = new Vue({
    el:"#rootApp",
    data:{
        config:{vista:'editar'},
        comentarios:[],
        modelo:'',
        id:''
    },
    ready(){
        this.modelo = 'Flexio\\Modulo\\Entradas\\Models\\Entradas';
        this.id = entrada_id;
        this.comentarios = coment_entrada;
    },
    components:{
        'vista_comments':require('./../../vue/components/comentario.vue')
    }
});