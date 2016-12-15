<template>

    <div class="ibox-title border-bottom" id="datos_generales">
        <h5>Detalles del item</h5>
    </div>

    <div class="ibox-content" style="display: block; border:0px">

        <div class="row">

            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3" :class="config.disableGuardar == true ? 'has-warning' : ''">
                <label>Número de item <span required="" aria-required="true">*</span></label>
                <input type="text" name="campo[codigo]" class="form-control" data-rule-required="true" aria-required="true" v-model="detalle.codigo" @blur="checkCodigo(detalle.codigo)" :disabled="config.disableDetalle">
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                <label>Nombre de item<span required="" aria-required="true">*</span></label>
                <input type="text" name="campo[nombre]" class="form-control" data-rule-required="true" aria-required="true" v-model="detalle.nombre" :disabled="config.disableDetalle">
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <label>Descripción </label>
                <input type="text" name="campo[descripcion]" class="form-control" v-model="detalle.descripcion" :disabled="config.disableDetalle">
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3" style="clear:both;">
                <label>Tipo de item <span required="" aria-required="true">*</span></label>
                <select name="campo[tipo_id]" data-rule-required="true" aria-required="true" v-model="detalle.tipo_id" v-select2="detalle.tipo_id" :config="config.select2" :disabled="config.disableDetalle">
                    <option value="">Seleccione</option>
                    <option :value="tipo.id_cat" v-for="tipo in catalogos.tipos">{{tipo.etiqueta}}</option>
                </select>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
                <label>Categoría(s) del item <span required="" aria-required="true">*</span></label>
                <select name="campo[categorias][]" data-rule-required="true" aria-required="true" multiple="multiple" v-select2="detalle.categorias" :config="config.select2" :disabled="config.disableDetalle">
                    <option :value="categoria.id" v-for="categoria in catalogos.categorias">{{categoria.nombre}}</option>
                </select>
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                <label>Código de barra </label>
                <input type="text" name="campo[codigo_barra]" class="form-control" v-model="detalle.codigo_barra" :disabled="config.disableDetalle">
            </div>

            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                <label>Estado <span required="" aria-required="true">*</span></label>
                <select name="campo[estado]" data-rule-required="true" aria-required="true" v-select2="detalle.estado" :config="config.select2" :disabled="config.disableDetalle">
                    <option value="">Seleccione</option>
                    <option :value="estado.id_cat" v-for="estado in catalogos.estados">{{estado.etiqueta}}</option>
                </select>
            </div>

        </div>




        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive" style="padding-bottom: 100px;">
                    <table id="unidadesTable" class="table table-noline tabla-dinamica unidadesTable" style="display: block;">
                        <thead>
                            <tr>
                                <th width="13.571428571429%" class="unidad_medida" style="background: white;color: #555;"> </th>
                                <th width="13.571428571429%" class="unidad" style="background: white;color: #555;">Unidad de Medida <span class="required" aria-required="true">*</span></th>
                                <th width="1%" class="base" style="background: white;color: #555;text-align:center;">Base </th>
                                <th width="13.571428571429%" class="factor_conversion" style="background: white;color: #555;">Factor de conversión <span class="required" aria-required="true">*</span></th>
                                <th width="1%" style="background: white;color: #555;">&nbsp;</th>
                                <th width="13.571428571429%" class="unidad_medida2" style="background: white;color: #555;"> </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr id="unidades{{$index}}" v-for="item_unidad in detalle.item_unidades">
                                <td>
                                    <label>Unidad de medida: </label>
                                </td>
                                <td>
                                    <select name="unidades[{{$index}}][id_unidad]" data-rule-required="true" aria-required="true" v-select2="item_unidad.id_unidad" :config="config.select2" :disabled="config.disableDetalle">
                                        <option value="">Seleccione</option>
                                        <option :value="unidad.id" v-for="unidad in catalogos.unidades">{{unidad.nombre}}</option>
                                    </select>
                                </td>
                                <td>
                                    <label class="radio" style="margin-top:0px;margin-bottom:0px;text-align:center;">
                                        <input type="radio" name="unidades[0][base]" data-rule-required="true" aria-required="true" :value="$index" style="position:relative; margin-left:0px;" v-model="detalle.item_unidades[0].base">
                                        <span style="width:72px;display:block"></span>
                                    </label>
                                </td>
                                <td>
                                    <input type="text" name="unidades[{{$index}}][factor_conversion]" class="form-control"  data-rule-required="true" aria-required="true" :readonly="detalle.item_unidades[0].base == $index" v-model="item_unidad.factor_conversion" v-inputmask="item_unidad.factor_conversion" :config="config.inputmask.currency2" :disabled="config.disableDetalle">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-default btn-block" v-if="$index == 0" @click="addUnit()" :disabled="config.disableDetalle"><i class="fa fa-plus"></i></button>
                                    <button type="button" class="btn btn-default btn-block" v-if="$index != 0" @click="removeUnit(item_unidad)" :disabled="config.disableDetalle"><i class="fa fa-trash"></i></button>
                                </td>
                                <td>
                                    <label v-if="detalle.item_unidades[0].base == $index">En unidad &lt;&lt;Base&gt;&gt;</label>
                                </td>
                            </tr>
                        </tbody>

                    </table>
                    <span class="tabla_dinamica_error"></span>
                </div>
            </div>
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

        return {};

    },

    computed:{

          //...

    },

    watch:{

          //...

    },

    methods:{

        checkCodigo:function(codigo){

            var context = this;
            var datos = $.extend({erptkn: tkn},{codigo:codigo});

            if(codigo.toString().length == 0 || context.config.vista == "editar"){return;}

            this.$http.post({
                url: window.phost() + "inventarios/ajax-get-codigo-validez",
                method:'POST',
                data:datos
            }).then(function(response){
                 if(_.has(response.data, 'session')){
                    window.location.assign(window.phost());
                    return;
                }
                if(!_.isEmpty(response.data)){

                    if(response.data.codigo_valido == true)
                    {
                        context.config.disableGuardar = false;
                        window.toastr['success']('Este n&uacute;mero ha sido validado exitosamente');
                    }else{
                        context.config.disableGuardar = true;
                        window.toastr['error']('Este n&uacute;mero de item ya existe, favor ingrese otro');
                    }

                }
            }).catch(function(err){
                window.toastr['error'](err.statusText + ' ('+err.status+') ');
            });

        },

        addUnit:function(){
            this.detalle.item_unidades.push({id_unidad:'', factor_conversion:1});
        },

        removeUnit:function(unit){
            this.detalle.item_unidades.$remove(unit);
        }

    }

}

</script>
