<template id="tablelizer-estado-cuenta-cliente">
  <div class="col-lg-12 bg-white ibox float-e-margins">
    <div class="ibox-title">
       <h2 v-text="titulo_reporte"></h2>
    </div>
    <div class="ibox-content">
    <div class="row col-lg-12">
      <div class="col-lg-6">
        <h3 v-text="clienteInfo.nombre"></h3>
        <p v-text="clienteInfo.direccion"></p>
        <h3 v-if="centro_facturable.length === 1">Centro de Facturaci&oacute;n</h3>
        <p v-if="centro_facturable.length === 1" v-text="centro_facturable[0].nombre"></p>
      </div>
      <div class="col-lg-6">
        <h3>Resumen de Cuenta</h3>
        <table class="table table-noline">
          <tr><td><strong>Balance inicial <span v-text="fecha_inicial"></span></strong></td><td>
            <strong v-text="resumenEstadoCuenta.balance_inicial | moneda"></strong>
          </td></tr>
          <tr><td><strong>Facturado</strong></td><td>
            <strong v-text="resumenEstadoCuenta.facturado | moneda"></strong>
          </td></tr>
          <tr><td><strong>Cobrado</strong></td><td>
            (<strong v-text="resumenEstadoCuenta.cobrado | moneda"></strong>)
          </td></tr>
          <tr><td><strong>Nota Cr&eacute;dito</strong></td><td>
            <strong v-text="resumenEstadoCuenta.nota_credito | moneda"></strong>
          </td></tr>
          <tr class="borde-tr"><td class="borde-tr"><strong>Balance final al <span v-text="fecha_final"></span></strong></td><td>
            <strong v-text="resumenEstadoCuenta.balance_final | moneda"></strong>
          </td></tr>
        </table>
      </div>
    </div>
    <div class="col-lg-12">
      <div class="alert alert-info text-center">
        Mostrando todas las facturas y cobros entre <span v-text="entre_fechas"></span>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Detalle</th>
            <th>Monto</th>
            <th>Balance</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="lista in detalle">
            <td v-text="lista.created_at"></td>
            <td v-text="lista.detalle | capitalize"></td>
            <td v-if="esFactura(lista.codigo)">
               <span v-text="lista.total | moneda"></span>
            </td>
            <td v-if="esPago(lista.codigo)">
               (<span v-text="lista.total | moneda"></span>)
            </td>
            <td v-text="lista.balance | moneda"></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="col-lg-12">
      <div class="row estado_total pull-right">Total a Pagar</div>
      <div class="row"></div>
      <div class="row balance_total pull-right" v-text="resumenEstadoCuenta.balance_final | moneda"></div>
    </div>
    <div class="col-lg-12">
  <div class="col-lg-2">
    <div class="input-group">
      <span class="input-group-addon">$</span>
      <input type="text" v-model="reporteAntiguedad.corriente" disabled style="width: 100%!important;">
    </div>
    <a class="btn btn-success btn-facebook" style="width: 100%; margin-top: 5px; background: #27AAE1!important; border-color: #27AAE1!important;">Corriente</a>
  </div>
  <div class="col-lg-2">
    <div class="input-group">
      <span class="input-group-addon">$</span>
      <input type="text" v-model="reporteAntiguedad._30_dias" disabled style="width: 100%!important;">
    </div>
    <a class="btn btn-success btn-facebook" style="width: 100%; margin-top: 5px; background: #27AAE1!important; border-color: #27AAE1!important;">30 d&iacute;as</a>
  </div>
  <div class="col-lg-2">
    <div class="input-group">
      <span class="input-group-addon">$</span>
      <input type="text" v-model="reporteAntiguedad._60_dias" disabled style="width: 100%!important;">
    </div>
    <a class="btn btn-success btn-facebook" style="width: 100%; margin-top: 5px; background: #27AAE1!important; border-color: #27AAE1!important;">60 d&iacute;as</a>
  </div>
  <div class="col-lg-2">
    <div class="input-group">
      <span class="input-group-addon">$</span>
      <input type="text" v-model="reporteAntiguedad._90_dias" disabled style="width: 100%!important;">
    </div>
    <a class="btn btn-success btn-facebook" style="width: 100%; margin-top: 5px; background: #27AAE1!important; border-color: #27AAE1!important;">90 d&iacute;as</a>
  </div>
  <div class="col-lg-2">
    <div class="input-group">
      <span class="input-group-addon">$</span>
      <input type="text" v-model="reporteAntiguedad._120_dias" disabled style="width: 100%!important;">
    </div>
    <a class="btn btn-success btn-facebook" style="width: 100%; margin-top: 5px; background: #27AAE1!important; border-color: #27AAE1!important;">120 d&iacute;as +</a>
  </div>
    </div>
    </div>
  </div>
</template>
