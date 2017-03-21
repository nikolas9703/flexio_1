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
                width: 47%;
            }
            .halfwidth2 {
                width: 55%;
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
//creates lines items for table
function createitemline($item) {
    $attributeoutput = '';

        try {
            if ($item->atributo_id <> 0){
                $attributeoutput = isset($item->getAttributes) ? $item->getAttributes[0]->nombre : '';
            } else {
                $attributeoutput = $item->atributo_text;
            }
            } catch (Exception $ex) {
                $attributeoutput = $item->atributo_text;
        }


    $lineitem = '<tr>
                            <td style="text-align: center;">'.$item->categoria->nombre.'</td>
                            <td style="text-align: center;">'. $attributeoutput .'</td>
                            <td style="text-align: center;">'.$item->cantidad.'</td>
                            <td style="text-align: center;">'.$item->unidad->nombre.'</td>
                            <td style="text-align: right;">'.$item->comentario.'</td>
                        </tr>';
    return $lineitem;
}

$logo = $info->empresa->logo<>''?$info->empresa->logo:'default.jpg';

$header = '<div id="header">
    <table>
        <tr>
            <td class="halfwidth"><img id="logo" src="'.$this->config->item('logo_path').'/'.$logo.'" alt="Logo" border="0" height="59.69px" /></td>
            <td class="titulo1">ORDEN DE TRABAJO</td>
        </tr>
        <tr>
            <td></td>
            <td class="titulo1">*** DOCUMENTO NO FISCAL ***</td>
        </tr>
        <tr>
            <td></td>
            <td class="titulo1">Orden de Trabajo No. <span class="rojo">'.$info->numero.'</span></td>
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

//<th>Items</th><!-- fecha_desde -->
$tablehead = '<thead>
                        <tr>
                            <th>Categoria</th><!-- info_proveedor -->
                            <th>Atributo</th> <!-- proveedor_id -->
                            <th>Cantidad</th> <!-- referencia -->
                            <th>Unidad</th> <!-- total sum(total) -->
                            <th>Comentarios</th>
                        </tr>
                    </thead>';

$table[$i] = "<table class='tabla_items' cellspacing='0' cellpadding='0'>".$tablehead;
$tableitems = "<tbody>";

$itemsnum = 1;

foreach($info->items as $items){

    $tableitems .= createitemline($items);


    if ($itemsnum>14&&count($info->items)>$itemsnum){

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

$table[$i] .= $tableitems."</table>";

$fin = '<table><tr>
                        <td class="titulo3">Observaciones:</td>
                        <td class="titulo3">Autorizaciones:</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black;"><br>'.$info->comentario.'<br><br></td>
                        <td style="border: 1px solid black;"><br><br><br></td>
                    </tr></table>';

?>
<div class="container">
    <?php  echo $header; ?><!--header of work order-->
    <div id="detalle">
        <table>
            <!--datos de la empresa-->
            <tr>
                <td class="halfwidth2"><br><br><?php echo strtoupper($info->empresa->nombre);?></td>
                <td><br><br>Fecha: <?php echo date('d-m-Y', time())?></td>
            </tr>
            <tr>
                <td><?php echo strtoupper($info->empresa->ruc);?></td>
                <td></td>
            </tr>
            <tr>
              <td><?php echo strtoupper($info->empresa->descripcion);?></td>
              <td>Preparado por: <?php echo $info->vendedor->nombre.' '.$info->vendedor->apellido?></td>
            </tr>
            <tr>
                <td><?php echo $info->empresa->telefono; ?></td>
                <td>Centro : <?php echo $info->centro->nombre; ?></td>
            </tr>
        </table>
    </div>
    <?php echo $separator; ?>
    <div id="datosclientes">
        <table>
            <!--datos del cliente-->
            <tr>
                <td class="titulo2 halfwidth2">CLIENTE:</td>
                <td class="titulo2">EQUIPO DE TRABAJO:</td>
            </tr>
            <tr>
                <td><?php echo $info->cliente->nombre;?></td>
                <td> <?php

                if(isset($info->equipoTrabajo[0]))
                echo $info->equipoTrabajo[0]->nombre; ?></td>
            </tr>
            <tr>
              <td><?php echo $info->centro_fact[0]->nombre; ?>       </td>
              <td></td>
            </tr>
            <tr>
                <td><?php echo $info->centro_fact[0]->direccion;?></td>
                <td></td>
            </tr>
        </table>
    </div>
    <?php $pagebreak=false;
        $f = 1;

        if ($i>1){
            $pagebreak=true;
        }

        $articulos = array();
        foreach ($table as $itemtable){
            $articulos[$f] = '<div id="articulo'.$f.'">';
            $articulos[$f] .= $itemtable;
            $articulos[$f] .= '</div>';
            $piedepagina = '<div class="piedepagina"><p>Página '.$f.' de '.count($table).'</p></div>';
            if ($f==count($table)){
                $articulos[$f] .= $separator;
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
