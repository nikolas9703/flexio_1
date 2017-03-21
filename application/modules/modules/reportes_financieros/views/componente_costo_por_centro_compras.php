<template id="tablelizer-costo-por-centro-compras">

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
					<td class="text-info"><strong>Categor&iacute;a(s):</strong></td>
					<td v-text="parametros.categoria | capitalize"></td>
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
		<div class="table-responsive">
		  <table class="table table-striped">
			<thead>
			  <tr>
				<th width="6%">Fecha de compra</th>
				<th width="6%" style="word-break: break-all !important">N&uacute;mero de factura de proveedor</th>
				<th width="10%">Proveedor</th>
				<th width="10%">Categor&iacute;a</th>
				<th width="10%">Item</th>
				<th width="7.69%">Cuenta contable</th>
				<th width="7.69%">Cantidad</th>
				<th width="5%">Unidad</th>
				<th width="5%">Subtotal</th>
				<th width="5%">Descuento</th>
				<th width="5%">Impuesto</th>
				<th width="5%">Total</th>
				<th width="5%" v-if="esRetenido()">Retenido</th>
			  </tr>
			</thead>
			<tbody>
        <tr v-for="factura in detalle">
          <td v-text="factura.fecha"></td>
          <td v-text="factura.codigo"></td>
          <td v-text="factura.proveedor | capitalize"></td>
          <td v-text="factura.categoria | capitalize"></td>
          <td v-text="factura.item | capitalize"></td>
          <td v-text="factura.cuenta"></td>
          <td v-text="factura.cantidad"></td>
          <td v-text="factura.unidad"></td>
          <td v-text="factura.subtotal | moneda"></td>
          <td v-text="factura.descuento | moneda"></td>
          <td v-text="factura.impuesto | moneda"></td>
          <td v-text="factura.total | moneda"></td>
          <td v-text="factura.retenido | moneda" v-if="esRetenido()"></td>
        </tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="7">&nbsp;</td>
					<td><span class="text-info"><strong>TOTALES</strong></span></td>
					<td><span v-text="totales.subtotal | moneda" class="label label-info"></span></td>
					<td><span v-text="totales.descuento | moneda" class="label label-info"></span></td>
					<td><span v-text="totales.impuesto | moneda" class="label label-info"></span></td>
					<td><span v-text="totales.total | moneda" class="label label-info"></span></td>
					<td v-if="esRetenido()"><span v-text="totales.retenido | moneda" class="label label-info"></span></td>
				</tr>
			</tfoot>
		  </table>
		</div>

    </div><!-- close: ibox-content -->
  </div>
  </div>
</template>
