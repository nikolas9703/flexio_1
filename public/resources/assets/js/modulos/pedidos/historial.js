Vue.transition('listado', {
  enterClass: 'fadeIn',
  leaveClass: 'fadeOut'
});

var pedidoHistorial = new Vue({
     el: '#pedido_historial',
     data:{
        historial:[],
     },
    ready:function(){  
      this.historial = window.historial;
     },
    components:{
        'timeline': require('./../../vue/components/historial-timeline.vue')
    }
});
