/**
 * Created by Ivan Cubilla on 10/1/17.
 */
Vue.transition('listado', {
    enterClass: 'fadeIn',
    leaveClass: 'fadeOut'
});

var pedidoHistorial = new Vue({
    el: '#factura_historial',
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
