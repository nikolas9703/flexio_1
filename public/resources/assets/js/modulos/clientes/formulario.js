

var form_crear_cliente = new Vue({

    el: "#form_crear_cliente_div",

    data:{

        comentario: {

            comentarios: [],
            comentable_type: "Flexio\\Modulo\\Cliente\\Models\\Cliente",
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

        if(context.config.vista == 'ver'){

            Vue.nextTick(function(){

                context.comentario.comentarios = JSON.parse(JSON.stringify(window.cliente.comentario_timeline));
                context.comentario.comentable_id = JSON.parse(JSON.stringify(window.cliente.id));

            });

        }

    },

});
