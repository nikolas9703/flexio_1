var ComentarioModulo = Vue.extend({
    template:'#comentario_modulo',
    props:['comentarios','titulo','enlace'],
    data:function(){
      return{
        limite:3,
        comentario:'',
        type_id:''
      };
  },
  ready:function(){
    if(!_.isUndefined(_.head(this.comentarios))){
      this.type_id = _.head(this.comentarios).comentable_id;
    }
  },
  methods:{
    bgcolor:function(index){
      return index % 2 ===0?'color-par':'color-impar';
    }
  },
  components:{
      'paginador':LandingPaginador,
      'texto-comentario':LandingAreaTexto
  }
});
