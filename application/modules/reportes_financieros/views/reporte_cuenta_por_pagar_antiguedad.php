<template id="tablelizer-cuenta-por-pagar-antiguedad">
  <div class="ibox float-e-margins col-lg-12 bg-white">
    <div class="ibox-title">
       <h2 v-text="titulo_reporte"></h2>
    </div>
    <div class="ibox-content">
      <table class="table table-responsive table-striped antiguedad">
        <thead>
            <tr>
              <th v-text="nombreColumna"></th>
              <th>Corriente</th>
              <th>30 dias</th>
              <th>60 dias</th>
              <th>90 dias</th>
              <th>120 dias +</th>
              <th>Total</th>
            </tr>
        </thead>
        <tbody>
          <tr v-for="dato in cuentas" :class="hijosClass(dato)"  id="{{getId(dato)}}" data-target="{{target(dato)}}">
            <td>
              <i v-if="dato.tipo==='proveedor' || dato.tipo==='cliente'" :class="dato.icono" data-toggle="{{open(dato)}}" v-on:click="toggleCollapse(dato,$event,$index)"></i>
              <span v-text="dato.nombre"></span>
            </td>
            <td v-text="dato.corriente | moneda"></td>
            <td v-text="dato['30_dias'] | moneda"></td>
            <td v-text="dato['60_dias'] | moneda"></td>
            <td v-text="dato['90_dias'] | moneda"></td>
            <td v-text="dato['120_dias'] | moneda"></td>
            <td v-text="dato.Totales | moneda"></td>
          </tr>
        </tbody>
    </div>
  </div>
</template>
