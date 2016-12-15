var comentariosAccionPersonal = new Vue({
  el:"#rootApp",
  data:{
    config:{vista:'editar'},
    comentarios:[],
    modelo:'',
    id:''
  },

  ready(){

    if(!_.isUndefined(localStorage)){
      this.modelo = localStorage.getItem('model_accion');
      this.id = localStorage.getItem('accion_personal_id');

    }
    this.comentarios = comentario_accion_personal;
  },
  components:{
    'vista_comments':require('./../../vue/components/comentario.vue')
  }
});
