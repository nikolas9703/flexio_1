 var anexoComentarios = Vue.extend({
  template:"#nota-comentario",
  props:['historial'],
  data:function(){
    return{
      moduloId:'',
      comentario:''
    };
  },
    ready:function(){
          if(vista == 'ver')
         {
             this.$set('moduloId',cliente.id);
             var comentarios = cliente.comentario_timeline.sort(function (a, b) {
            	 return a.id < b.id;
            	 //return a.id;
            });
             this.$set('historial',comentarios);
          }
     },
    methods:{
    	 	limpiarEditor:function(){
    	      CKEDITOR.instances.tcomentario.setData('');
    	    },
    	    guardar_comentario:function(){
 
    	      var comentario = CKEDITOR.instances.tcomentario.getData();
     	      
    	      if(!_.isEmpty(comentario)){
    	    	  var context = this;
                  $.ajax({
                      url: phost() + "clientes/ajax-guardar-comentario",
                      type:"POST",
                      data:{
                          erptkn:tkn,
                          comentario:comentario,
                          modelId:context.moduloId
                      },
                      dataType:"json",
                      success: function(response){
                          if(!_.isEmpty(response)){
                              context.$set('historial',response);
                              console.log(context.historial);
                              CKEDITOR.instances.tcomentario.setData('');
                          }
                      }
                  });
                  
     	      }
     	   }
  }
});
 