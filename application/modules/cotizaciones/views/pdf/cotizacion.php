<style type="text/css">
            .container {
                white-space: normal;
            }
            .titulo1{
                font-weight:bold;
                font-size: 18px;
                text-align: center;
            }

            .titulo2{
                font-weight:bold;
                text-decoration: underline;
                font-size: 14px;
                padding-top: 10px;
            }

            .titulo3{
                padding-top: 20px;
            }
            table {
                width: 100%;
                padding-bottom: 15px;
                font-size: 11pt;
            }
            .halfwidth {
                width: 60%;
            }
            .titulotabla{
                background: #2F75B5;
                color: white;
                border: 1px solid black;
                margin: 0px;
                padding: 5px;
                text-align: center;
            }
            .rojo{
                color:red;
            }
                .numero{
                text-align: right;
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
<?php
//<td>".$item->inventario_item->nombre."</td>
function createitemline($item){

    $attributo = $item->atributo_id <> 0 ? $item->getAttributes[0]->nombre : $item->atributo_text;
    $tableline = "<tr><td>".$item->categoria->nombre."</td>

                            <td>".$attributo."</td>
                            <td class='numero'>".number_format($item->cantidad, 2, '.', ',')."</td>
                            <td style='text-align: center;'>".$item->unidad->nombre."</td>
                            <td class='numero'>".number_format($item->precio_unidad, 2, '.', ',')."</td>
                            <td class='numero'>".number_format($item->descuento, 2,'.',',')."</td>
                            <td class='numero'>".number_format($item->precio_total, 2, '.',',')."</td>
                        </tr>";
    return $tableline;
}

$logo = !empty($cotizacion->empresa->logo)?$cotizacion->empresa->logo:'default.jpg';
$header = "<div id='header'>
            <table>
                <tr>
                    <td class='halfwidth'><img id='logo' src='".$this->config->item('logo_path').$logo."' alt='Logo' border='0' height='59.69px' /> </td>
                    <td class='titulo1'>COTIZACION</td>
                </tr>
                <tr>
                    <td></td>
                    <td class='titulo1'>No. de Cotizaci&oacute;n: <span class='rojo'>".$cotizacion->codigo."</span></td>
                </tr>
                <tr>
                    <td><br></td>
                </tr>
            </table>
            </div>";


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
                            <th>Categor&iacute;a</th>

                            <th>Atributos</th>
                            <th>Cant.</th>
                            <th>Unidad</th>
                            <th>Precio unitario</th>
                            <th>Descuento</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>";

$table[$i] = "<table class='tabla_items' cellspacing='0' cellpadding='0'>".$tablehead;
$tableitems = "<tbody>";

$itemsnum = 1;
foreach($cotizacion->items as $items){

    $tableitems .= createitemline($items);


    if ($itemsnum>13&&count($cotizacion->items)>$itemsnum){

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
<div class="container">
    <?php echo $header ?><!--header of quote-->
    <div id="detalle">
        <table>
            <tr>
                <td><br><br><?php echo strtoupper($cotizacion->empresa->nombre);?></td>
                <td><br><br>Fecha: <?php echo date('d-m-Y', time())?></td>
            </tr>
            <tr>
                <td><?php echo strtoupper($cotizacion->empresa->descripcion); //split address descripcion == direccion?></td>
                <td>Vendedor: <?php echo $cotizacion->vendedor->nombre.' '.$cotizacion->vendedor->apellido?></td>
            </tr>
            <tr>
                <td><?php //direccion dos ?></td>
                <td>Centro: <?php echo is_null($cotizacion->centro_contable)?"": $cotizacion->centro_contable->nombre; ?></td>
            </tr>
            <tr>
                <td><?php echo $cotizacion->empresa->telefono?></td>
                <td></td>
            </tr>
        </table>
    </div>
    <?php echo $separator ?>
    <div id="datosclientes">
        <table>
            <tr>
                <td class="titulo2">CLIENTE:</td>
                <td class="titulo2">ENTREGAR EN:</td>
            </tr>
            <tr>
                <td><?php echo $cotizacion->cliente->nombre;?></td>
                <td><?php echo count($cotizacion->centro_facturacion) ? $cotizacion->centro_facturacion->direccion : 'No se indic&oacute';?></td>
            </tr>
            <tr>
                <td><?php echo count($cotizacion->centro_facturacion) ? $cotizacion->centro_facturacion->nombre : 'No se indic&oacute;';?></td>
                <td></td>
            </tr>
            <tr>
                <td><?php echo $cotizacion->cliente->identificacion;?></td>
                <td></td>
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
        $totalesfinal = '<table>
                                <tr>
                                    <td>T&eacute;rminos de pago:  '.$cotizacion->termino_pago_catalogo->valor.'</td>
                                    <td rowspan="3">

                                        <table style="width: 100%">
                                            <tr>
                                                <td>Subtotal:</td>
                                                <td class="numero">$'.number_format($cotizacion->subtotal, 2, '.', ',').'</td>
                                            </tr>
                                            <tr>
                                                <td>Descuento:</td>
                                                <td class="numero">$'.number_format($cotizacion->descuento, 2, '.', ',').'</td>
                                            </tr>
                                            <tr>
                                                <td>Impuesto:</td>
                                                <td class="numero">$'.number_format($cotizacion->impuestos, 2, '.', ',').'</td>
                                            </tr>
                                            <tr>
                                                <td>Valor total de la orden:</td>
                                                <td class="numero">$'.number_format($cotizacion->total,2, '.', ',').'</td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                                <tr>
                                    <td>Cotizaci&oacute;n valida hasta: '.$cotizacion->fecha_hasta.'</td>
                                </tr>
                            </table>';
        $fin = '<table><tr>
                        <td class="titulo3">Observaciones:</td>
                        <td class="titulo3">Autorizaciones:</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black;">'.$cotizacion->comentario.'<br></td>
                        <td style="border: 1px solid black;"><br><br><br></td>
                    </tr></table>';

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
