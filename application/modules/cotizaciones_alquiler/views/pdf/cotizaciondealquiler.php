<?php
$logo = $cotizacion_alquiler->empresa->logo<>''?$cotizacion_alquiler->empresa->logo:'default.jpg';
$header = '<div id="header">
            <table>
                <tr>
                    <td class="halfwidth"><img id="logo" src="'.$this->config->item("logo_path").$logo.'" alt="Logo" border="0" height="59.69px" /> </td>
                    <td class="titulo1">COTIZACIÓN DE ALQUILER</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="titulo1">No. de Cotización: '. $cotizacion_alquiler->codigo .'</td>
                </tr>
            </table>
        </div>';
$separator = "<div id='separator'><table>
                    <tr>
                    <td colspan='6' style='border-bottom: 2px solid black;'></td>
                </tr></table></div>";

//tabla de items
$table = array();
$i = 1;
//<th>Item</th>
$tablehead = "<thead>
                        <tr>
                            <th>Item</th>

                            <th>Atributos</th>
                            <th>Cantidad</th>
                            <th>Precio tarifario</th>
                            <th>Tarifa Pactada</th>

                        </tr>
                    </thead>";

$detalleheader = ' <div id="detalleheader">
            <table>
                <tr>
                    <td class="halfwidth">'. $cotizacion_alquiler->empresa->nombre .'</td>
                    <td>Fecha de Emisión: '. $cotizacion_alquiler->fecha_desde .'</td>
                </tr>
                <tr>
                    <td>'.$cotizacion_alquiler->empresa->getRuc().'</td>
                    <td>Creado por: '.$cotizacion_alquiler->getCreadoPor().'</td>
                </tr>
                <tr>
                    <td style="padding-right: 63px;">'.$cotizacion_alquiler->empresa->descripcion.'</td>
                    <td class="negrita" style="vertical-align: top;">Centro Contable: '.$cotizacion_alquiler->getCentroContable().'</td>
                </tr>
                <tr>
                    <td>'.$cotizacion_alquiler->empresa->telefono.'</td>
                    <td></td>
                </tr>
            </table>
        </div>';

function createitemline($item){
        if ($item->atributo_id<>0) {

            $attributo = $item->getAttributes[0]->nombre;
        }else {
            $attributo = $item->atributo_text;
        }
        $periodo_nombre = '';
if(count($item->periodotarifario)>0){
  $periodo_nombre = iconv('UTF-8', 'ASCII//TRANSLIT', $item->periodotarifario->nombre);
}
    $tableline = "<tr><td style='text-align: center'>".$item->item['nombre']."</td>

                            <td style='text-align: center'>".$attributo."</td>

                            <td class='numero' style='text-align: center'>".$item->cantidad."</td>
                            <td style='text-align: center'>".$periodo_nombre."</td>
                            <td class='numero' style='text-align: right'>$".number_format($item->precio_unidad, 2, '.',',')."</td>
                        </tr>";
    return $tableline;
}

$table[$i] = "<table class='tabla_items' cellspacing='0' cellpadding='0'>".$tablehead;
$tableitems = "<tbody>";

$itemsnum = 1;
foreach($cotizacion_alquiler->items as $items){

    $tableitems .= createitemline($items);


    if ($itemsnum>10&&count($cotizacion_alquiler->items)>$itemsnum){

        $tableitems .= "</tbody>";
        $table[$i] .= $tableitems."</table>";
        $i++;

            $table[$i] = "<table class='tabla_items' cellspacing='0' cellpadding='0'>".$tablehead;
            $tableitems = "<tbody>";

        $itemsnum = 1;
    }else{
        $itemsnum++;
    }
}
$tableitems .= "</tbody>";
$table[$i] .= $tableitems."</table>"

?>

        <style type="text/css">
            .container {
                font-family: Arial;
                font-size: 11pt;
                white-space: normal;
            }
            .titulo1 {
                font-size: 16pt;
            }
            .titulo2 {
                font-size: 12pt;
            }
            table {
                width: 100%;
                margin-bottom: 20px;
                font-size: 11pt;
                cellspacing: 0px;
                cellpadding: 0px;
            }
            .halfwidth {
                width: 50%;
            }
            .titulotabla{
                background: #2F75B5;
                color: white;
                border: 1px solid black;
                margin: 0px;
                padding: 5px;
                text-align: center;
            }
            .underline {
                text-decoration: underline;
            }
            .negrita {
                font-weight: bold;
            }
            .camposauth {
                min-height: 60px;
                    position: relative;
            }
            .allbox {
                border: #000 solid 1px;
            }
            .columnseparator {
                    padding-left: 50px;
                    white-space: nowrap;
                }
                .bottomborder {
                    border-bottom: #000 solid 1px;
                    padding-bottom: 10px;
                }
                            .tabla_items th{
                border: 1px solid black;
            }

            .tabla_items td{
                border: 1px solid black;
                padding: 2px;
            }
            .piedepagina {
                text-align: center;
                position: fixed;
                bottom: 0px;
            }
        </style>
        <div class="container">
        <?php echo $header; ?>
        <?php echo $detalleheader; ?>
        <?php echo $separator; ?>
        <div id="datosdecotizacion">
            <table>
                <tr>
                    <td class="underline titulo2 negrita">Cotización para:</td>
                </tr>
                <tr>
                    <td class='negrita'><?php echo $cotizacion_alquiler->cliente->nombre?></td>
                </tr>
                <tr>
                    <td>
