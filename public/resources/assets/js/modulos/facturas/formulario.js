

var form_crear_facturas = new Vue({

    el: "#form_crear_facturas_div",

    data:{

        comentario: {

            comentarios: [],
            comentable_type: "Flexio\\Modulo\\FacturasVentas\\Models\\FacturaVenta",
            comentable_id: '',

        },

        config: {

            vista: window.vista
            

        }

    },

    components:{

        'vista_comments': require('./../../vue/components/comentario.vue')

    },

    ready:function(){

        var context = this;

        if(context.config.vista == 'editar'){

            Vue.nextTick(function(){

                context.comentario.comentarios = JSON.parse(JSON.stringify(window.infofactura.comentario_timeline));
                context.comentario.comentable_id = JSON.parse(JSON.stringify(window.infofactura.id));

            });

        }
    }

});
