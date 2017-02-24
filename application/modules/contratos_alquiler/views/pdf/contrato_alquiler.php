<style type="text/css">
    body {
      font-family: Arial, sans-serif;
    }
    td{
        font-size: 12px;
    }
    .titulo1{
        font-size: 18px;
    }

    .titulo1_1{
        font-size: 16px;
    }

    .titulo2{
        font-weight:bold;
        font-size: 12px;
        padding-top: 10px;
    }

    .titulo3{
        padding-top: 20px;
    }

    .tabla_items{
        border: 1px solid black;
        border-collapse: collapse;
        font-size:12px!important;
        padding-top: 10px;
    }

    .tabla_items th{
        border: 1px solid black;
    }

    .tabla_items td{
        border: 1px solid black;
        padding: 2px;
    }

    .numero{
        text-align: right;
    }

    .rojo{
        color:red;
    }

    .recuadros{
        border: 1px solid black;
        height: 100px;
        vertical-align: top;
        padding: 4px;
    }

	.titulo4{
        font-weight:bold;
        font-size: 12px;
    }

	.titulo5{
        font-size: 10px;
        padding-top: 10px;
    }

</style>
<?php
use Flexio\Modulo\Inventarios\Repository\ItemsRepository as itemsRep;
use Carbon\Carbon;
$this->itemsRep = new itemsRep();
$ruta = empty($empresa->logo)?$this->config->item('base_url')."public/themes/erp/images/" : $this->config->item('base_url')."/public/logo/";
?>
<div id="container">
  <table style="width: 100%;">
        <!--seccion de cabecera-->
        <tr>

            <td rowspan="3"> <img id="logo" src="<?php $logo = !empty($empresa->logo)?$empresa->logo:'default.png'; echo $ruta . $logo;?>" height="56.69px" alt="Logo" border="0" /></td>

            <td class="titulo1">CONTRATO DE ALQUILER</td>
        </tr>
        <tr>
            <td class="titulo1_1"><span class="titulo1">No. de Contrato: <?php echo $contrato_info->codigo;?></span></td>
        </tr>
        <tr>
            <td></td>
        </tr>

        <!--datos de la empresa-->
        <tr>
            <td style="width: 50%!important; padding-right: 100px!important;"><?php echo strtoupper($empresa->nombre);?><br />
            <?php echo strtoupper($empresa->tomo . "-" . $empresa->folio . "-" . $empresa->asiento . "-" . $empresa->digito_verificador);?><br />
            <?php echo strtoupper($empresa->descripcion);?><br />
            <?php echo !empty($empresa->telefono)?$empresa->telefono:''?>
            </td>
            <td>Fecha de contrato: <?php echo !empty($contrato_info->fecha_inicio) ? Carbon::createFromFormat('Y-m-d H:i:s', $contrato_info->fecha_inicio)->format('d/m/Y') : '';?> a <?php echo !empty($contrato_info->fecha_fin) ? Carbon::createFromFormat('Y-m-d H:i:s', $contrato_info->fecha_fin)->format('d/m/Y') : ''; ?><br />
            Creado por: <?php echo !empty($usuario->nombre) ? $usuario->nombre . " " . $usuario->apellido : ''; ?><br />
            Referencia: <?php echo !empty($contrato_info->referencia)?$contrato_info->referencia:''?><br />
            <strong>Centro contable: <?=$centro_contable;?></strong>
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td style="padding-right: 150px!important;"></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <!--division-->
        <tr>
            <td colspan="2" style="border-bottom: 2px solid black;"></td>
        </tr>

        <!--datos del proveedor-->
        <tr>
            <td class="titulo2">Contrato para:</td>
            <td class="titulo2"></td>
        </tr>
        <tr>
            <td><strong><?php echo $cliente->nombre;?></strong></td>
            <td>Facturaci&oacute;n recurrente: <?php echo $contrato_info->corte_facturacion->nombre;?></td>
        </tr>
        <tr>
            <td><strong><?php echo $contrato_info->centro_facturacion->nombre;?></strong></td>
            <td>Corte de facturaci&oacute;n: <?php echo $contrato_info->dia_corte?></td>
        </tr>
        <tr>
            <td><strong><?php echo $contrato_info->centro_facturacion->direccion;?></strong></td>
            <td></td>
        </tr>
        <tr><?php //dd($cliente->telefonos_asignados); ?>
          <td><strong><?php echo empty($cliente->telefonos_asignados) ? $cliente->telefonos_asignados[0]->telefono : '';?></strong></td>
          <td></td>
        </tr>
        <tr>
          <td style="padding-bottom: -10px;"><br /><br /><strong>Items contratados</strong></td>
        </tr>
        <!--tabla de items-->
        <tr>
            <td colspan="2">

                <table style="width: 100%;" class="tabla_items">
                    <thead>
                        <tr class="titulo2">
                            <th>Item</th>
                            <th>Atributos</th>
                            <th>Cantidad</th>
                            <th>Per&iacute;odo tarifario</th>
                            <th>Tarifa pactada</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php $i=0; ?>
                      <?php foreach($items->contratos_items as $row){?>

                        <tr class="titulo5">
                          <td style="text-align:center"><?php echo $row->item->codigo . " - " . $row->item->nombre; ?></td>
                          <td width="18%" style="text-align:center"><?php
                          if($row->atributo_id <> 0){
                          echo $atributos[$i]['atributos'][0]['nombre']; } else {
                              echo $row->atributo_text;
                          }
                          ?></td>
                          <td width="6%" style="text-align:center"><?php echo $row->cantidad; ?></td>
                          <td width="8%" style="text-align:center"><?php echo $row->ciclo->nombre; ?></td>
                          <td width="7.5%" class="numero" style="text-align:center"><?php echo $row->tarifa; ?></td>
                        </tr>

                        <?php
                        $i++;
                      } ?>

                    </tbody>
                </table>
            </td>
        </tr>
        <?php if(!empty($items_entregados)){ ?>
        <tr>
          <td style="padding-bottom: -10px;"><br /><br /><strong>Items entregados</strong></td>
        </tr>
        <!--tabla de items-->
        <tr>
            <td colspan="2">

                <table style="width: 100%;" class="tabla_items">
                    <thead>
                        <tr class="titulo2">
                            <th>Item</th>
                            <th>Cantidad o Serie</th>
                            <th>No. de entrega</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php

                      $i=0; ?>
                      <?php foreach($items_entregados as $info){
                         foreach($info[0]['contrato_alquiler']['contratos_items'] as $row){

                        $itemsEntregados = !empty($row["contratos_items_detalles_entregas"]) ? $row["contratos_items_detalles_entregas"] : "";
                        $series = collect($itemsEntregados)->pluck('serie')->reject(function ($name) { return empty($name); });
                        //dd($itemsEntregados);
                          $nombre = $row->item->nombre;
                          $codigo = $row->item->codigo;
                          $cantidad = $row->cantidad;
                          //dd($row);
                         ?>
                        <?php foreach($series as $val) { ?>
                       <tr class="titulo5">
                          <td style="text-align:center"><?php echo $codigo . " - " . $nombre; ?></td>
                          <td width="16%" style="text-align:center"><?php echo !empty($val)?$val:$cantidad; ?></td>

                            <td width="12%" style="text-align:center"><?php echo $info[0]['codigo']; ?></td>
                            <td width="10%" style="text-align:center"><?php echo !empty($info[0]['fecha_entrega']) ? Carbon::createFromFormat('Y-m-d H:i:s', $info[0]['fecha_entrega'])->format('d/m/Y') : ''; ?></td>
                        </tr>
                        <?php } ?>
                        <?php } ?>
                      <!--   <tr class="titulo5">
                          <td style="text-align:center"><?php //echo $codigo . " - " . $nombre; ?></td>
                          <td width="18%" style="text-align:center"><?php //echo $cantidad; ?></td>

                            <td width="14%" style="text-align:center"><?php //echo $info[0]['codigo']; ?></td>
                            <td width="7.5%" style="text-align:center"><?php //echo !empty($info[0]['fecha_entrega']) ? Carbon::createFromFormat('Y-m-d H:i:s', $info[0]['fecha_entrega'])->format('d/m/Y') : ''; ?></td>
                        </tr> -->


                        <?php
                        $i++;
                      } ?>

                    </tbody>
                </table>
            </td>
        </tr>
        <?php } ?>
        <!--pie de tabla de items-->


        <!--division-->

    </table>
    <br /><br />
     <!-- ************************************************************************************************************************************** -->
     <table style="width:100%; border:1px solid;">
       <tr>
       	<td class="titulo4">Autorizado por:</td>
          <td class="titulo4">Observaciones:</td>
       </tr>
       <tr>
       	<td><?php echo $creador->nombre.' '.$creador->apellido?></td>
          <td><?php echo $contrato_info->observaciones; ?></td>
       </tr>
     </table>
     <table style="width: 100%;">
     <tr>
     	<td></td>
     	<td>&nbsp;</td>
     </tr>
     <!--division-->
    <tr>
      <td>
      <strong>Por el cliente:</strong>
      </td>
      <td>
      <strong>Por la empresa:</strong>
      </td>
      </tr>
    <tr>
      <td>
      Nombre: <br />________________________________
      </td>
      <td>
      Nombre: <br />________________________________
      </td>
    </tr>
    <tr>
      <td>
      Firma: <br />________________________________
      </td>
      <td>
      Cargo: <br />________________________________
      </td>
    </tr>
    <tr>
      <td>

      </td>
      <td>
      Firma: <br />________________________________
      </td>
    </tr>
     </table>
</div>