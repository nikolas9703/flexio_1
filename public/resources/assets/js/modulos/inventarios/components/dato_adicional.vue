<template>

    <div class="ibox-title" v-show="dato_adicional.datos_adicionales.length">
        <h5><i class="fa fa-info-circle"></i>&nbsp;Datos adicionales <small v-html="dato_adicional.categoria_nombre"></small></h5>
    </div>
    <div class="ibox-content" v-show="dato_adicional.datos_adicionales.length">
        <div class="row">
            <div class="col-md-3" v-for="row in dato_adicional.datos_adicionales">
                <label v-html="row.llave"></label>
                <input type="text" class="form-control" name="campo[datos_adicionales][{{$index}}][valor]" :disable="waiting" v-model="row.valor">
                <input type="hidden" name="campo[datos_adicionales][{{$index}}][llave]" :value="row.llave">
            </div>
            <p v-if="waiting">Por favor, espere mientras se obtiene la informaci&oacute;n.</p>
        </div>
        <br>
    </div>

</template>

<script>

export default {

    data:function(){
        return {
            dato_adicional:{
                categoria_nombre:'',
                datos_adicionales:[]
            },
            waiting:false
        };
    },

    computed:{
        //...
    },

    watch:{
        //...
    },

    methods:{
        getDatosAdicionales:function(params){
            var context = this;
            context.waiting = true;
            $.ajax({
    			url: phost() + "catalogos_inventario/ajax_get_datos_adicionales",
    			type: "POST",
    			data: $.extend({categoria_id:params.categoria.id}, {erptkn:window.tkn}),
    			dataType: "json",
    			success: function (response) {
    				if (!_.isEmpty(response)) {
    					context.dato_adicional.datos_adicionales = response.data;
                        if(typeof params.detalle.datos_adicionales !== 'undefined')
                        {
                            context.setDatosAdicionales(params.detalle.datos_adicionales);
                        }
    				}
                    context.waiting = false;
    			}
    		});
        },
        setDatosAdicionales:function(datos_adicionales){
            var context = this;
            _.forEach(context.dato_adicional.datos_adicionales, function(dato_adicional){
                var aux = _.find(datos_adicionales, function(o){
                    return dato_adicional.llave == o.llave;
                })
                if(!_.isEmpty(aux))dato_adicional.valor = aux.valor;
            });
        }
    },

    events:{
        ePopulateDatoAdicional:function(params){
            var context = this;
            context.dato_adicional.datos_adicionales = [];
            context.dato_adicional.categoria_nombre = params.categoria.nombre;
            context.getDatosAdicionales(params);
        }
    }

}

</script>
