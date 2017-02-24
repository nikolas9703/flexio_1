var LandingPaginador = Vue.extend({
    template:'#landing-paginador',
    props:['limite','total'],
    data:function(){
      return{
          paginadorTexto:'Ver m\u00E1s',
          default_limite:3,
          estado:'ver_mas'
      };
  },
    computed:{
        nombre_paginador:function(){
          return this.limite < this.total && this.estado=== 'ver_mas'?'Ver m\u00E1s':'Ocultar';
        }
    },
    methods:{
      mostrar:function(){
        if(this.limite <= this.default_limite){
          this.estado= 'ver_mas';
        }
        if(this.limite < this.total && this.estado =='ver_mas'){
          this.$set('limite',this.limite + 3);
          this.estado= 'ver_mas';
          return;
        }
        if(this.total > 0 &&  this.limite >= this.total){
          this.$set('limite',this.limite - 3);
          this.estado= 'ocultar';

        }
        if(this.estado==='ocultar'){
          this.estado = 'ver_mas';
          return;
        }

      }
    }
});
