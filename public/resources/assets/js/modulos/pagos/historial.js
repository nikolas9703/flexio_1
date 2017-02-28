/**
 * Created by Ivan Cubilla on 11/1/17.
 */
Vue.transition('listado', {
    enterClass: 'fadeIn',
    leaveClass: 'fadeOut'
});

var pedidoHistorial = new Vue({
    el: '#pagos_historial',
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