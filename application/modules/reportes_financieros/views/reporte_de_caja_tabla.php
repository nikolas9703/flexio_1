<template id="tablaReporteDeCaja">
    <style>
        .td1 {
            text-align: right;
            padding-right: 20px !important;
        }
        th {
            text-align: center;
        }
        .debito {
            margin-bottom: 0px !important;
            background-color: #ffa500;
            padding-top: 3px;
            padding-bottom: 3px;
            padding-left: 5px;
            padding-right: 5px;
            border-radius: 2px;
            color: #fff;
        }
        .credito {
            margin-bottom: 0px !important;
            background-color: #2f9070;
            padding-top: 3px;
            padding-bottom: 3px;
            padding-left: 5px;
            padding-right: 5px;
            border-radius: 2px;
            color: #fff;
        }
        .normalbold{
            font-weight: normal;
        }
    </style>
  <div class="col-lg-12 bg-white ibox float-e-margins">
    <div class="ibox-title">
       <h2 v-text="titulo_reporte">Informe de caja menuda</h2>
    </div>
      
    <div class="ibox-content">
      <div class="row col-lg-12">
          <div id="headertabla" class="col-lg-6">
              <p><strong>Nombre de la caja:</strong> <label class="normalbold" for="nombredelacaja" v-text="nombrecaja"></label></p>
              <p><strong>Centro contable:</strong> <label class="normalbold" for="centrocont" v-text="centrocontable"></label></p>
              <p><strong>Responsable:</strong> <label class="normalbold" for="responsable" v-text="responsab"></label></p>
              <p><strong>Rango de fechas:</strong> <label class="normalbold" for="desdefecha" v-text="rangodefechas.desde"></label> - <label class="normalbold" for="hastafecha" v-text="rangodefechas.hasta"></label></p>
    </div>
        <div class="table-responsive">
          <table class="table table-striped antiguedad">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Transacción</th>
                <th>Descripción</th>
                <th>Débito</th>
                <th>Crédito</th>
                
             </tr>
            </thead>
            <tbody>
              <tr v-for="reporte in datos_reporte">
                <td v-text="reporte.created_at"></td>
                <td v-text="reporte.descripcion"></td>
                <td v-text="reporte.nombre"></td>
                <td class="td1"><label class="debito" v-text="reporte.debito" v-show="showornot(reporte.debito)" v-moneda></label></td>
                <td class="td1"><label class="credito" v-text="reporte.credito" v-show="showornot(reporte.credito)" v-moneda></label></td>
                
             </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td>Total</td>
                    
                    <td class="td1" style="font-weight: bold"><label v-text='totaldebito | moneda' id="totaldebitos">$</label></td>
                    <td class="td1" style="font-weight: bold"><label v-text='totalcredito | moneda' id="totalcreditos">$</label></td>
                </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>

</template>