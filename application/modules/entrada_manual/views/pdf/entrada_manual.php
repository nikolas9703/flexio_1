<?php
  function moneda($valor){
    $valor = strlen($valor)==0?0:$valor;
    return money_format('%(#4.2n',$valor);
  }
?>

<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <link href="public/assets/css/modules/stylesheets/pdf_entrada_manual.css" rel="stylesheet" />
</head>
<body>
  <h2><?php echo $empresa?> - Entradas Manuales <h2>

  <div class="row col-lg-12">
      <table class="table table-noline entradas" style="width:100%">
        <thead>
                <tr>
                    <th>N&uacute;mero de entrada manual</th>
                    <th>Fecha y hora</th>
                    <th>usuario</th>
                    <th>D&eacute;bito</th>
                    <th>Cr&eacute;dito</th>
                </tr>
        </thead>
        <tbody>
            <?php foreach($entradaManual as $entrada):?>
                <tr>
                    <td><?php echo $entrada->codigo?></td> <td><?php echo $entrada->present()->fecha_hora?></td>
                    <td><?php echo utf8_decode($entrada->usuario_nombre)?></td> <td><?php echo $entrada->present()->debito?></td>
                    <td><?php echo $entrada->present()->credito?></td>
                </tr>
                <tr>
                    <td colspan="5">
                          <table class="table table-striped transacciones" style="width:100%">
                            <thead>
                              <tr>
                                <th>Cuenta contable</th>
                                <th>Descripcion</th>
                                <th>Centro contable</th>
                                <th>D&eacute;bito</th>
                                <th>Cr&eacute;dito</th>
                              </tr>
                            </thead>
                            </tbody>
                              <?php foreach($entrada->transacciones as $transaccion): ?>
                                  <tr>
                                      <td><?php echo utf8_decode($transaccion->cuenta_contable)?></td><td><?php echo utf8_decode($transaccion->nombre)?></td>
                                      <td><?php echo $transaccion->nombre_centro_contable?></td><td><?php echo $transaccion->present()->debito?></td><td><?php echo $transaccion->present()->credito_entrada?></td>
                                  </tr>
                              <?php endforeach;?>
                            </tbody>
                           </table>
                    <td>
                </tr>
            <?php endforeach;?>
        </tbody>
      </table>
  </div>
</body>
</html>
