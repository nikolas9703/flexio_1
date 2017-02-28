
<template>
<table id="notaDebitoItemTable" class="table table-striped tabla-dinamica">
	<thead>
		<tr>
			<th width="25%" v-html="(empezable.type != '') ? 'Items' : 'Descripci&oacute;n'"></th>
			<th width="25%">Cuenta</td>
				<th width="15%" v-show="(empezable.type != '') ? true : false">Monto</td>
					<th width="15%">Impuesto</th>
					<th width="15%">D&eacute;bito</th>
					<th width="1%" style="background:white;" v-show="!(empezable.type != '') ? true : false"></th>
		</tr>
	</thead>
	<tbody>

		<!--componente articulo-->
		<tr v-for="item in rows" :is="'articulo'" :config="config" :detalle.sync="detalle" :catalogos="catalogos" :parent_index="$index" :item.sync="item" :rows.sync="rows" :empezable.sync="empezable" :error.sync="error" :boton.sync="boton"></tr>

	</tbody>
	<tfoot>
		<tr class="no-line">
			<td class="no-line" :colspan="(empezable.type != '') ? '4' : '3'"></td>
			<td class="no-line">
				<input type="hidden" name="campo[subtotal]" value="{{subtotal}}">
				<div class="fila-total1">
					<div class="posicion text-left titulo-total">
						Subtotal
					</div>
					<div class="posicion monto-total text-right" v-text="subtotal | currencyDisplay">

					</div>
				</div>
			</td>
			<td class="no-line" v-show="!(empezable.type != '') ? true : false"></td>
		</tr>
		<tr class="no-line">
			<td class="no-line" :colspan="(empezable.type != '') ? '4' : '3'"></td>
			<td class="no-line">
				<input type="hidden" name="campo[impuesto]" value="{{impuesto}}">
				<div class="fila-total1">
					<div class="posicion text-left titulo-total">
						Impuesto
					</div>
					<div class="posicion monto-total text-right" v-text="impuesto | currencyDisplay">

					</div>
				</div>
			</td>
			<td class="no-line" v-show="!(empezable.type != '') ? true : false"></td>
		</tr>

		<tr class="no-line" v-show="retieneImpuesto">
			<td class="no-line" :colspan="(empezable.type != '') ? '4' : '3'"></td>
			<td class="no-line">
				<input type="hidden" name="campo[retenido]" value="{{retenido}}">
				<div class="fila-total1">
					<div class="posicion text-left titulo-total">
						Retenido
					</div>
					<div class="posicion monto-total text-right" v-text="retenido | currencyDisplay">

					</div>
				</div>
			</td>
			<td class="no-line" v-show="!(empezable.type != '') ? true : false"></td>
		</tr>


		<tr>
			<td :colspan="(empezable.type != '') ? '4' : '3'" class="no-line"></td>
			<td class="no-line">
				<input type="hidden" name="campo[total]" value="{{total}}">
				<div class="fila-total1">
					<div class="posicion text-left titulo-total">
						Total
					</div>
					<div class="posicion monto-total text-right" v-text="total | currencyDisplay">

					</div>
				</div>
			</td>
			<td class="no-line" v-show="!(empezable.type != '') ? true : false"></td>
		</tr>
	</tfoot>
</table>
</template>

<script>
export default {

	props: ['rows', 'boton', 'error', 'catalogos', 'config', 'empezable', 'detalle'],

	data: function () {

		return {
			itemDisable: true
		};

	},

	components: {
		'articulo': require('./nota-debito-row.vue')
	},
	computed: {

        retieneImpuesto:function(){
            var context = this;
            var proveedor = _.find(context.catalogos.proveedores, function(proveedor){
                return proveedor.id == context.detalle.proveedor_id;
            });

            if(!_.isEmpty(proveedor) && proveedor.retiene_impuesto == 'no' && context.catalogos.empresa.retiene_impuesto == 'si'){
                return true;
            }
            return false;
        },

		total: function () {
            return this.subtotal + this.impuesto;
		},

		subtotal: function () {
			var subtotal = _.sumBy(this.rows, function (o) {
				return parseFloat(o.monto) || 0;
			});
			return subtotal;
		},

		impuesto: function () {
			var impuesto = _.sumBy(this.rows, function (o) {
				return parseFloat(o.impuesto_total) || 0;
			});
			return impuesto;
		},
		retenido() {
            if(!this.retieneImpuesto)return 0;
			let retenido = parseFloat(roundNumber(this.impuesto, 2)) * 0.50;
			return parseFloat(roundNumber(retenido, 2))
		},
		monto_retenido() {
			let monto_retenido = parseFloat(roundNumber(this.subtotal, 2)) * parseFloat(this.detalle.factura.porcentaje_retencion);
			return parseFloat(roundNumber(monto_retenido, 2));
		}

	},

};
</script>
