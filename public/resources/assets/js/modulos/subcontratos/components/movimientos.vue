<template>

    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div class="row col-xs-12 col-sm-12 col-md-9 col-lg-9"></div>
      <div class="row col-xs-12 col-sm-12 col-md-3 col-lg-3" style="padding: 10px;font-size: 15px;margin-left: 20px;">Cuenta</div>
    </div>

    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12" v-for="movimiento in detalle.movimientos | orderBy 'tipo'">
        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><span v-text="movimiento.label +': ' | capitalize"></span></div>
        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="text" name="movimientos[{{$index}}][monto]" class="form-control" v-model="movimiento.monto | currencyDisplay" @change="calcularPorcentajes()" :disabled="!config.disablePermisoAdenda">
            </div>
        </div>
        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
        {{config.disablPermisoAdenda}}
            <div class="input-group">
                <input type="text" name="movimientos[{{$index}}][porcentaje]" class="form-control" v-model="movimiento.porcentaje" @change="calcularMontos()"   :disabled="!config.disablePermisoAdenda">
                <span class="input-group-addon">%</span>
            </div>
        </div>
        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <select class="form-control" name="movimientos[{{$index}}][cuenta_id]" aria-required="true" v-select2="movimiento.cuenta_id" :config="config.select2" :disabled="disabledCuentaId">
                <option value="">Seleccione</option>
                <option :value="cuenta.id" v-for="cuenta in catalogos.cuentas" v-html="cuenta.codigo +' '+ cuenta.nombre"></option>
            </select>
            <input type="hidden" name="movimientos[{{$index}}][tipo]" :value="movimiento.tipo">
        </div>
    </div>


</template>

<script>

import calcular_movimientos from '../mixins/calcular-movimientos';

export default {

  mixins: [calcular_movimientos],

  props:{

        config: Object,
        detalle: Object,
        catalogos: Object

    },

    data:function(){

        return {
            disablecuentaid: true
        };

    },

    computed: {
        disabledCuentaId: function() {
            if(!this.config.disablePermisoAdenda||this.disablecuentaid){
                return true;
            }else{
                return false;
            }
        }
    }

}

</script>
