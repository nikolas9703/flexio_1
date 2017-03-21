<template id="reporte_impuesto_sobre_ventas">
  <div class="col-lg-12 bg-white ibox float-e-margins">
    <div class="ibox-title">
       <h2 v-text="titulo_reporte"></h2>
    </div>
    <div class="ibox-content">
      <div class="row col-lg-12">

        <table class="table table-noline impuestos" style="width:100%">
            <thead>
                <tr>
                  <th>Ventas</th>
                  <th v-for="cabecera in ventas.header" v-text="header_formato(cabecera)"></th>
                </tr>
            </thead>
            <tbody>
              <tr>
                <td>Facturas de ventas</td>
                <td v-for="total in ventas.filas" v-text="total | moneda"></td>
              </tr>
              <tr>
                <td>Notas de cr&eacute;dito en ventas</td>
                <td v-for="total in notas_creditos.filas" v-text="total | moneda"></td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                 <td>Total de impuestos en ventas</td>
                 <td v-for="total in totales_ventas" v-text="total | moneda"></td>
              </tr>
            </tfoot>
        </table>

        <table class="table table-noline impuestos" style="width:100%">
            <thead>
                <tr>
                  <th>Compras</th>
                  <th v-for="cabecera in compras.header" v-text="header_formato(cabecera)"></th>
                </tr>
            </thead>
            <tbody>
              <tr>
                <td>Facturas de compras</td>
                <td v-for="total in compras.filas" v-text="total | moneda"></td>
              </tr>
              <tr>
                <td>Notas de d&eacute;bito en compras</td>
                <td v-for="total in notas_debitos.filas" v-text="total | moneda"></td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                 <td>Total de impuestos en compras</td>
                 <td v-for="total in totales_compras" v-text="total | monedaContabilidad"></td>
              </tr>
              <tr>
                  <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="2">Impuestos por pagar</td>
                <td v-for="total in impuesto_pagar" v-text="total | monedaContabilidad"></td>
                <td>&nbsp;</td>
              </tr>
            </tfoot>
        </table>
        <div class="row space"></div>
        <div class="col-lg-12">
          <div class="row estado_total pull-right">Total a Pagar</div>
          <div class="row"></div>
          <div class="row balance_total pull-right" v-text="sumaTotales(impuesto_pagar) | monedaContabilidad"></div>
        </div>
      </div>
    </div>
  </div>
</template>
