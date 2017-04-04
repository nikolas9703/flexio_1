Vue.transition('listado',{
    enterClass:'fadeIn',
    leaveClass:'fadeOutLeft'
});
var Detalle = new Vue({

    el:'#detalle',
    data:{
        detalle:[],
        icono:'',
        //descarga:''
    },
    ready:function(){
        //console.log(detalle);
        this.detalle = detalle;
    },
    methods:{
        icono:function(tipo){
            if(tipo ==='pdf')
                return  'fa fa-file-pdf-o';
            if(tipo ==='xls')
                return  'fa fa-file-excel-o';
            if(tipo ==='doc')
                return  'fa fa-file-word-o';
            if(tipo ==='jpg')
                return  'fa fa-image-o';
        },
        descarga:function (id) {
            $.ajax({
                url: phost() + "documentos/ajax-descargar-documento-detalle",
                type:"POST",
                data:{
                    erptkn:tkn,
                    documento_id: id
                },
                dataType:"json",
                success: function(data){
                    var fileurl = phost() + data.file_url;
                    //Descargar archivo
                    downloadURL(fileurl, data.file_name);
                }

            });
        }
    }
});
