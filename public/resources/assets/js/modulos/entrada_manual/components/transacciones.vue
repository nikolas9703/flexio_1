<template>

    <div class="row">
        <div class="hr-line-dashed"></div>
        <table class="table">
            <thead>
                <tr>
                    <th>Descripci&oacute;n</th>
                    <th>Cuenta</th>
                    <th>Centro contable</th>
                    <th>D&eacute;bito</th>
                    <th>Cr&eacute;dito</th>
                    <th width="1%">
                        <button type="button" class="btn btn-default btn-block-sm" @click="addRow()" :disabled="config.disableDetalle"><i class="fa fa-plus"></i></button>
                    </th>
                </tr>
            </thead>
            <tbody>

                <!--componente articulo-->
                <tr v-for="trans in detalle.transacciones" :is="'transaccion'" :config.sync="config" :detalle.sync="detalle" :parent_index="$index" :trans.sync="trans"></tr>

                <tr>
                    <td colspan="3"></td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                            <input type="input-left-addon" class="form-control" disabled="" :value="getTotalDebito | currency ''">
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                            <input type="input-left-addon" class="form-control" disabled="" :value="getTotalCredito | currency ''">
                        </div>
                    </td>
                    <td></td>
                </tr>

            </tbody>
        </table>
        <br>
        <!-- 3rd Row Table-->
    </div>

</template>

<script>

export default {

  props:{

        config: Object,
        detalle: Object

    },

    data:function(){

        return {};

    },

    components:{
        'transaccion': require('./transaccion.vue'),
    },

    methods:{
        addRow: function(){
            this.detalle.transacciones.push({
                id: '',
                nombre: '',
                cuenta_id: '',
                centro_id: '',
                debito: 0,
                credito: 0
            });
        }
    },

    computed:{

        getTotalDebito:function(){
            var context = this;
			return _.sumBy(context.detalle.transacciones, function (trans) {
				return parseFloat(trans.debito) || 0;
			});
        },

        getTotalCredito:function(){
            var context = this;;
            return _.sumBy(context.detalle.transacciones, function (trans) {
				return parseFloat(trans.credito) || 0;
			});
        }
    }

}

</script>
