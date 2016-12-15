var ComponentLandingPage = Vue.extend({
    template:'#landing_page',
    props:[],
    data:function(){
      return{
          modulo:[]
      };
    },
    ready:function(){
        var comentario = _.map(landing,function(lista){
           // var titulo = lista.icono ==="fa fa-line-chart"? 'Ventas / ': 'Compras / ';
            var titulo ='';
            if (lista.icono ==="fa fa-line-chart"){
                titulo = 'Ventas / ';
            }else if (lista.icono ==="fa fa-file-text"){
                titulo = 'Contratos / ';
            }else if (lista.icono ==="fa fa-institution"){
                titulo = 'Nomina / ';
            }else if (lista.icono ==="fa fa-car"){
                titulo = 'Alquileres / ';
            }else if (lista.icono ==="fa fa-wrench"){
                titulo = 'Talleres / ';
            }else if (lista.icono ==="fa fa-users"){
                titulo = 'Recursos Humanos / ';
            }else if (lista.icono ==="fa fa-cubes"){
                titulo = 'Inventario / ';
            }else{
                titulo = 'Compras / ';
            }


            return {'icono':lista.icono,'titulo':titulo+lista.codigo,'comentarios':lista.landing_comments,enlace:lista.enlace};
        });
        this.$set('modulo',comentario);
    },
    components:{
        'comentario-modulo': ComentarioModulo
    }

});
