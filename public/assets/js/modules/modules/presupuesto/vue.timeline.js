Vue.transition('listado',{
    enterClass:'fadeIn',
    leaveClass:'fadeOutLeft'
});
var PresupuestoHistorial = new Vue({
    el:'#vertical-timeline',
    data:{
        historial:[],
        tipo:''
    },
    ready:function(){
        this.historial = timeline.historial;
        this.tipo = timeline.tipo;
    },
    components:{
        'timeline': presupuestoTimeLine,
    }
});
