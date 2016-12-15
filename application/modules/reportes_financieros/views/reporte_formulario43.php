<template id="reporte_formulario43">

  <div class="col-lg-12 bg-white ibox float-e-margins">
    <div class="ibox-title">
       <h2 v-text="titulo_reporte"></h2>
    </div>
    <div class="ibox-content">
      <div class="row col-lg-12">
        <div class="table-responsive">
          <table class="table table-striped antiguedad">
            <thead>
              <tr>
                <th>Tipo de Persona</th>
                <th>RUC</th>
                <th>DV</th>
                <th>Nombre o Raz&oacute;n</th>
                <th>Factura</th>
                <th>Fecha</th>
                <th>Monto en Balboas</th>
                <th>ITBMS Pagado en Balboas</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="reporte in datos_reporte">
                <td v-text="tipoIndentificacion(reporte.proveedor.identificacion)"></td>
                <td v-text="getRUC(reporte.proveedor)"></td>
                <td v-text="getDV(reporte.proveedor)"></td>
                <td v-text="reporte.proveedor.nombre"></td>
                <td v-text="reporte.codigo"></td>
                <td v-text="reporte.fecha_desde"></td>
                <td v-text="reporte.monto | moneda"></td>
                <td v-text="reporte.impuestos | moneda"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</template>
