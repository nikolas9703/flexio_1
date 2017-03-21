<?php
  function moneda($valor){
    $valor =(float) strlen($valor)==0?0:$valor;
    return $valor >= 0?"$".number_format($valor, 2,'.', ','): "($".number_format(abs($valor), 2,'.', ',').")";
  }

  function start_case($string){
    return ucwords(str_replace("-"," ",$string));
  }

  $totales_impuestos_ventas = [];
  $totales_impuestos_compras = [];
  $impuesto_pagar = [];
  foreach($ventas as $key=>$venta){
    $totales_impuestos_ventas[$key] = $venta + $notas_creditos[$key];
  }
  foreach($compras as $key=>$compra){
    $totales_impuestos_compras[$key] = $compra + $notas_debitos[$key];
  }

  foreach($totales_impuestos_ventas as $key=>$impuesto_venta){
    if($key != 'total' && $key != 'subtotal'){
      $impuesto_pagar[$key] = $impuesto_venta - $totales_impuestos_compras[$key];
    }
  }
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <link href="public/assets/css/default/bootstrap.min.css" rel="stylesheet" />
  <link type="text/css" href="public/assets/css/modules/stylesheets/reporte_financiero.css" rel="stylesheet" />
</head>
<body>
  <div class="col-lg-12 bg-white ibox float-e-margins">
    <div class="ibox-title">
       <h2>Informe de impuestos sobre las ventas</h2>
    </div>
    <div class="ibox-content">
      <div class="row col-lg-12">

        <table class="table table-noline impuestos" style="width:100%">
            <thead>
              <tr>
                  <th>Ventas</th>

                  <?php
                    foreach($ventas as $key=>$venta){
                  ?>
                  <th><?php echo start_case($key)?></th>
                  <?php
                    }
                  ?>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Facturas de ventas</td>
                <?php
                  foreach($ventas as $key=>$venta){
                ?>
                <td><?php echo moneda($venta)?></td>
                <?php
                  }
                ?>
              </tr>
              <tr>
                <td>Notas de cr&eacute;dito en ventas</td>
                <?php
                  foreach($notas_creditos as $key=>$credito){
                ?>
                <td><?php echo moneda($credito)?></td>
                <?php
                  }
                ?>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                 <td>Total de impuestos en ventas</td>
                 <?php
                   foreach($totales_impuestos_ventas as $key=>$total_venta){
                 ?>
                 <td><?php echo moneda($total_venta)?></td>
                 <?php
                   }
                 ?>
              </tr>
            </tfoot>
        </table>

        <table class="table table-noline impuestos" style="width:100%">
            <thead>
                <tr>
                  <th>Compras</th>
                  <?php
                    foreach($compras as $key=>$compra){
                  ?>
                  <th><?php echo start_case($key)?></th>
                  <?php
                    }
                  ?>
                </tr>
            </thead>
            <tbody>
              <tr>
                <td>Facturas de compras</td>
                <?php
                  foreach($compras as $key=>$compra){
                ?>
                <td><?php echo moneda($compra)?></td>
                <?php
                  }
                ?>
              </tr>
              <tr>
                <td>Notas de d&eacute;bito en compras</td>
                <?php
                  foreach($notas_debitos as $key=>$nota_debito){
                ?>
                <td><?php echo moneda($nota_debito)?></td>
                <?php
                  }
                ?>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                 <td>Total de impuestos en compras</td>
                 <?php
                  $i=1;
                   foreach($totales_impuestos_compras as $key=>$total_compra){
                  $i++;
                 ?>
                 <td><?php echo moneda($total_compra)?></td>
                 <?php
                   }
                 ?>
              </tr>
              <tr>
                  <td colspan="<?php echo $i ?>">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="2">Impuestos por pagar</td>
                <?php
                  foreach($impuesto_pagar as $key=>$impuestos){
                ?>
                <td><?php echo moneda($impuestos)?></td>
                <?php
                  }
                ?>
                <td>&nbsp;</td>
              </tr>
            </tfoot>
        </table>
        <div class="row space"></div>
        <div class="col-lg-12">
          <div class="row estado_total text-right">Total a Pagar</div>
          <div class="row balance_total text-right" v-text="sumaTotales(impuesto_pagar) | monedaContabilidad">
                      <?php echo moneda(array_sum($impuesto_pagar)) ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
