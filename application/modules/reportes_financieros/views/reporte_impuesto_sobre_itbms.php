<template id="reporte_impuesto_sobre_itbms">
  <div class="col-lg-12 bg-white ibox float-e-margins">
    <div class="ibox-title">
       <h2 v-text="titulo_reporte"></h2>
    </div>
    <div class="ibox-content">
      <div class="row col-lg-12">
      <div class="col-sm-6">
      <p>
      <span><strong>Nombre del proveedor:</strong> {{proveedor.nombre}}</span>
      <br>
      <span><strong>RUC:</strong> {{proveedor.tomo_rollo}}-{{proveedor.folio_imagen_doc}}-{{proveedor.asiento_ficha}}</span> <span><strong class="text-navy">DV:</strong> {{proveedor.digito_verificador}}</span>
      </p>
      </div>        
      <div class="col-sm-12">
      <table class="table table-striped table-responsive">
      <thead>
      <tr>
      <th>Periodo</th>
      <th>Monto sujeto a retención</th>
      <th>I.T.B.M.S. causado total</th>
      <th>I.T.B.M.S. retenido total</th>
      </tr>
      </thead>
      <tbody>
      <tr>
      <td>{{fecha_inicial}} - {{fecha_final}}</td>
      <td><span class="label label-primary" v-text="resumen.total_facturado | moneda"></span></td>
      <td><span class="label label-info" v-text="resumen.total_itbms | moneda"></span></td>
      <td><span class="label label-success" v-text="resumen.total_retenido | moneda"></span></td>
      </tr>
      </tbody>
      </table>  
        </div>  
        </div>
      </div>
    </div>
  </div>
</template>
