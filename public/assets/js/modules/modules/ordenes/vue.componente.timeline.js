var ordenesTimeLine = Vue.extend({
  template:'#ordenes-timeline',
  props:['historial'],
  data:function(){   
     return{
      show:true
    };
  },
  methods:{
      icono:function(tipo){
          if(tipo ==='creado')
               return  'fa-floppy-o';
          if(tipo ==='actualizado')
               return  'fa-pencil-square-o';
          if(tipo ==='comentario')
               return  'fa-comment-o';
           
       },
      bgcolor:function(tipo){
        return tipo ==='creado'?'blue-bg':'flexio-bg';
      },
 
  }
 });
