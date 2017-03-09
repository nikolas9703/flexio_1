<template>

    <div class="panel-body">
        <!-- Form Section Start -->
        <div class="row">
            <div class="col-md-3">
                <label>Categor&iacute;a</label>
                <input type="text" class="form-control" disabled="" v-model="detalle.categoria_nombre">
            </div>
            <div class="col-md-3">
                <label>Nombre del campo</label>
                <input type="text" class="form-control" v-model="detalle.nombre">
            </div>
            <div class="col-md-3">
                <label>Requerido</label>
                <select v-select2="detalle.requerido" :config="config.select2">
                    <option value="">Seleccione</option>
                    <option value="no">No</option>
                    <option value="si">S&iacute;</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Incluir en b&uacute;squeda avanzada</label>
                <select v-select2="detalle.en_busqueda_avanzada" :config="config.select2">
                    <option value="">Seleccione</option>
                    <option value="no">No</option>
                    <option value="si">S&iacute;</option>
                </select>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="col-md-3">
                <label>Estado</label>
                <select v-select2="detalle.estado" :config="config.select2">
                    <option value="">Seleccione</option>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
        </div>
    </div>
    <!-- Form Section End -->
    <br>
    <br>
    <!-- Save & Cancel Button Section Start -->
    <div class="row">
        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
            <a href="#" class="btn btn-default btn-block btn-facebook" @click="mCancelarBtn()">
            <i class="fa fa-ban"> </i> Cancelar</a>
        </div>
        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
            <a href="#" class="btn btn-success btn-block btn-facebook" :disabled="disableGuardar" @click="mGuardarBtn()">
            <i class="fa fa-save"> </i> Guardar</a>
        </div>
    </div>

</template>

<script>

export default {

    props:{
        config: Object,
        detalle: Object
    },

    data:function(){
        return {
            disableGuardar: false
        };
    },

    methods:{
        mCancelarBtn:function(){
            this.$root.$emit('eClearForm');
        },
        mGuardarBtn:function(){
            var context = this;
            if(!context.camposRequeridos())return;
            context.disableGuardar = true;
            $.ajax({
    			url: phost() + "catalogos_inventario/ajax_guardar_dato_adicional",
    			type: "POST",
    			data: $.extend(context.detalle, {erptkn:window.tkn}),
    			dataType: "json",
    			success: function (response) {
    				if (!_.isEmpty(response)) {
    					toastr[response.response ? 'success' : 'error'](response.mensaje);
                        context.disableGuardar = false;
                        context.$root.$emit('eClearForm');
                        context.$root.$broadcast('eReloadGrid');
    				}
    			}
    		});
        },
        camposRequeridos: function(){
            var context = this;
            if(context.detalle.nombre == '' || context.detalle.requerido == '' || context.detalle.en_busqueda_avanzada == '' || context.detalle.estado == ''){
                toastr['error']('Todos los campos son requeridos');
                return false;
            }
            return true;
        }
    }

}


</script>
