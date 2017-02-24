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
            <td rowspan="3"><img id="logo" src="<?php $logo = !empty($factura->empresa->logo)?$factura->empresa->logo:'default.jpg'; echo $this->config->item('logo_path').'/'.$logo;?>" alt="Logo" border="0" height="85px" width="85px" /></td>
            <td class="titulo1">FACTURA</td>
        </tr>
        <tr>
            <td class="titulo1">*** DOCUMENTO NO FISCAL ***</td>
        </tr>
        <tr>
            <td class="titulo1">No. de Factura: <span class="rojo"><?php echo $factura->codigo?></span></td>
        </tr>

        <!--datos de la empresa-->
        <tr>
            <td><br><br><?php echo strtoupper($factura->empresa->nombre);?></td>
            <td><br><br>Fecha: <?php echo date('d-m-Y', time())?></td>
        </tr>
        <tr>
            <td><?php echo strtoupper($factura->empresa->ruc);?></td>
            <td>Pagar antes de: <?php  
            $date = DateTime::createFromFormat('d/m/Y H:i', $factura->fecha_hasta.' 00:00');
            echo $date->format('d-m-Y');

            //echo  $factura->setFechaHastaAttributePdf();  se estaba usando este metodo, pero mandada un error, tuve q user php para salir rapido, corregir, Kimi
            ?></td>
        </tr>
        <tr>
          <td><?php echo strtoupper($factura->empresa->descripcion);?></td>
          <td>Preparado por: <?php echo $factura->vendedor->nombre.' '.$factura->vendedor->apellido?></td>
        </tr>
        <tr>
            <td><?php echo $factura->empresa->telefono; ?></td>
            <td>Centro : <?php echo $factura->centro->nombre; ?></td>
        </tr>

        <!--division-->
        <tr>
            <td colspan="2" style="border-bottom: 1px solid black;"></td>
        </tr>

        <!--datos del cliente-->
        <tr>
            <td class="titulo2">CLIENTE:</td>
            <td class="titulo2">ENTREGAR EN:</td>
        </tr>
        <tr>
            <td><?php echo $factura->cliente->nombre;?></td>
            <td> <?php echo $factura->centros_fac->direccion; ?></td>
        </tr>
        <tr>
          <td><?php echo $factura->centros_fac->nombre; ?>       </td>
          <td></td>
        </tr>
        <tr>
            <td><?php echo $factura->cliente->identificacion;?></td>
            <td></td>
        </tr>
        
        <!--tabla de items-->
        <tr>
            <td colspan="2">
                
                <table style="width: 100%;" class="tabla_items">
                    <thead>
                        <tr>
                            <th>No. Factura</th><!-- factura_proveedor -->
                            <th>Fecha de Emisión</th><!-- fecha_desde -->
                            <th>Proveedor</th> <!-- proveedor_id -->
                            <th>Referencia</th> <!-- referencia -->
                            <th>Monto</th> <!-- total sum(total) -->
                            
                        </tr>
                    </thead>
                    <tbody> 
                        <?php foreach($factura->refactura as $item):?>
                        <tr>
                            <td style="text-align: center;"><?php echo $item->factura_proveedor;?></td>
                            <td style="text-align: center;"><?php $date = DateTime::createFromFormat('d/m/Y H:i', $item->fecha_desde .' 00:00');
                                      echo $date->format('d-m-Y'); ?></td>
                            <td style="text-align: center;"><?php echo $item->proveedor->nombre;?></td>
                            
                            <td style="text-align: center;"><?php echo $item->referencia;?></td>
                            <td style="text-align: right;">$<?php echo $item->total;
                                $sum_total += $item->total;
                            ?></td>
                        </tr>
                        
                        <?php endforeach;?>
                    </tbody>
                    
                </table>
            </td>
        </tr>
        
        <tr>
            <td style="width: 430px;    padding-top: 33px">Modo de pago: <?php echo $factura->termino_pago2->valor;?></td>
            <td rowspan="3">
                <table>
                    <tr>
                        <td>Total:</td>
                        <td style='padding-right:100px'></td>
                        <td class="number">$<?php echo $sum_total?></td>
                    </tr>
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
            <td ><?php echo $factura->comentario;?></td>
            <td ><?php 
            //if estado is por_cobrar then usuario + fecha y hora de actualizacion 
            $estado = $factura->estado;
            if ($estado == 'por_cobrar' OR $estado == 'cobrado_parcial' OR $estado == 'cobrado_completo') {
                $autorizadopor = $history->usuario;
                $autorizadoel = $factura->updated_at->format('d-m-Y');
                echo $autorizadopor . '<br />' . $autorizadoel;
                //ver cambio
            }
            ?><br><br><br></td>
        </tr>
        
        <tr>
            <td colspan="2" style="border-bottom: 1px solid black;"></td>
        </tr>
    </table>
    
</div>

