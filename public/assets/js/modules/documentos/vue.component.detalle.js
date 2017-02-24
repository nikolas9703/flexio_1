var getDetalle = Vue.extend({
    template:'#vista-detalle',
    props:['detalle'],
    data:function(){
        return{
            show:true
        };
    },
    methods:{
        icono:function(tipo){
            if(tipo ==='pdf')
                return  'fa-file-pdf-o';
            if(tipo ==='xls')
                return  'fa-file-excel-o';
            if(tipo ==='doc')
                return  'fa-file-word-o';
            if(tipo ==='jpg')
                return  'fa-book';

        },
        bgcolor:function(tipo){
            return tipo !=='imagen'?'':'flexio-bg';
        },

    }
});