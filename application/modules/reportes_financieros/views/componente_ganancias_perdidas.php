<template id="tablelizer-ganancias-perdidas">
  <div class="col-lg-12 bg-white">
    <h2 v-text="titulo_reporte"></h2>
  <table id="ingresos" class="controller cuentas" style="width:100%" cellpadding="10">

    <tr data-level="header" class="header">
      <td width="40%"></td>
      <td v-for="titulo in ingresos.header" v-show="$index > 3"  class="data">
              <strong v-text="titulo" class="underline" v-show="seeTotal.totales || titulo!='Totales'"></strong>
      </td>
    </tr>
  	<tr v-for="cuenta in ingresos.filas"  data-level="{{nivel(cuenta.codigo)}}" id="ingresos{{$index}}">
      <td width="40%" v-text="cuenta.codigo +'  '+ cuenta.nombre"></td>
      <td class="data" v-for="column in cuenta" v-show="$index > 3" >
          <span v-text="cuenta[$key] | moneda" v-show="seeTotal.totales || $key!='Totales'"></span>
      </td>
    </tr>

</table>
<table style="width:100%" class="table-total">
  <tr>
    <td width="40%"> <div class="totalizador" v-text="ingresos.footer_titulo"></div></td>
    <td v-for="total in ingresos.sumatorias" v-show="$index > 3">
      <span v-text="total | moneda" v-show="seeTotal.totales || $key!='Totales'"></span>
    </td>
  </tr>
  <tr>
    <td width="40%"> <div class="totalizador" v-text="ingresos.ganancia_bruta"></div></td>
    <td v-for="total in ingresos.sumatorias" v-show="$index > 3">
      <span v-text="total | moneda" v-show="seeTotal.totales || $key!='Totales'"></span>
    </td>
  </tr>
</table>
<h3>Menos costos de venta</h3>
<table id="costos" class="controller cuentas" style="width:100%" cellpadding="10">

  <tr data-level="header" class="header">
    <td width="40%"></td>
    <td v-for="titulo in costos.header" v-show="$index > 3"  class="data">

    </td>
  </tr>
  <tr v-for="cuenta in costos.filas"  data-level="{{nivel_hijos(cuenta.codigo)}}" id="costos{{$index}}">
    <td width="40%" v-text="cuenta.codigo +'  '+ cuenta.nombre"></td>
    <td class="data" v-for="column in cuenta" v-show="$index > 3">
      <span v-text="cuenta[$key] | moneda" v-show="seeTotal.totales || $key!='Totales'"></span>
    </td>
  </tr>

</table>
<table style="width:100%" class="table-total">
<tr>
  <td width="40%"> <div class="totalizador" v-text="costos.footer_titulo"></div></td>
  <td v-for="total in costos.sumatorias" v-show="$index > 3"><span v-text="total | moneda" v-show="seeTotal.totales || $key!='Totales'"></span></td>
</tr>
<tr>
  <td width="40%"> <div class="totalizador" v-text="costos.ganancia_bruta"></div></td>
  <td v-for="total in costos.sumatorias" v-show="$index > 3"  >
    <span v-text="(ingresos.sumatorias[$key] - costos.sumatorias[$key])| moneda" v-show="seeTotal.totales || $key!='Totales'"></span>
  </td>
</tr>
</table>
<h3>Menos gastos</h3>
<table id="gastos" class="controller cuentas" style="width:100%" cellpadding="10">

  <tr data-level="header" class="header">
    <td width="40%"></td>
    <td v-for="titulo in gastos.header" v-show="$index > 3"  class="data"></td>
  </tr>
  <tr v-for="cuenta in gastos.filas"  data-level="{{nivel_hijos(cuenta.codigo)}}" id="gastos{{$index}}">
    <td width="40%" v-text="cuenta.codigo +'  '+ cuenta.nombre"></td>
    <td class="data" v-for="column in cuenta" v-show="$index > 3">
      <span v-text="cuenta[$key] | moneda" v-show="seeTotal.totales || $key!='Totales'"></span>
    </td>
  </tr>

</table>
<table style="width:100%" class="table-total">
<tr>
  <td width="40%"> <div class="totalizador" v-text="gastos.footer_titulo"></div></td>
  <td v-for="total in gastos.sumatorias" v-show="$index > 3"><span v-text="total | moneda" v-show="seeTotal.totales || $key!='Totales'"></span></td>
</tr>
<tr>
  <td width="40%"> <div class="totalizador" v-text="gastos.ganancia_neta"></div></td>
  <td v-for="total in gastos.sumatorias" v-show="$index > 3"><span v-text="((ingresos.sumatorias[$key] - costos.sumatorias[$key]) - gastos.sumatorias[$key])| moneda" v-show="seeTotal.totales || $key!='Totales'"></span></td>
</tr>
</table>
</div>
</template>
