<template>
<div class="row animated fadeInRight">
	<!-- Centro de Facturacion Crear Start -->
	<div class="col-md-12">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5><i class="fa fa-briefcase"></i>&nbsp;<tem id="centro_fact_dir">Centro de facturación </tem><small id="centro_fact_small">Crear centros facturables</small></h5>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<div class="row">
					<div class="col-md-12">
						<table class="table table-noline">
							<thead>
								<tr>
									<th><tem id="centro_fact_label">Centro de facturación </tem><span required="" aria-required="true">*</span></th>
									<th>Provincia</th>
									<th>Distrito</th>
									<th>Corregimiento</th>
									<th>Dirección <span required="" aria-required="true">*</span></th>
                                    <th width="1%"></th>
								</tr>
							</thead>
							<tbody>

								<tr v-for="centro_facturacion in detalle.centros_facturacion" :is="'centros-facturacion-fila'" :config="config" :detalle.sync="detalle" :catalogos="catalogos" :parent_index="$index" :centro_facturacion.sync="centro_facturacion"></tr>

							</tbody>
						</table>
					</div>
				</div>

			</div>
		</div>
	</div>
	<!-- Centro de Facturacion Crear End -->
</div>
</template>

<script>
export default {

	props: {

		config: Object,
		detalle: Object,
		catalogos: Object

	},

	components:{

        'centros-facturacion-fila': require('./centros-facturacion-fila.vue')

    },

	data: function () {

		return {};

	}

}
</script>
