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
            <td class="titulo1">Nombre del Endoso <?php echo "<br>".$datos->endoso; ?></td>
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
            <th class='columnasnombres'>N° endoso :</th>
            <td><?php echo strtoupper($datos->endoso); ?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Ramo :</th>
            <td><?php echo strtoupper($datos->ramos->nombre);?></td>
        </tr>
        
        <tr>
            <th class='columnasnombres'>Cliente : </th>
            <td><?php echo strtoupper($datos->cliente->nombre);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Poliza : </th>
            <td><?php echo strtoupper($datos->polizas->numero);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Tipo de endoso : </th>
            <td><?php echo $datos->tipo; ?></td>
        </tr>
        <tr>
            <th class="columnasnombres">Motivo : </th>
            <td><?php echo $datos->motivos->valor; ?></td>
        </tr>
        <tr>
            <th class="columnasnombres">Modifica prima : </th>
            <td><?php echo strtoupper($datos->modifica_prima);?></td>
        </tr>
        <?php if($datos->modifica_prima != 'no'){?>
        <tr>
            <th class="columnasnombres">Fecha efectividad : </th>
            <td><?php echo $datos->fecha_efectividad; ?></td>
        </tr>
        <?php }?>
        <tr>
            <th class="columnasnombres">Descripcion : </th>
            <td><?php echo $datos->descripcion; ?></td>
        </tr>
        <tr>
            <th class="columnasnombres">Estado : </th>
            <td><?php echo $datos->estado; ?></td>
        </tr>
        
    </table>
</div>


