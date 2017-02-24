<?php 
$uuid_empresa = $this->session->userdata('uuid_empresa');
$empresaObj  = new Buscar(new Empresa_orm,'uuid_empresa');
$this->empresaObj = $empresaObj->findByUuid($uuid_empresa);
$nombre = !empty($this->session->userdata['nombre']) ? $this->session->userdata['nombre'] : '';
$apellido = !empty($this->session->userdata['apellido']) ? $this->session->userdata['apellido'] : '';
?>
<div class="col-lg-12 bg-white ibox float-e-margins">
<div class="ibox-title">
<h2 style="text-align: center;"><?php echo !empty($this->empresaObj->nombre) ? $this->empresaObj->nombre : ''; ?></h2>
<p style="text-align: center;"><strong>RUC <?php echo !empty($this->empresaObj->tomo) ? $this->empresaObj->tomo : ''; ?>-<?php echo !empty($this->empresaObj->folio) ? $this->empresaObj->folio : '';?>-<?php echo !empty($this->empresaObj->asiento) ? $this->empresaObj->asiento : ''; ?> DV <?php echo !empty($this->empresaObj->digito_verificador) ? $this->empresaObj->digito_verificador : ''; ?></strong></p>
<p style="text-align: center;"><strong>CERTIFICADO DE RETENCI&Oacute;N DE ITBMS</strong></p>
</div>
<div class="ibox-content">
<div class="row col-lg-12">
<div class="col-lg-12">&nbsp;</div>
<div class="col-lg-6">
<h3><span style="text-decoration: underline;">RETENCIONES PRACTICADAS A:</span>&nbsp;</h3>
</div>
<div class="col-lg-6">
<table style="height: 40px;" width="100%">
<tbody>
<tr>
<td width="40%">&nbsp;<strong><span style="text-decoration: underline;">Nombre del Proveedor</span></strong></td>
<td width="30%">&nbsp;<span style="text-decoration: underline;"><strong>RUC</strong></span></td>
<td width="30%"><strong><span style="text-decoration: underline;">DV</span></strong></td>
</tr>
<tr>
<td><?php echo !empty($proveedor['nombre']) ? $proveedor['nombre'] : ''; ?></td>
<td><?php echo !empty($proveedor['tomo_rollo']) ? $proveedor['tomo_rollo'] : ''; ?>-<?php echo !empty($proveedor['folio_imagen_doc']) ? $proveedor['folio_imagen_doc'] : '';?>-<?php echo !empty($proveedor['asiento_ficha']) ? $proveedor['asiento_ficha'] : ''; ?></td>
<td><?php echo !empty($proveedor['digito_verificador']) ? $proveedor['digito_verificador'] : ''; ?></td>
</tr>
</tbody>
</table>
<p>&nbsp;</p>
<table style="height: 40px;" width="100%">
<tbody>
<tr>
<td style="text-align: center;" width="25%">&nbsp;<span style="text-decoration: underline;"><strong>Periodo</strong></span></td>
<td style="text-align: center;" width="25%"><span style="text-decoration: underline;"><strong>Monto sujeto a Retenci&oacute;n</strong></span></td>
<td style="text-align: center;" width="25%">&nbsp;<span style="text-decoration: underline;"><strong>ITBMS Causado Total</strong></span></td>
<td style="text-align: center;" width="25%">&nbsp;<span style="text-decoration: underline;"><strong>ITBMS Retenido Total</strong></span></td>
</tr>
<tr>
<td style="text-align:center" width="25%"><?php echo !empty($_POST['fecha_desde']) ? $_POST['fecha_desde'] : ''; ?> - <?php echo !empty($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : ''; ?></td>
<td style="text-align:center" width="25%"><?php echo !empty($resumen['total_facturado']) ? number_format($resumen['total_facturado'], 2) : '0.00'; ?></td>
<td style="text-align:center" width="25%"><?php echo !empty($resumen['total_itbms']) ? number_format($resumen['total_itbms'], 2) : '0.00'; ?></td>
<td style="text-align:center" width="25%"><?php echo !empty($resumen['total_retenido']) ? number_format($resumen['total_retenido'], 2) : '0.00'; ?></td>
</tr>
</tbody>
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<p style="font-size:10px!important">Usuario responsable: <?php echo !empty($nombre) ? $nombre : ''; ?> <?php echo !empty($apellido) ? $apellido : ''; ?></p>
<p style="font-size:10px!important"><?php echo date('d-m-Y H:i:s'); ?></p>
</div>
</div>
</div>
</div>