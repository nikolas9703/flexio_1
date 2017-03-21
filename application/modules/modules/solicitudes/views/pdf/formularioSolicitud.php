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
            <td class="titulo1">Nombre de la solicitud <?php echo "<br>".$datos->numero?></td>
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
        <?php 
            if($cliente->tipo_identificacion == 'natural'){
        ?>
            <tr>
                <th class='columnasnombres'>Nombre del cliente :</th>
                <td><?php echo strtoupper($cliente->nombre);?></td>
            </tr>
            <tr>
                <th class='columnasnombres'>Identificación : </th>
                <td><?php echo strtoupper($cliente->tipo_identificacion);?></td>
            </tr>
            <tr>
                <th class='columnasnombres'>Provincia : </th>
                <td>
                <?php 
                    $provincia = explode('-',$cliente->identificacion);
                    foreach ($provincias as $key => $value) {
                        if($value->id == $provincia[0]){
                            echo strtoupper($value->valor);
                        }
                    }
                ?>
                </td>
            </tr>
            <tr>
                <th class='columnasnombres'>Letras : </th>
                <td><?php echo strtoupper($cliente->letra);?></td>
            </tr>
            <tr>
                <th class='columnasnombres'>Tomo: </th>
                <td><?php $tomo = explode('-',$cliente->identificacion);echo strtoupper($tomo[1]);?></td>
            </tr>
            <tr>
                <th class='columnasnombres'>Asiento : </th>
                <td><?php echo strtoupper($tomo[2]);?></td>
            </tr>
            <tr>
                <th class='columnasnombres'>Grupo : </th>
                <td><?php if($grupo != false){echo strtoupper($grupo);}?></td>
            </tr>
            <tr>
                <th class='columnasnombres'>Telefono: </th>
                <td><?php echo strtoupper($cliente->telefono);?></td>
            </tr>
            <tr>
                <th class='columnasnombres'>Correo electronico : </th>
                <td><?php echo strtoupper($cliente->correo);?></td>
            </tr>
            <tr>
                <th class='columnasnombres'>Dirección : </th>
                <td><?php echo strtoupper($cliente->direccion);?></td>
            </tr>
            <tr>
                <th class='columnasnombres'>Exonerado de impuestos : </th>
                <td><?php if($cliente->exonerado_impuesto != ''){ echo "SI";}else{echo "NO";}?></td>
            </tr>
        <?php 
            }elseif($cliente->tipo_identificacion == 'juridico'){
        ?>
                <tr>
                    <th class='columnasnombres'>Nombre del cliente :</th>
                    <td><?php echo strtoupper($cliente->nombre);?></td>
                </tr>
                <tr>
                    <th class='columnasnombres'>Identificación : </th>
                    <td><?php echo strtoupper($cliente->tipo_identificacion);?></td>
                </tr>
                <tr>
                    <th class='columnasnombres'>Tomo/Rollo : </th>
                    <td><?php $rollo = explode('-',$cliente->identificacion);  echo strtoupper($rollo[0]);?></td>
                </tr>
                <tr>
                    <th class='columnasnombres'>Folio/Imagen/Documento : </th>
                    <td><?php echo strtoupper($rollo[1]);?></td>
                </tr>
                <tr>
                    <th class='columnasnombres'>Asiento/Ficha : </th>
                    <td><?php echo strtoupper($rollo[2]);?></td>
                </tr>
                <tr>
                    <th class='columnasnombres'>Digito verificador : </th>
                    <td><?php echo strtoupper($rollo[3]);?></td>
                </tr>
                <tr>
                    <th class='columnasnombres'>Grupo : </th>
                    <td><?php if($grupo != false){echo strtoupper($grupo);}?></td>
                </tr>
                <tr>
                    <th class='columnasnombres'>Telefono : </th>
                    <td><?php echo strtoupper($cliente->telefono);?></td>
                </tr>
                <tr>
                    <th class='columnasnombres'>Correo electronico : </th>
                    <td><?php echo strtoupper($cliente->correo);?></td>
                </tr>
                <tr>
                    <th class='columnasnombres'>Dirección : </th>
                    <td><?php echo strtoupper($cliente->direccion);?></td>
                </tr>
                <tr>
                    <th class='columnasnombres'>Exonerado de impuestos : </th>
                    <td><?php if($cliente->exonerado_impuesto != ''){ echo "SI";}else{echo "NO";}?></td>
                </tr>
        <?php 
            }elseif($cliente->tipo_identificacion == 'pasaporte'){ ?>
                <tr>
                    <th class='columnasnombres'>Nombre del cliente :</th>
                    <td><?php echo strtoupper($cliente->nombre);?></td>
                </tr>
                <tr>
                    <th class='columnasnombres'>Identificación : </th>
                    <td><?php echo strtoupper($cliente->tipo_identificacion);?></td>
                </tr>
                <tr>
                    <th class='columnasnombres'>Grupo : </th>
                    <td><?php if($grupo != false){echo strtoupper($grupo);}?></td>
                </tr>
                <tr>
                    <th class='columnasnombres'>Telefono : </th>
                    <td><?php echo strtoupper($cliente->telefono);?></td>
                </tr>
                <tr>
                    <th class='columnasnombres'>Correo electronico : </th>
                    <td><?php echo strtoupper($cliente->correo);?></td>
                </tr>
                <tr>
                    <th class='columnasnombres'>Dirección : </th>
                    <td><?php echo strtoupper($cliente->direccion);?></td>
                </tr>
                <tr>
                    <th class='columnasnombres'>Exonerado de impuestos : </th>
                    <td><?php if($cliente->exonerado_impuesto != ''){ echo "SI";}else{echo "NO";}?></td>
                </tr>
        
        <?php }?>
        <!--datos del plan  titulo1 -->
         <tr>
            <td colspan=2 class="titulo1"><br></td>
        </tr>
        <tr colspan="5" class="text-center">
            <th>Plan</th>
        </tr>
        <tr>
            <td colspan=2 class="titulo1"><br></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Aseguradora :</th>
            <td><?php echo strtoupper($datos->aseguradora->nombre);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Nombre del plan : </th>
            <td><?php echo strtoupper($datos->plan->nombre);?></td>
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
        <tr colspan="10" class="text-center">
            <th>Vigencia y detalle</th>
        </tr>
        <tr>
            <td colspan=2 class="titulo1"><br></td>
        </tr>
         <tr colspan="5" class="text-center">
            <th>Vigencia</th>
        </tr>
        <tr>
            <th class='columnasnombres'>Fecha inicio: </th>
            <th class='columnasnombres'>Fecha final: </th>
        </tr>
        <tr>
            <td><?php echo strtoupper($datos->vigencia->vigencia_desde);?></td>
            <td><?php echo strtoupper($datos->vigencia->vigencia_hasta);?></td>
        </tr>

        <tr>
            <th class='columnasnombres'>Suma asegurada :</th>
            <td><?php echo strtoupper($datos->vigencia->suma_asegurada);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Pagador :</th>
            <td><?php echo strtoupper($datos->vigencia->tipo_pagador);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Nombre :</th>
            <td><?php echo strtoupper($datos->vigencia->pagador);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Póliza declarativa :</th>
            <td><?php echo strtoupper($datos->vigencia->poliza_declarativa);?></td>
        </tr>

        <!--datos de la prima  titulo1 -->
        <tr>
            <td colspan=2 class="titulo1"><br></td>
        </tr>
        <tr colspan="10" class="text-center">
            <th>Prima e informacion de cobro</th>
        </tr>
        <tr>
            <td colspan=2 class="titulo1"><br></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Prima anual :</th>
            <td><?php echo strtoupper($datos->prima->prima_anual);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>impuesto :</th>
            <td><?php echo strtoupper($datos->prima->impuesto);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Otros :</th>
            <td><?php echo strtoupper($datos->prima->otros);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Descuentos :</th>
            <td><?php echo strtoupper($datos->prima->descuentos);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Total :</th>
            <td><?php echo strtoupper($datos->prima->total);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Frecuencia de pagos :</th>
            <td><?php echo strtoupper($datos->prima->frecuencia_pago);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Método de pago :</th>
            <td><?php echo strtoupper($datos->prima->metodo_pago);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Fecha primer pago :</th>
            <td><?php echo strtoupper($datos->prima->fecha_primer_pago);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Cantidad de pagos :</th>
            <td><?php echo strtoupper($datos->prima->cantidad_pagos);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Sitio de pago :</th>
            <td><?php echo strtoupper($datos->prima->sitio_pago);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Centro de facturación :</th>
            <td><?php echo strtoupper($facturacion->nombre);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Dirección :</th>
            <td><?php echo strtoupper($datos->prima->direccion_pago);?></td>
        </tr>

        <!--datos de la participacion  titulo1 -->
        <tr>
            <td colspan=2 class="titulo1"><br></td>
        </tr>
        <tr colspan="10" class="text-center">
            <th>Participación</th>
        </tr>
        <tr>
            <td colspan=2 class="titulo1"><br></td>
        </tr>

        <tr>
            <th class='columnasnombres'>Agente </th>
            <th class='columnasnombres'>% Participación </th>
        </tr>
        <?php
        $total = 0;
        $i = 0;
        foreach ($datos->participacion as $key => $value) {
           echo "
            <tr>
                <td>".strtoupper($agentes[$i]->nombre.' '.$agentes[$i]->apellido)."</td>
                <td>".strtoupper($value->porcentaje_participacion)."</td>
            </tr>";
            $total += $value->porcentaje_participacion;
            $i++;
        }
        ?>
        <tr>
            <th class='columnasnombres'>Total :</th>
            <td><?php echo strtoupper($total);?></td>
        </tr>


         <tr>
            <td colspan=2 class="titulo1"><br></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Observaciones :</th>
            <td><?php echo strtoupper($datos->observaciones);?></td>
        </tr>
         <tr>
            <th class='columnasnombres'>Estado :</th>
            <td><?php echo strtoupper($datos->estado);?></td>
        </tr>
         
    </table>
</div>


