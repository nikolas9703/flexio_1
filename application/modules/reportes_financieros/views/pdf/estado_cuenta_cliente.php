  <?php
    function moneda($valor){
      $valor = strlen($valor)==0?0:$valor;
      return money_format('%(#4.2n',$valor);
    }
    $uuid_empresa = $this->session->userdata('uuid_empresa');
    $empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
    $this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
    $cambio = str_replace ("system/","",BASEPATH);
    $logo_path = 'public/logo/';
    $logo_empresa = !empty($this->empresaObj->logo) ? $this->empresaObj->logo : 'default.jpg';   
    $logo_final = $cambio . $logo_path . $logo_empresa;    
  ?>
  <html>
  <head>
    <link href="public/assets/css/default/bootstrap.min.css" rel="stylesheet" />
    <link type="text/css" href="public/assets/css/modules/stylesheets/reporte_financiero.css" rel="stylesheet" />
  </head>
<body>
  <div class="col-lg-12 bg-white ibox float-e-margins">
    <div class="ibox-title">
       <h2>Estado de cuenta de cliente</h2>
    </div>
    <div class="ibox-content">
    <div class="row col-lg-12">        
        <div class="col-lg-12"><img src="<?php echo $logo_final; ?>" width="120px" /></div>     
    
      <div class="col-lg-6">
        <h3><?php echo $cliente['nombre']; ?></h3>
        <p><?php echo $cliente['direccion']; ?></p>
        <?php if(isset($cliente['centro_facturable'])){ ?>
          <h3>Centro de Facturaci&oacute;n</h3>
          <p><?php echo $cliente['centro_facturable'][0]['nombre']; ?></p>
        <?php }?>
      </div>
       
      <div class="col-lg-6">
        <h3>Resumen de Cuenta</h3>
        <table class="table table-noline">
          <tr><td><strong>Balance inicial <span><?php echo $fecha_inicial;?></span></strong></td><td>
            <strong><?php echo moneda($resumen["balance_inicial"]);?></strong>
          </td></tr>
          <tr><td><strong>Facturado</strong></td><td>
            <strong><?php echo moneda($resumen["facturado"]);?></strong>
          </td></tr>
          <tr><td><strong>Cobrado</strong></td><td>
            <strong>(<?php echo moneda($resumen["cobrado"]);?>)</strong>
          </td></tr>
          <tr><td><strong>Nota Cr&eacute;dito</strong></td><td>
            <strong><?php echo moneda($resumen["nota_credito"]);?></strong>
          </td></tr>
          <tr class="borde-tr">
            <td class="borde-tr"><strong>Balance final al <?php echo $fecha_final;?></strong></td>
            <td class="borde-tr">
            <strong><?php echo moneda($resumen["balance_final"]);?></strong>
          </td>
        </tr>
        </table>
      </div>
    </div>
    <div class="col-lg-12">
      <div class="alert alert-info text-center">
        Mostrando todas las facturas y cobros entre <span><?php echo $fecha_inicial .' al '. $fecha_final?></span>
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
          <?php foreach($detalle as $lista){ ?>
          <tr>
            <td><?php echo $lista['created_at'];?></td>
            <td><?php echo ucfirst($lista['detalle']);?></td>
            <?php if(starts_with($lista['codigo'], "INV")){ ?>
              <td >
                 <?php echo moneda($lista['total']); ?>
              </td>
            <?php }?>
            <?php if(starts_with($lista['codigo'], "PAY")){ ?>
              <td >
                (<?php echo moneda($lista['total']); ?>)
              </td>
            <?php }?>

            <td><?php echo moneda($lista['balance']); ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
    <div class="col-lg-12">
      <div class="row estado_total text-right">Total a Pagar</div>
      <div class="row balance_total text-right"><?php echo moneda($resumen["balance_final"])?></div>
    </div>
    </div>
  </div>
</body>
</html>
