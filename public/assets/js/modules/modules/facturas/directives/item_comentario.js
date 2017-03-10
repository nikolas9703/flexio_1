Vue.directive('item-comentario', {
     params: ['i','comentado'],
    bind:function() {
        var self = this;


        var contenido ='<textarea id="item_comentario'+this.params.i+'"></textarea><br /><br /><button id="close-me'+this.params.i+'" class="btn btn-primary btn-sm pull-right">Comentar</button>';
        $(this.el).webuiPopover({
            title:'Comentario',
            content:contenido,
            trigger:'click',
            delay:300,
            padding:true,
            backdrop:false,
            onShow: function($elm){
                var comentado = self.params.comentado;
                var tiene_comentario =  $('#item_comentario'+self.params.i).val();
                if(_.isEmpty(comentado) && _.isEmpty(tiene_comentario)){
                    comentado = '';
                }else if (!_.isEmpty(tiene_comentario)) {
                    comentado = tiene_comentario;
                }
                $('#item_comentario'+self.params.i).val(comentado);
            }
        });

        $('body').on('click', 'button#close-me'+this.params.i, function() {
       var comentario =  $('#item_comentario'+self.params.i).val();
        if(comentario !==''){
            $(self.el).removeClass("btn btn-default");
            $(self.el).addClass("btn btn-primary");
        }else{
            $(self.el).removeClass("btn btn-primary");
            $(self.el).addClass("btn btn-default");
        }
       $('#comentario'+self.params.i).val(comentario);
       $(self.el).webuiPopover('hide');
     });
 },
 update:function(value){
     var comentado = value;
     if(_.isEmpty(comentado)){
         comentado = '';
         $(self.el).removeClass("btn btn-primary");
         $(self.el).addClass("btn btn-default");
     }else{
         $(this.el).removeClass("btn btn-default");
         $(this.el).addClass("btn btn-primary");
     }
 }
});
