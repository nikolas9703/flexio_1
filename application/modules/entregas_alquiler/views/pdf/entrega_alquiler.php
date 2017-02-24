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
    
    #tablaauth {
        page-break-before: always;
    }
    
    #tablaitems {
        page-break-inside: always;
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

            <td rowspan="3"> <img id="logo" src="<?php $logo = !empty($empresa->logo)?$empresa->logo:'default.png'; echo $ruta.$logo;?>" height="56.69px" alt="Logo" border="0" /></td>

            <td class="titulo1">NOTA DE ENTREGA</td>
        </tr>
        <tr>
            <td class="titulo1_1"><span class="titulo1">No. de Entrega: <?php echo $entrega_info->codigo;?></span></td>
        </tr>
        <tr>
            <td></td>
        </tr>

        <!--datos de la empresa-->

        <tr>
            <td style="width: 50%!important; padding-right: 100px!important;">
        <?php echo strtoupper($empresa->nombre);?><br />
        <?php echo strtoupper($empresa->tomo . "-" . $empresa->folio . "-" . $empresa->asiento . "-" . $empresa->digito_verificador);?><br />
        <?php echo strtoupper($empresa->descripcion);?><br />
        <?php echo $empresa->telefono?>
            </td>
            <td>Fecha de entrega: <?php echo !empty($entrega_info->fecha_entrega) ? Carbon::createFromFormat('Y-m-d H:i:s', $entrega_info->fecha_entrega)->format('d/m/Y') : ''; ?><br />
              Fecha de contrato: <?php echo !empty($entrega_info->contrato_alquiler->fecha_inicio) ? Carbon::createFromFormat('Y-m-d H:i:s', $entrega_info->contrato_alquiler->fecha_inicio)->format('d/m/Y') : '';?> a <?php echo !empty($entrega_info->contrato_alquiler->fecha_fin) ? Carbon::createFromFormat('Y-m-d H:i:s', $entrega_info->contrato_alquiler->fecha_fin)->format('d/m/Y') : ''; ?><br />
              Entregado por: <?php echo $usuario->nombre . " " . $usuario->apellido; ?><br />
              Creado por: <?php echo $creador->nombre . " " . $creador->apellido; ?><br />
              Referencia: <?php echo $entrega_info->contrato_alquiler->referencia?><br />
              <strong>Centro contable: <?=$centro_contable;?></strong>
            </td>
        </tr>
        <tr>
          <td>
            
            </td>
            <td></td>
        </tr>
        <tr>
          <td>
            
          </td>
          <td></td>
        </tr>
        <tr>
            <td></td>
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
            <td class="titulo2">Entrega para:</td>
            <td class="titulo2"></td>
        </tr>
        <tr>
            <td><strong><?php echo $cliente->nombre;?></strong></td>
            <td></td>
        </tr>
        <tr>
          <td><strong><?php echo !empty($cliente->telefonos_asignados[0])?$cliente->telefonos_asignados[0]->telefono:'';?></strong></td>
          <td></td>
        </tr>
        <!--tabla de items-->
        <tr>
          <td style="padding-bottom: -10px;"><br /><br /><strong>Items entregados</strong></td>
        </tr>
        <!--tabla de items-->
       
        <tr>
            <td colspan="2">

                <table style="width: 100%;" class="tabla_items" id="tablaitems">
                    <thead>
                        <tr class="titulo2">
                            <th>Item</th>
                            <th>Atributos</th>
                            <th>Cantidad o Serie</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php $i=0;  ?>
                        
                      <?php foreach($items_entregados as $info){
                        
                        $j=0;
                        foreach($info->contrato_alquiler->contratos_items as $row){  
                            
                        $itemsEntregados = !empty($row["contratos_items_detalles_entregas"]) ? $row["contratos_items_detalles_entregas"] : typeof($row["contratos_items_detalles_entregas"])!='undefined'? $row["contratos_items_detalles_entregas"] : "";
                        
                        $series = collect($itemsEntregados)->pluck('serie')/*->reject(function ($name, $j) { return empty($name[$j]); })*/;
                        //dd($series);
                        $nombre = $row->item->nombre;
                        $codigo = $row->item->codigo;
                       
                          $cantidad = $row->cantidad;                        
                         ?>
                        <?php //dd($row->atributo_text); ?>
                        <?php                         
                        foreach($series as $val) { ?>
                       <tr class="titulo5">
                          <td  width="30%" style="text-align:center"><?php echo $codigo . " - " . $nombre; ?></td>
                          <td width="12%" style="text-align:center"><?php
                          if($row->atributo_id<>0){
                          echo $atributos[$j]['atributos'][0]['nombre']; 
                          }
                          ?></td>  
                            <td width="8%" style="text-align:center"><?php echo !empty($val) ? $val : $cantidad; ?></td>
                        </tr>
                        <?php } 
                        
                        ?> 
                       <!-- <tr class="titulo5">
                          <td  width="18%" style="text-align:center"><?php //echo $codigo . " - " . $nombre; ?></td>
                          <td width="18%" style="text-align:center"><?php 
                          //if($row->atributo_id<>0){
                         // echo $atributos[$j]['atributos'][0]['nombre']; } else { echo $row->atributo_text; } ?></td>                         
                            <td width="15%" style="text-align:center"><?php //echo !empty($val) ? $val : $cantidad; ?></td>
                        </tr> -->
                      <?php $j++; } ?>
                        
                        <?php
                        $i++;
                      } ?>

                    </tbody>
                </table>
            </td>
        </tr>
        <!--division-->

    </table>
    <br /><br />
     <!-- ************************************************************************************************************************************** -->
     <table style="width:100%; border:1px solid;" id="tablaauth">
       <tr>
       	<td class="titulo4">Autorizado por:</td>
          <td class="titulo4">Observaciones:</td>
       </tr>
       <tr>
       	<td><?php echo $creador->nombre.' '.$creador->apellido?></td>
          <td><?php echo $entrega_info->observaciones;?></td>
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
