 var adendaComentarios = Vue.extend({
  template:"#nota-comentario",
  props:['historial'],
  data:function(){
    return{
      moduloId:'',
      comentario:''
    };
  },
    ready:function(){
       //   if(vista == 'editar')
      //  {
        	 
           this.$set('moduloId',orden.id);
            var comentarios = orden.comentario.sort(function (a, b) {
                return a.id < b.id;
            });
            this.$set('historial',comentarios); 
     //   }
     },
    methods:{
    	 	limpiarEditor:function(){
    	      CKEDITOR.instances.tcomentario.setData('');
    	    },
    	    guardar_comentario:function(){
      	      var comentario = CKEDITOR.instances.tcomentario.getData();
     	     //if(!_.isEmpty(comentario)){
     	    	  var context = this;
     	    	  
              	$.ajax({
                      url: phost() + "ordenes/ajax-guardar-comentario",
                      type:"POST",
                      data:{
                          erptkn:tkn,
                          comentario:comentario,
                          modelId:context.moduloId
                      },
                      dataType:"json",
                      success: function(response){
                         // if(!_.isEmpty(response)){
                              context.$set('historial',response);
                              console.log(context.historial);
                              CKEDITOR.instances.tcomentario.setData('');
                        //  }
                      }
                  }); 
      	     //}
     	   }
  }
});

 