<?php

  if(!empty($cotizacion_alquiler->getCentroFacturacion()) && $cotizacion_alquiler->getCentroFacturacion()->nombre != 'Indefinido'){
      echo 'Centro de facturación: <span class="negrita">'.$cotizacion_alquiler->getCentroFacturacion()->nombre.'</span>';
  }
  else{
    echo "";
  }

?>

                    <!--Centro de facturación: <span class="negrita"><?php //echo !empty($cotizacion_alquiler->getCentroFacturacion()) ? $cotizacion_alquiler->getCentroFacturacion()->nombre : '' ?></span>--></td>
                </tr>
                <tr>
                    <td class="negrita"><?php echo !empty($cotizacion_alquiler->getCentroFacturacion()) ? $cotizacion_alquiler->getCentroFacturacion()->direccion : ''; ?></td>
                </tr>
                <tr>

                    <td><?php echo count($cotizacion_alquiler->cliente->telefonos_asignados) ? $cotizacion_alquiler->cliente->telefonos_asignados()->first()->telefono : ''; ?></td>
                </tr>
            </table>
        </div>
        <?php
            $pagebreak=false;
            $f = 1;

            if ($i>1){
                $pagebreak=true;
            }

            $articulos = array();

            $totalesfinal = '<table style="width: 100%">
  <tr>
    <td width="62%">&nbsp;</td>
    <td width="25%">Subtotal:</td>
    <td width="13%" class="numero" style="font-weight: bold;text-align: right">$ '.$cotizacion_alquiler->subtotal.'</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Descuento:</td>
    <td class="numero" style="font-weight: bold;text-align: right">$ '.$cotizacion_alquiler->descuento.'</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Impuesto:</td>
    <td class="numero" style="font-weight: bold;text-align: right">$ '.$cotizacion_alquiler->impuestos.'</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Total:</td>
    <td class="numero" style="font-weight: bold;text-align: right">'.$cotizacion_alquiler->total.'</td>
  </tr>
</table>';

            $fin = '<div id="datosdeautorizacion">
            <!--un solo cuadro-->
            <table class="allbox">
                <tr>
                    <td class="halfwidth">Autorizado por:</td>
                    <td>Observaciones:</td>
                </tr>
                <tr class="camposauth">
                    <td>'.$this->session->userdata['nombre']. ' ' .$this->session->userdata['apellido'].'</td>
                    <td>'.$cotizacion_alquiler->comentario.'</td>
                </tr>
                <tr class="camposauth">
                    <td><br><br><br></td>
                    <td></td>
                </tr>
            </table>
        </div>
        <div id="firmas">
            <table>
                <tr>
                    <td class="halfwidth2 negrita" style="padding-bottom: 10px;">Por el cliente:</td>
                    <td class="columnseparator"></td>
                    <td class="negrita">Por la empresa:</td>
                    <td class="columnseparator"></td>
                </tr>
                <tr>
                    <td class="bottomborder">Nombre:</td>
                    <td class="columnseparator"></td>
                    <td class="bottomborder">Nombre:</td>
                    <td class="columnseparator"></td>
                </tr>
                <tr>
                    <td class="bottomborder">Firma:</td>
                    <td class="columnseparator"></td>
                    <td class="bottomborder">Cargo:</td>
                    <td class="columnseparator"></td>
                </tr>
                <tr>
                    <td></td>
                    <td class="columnseparator"></td>
                    <td class="bottomborder">Firma:</td>
                    <td class="columnseparator"></td>
                </tr>
            </table>
        </div>';
            foreach ($table as $itemtable){
            $articulos[$f] = '<div id="articulo'.$f.'">';
            $articulos[$f] .= $itemtable;
            $articulos[$f] .= '</div>';
            $piedepagina = '<div class="piedepagina"><p>Página '.$f.' de '.count($table).'</p></div>';


            if ($f==count($table)){
                //add observation and auth part
                $articulos[$f] .= $totalesfinal;
                $articulos[$f] .= $fin;
            }
            $articulos[$f] .= $piedepagina;
            if($pagebreak && $f<count($table)){
                $articulos[$f] .= '<div style="page-break-before: always;"></div>';
            }
            $f++;
        }
        $x = 0;
        foreach($articulos as $art){
            if($x>0){
                $art = $header.$art;
            }
            echo $art;

            $x++;
        }
        ?>

        </div>
