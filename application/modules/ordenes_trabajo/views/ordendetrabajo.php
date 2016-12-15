
<style type="text/css">

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

    .tabla_items{
        border: 1px solid black;
        border-collapse: collapse;
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


</style>
<?php $sum_total = 0; ?>

<div id="container">
    <table style="width: 100%;">
        <!--seccion de cabecera-->
        <tr>
            <td rowspan="3"><img id="logo" src="<?php $logo = !empty($info->empresa->logo)?$info->empresa->logo:'default.jpg'; echo $this->config->item('logo_path').'/'.$logo;?>" alt="Logo" border="0" height="85px" width="85px" /></td>
            <td class="titulo1">ORDEN DE TRABAJO</td>
        </tr>
        <tr>
            <td class="titulo1">*** DOCUMENTO NO FISCAL ***</td>
        </tr>
        <tr>
            <td class="titulo1">Orden de Trabajo No. <span class="rojo"><?php echo $info->numero;?></span></td>
        </tr>

        <!--datos de la empresa-->
        <tr>
            <td><br><br><?php echo strtoupper($info->empresa->nombre);?></td>
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

        <!--division-->
        <tr>
            <td colspan="2" style="border-bottom: 1px solid black;"></td>
        </tr>

        <!--datos del cliente-->
        <tr>
            <td class="titulo2">CLIENTE:</td>
            <td class="titulo2">EQUIPO DE TRABAJO:</td>
        </tr>
        <tr>
            <td><?php echo $info->cliente->nombre;?></td>
            <td> <?php echo $info->equipoTrabajo[0]->nombre; ?></td>
        </tr>
        <tr>
          <td><?php echo $info->centro_fact[0]->nombre; ?>       </td>
          <td></td>
        </tr>
        <tr>
            <td><?php echo $info->centro_fact[0]->direccion;?></td>
            <td></td>
        </tr>
        
        <!--tabla de items-->
        <tr>
            <td colspan="2">
                
                <table style="width: 100%;" class="tabla_items">
                    <thead>
                        <tr>
                            <th>Categoria</th><!-- info_proveedor -->
                            <th>Items</th><!-- fecha_desde -->
                            <th>Atributo</th> <!-- proveedor_id -->
                            <th>Cantidad</th> <!-- referencia -->
                            <th>Unidad</th> <!-- total sum(total) -->
                            <th>Comentarios</th>
                        </tr>
                    </thead>
                    <tbody> 
                        <?php foreach($info->items as $item):?>
                        <tr>
                            <td style="text-align: center;"><?php echo $item->categoria->nombre;?></td>
                            <td style="text-align: center;"><?php echo $item->item->nombre; ?></td>
                            <td style="text-align: center;"><?php
                                $attributeoutput = '';
                            
                            try {
                                if ($item->atributo_id <> 0){
                                    $attributeoutput = $item->getAttributes[0]->nombre;
                                } else {
                                    $attributeoutput = $item->atributo_text;
                                }
                            } catch (Exception $ex) {
                                $attributeoutput = $item->atributo_text;    
                            }
                            
                                echo $attributeoutput;
                            ?></td>
                            
                            <td style="text-align: center;"><?php echo $item->cantidad;?></td>
                            <td style="text-align: center;"><?php echo $item->unidad->nombre;?></td>
                            <td style="text-align: right;"><?php echo $item->comentario;
                            ?></td>
                        </tr>
                        
                        <?php endforeach;?>
                    </tbody>
                    
                </table>
            </td>
        </tr>
        
        
        <tr>
            <td><br></td>
           
        </tr>

        
        <tr>
            <td colspan="2" style="border-bottom: 1px solid black;"></td>
        </tr>
        <tr>
            <td><br></td>
        </tr>
        <tr>
            <td class="titulo3">Observaciones:</td>
            <td class="titulo3">Autorizaciones:</td>
        </tr>
        <tr>
            <td><br></td>
        </tr>
        <tr>
            <td style="border: 1px solid black" ><?php echo $info->comentario;?></td>
            <td style="border: 1px solid black"><?php 
            //if estado is por_cobrar then usuario + fecha y hora de actualizacion 
            
            $estado = $info->estado_id;
            if ($estado == '12') {
                $autorizadopor = $history->usuario;
                $autorizadoel = $info->updated_at->format('d-m-Y');
                echo $autorizadopor . '<br />' . $autorizadoel;
                //ver cambio
            }
            ?><br><br><br></td>
        </tr>
        
        
    </table>
    
</div>

