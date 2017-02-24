<style type="text/css">
    .titulo1{
        font-weight:bold;
        font-size: 18px;
        text-align: center;
    }
    .columnasnombres{
           width: 30%;
           text-align: left !important;
    }

</style>
<div id="container">
  <table style="width: 100%; margin-left: 40%;">
        <!--seccion de cabecera-->
        <tr>
            <td> <img id="logo" src="<?php $logo = !empty($datos->datosEmpresa->logo)?$datos->datosEmpresa->logo:'default.jpg'; echo $this->config->item('logo_path').$logo;?>" height="56.69px" alt="Logo" border="0" /></td>
            <td class="titulo1">Nombre de la Remesa <?php echo "<br>".$datos->remesa?></td>
        </tr>                                  
        <tr>
            <td colspan=2 class="titulo1"><br><br></td>
        </tr>
        
        <!--datos de la empresa  titulo1 -->
        <tr colspan="5" class="text-center">
            <th>Datos generales</th>
        </tr>
        
        <tr>
            <td colspan=2 class="titulo1"><br></td>
        </tr>
        <tr>
            <th class='columnasnombres'>N° remesa :</th>
            <td><?php echo strtoupper($datos->remesa);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Aseguradora :</th>
            <td><?php echo strtoupper($aseguradora->nombre);?></td>
        </tr>
        
        <tr>
            <th class='columnasnombres'>Fecha inicial : </th>
            <td><?php echo strtoupper($fecha_inicial);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Fecha final : </th>
            <td><?php echo strtoupper($fecha_final);?></td>
        </tr>
        
        <tr>
            <th class='columnasnombres'>Ramos : </th>
            <td></td>
        </tr>
        <?php 

            foreach ($nombreRamos as $key => $value) {
                echo '
                    <tr>
                        <th class="columnasnombres"></th>
                        <td>'.$value["nombre"].'</td>
                    </tr>
                ';
            }
        ?>
         
    </table>
    <br>
    <table style="width: 100%;">
        <!--datos de la remesa  titulo1 -->
        <tr class="text-center">
            <th colspan="12">Datos de la Remesa</th>
        </tr>
        <tr>
            <th class='columnasnombres' width="7%">No. Recibo </th>
            <th class='columnasnombres' width="7%">No. poliza </th> 
            <th class='columnasnombres' width="7%">Inicio vigencia </th> 
            <th class='columnasnombres' width="7%">Fin vigencia </th> 
            <th class='columnasnombres' width="7%">Prima total </th> 
            <th class='columnasnombres' width="7%">Comisión descontada </th> 
            <th class='columnasnombres' width="7%">S.Comisión descontada </th> 
            <th class='columnasnombres' width="7%">Pago a aseguradora </th>
        </tr> 
        <?php
            foreach ($datosRemesa as $value) {
                echo '
                <tr>
                    <td style="'.$value['estilos'].'">'.$value['codigo'].'</td>
                    <td style="'.$value['estilos'].'">'.$value['numero_poliza'].'</td>
                    <td style="'.$value['estilos'].'">'.$value['inicio_vigencia'].'</td>
                    <td style="'.$value['estilos'].'">'.$value['fin_vigencia'].'</td>
                    <td style="'.$value['estilos'].'">'.$value['prima_total'].'</td>
                    <td style="'.$value['estilos'].'">'.$value['valor_descuento'].'</td>
                    <td style="'.$value['estilos'].'">'.$value['valor_sobreComision'].'</td>
                    <td style="'.$value['estilos'].'">'.$value['total_aseguradora'].'</td>
                </tr>
                ';
            }
        ?>       
    </table>

</div>


