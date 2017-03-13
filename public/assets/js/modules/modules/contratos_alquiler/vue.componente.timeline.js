var getTimeLine = Vue.extend({
  template:'#vista-timeline',
  props:['historial'],
  data:function(){   
     return{
      show:true
    };
  },
  methods:{
      icono:function(tipo){
          if(tipo ==='creado')
               return  'fa fa-car';
          if(tipo ==='actualizado')
               return  'fa fa-refresh';
          if(tipo ==='comentario')
               return  'fa fa-comments';
             if(tipo ==='documento')
               return  'fa fa-book';
           
       },
      bgcolor:function(tipo){
        return tipo ==='creado'?'blue-bg':'flexio-bg';
      },
 
  }
 });
