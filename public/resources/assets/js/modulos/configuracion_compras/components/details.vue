<template>

    <div class="panel-body">
        <!-- Form Section Start -->
        <div class="row">
            <div class="col-md-3">
                <label>M&oacute;dulo</label>
                <select v-select2="detalle.modulo" :config="config.select2">
                    <option value="">Seleccione</option>
                    <option :value="row.etiqueta" v-for="row in getModules" v-html="row.valor"></option>
                </select>
            </div>
            <div class="col-md-3" v-if="mCompras()">
                <label>Categor&iacute;a(s) del item</label>
                <select multiple="" v-select2="detalle.categorias" :config="config.select2">
                    <option value="">Seleccione</option>
                    <option :value="row.id" v-for="row in catalogos.categorias" v-html="row.nombre"></option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Descripci&oacute;n</label>
                <input type="text" class="form-control" v-model="detalle.descripcion">
            </div>
            <div class="col-md-3" v-if="!mCompras()">
                <label>Estado</label>
                <select v-select2="detalle.estado" :config="config.select2">
                    <option value="">Seleccione</option>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="col-md-3" v-if="mCompras()">
                <label>Estado</label>
                <select v-select2="detalle.estado" :config="config.select2">
                    <option value="">Seleccione</option>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
            <div style="clear:both"></div>
            <br><br>
        </div>
        <div class="row" v-show="detalle.id != ''">
            <div class="col-md-12">
                <textarea
                    class="form-control"
                    id="editor1"
                    rows="10"
                    placeholder=""
                    :config="config.tinymce"
                    v-tinymce="detalle.content">
                </textarea>
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
            <a href="#" class="btn btn-default btn-block btn-facebook" @click.prevent="mCancelarBtn()">
            <i class="fa fa-ban"> </i> Cancelar</a>
        </div>
        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
            <a href="#" class="btn btn-success btn-block btn-facebook" :disabled="disableGuardar" @click.prevent="mGuardarBtn()">
            <i class="fa fa-save"> </i> Guardar</a>
        </div>
    </div>

</template>

<script>

export default {

    props:{
        config: Object,
        detalle: Object,
        catalogos: Object
    },

    data:function(){
        return {
            disableGuardar: false,
            modulos:[
                //{etiqueta:'pedidos', valor:'Pedidos'},need add to pdf
                {etiqueta:'ordenes', valor:'&Oacute;rdenes de compra'},
                //{etiqueta:'facturas_compras', valor:'Facturas de compra'},need add to pdf
                {etiqueta:'cotizaciones', valor:'Cotizaciones'},
                //{etiqueta:'ordenes_ventas', valor:'&Oacute;rdenes de venta'},need add to pdf
                //{etiqueta:'facturas', valor:'Facturas de venta'},need add to pdf
            ]
        };
    },

    computed:{
        getModules:function(){
            var context = this;
            return _.filter(context.modulos, function(modulo){
                if(context.mCompras()){
                    return modulo.etiqueta == 'pedidos' || modulo.etiqueta == 'ordenes' || modulo.etiqueta == 'facturas_compras';
                }else if(context.mVentas()){
                    return modulo.etiqueta == 'cotizaciones' || modulo.etiqueta == 'ordenes_ventas' || modulo.etiqueta == 'facturas';
                }
                return false;//dont forget ventas module logic...
            });
        }
    },

    methods:{
        mCompras:function(){
            return this.config.msSelected == 'compras';
        },
        mVentas:function(){
            return this.config.msSelected == 'ventas';
        },
        mCancelarBtn:function(){
            this.$root.$emit('eClearForm');
        },
        mGuardarBtn:function(){
            var context = this;
            if(!context.camposRequeridos())return;
            context.disableGuardar = true;
            $.ajax({
    			url: phost() + "configuracion_compras/ajax_guardar_termino_condicion",
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
            if(context.mCompras() && (context.detalle.modulo == '' || (!context.detalle.categorias || context.detalle.categorias.length == 0) || context.detalle.descripcion == '' || context.detalle.estado == '')){
                toastr['error']('Todos los campos son requeridos');
                return false;
            }else if(context.mVentas() && (context.detalle.modulo == '' || context.detalle.descripcion == '' || context.detalle.estado == '')){
                toastr['error']('Todos los campos son requeridos');
                return false;
            }
            return true;
        }
    }

}


</script>
