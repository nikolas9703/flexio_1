Vue.transition('listado', {
    enterClass: 'fadeIn',
    leaveClass: 'fadeOut'
});

var subcontratoHistorial = new Vue({
    el: '#subcontrato_historial',
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
