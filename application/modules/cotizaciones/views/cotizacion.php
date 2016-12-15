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
 
    <?php

 
    ?>
    
<div id="container">
    
    <table style="width: 100%;">
        <!--seccion de cabecera-->
        <tr> 
            <td rowspan="3"> 
             	 <img id="logo" src="<?php $logo = !empty($cotizacion->empresa->logo)?$cotizacion->empresa->logo:'default.jpg'; echo $this->config->item('logo_path').'/'.$logo;?>" alt="Logo" border="0" height="85px" width="85px" />
             	 <!-- <img id="logo" src="https://3.bp.blogspot.com/-W__wiaHUjwI/Vt3Grd8df0I/AAAAAAAAA78/7xqUNj8ujtY/s1600/image02.png" alt="Logo" border="0" height="85px" width="85px" />-->
             <td class="titulo1">COTIZACION</td>
        </tr>
        <tr>
            <td class="titulo1">No. de Cotizaci&oacute;n: <span class="rojo"><?php echo $cotizacion->codigo?></span></td>
        </tr>
        <tr>
            <td><br></td>
        </tr>
        
        <!--datos de la empresa-->
        <tr>
            <td><br><br><?php echo strtoupper($cotizacion->empresa->nombre);?></td>
            <td><br><br>Fecha: <?php echo date('d-m-Y', time())?></td>
        </tr>
        <tr>
            <td><?php echo strtoupper($cotizacion->empresa->descripcion);?></td>
            <td>Vendedor: <?php echo $cotizacion->vendedor->nombre.' '.$cotizacion->vendedor->apellido?></td>
        </tr>
        <tr>
            <td><?php echo $cotizacion->empresa->telefono?></td>
            <td></td>
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
            <td><?php echo $cotizacion->cliente->nombre;?></td>
            <td><?php echo count($cotizacion->centro_facturacion) ? $cotizacion->centro_facturacion->direccion : 'No se indic&oacute;'?></td>
        </tr>
        <tr>
            <td><?php echo $cotizacion->cliente->identificacion;?></td>
            <td></td>
        </tr>
        
        
        <!--tabla de items-->
        <?php $this->load->view('pdf/articulos_ventas',['venta'=>$cotizacion])?>
        
        <tr>
            <td>Cotizaci&oacute;n valida hasta: <?php echo $cotizacion->fecha_hasta;?></td>
        </tr>
        <tr>
            <td><br></td>
        </tr>
        
        <!--division-->
        <tr>
            <td colspan="2" style="border-bottom: 1px solid black;"></td>
        </tr>
        
        <tr>
            <td class="titulo3">Observaciones:</td>
            <td class="titulo3">Autorizaciones:</td>
        </tr>
        <tr>
            <td style="border: 1px solid black;"><br><br><br></td>
            <td style="border: 1px solid black;"><br><br><br></td>
        </tr>
        
    </table>
    
</div>


