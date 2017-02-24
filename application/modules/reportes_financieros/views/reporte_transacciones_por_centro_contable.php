<template id="tablelizer-transacciones-por-centro-contable">

  <div class="col-lg-12 bg-white">
    <div class="ibox float-e-margins">
    <div class="ibox-title">
       <h2 v-html="titulo_reporte"></h2>
    </div>
    <div class="ibox-content">
		<div class="row col-lg-12">
		  <div class="col-lg-4">
			<table class="table table-noline">
				<tr>
					<td class="text-info"><strong>Centro contable: <span v-text="fecha_inicial"></span></strong></td>
					<td v-text="parametros.centro | capitalize"></td>
				</tr>
				<tr>
					<td class="text-info"><strong>Rango de fechas:</strong></td>
					<td v-text="parametros.rango_fechas"></td>
				</tr>
			</table>
		  </div>
		</div>
		<div class="col-lg-12 hide">
		  <div class="alert alert-info">
			Mostrando todas las facturas y pagos entre <span v-text="entre_fechas"></span>
		  </div>
		</div>
		<div class="table-responsive" v-show="transacciones.length > 0">
		  <table class="table table-striped">
			<thead>
			  <tr>
          <th width="25%">Cuenta</th>
  				<th width="10%">No. Transacci&oacute;n</th>
  				<th width="10%">Fecha</th>
  				<th width="20%">Centro Contable</th>
  				<th width="25%">Transacci&oacute;n</th>
  				<th width="5%">D&eacute;bito</th>
  				<th width="5%">Cr&eacute;dito</th>
			  </tr>
			</thead>
			<tbody>
        <tr v-for="transaccion in transacciones">
          <td v-text="transaccion.cuenta"></td>
          <td v-text="transaccion.no_transaccion"></td>
          <td v-text="transaccion.fecha"></td>
          <td v-text="transaccion.centro_contable"></td>
          <td v-text="transaccion.transaccion"></td>
          <td v-text="transaccion.debito | moneda"></td>
          <td v-text="transaccion.credito | moneda"></td>
        </tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5">&nbsp;</td>
					<td><span v-text="totales.total_debito | moneda" class="label label-info"></span></td>
					<td><span v-text="totales.total_credito | moneda" class="label label-info"></span></td>
				</tr>
			</tfoot>
		  </table>
		</div>

    <div v-show="transacciones.length==0">
      <h4 class="text-center">No se encontraron registros.</h4>
    </div>

    </div><!-- close: ibox-content -->
  </div>
  </div>
</template>
