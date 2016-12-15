

var zFlexio = new Vue({

    el: '#z_flexio_div',

    data:{

        mensaje: {

            mensaje: '',
            tipo: 'success',
            titulo: 'Flexio',

        }

    },

    components:{

        'toast_v2': require('./../../vue/components/toastV2.vue')

    },

    ready:function(){

        var context = this;
        if(typeof window.flexio_mensaje != 'undefined' && !_.isEmpty(window.flexio_mensaje)){

            context.mensaje = $.extend(context.mensaje, window.flexio_mensaje);

        }
        
    }

});
