<template id="tablelizer">
  <div class="col-lg-12 bg-white">
    <h2 v-text="titulo_reporte"></h2>
  <table id="activos" class="controller cuentas" style="width:100%" cellpadding="10">

    <tr data-level="header" class="header">
      <td width="40%"></td>
      <td v-for="titulo in activos.header" v-if="$index > 3"  class="data">
              <strong v-text="titulo" class="underline"></strong>
      </td>
    </tr>
  	<tr v-for="cuenta in activos.filas"  data-level="{{nivel(cuenta.codigo)}}" id="activos{{$index}}">
      <td width="40%" v-text="cuenta.codigo +'  '+ cuenta.nombre"></td>
      <td class="data" v-for="column in cuenta" v-if="$index > 3" >
          <span v-text="cuenta[$key] | moneda"></span>
      </td>
    </tr>

</table>
<table style="width:100%" class="table-total">
  <tr>
    <td width="40%"> <div class="totalizador" v-text="activos.footer_titulo"></div></td>
    <td v-for="total in activos.sumatorias" v-if="$index > 3" v-text="total | moneda"></td>
  </tr>
</table>

<table id="pasivos" class="controller cuentas" style="width:100%" cellpadding="10">

  <tr data-level="header" class="header">
    <td width="40%"></td>
    <td v-for="titulo in pasivos.header" v-if="$index > 3"  class="data">

    </td>
  </tr>
  <tr v-for="cuenta in pasivos.filas"  data-level="{{nivel(cuenta.codigo)}}" id="pasivos{{$index}}">
    <td width="40%" v-text="cuenta.codigo +'  '+ cuenta.nombre"></td>
    <td class="data" v-for="column in cuenta" v-if="$index > 3" v-text="column | moneda"></td>
  </tr>

</table>
<table style="width:100%" class="table-total">
<tr>
  <td width="40%"> <div class="totalizador" v-text="pasivos.footer_titulo"></div></td>
  <td v-for="total in pasivos.sumatorias" v-if="$index > 3"><span v-text="total | moneda"></span></td>
</tr>
<tr>
  <td width="40%"> <div class="totalizador" v-text="pasivos.activo_neto"></div></td>
  <td v-for="total in pasivos.sumatorias" v-if="$index > 3" v-text="(activos.sumatorias[$key] - pasivos.sumatorias[$key])| moneda" ></td>
</tr>
</table>
<table id="patrimonios" class="controller cuentas" style="width:100%" cellpadding="10">

  <tr data-level="header" class="header">
    <td width="40%"></td>
    <td v-for="titulo in patrimonios.header" v-if="$index > 3"  class="data">
    </td>
  </tr>
  <tr v-for="cuenta in patrimonios.filas"  data-level="{{nivel(cuenta.codigo)}}" id="patrimonios{{$index}}">
    <td v-text="cuenta.codigo +'  '+ cuenta.nombre"></td>
    <td class="data" v-for="column in cuenta" v-if="$index > 3" v-text="column | moneda"></td>
  </tr>

</table>
<table style="width:100%" class="table-total">
<tr>
  <td width="40%"> <div class="totalizador" v-text="patrimonios.footer_titulo"></div></td>
  <td v-for="total in patrimonios.sumatorias" v-if="$index > 3"><span v-text="total | moneda"></span></td>
</tr>
</table>
</div>
</template>
