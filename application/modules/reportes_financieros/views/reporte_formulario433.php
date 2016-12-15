<template id="reporte_formulario433">

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
                <th>RUC Informado</th>
                <th>DV-RUC</th>
                <th>Nombre o Raz&oacute;n</th>

                <th>Monto PA</th>
                <th>ITBMS Retenido</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="reporte in datos_reporte">
                <td v-text="tipoIndentificacion(reporte.identificacion)"></td>
                <td v-text="getRUC(reporte)"></td>
                <td v-text="getDV(reporte)"></td>
                <td v-text="reporte.nombre"></td>
                <td v-text="reporte.monto | moneda"></td>
                <td v-text="reporte.retenido | moneda"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</template>
