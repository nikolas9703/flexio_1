var comentariosContratos = new Vue({
    el:"#rootApp",
    data:{
        config:{vista:'editar'},
        comentarios:[],
        modelo:'',
        id:''
    },
    ready(){

            this.modelo = 'Flexio\\Modulo\\Contratos\\Models\\Contrato';
            this.id = contrato_coment_id;
            this.comentarios = coment_contrato;

    },
    components:{
        'vista_comments':require('./../../vue/components/comentario.vue')
    }
});