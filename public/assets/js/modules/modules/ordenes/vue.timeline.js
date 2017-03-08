Vue.transition('listado',{
    enterClass:'fadeIn',
    leaveClass:'fadeOutLeft'
});
var OrdenesHistorial = new Vue({
   
    el:'#vertical-timeline',
    data:{
        historial:[],
     },
    ready:function(){  
      this.historial = timeline_orden.historial;
     },
    components:{
        'timeline': ordenesTimeLine,
    }
});
