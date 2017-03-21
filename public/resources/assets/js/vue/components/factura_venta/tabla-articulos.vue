<style></style>

<template src="./template/tabla-articulos.html"></template>

<script>
export default {
	props: ['tablaHeader', 'articulos', 'catalogos', 'campoDisabled', 'config'],
	data() {
		return {
			articulo: 'articulo'
		}
	},
	components: {
		'articulo': require('./articulo.vue'),
	},
	vuex: {
		getters: {
			currentEmpezableType: (state) => state.current,
			estado: (state) => state.estado,
		}
	},
	computed: {
		getArticulos() {
			return this.articulos;
		},
		desabledPorEstado() {
			return this.estado != null && this.estado != "por_aprobar" && (this.currentEmpezableType == null || this.currentEmpezableType != "orden_alquiler");
		}

	},
	methods: {
		addRow() {
			this.articulos.push({
				categoria_id: '',
				items: [],
				item_id: '',
				atributos: [],
				cantidad: 1,
				periodo_tarifario: '',
				tarifa: '',
				en_alquiler: 0,
				precio_unidad: "0.00",
				impuesto_id: '',
				descuento: '0.00',
				cuenta_id: '',
				atributo_text: '',
				atributo_id: '',
				comentario: '',
				facturado: true,
				cuentas: [],
				tipo_id: '',
				por_entregar: 0,
				entregado: 0,
				devuelto: 0,
				unidades: [],
				unidad_id: '',
				total_impuesto: 0,
				total_descuento: 0,
				impuesto: 0,
				subtotal: 0
			});
		}
	}
}
</script>
