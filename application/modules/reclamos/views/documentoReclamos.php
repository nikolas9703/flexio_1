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
  <table style="width: 100%;margin-left: 40%;">
        <!--seccion de cabecera-->
        <tr>
            <td> <img id="logo" src="<?php $logo = !empty($datos->datosEmpresa->logo)?$datos->datosEmpresa->logo:'default.jpg'; echo $this->config->item('logo_path').$logo;?>" height="56.69px" alt="Logo" border="0" /></td>
            <td class="titulo1">Numero del Reclamo <?php echo "<br>".$datos->numero?></td>
        </tr>                                  
        <tr>
            <td colspan=2 class="titulo1"><br><br></td>
        </tr>
        
        <!--datos de la empresa  titulo1 -->
        <tr colspan="5" class="text-center">
            <th>Datos de la Póliza</th>
        </tr>
        <tr>
            <td colspan=2 class="titulo1"><br></td>
        </tr>
        <tr>
        <th class='columnasnombres'>Numero de la Póliza :</th>
            <td><?php echo strtoupper($poliza['numeropoliza']);?></td>
        </tr>
        <tr>
        <th class='columnasnombres'>Inicio de Vigencia :</th>
            <td><?php echo strtoupper($poliza['vigencia_desde']);?></td>
        </tr>
        <tr>
        <th class='columnasnombres'>Fin de Vigencia :</th>
            <td><?php echo strtoupper($poliza['vigencia_hasta']);?></td>
        </tr>
        <tr>
        <th class='columnasnombres'>Cliente :</th>
            <td><?php echo strtoupper($poliza['nombre_cliente']);?></td>
        </tr>
        <tr>
        <th class='columnasnombres'>Aseguradora :</th>
            <td><?php echo strtoupper($poliza['nombre_aseguradora']);?></td>
        </tr>
        <?php 
        $tipo_interes = $poliza['tipo_interes'];
        if($tipo_interes == 2 || $tipo_interes == 4 || $tipo_interes == 6 || $tipo_interes == 7 || $tipo_interes == 8){
            ?>
            <tr>
                <th class='columnasnombres'>Acreedor Hipotecario :</th>
                <td><?php echo strtoupper($poliza['acreedor_hipotecario']);?></td>
            </tr>
            <tr>
                <th class='columnasnombres'>% Asignado al acreedor :</th>
                <td><?php echo strtoupper($poliza['porcentaje_acreedor']);?></td>
            </tr>
            <?php
        } 
        ?>
        
        <!--datos del plan  titulo1 -->
         <tr>
            <td colspan=2 class="titulo1"><br></td>
        </tr>
        <tr colspan="5" class="text-center">
            <th>Datos de Reclamo</th>
        </tr>
        <tr>
            <td colspan=2 class="titulo1"><br></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Numero de Caso :</th>
            <td><?php echo strtoupper($datos->numero_caso);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Fecha de Reclamo : </th>
            <td><?php echo strtoupper($datos->fecha);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Fecha de Siniestro : </th>
            <td><?php echo strtoupper($datos->fecha_siniestro);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Fecha de Notificación: </th>
            <td><?php echo strtoupper($datos->fecha_notificacion);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Reclamante : </th>
            <?php $reclamante = $datos->reclamante != "otros" ? $datos->reclamante : $datos->reclamante_otro; ?>
            <td><?php echo strtoupper($datos->reclamante);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Teléfono : </th>
            <td><?php echo strtoupper($datos->telefono);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Celular : </th>
            <td><?php echo strtoupper($datos->celular);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Correo : </th>
            <td><?php echo strtoupper($datos->correo);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>No. de Certificado : </th>
            <td><?php echo strtoupper($datos->no_certificado);?></td>
        </tr>
        <tr>
            <td colspan=2 class="titulo1"><br></td>
        </tr>
        <tr colspan="5" class="text-center">
            <th>Coberturas</th>
        </tr>
        <tr>
            <th class='columnasnombres'>Nombre: </th>
            <th class='columnasnombres'>valor: </th> 
        </tr>
        <?php 
        foreach ($datos->coberturas as $key => $value) {
           echo "
            <tr>
                <td>".strtoupper($value->cobertura)."</td>
                <td>".strtoupper($value->valor_cobertura)."</td>
            </tr>";
        }
        ?>
        <tr>
            <td colspan=2 class="titulo1"><br></td>
        </tr>
        <tr colspan="5" class="text-center">
            <th>Deducible</th>
        </tr>
        <tr>
            <th class='columnasnombres'>Nombre: </th>
            <th class='columnasnombres'>valor: </th> 
        </tr>
        <?php 
        foreach ($datos->deduccion as $key => $value) {
           echo "
            <tr>
                <td>".strtoupper($value->deduccion)."</td>
                <td>".strtoupper($value->valor_deduccion)."</td>
            </tr>";
        }
        ?>

        <!--datos de la vigencia  titulo1 -->
        <tr>
            <td colspan=2 class="titulo1"><br></td>
        </tr>
        <tr colspan="5" class="text-center">
            <th>Detalle de Reclamo</th>
        </tr>
        <tr>
            <td colspan=2 class="titulo1"><br></td>
        </tr>
        <?php 
        if ($tipo_interes != 8) {
            ?>
            <tr>
                <th class='columnasnombres'>Ajustador :</th>
                <td><?php echo strtoupper($datos->ajustadores->nombre);?></td>
            </tr>
            <tr>
                <th class='columnasnombres'>Contacto : </th>
                <td><?php echo strtoupper($datos->contacto);?></td>
            </tr>
            <tr>
                <th class='columnasnombres'>Telefono : </th>
                <td><?php echo strtoupper($datos->detalletelefono);?></td>
            </tr>
            <tr>
                <th class='columnasnombres'>Descripción de Accidente: </th>
                <td><?php echo strtoupper($datos->descripcionsiniestro);?></td>
            </tr>
            <?php
        }else{
            ?>
            <tr>
                <th class='columnasnombres'>Causa :</th>
                <td><?php echo strtoupper($datos->causa);?></td>
            </tr>
            <tr>
                <th class='columnasnombres'>Ajustador :</th>
                <td><?php echo strtoupper($datos->ajustadores->nombre);?></td>
            </tr>
            <tr>
                <th class='columnasnombres'>Contacto : </th>
                <td><?php echo strtoupper($datos->contactos->nombre);?></td>
            </tr>
            <tr>
                <th class='columnasnombres'>Teléfono : </th>
                <td><?php echo strtoupper(trim($datos->telefonodetalle, "_"));?></td>
            </tr>
            <tr>
                <th class='columnasnombres'>Taller : </th>
                <td><?php echo strtoupper($datos->taller);?></td>
            </tr>
            <tr>
                <th class='columnasnombres'>Fecha de Juicio : </th>
                <td><?php echo strtoupper($datos->fecha_juicio);?></td>
            </tr>
            <tr>
                <th class='columnasnombres'>Descripción de Siniestro: </th>
                <td><?php echo strtoupper($datos->descripcionsiniestro);?></td>
            </tr>
            <tr>
                <th class='columnasnombres'>Detalle de Accidentes: </th>            
                <?php
                $acc = "";
                foreach ($accidentes as $value) { 
                    $acc.=$value['etiqueta']."<br>";
                }
                ?>
                <td><?php echo strtoupper($acc); ?></td>
            </tr>
            <?php
        }
        ?>
        
        <tr>
            <td colspan=2 class="titulo1"><br></td>
        </tr>
        <tr colspan="5" class="text-center">
            <th>Información de Pago</th>
        </tr>
        <tr>
            <td colspan=2 class="titulo1"><br></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Total a reclamar :</th>
            <td><?php echo "$".number_format($datos->total_reclamar, 2);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Pago asegurado : </th>
            <td><?php echo "$".number_format($datos->pago_asegurado, 2);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Pago a deducible : </th>
            <td><?php echo "$".number_format($datos->pago_deducible, 2);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Gastos no cubiertos: </th>
            <td><?php echo "$".number_format($datos->gastos_no_cubiertos, 2);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Número de Cheque : </th>
            <td><?php echo strtoupper($datos->numero_cheque);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Fecha de Cheque : </th>
            <td><?php echo strtoupper($datos->fecha_cheque);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Asignado a : </th>
            <td><?php echo strtoupper($datos->usuarios->nombre." ".$datos->usuarios->apellido);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Estado del Reclamo : </th>
            <td><?php echo strtoupper($datos->estado);?></td>
        </tr>
         
    </table>
</div>


