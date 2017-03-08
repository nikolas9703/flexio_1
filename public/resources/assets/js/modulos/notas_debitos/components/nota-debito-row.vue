
<template>
<tr id="items{{parent_index}}" class="item-listing">
	<td>
		<input type="hidden" name="items[{{parent_index}}][id]" id="id{{parent_index}}" value="{{item.id}}">
		<input type="hidden" name="items[{{parent_index}}][item_id]" id="item_id{{parent_index}}" value="{{item.item_id}}">
		<input type="hidden" name="items[{{parent_index}}][impuesto_total]" id="impuesto_total{{parent_index}}" value="{{get_total_impuesto(parent_index)}}">
		<input :disabled="(empezable.type != '' || config.disableDetalle) ? true : false" type="text" name="items[{{parent_index}}][descripcion]" id="descripcion{{parent_index}}" v-model="item.descripcion" class="form-control" data-rule-required="true">
	</td>
	<td>
		<select name="items[{{parent_index}}][cuenta_id]" id="cuenta_id{{parent_index}}" class="form-control" data-rule-required="true" v-select2="item.cuenta_id" :config="config.select2" :disabled="(empezable.type != '' || config.disableDetalle) ? true : false">
        <option value="">Seleccione</option>
        <option :value="cuenta.id" v-for="cuenta in catalogos.cuentas">{{cuenta.codigo+ ' ' +cuenta.nombre}}</option>
    </select>
	</td>
	<td v-show="(empezable.type != '') ? true : false">
		<div class="input-group">
			<span class="input-group-addon">$</span>
			<input type="text" :disabled="true" name="items[{{parent_index}}][precio_total]" id="monto{{parent_index}}" value="{{item.precio_total | currency ''}}" class="form-control" data-rule-required="true">
		</div>
	</td>
	<td>
		<select name="items[{{parent_index}}][impuesto_id]" class="form-control" data-rule-required="true" v-select2="item.impuesto_id" :config="config.select2" :disabled="(empezable.type != '' || config.disableDetalle) ? true : false">
        <option value="">Seleccione</option>
        <option :value="impuesto.id" v-for="impuesto in catalogos.impuestos" v-html="impuesto.nombre"></option>
    </select>
	</td>
	<td>
		<div class="input-group">
			<span class="input-group-addon">$</span>
			<input type="text" name="items[{{parent_index}}][monto]" id="monto{{parent_index}}" v-model="item.monto | currencyDisplay" class="form-control currencyDisplay" @keyup="calcular(item.monto,parent_index)" data-rule-required="true" :disabled="config.disableDetalle">
		</div>
	</td>
	<td v-show="!(empezable.type != '') ? true : false">
		<button type="button" v-show="parent_index === 0" class="btn btn-default btn-block" data-rule-required="true" agrupador="items" aria-required="true" v-on:click="rows.length === 0 ?'':addRow()" :disabled="config.disableDetalle"><i class="fa fa-plus"></i></button>
		<button type="button" v-show="parent_index !== 0" class="btn btn-default btn-block" data-rule-required="true" agrupador="items" aria-required="true" v-on:click="rows.length === 1 ?'':deleteRow(item)" :disabled="config.disableDetalle"><i class="fa fa-trash"></i></button>
	</td>
</tr>
</template>

<script>
export default {

  props: {

		config: Object,
		detalle: Object,
		catalogos: Object,
		parent_index: Number,
		item: Object,
        rows: Array,
		empezable: Object,
        boton: Boolean,
        error: String

	},

	data: function () {

		return {

		};

	},

    watch:{

        'item.impuesto_id': function(val, oldVal){
            var context = this;
            context.calcular(context.item.monto, context.parent_index);
        }

    },

	events:{

		eCalculate: function(){
			var context = this;
			context.calcular(context.item.monto, context.parent_index);
		}

	},


	methods: {

		addRow: function () {
			var context = this;
			context.detalle.filas.push({
				id: '',
				cuenta_id: '',
				monto: 0,
				precio_total: 0,
				descripcion: '',
				impuesto_total: 0,
				impuesto_id: '',
				item_id: 0
			});
		},

		deleteRow: function (row) {
			var context = this;
			context.detalle.filas.$remove(row);
		},

        get_porcentaje_impuesto: function(impuesto_id){
            var context = this;
            var impuesto = _.find(context.catalogos.impuestos, function(impuesto){
                return impuesto_id == impuesto.id;
            });

            return !_.isEmpty(impuesto) ? impuesto.impuesto : 0;
        },

        get_total_impuesto:function(index){
            return this.rows[index].impuesto_total;
        },

		calcular: function (monto, index) {

			var context = this;
			var operacionPorcentaje = new Operacion(monto, context.get_porcentaje_impuesto(this.rows[index].impuesto_id));
			this.rows[index].impuesto_total = operacionPorcentaje.porcentajeDelTotal();
			this.rows[index].monto = monto;
			this.rows.$set(index, this.rows[index]);

			if (context.empezable.id != '' && !(context.config.vista == 'ver' && window.nota_debito.estado == 'aprobado')) {

				var total_nota_debito = parseFloat(this.total).toFixed(2) - parseFloat(context.retenido).toFixed(2);
				if (total_nota_debito > parseFloat(context.detalle.monto_factura) || total_nota_debito > context.detalle.saldo_factura) {
                    this.boton = true;
					this.error = "El total no puede ser mayor al saldo de la factura";
				} else if (parseFloat(monto) > parseFloat(this.rows[index].precio_total)) {
					this.boton = true;
					this.error = "El débito no puede ser mayor al monto del item";
				} else {
					this.boton = false;
					this.error = "";
				}
			}

		}

	},

    computed:{

        total:function(){
            return this.subtotal + this.impuesto;
        },

        subtotal:function(){
            var subtotal = _.sumBy(this.rows,function(o){
                return parseFloat(o.monto) || 0;
            });
            return subtotal;
        },

        impuesto:function(){
            var impuesto = _.sumBy(this.rows,function(o){
                return parseFloat(o.impuesto_total) || 0;
            });
            return impuesto;
        },
        retenido(){
            let retenido = parseFloat(roundNumber(this.impuesto,2))  * 0.50;
            return parseFloat(roundNumber(retenido,2))
        },
        monto_retenido(){
            let monto_retenido = parseFloat(roundNumber(this.subtotal,2)) * parseFloat(this.detalle.factura.porcentaje_retencion);
            return parseFloat(roundNumber(monto_retenido,2));
        }

  },
};
</script>
