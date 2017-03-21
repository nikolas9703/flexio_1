 <style type="text/css">
<!--
 .Estilo5 {font-family: Arial, Helvetica, sans-serif; font-size: 12px; }
.linea	{border-bottom:1px; border-color:#000000; border-bottom-style:solid;}
.linea2	{border-bottom:1px;border-top:1px;  border-top-style:solid;border-color:#000000; border-bottom-style:solid;}
.linea3	{border-bottom:1px;border-top:1px; border-right:1px;  border-top-style:solid;border-right-style:solid;border-color:#000000; border-bottom-style:solid;}
.linea4	{  border-right:1px;  border-right-style:solid;border-color:#000000; }
.Estilo7 {font-family: Arial, Helvetica, sans-serif; font-size: 12px; font-weight: bold; }

#container {
    width:100%;
    text-align:center;
}

#left {
    float:left;
    width:50px;
}

#center {
    display: inline-block;
    margin:0 auto;
    width:250px;
}

#right {
    float:right;
    width:50px;
}
-->
</style>
 
<div id="container">
  <div id="left"><?php echo date("d/m/Y");?></div>
  <div id="right"></div>
  <div id="center">Comprobante de Pago</div>
</div>
<br><br>

 <table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
    <td width="15%" ><span class="Estilo5">Planilla No.: </span></td>
    <td width="20%" ><span class="Estilo5"><?php echo $planilla_numero; ?></span></td>
    <td width="15%" ><span class="Estilo5">Centro contable: </span></td>
    <td width="15%" ><span class="Estilo5"><?php echo $centro_contable; ?></span></td>
    <td width="18%" ><span class="Estilo5">Ciclo de pago: </span></td>
    <td width="17%" ><span class="Estilo5"><?php echo $ciclo_de_pago; ?></span></td>
  </tr>
  
  <tr>
    <td width="15%" class="linea"><span class="Estilo5">Puesto.: </span></td>
    <td width="20%" class="linea"><span class="Estilo5"><?php echo $puesto; ?></span></td>
    <td width="15%" class="linea"><span class="Estilo5">&Aacute;rea de negocio: </span></td>
    <td width="15%" class="linea"><span class="Estilo5"><?php echo $area_negocio; ?></span></td>
    <td width="18%" class="linea"><span class="Estilo5">Tipo de salario: </span></td>
    <td width="17%" class="linea"><span class="Estilo5"><?php echo $tipo_salario; ?></span></td>
  </tr>
  <tr>
    <td><span class="Estilo5">No. Colab: </span></td>
    <td><span class="Estilo5"><?php echo $no_colaborador; ?></span></td>
    <td><span class="Estilo5">C&eacute;dula:</span></td>
    <td><span class="Estilo5"><?php echo $cedula; ?></span></td>
    <td><span class="Estilo5">Periodo:</span></td>
    <td><span class="Estilo5"><?php echo $periodo; ?></span></td>
  </tr>
  <tr>
    <td><span class="Estilo5">Nombre:</span></td>
    <td><span class="Estilo5"><?php echo $nombre_completo; ?></span></td>
    <td><span class="Estilo5">No. SS. </span></td>
    <td><span class="Estilo5"><?php echo $no_ss; ?></span></td>
    <td><span class="Estilo5">Fecha de pago: </span></td>
    <td><span class="Estilo5"><?php echo $fecha_pago; ?></span></td>
  </tr>
  <tr>
    <td><span class="Estilo5">Tipo de pago:</span></td>
    <td><span class="Estilo5"><?php echo $tipo_pago; ?></span></td>
    <td></td>
    <td>&nbsp;</td>
    <td><span class="Estilo5">C&oacute;digo de SS: </span></td>
    <td><span class="Estilo5"><?php echo $codigo_ss; ?></span></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="50%"  valign="top" class="linea4">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50%" class="linea2"><span class="Estilo5">&nbsp;Ingresos </span></td>
        <td width="25%" class="linea2"><span class="Estilo5">Anual</span></td>
        </tr>
         <?php
         $sumatoria_descuentos = $sumatoria_deducciones = $sumatoria_ingresos = 0;
          
         
         if(!empty($lista_acumulados))
  {
  foreach ($lista_acumulados as $acumulado)	{
  $sumatoria_ingresos += $acumulado['acumulado'];
   ?>
      <tr>
        <td><span class="Estilo5">&nbsp;<?php echo isset($acumulado['nombre'])?$acumulado['nombre']:''?>  </span></td>
        <td><span class="Estilo5">$&nbsp;<?php echo isset($acumulado['acumulado'])?number_format($acumulado['acumulado'],2):''?> </span> </td>
        </tr>
        <?php } } ?>
    </table>
	
	</td>
    <td width="50%" valign="top">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50%" class="linea2"><span class="Estilo5">&nbsp;Deducciones </span></td>
        <td width="25%" class="linea2"><span class="Estilo5">Deducir</span></td>
        <td width="25%" class="linea2"><span class="Estilo5">Pagado al a&ntilde;o</span></td>
        </tr>
         <?php if(!empty($lista_deducciones))
  {
  	
  foreach ($lista_deducciones as $deduccion)	{
	
	$deduccion_individual =  isset($deduccion['descuento'])?$deduccion['descuento']:'0';
 ?>
      <tr>
        <td><span class="Estilo5">&nbsp;<?php echo isset($deduccion['nombre'])?$deduccion['nombre']:''?>  </span></td>
        <td><span class="Estilo5">$&nbsp;<?php echo number_format($deduccion_individual,2); ?> </span> </td>
        <td><span class="Estilo5">$&nbsp;<?php echo isset($deduccion['saldo'])?number_format($deduccion['saldo'],2):''?>  </span></td>
        </tr>
        <?php 
        $sumatoria_deducciones += $deduccion_individual;
} } ?>
    </table>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td colspan="3" class="linea2"><span class="Estilo5">&nbsp;Descuentos directos</span></td>
        </tr>
         <?php if(!empty($lista_descuentos))
  {
  	
  foreach ($lista_descuentos as $descuentos)	{ 
	$descuento_individual =  isset($descuentos['monto_ciclo'])?number_format($descuentos['monto_ciclo'],2):'0';
?>
      <tr>
        <td  width="50%" ><span class="Estilo5">&nbsp;<?php echo isset($descuentos['acreedor'])?$descuentos['acreedor']:''?>  </span></td>
        <td  width="25%" ><span class="Estilo5">$&nbsp;<?php echo number_format($descuento_individual,2); ?> </span> </td>
        <td  width="25%" ><span class="Estilo5">$&nbsp;<?php echo isset($descuentos['saldo_restante'])?number_format($descuentos['saldo_restante'],2):''?>  </span></td>
        </tr>
        <?php $sumatoria_descuentos += $descuento_individual;
 } } ?>
    </table>
	 
	</td>
  </tr>
</table>

 <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="15%" class="linea2"><span class="Estilo5">Rata por hora</span></td>
    <td width="16%" class="linea2"><span class="Estilo5">$&nbsp;<?php echo number_format($rata,2); ?></span></td>
    <td width="13%" class="linea2"><span class="Estilo5">Ingresos:</span></td>
    <td width="22%" class="linea2"><span class="Estilo5">$&nbsp;<?php echo number_format($sumatoria_ingresos,2); ?></span></td>
    <td width="17%" class="linea2"><span class="Estilo5">Deducciones:</span></td>
    <td width="17%" class="linea2"><span class="Estilo5">$&nbsp;<?php echo number_format($sumatoria_descuentos+ $sumatoria_deducciones,2); ?></span></td>
    <td width="17%" class="linea2"><span class="Estilo7">Pago Neto: </span></td>
    <td width="17%" class="linea2"><span class="Estilo7">$&nbsp;<?php echo number_format($sumatoria_ingresos - ($sumatoria_descuentos+ $sumatoria_deducciones),2); ?></span></td>
  </tr>
</table>
 