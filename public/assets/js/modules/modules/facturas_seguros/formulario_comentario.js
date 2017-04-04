var formulario_adenda = new Vue({
  el:"#vue-form-comentario",
  data:{
    acceso: acceso === 1 ? true: false,
    vista: vista,
   vista_comments:"",
    comentarios:[],
  },
  components:{
 	  'comments':anexoComentarios
  },
    ready:function(){
    	
      if(this.vista == 'editar')
      {
             this.vista_comments ="comments";
            this.$nextTick(function(){
                CKEDITOR.replace('tcomentario',
                {
                  toolbar :
                        [
                                { name: 'basicstyles', items : [ 'Bold','Italic' ] },
                                { name: 'paragraph', items : [ 'NumberedList','BulletedList' ] }
                        ],
                  uiColor : '#F5F5F5'
                });
            });
      }
    },
 });
