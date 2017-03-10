Vue.transition('listado',{
    enterClass:'fadeIn',
    leaveClass:'fadeOutLeft'
});
var Historial = new Vue({

    el:'#vertical-timeline',
    data:{
        historial:[],
    },
    ready:function(){
        this.historial = timeline.historial;
    },
    components:{
        'timeline': getTimeLine,
    }
});
