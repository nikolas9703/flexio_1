var comentarioDevoluciones = new Vue({
    el:"#rootApp",
    data:{
        config:{vista:'editar'},
        comentarios:[],
        modelo:'',
        id:''
    },
    ready(){
            this.modelo = 'Flexio\\Modulo\\Devoluciones\\Models\\Devolucion';
            this.id = devoluciones_id;
            this.comentarios = coment_devoluciones;
    },
    components:{
        'vista_comments':require('./../../vue/components/comentario.vue')
    }
});