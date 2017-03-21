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
            <td class="titulo1">Nombre de la Poliza <?php echo "<br>".$datos->numero?></td>
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
            <th class='columnasnombres'>Nombre del cliente :</th>
            <td><?php echo strtoupper($datos->clientepolizafk->nombre_cliente);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Identificación :</th>
            <td><?php echo strtoupper($datos->clientepolizafk->identificacion);?></td>
        </tr>
        
        <tr>
            <th class='columnasnombres'>Grupo : </th>
            <td><?php echo strtoupper($datos->clientepolizafk->grupo);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Telefono : </th>
            <td><?php echo strtoupper($datos->clientepolizafk->telefono);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Correo electronico : </th>
            <td><?php echo strtoupper($datos->clientepolizafk->correo_electronico);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Dirección : </th>
            <td><?php echo strtoupper($datos->clientepolizafk->direccion);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Exonerado de impuestos : </th>
            <td><?php echo strtoupper($datos->clientepolizafk->exonerado_impuesto) ?></td>
        </tr>
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
            <td><?php echo strtoupper($datos->aseguradorafk->nombre);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Nombre del plan : </th>
            <td><?php echo strtoupper($datos->planesfk->nombre);?></td>
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
        foreach ($datos->coberturasfk as $key => $value) {
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
        foreach ($datos->deduccionesfk as $key => $value) {
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
            <td><?php echo strtoupper($datos->vigenciafk->vigencia_desde);?></td>
            <td><?php echo strtoupper($datos->vigenciafk->vigencia_hasta);?></td>
        </tr>

        <tr>
            <th class='columnasnombres'>Suma asegurada :</th>
            <td><?php echo strtoupper($datos->vigenciafk->suma_asegurada);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Pagador :</th>
            <td><?php echo strtoupper($datos->vigenciafk->tipo_pagador);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Nombre :</th>
            <td><?php echo strtoupper($datos->vigenciafk->pagador);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Póliza declarativa :</th>
            <td><?php echo strtoupper($datos->vigenciafk->poliza_declarativa);?></td>
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
            <td><?php echo strtoupper($datos->primafk->prima_anual);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>impuesto :</th>
            <td><?php echo strtoupper($datos->primafk->impuesto);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Otros :</th>
            <td><?php echo strtoupper($datos->primafk->otros);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Descuentos :</th>
            <td><?php echo strtoupper($datos->primafk->descuentos);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Total :</th>
            <td><?php echo strtoupper($datos->primafk->total);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Frecuencia de pagos :</th>
            <td><?php echo strtoupper($datos->primafk->frecuencia_pago);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Método de pago :</th>
            <td><?php echo strtoupper($datos->primafk->metodo_pago);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Fecha primer pago :</th>
            <td><?php echo strtoupper($datos->primafk->fecha_primer_pago);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Cantidad de pagos :</th>
            <td><?php echo strtoupper($datos->primafk->cantidad_pagos);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Sitio de pago :</th>
            <td><?php echo strtoupper($datos->primafk->sitio_pago);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Centro de facturación :</th>
            <td><?php echo strtoupper($centro_facturacion->nombre);?></td>
        </tr>
        <tr>
            <th class='columnasnombres'>Dirección :</th>
            <td><?php echo strtoupper($datos->primafk->direccion_pago);?></td>
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
        foreach ($datos->participacionfk as $key => $value) {
           echo "
            <tr>
                <td>".strtoupper($value->agente)."</td>
                <td>".strtoupper($value->porcentaje_participacion)."</td>
            </tr>";
        }
        ?>
        <tr>
            <th class='columnasnombres'>Total :</th>
            <td><?php echo strtoupper($total_participacion).'.00';?></td>
        </tr>

        <tr>
            <td colspan=2 class="titulo1"><br></td>
        </tr>
         <tr>
            <th class='columnasnombres'>Estado :</th>
            <td><?php echo strtoupper($datos->estado);?></td>
        </tr>
         
    </table>
    <br>
    <table style="width: 100%;">
        <!--datos del interes_asegurado  titulo1 -->
        <?php 
            if($datos->tipo_ramo == "colectivo"){
                if($datos->id_tipo_int_asegurado == 1){ ?>
                    <tr class="text-center">
                        <th colspan="12">Datos del artículo</th>
                    </tr>
                    <tr>
                        <th class='columnasnombres' >No. Interés </th>
                        <th class='columnasnombres'>Nombre </th> 
                        <th class='columnasnombres'>No. Certificado </th> 
                        <th class='columnasnombres'>Suma asegurada </th> 
                        <th class='columnasnombres'>Prima neta </th> 
                        <th class='columnasnombres'>Deducible </th> 
                        <!--<th class='columnasnombres'>Clase de equipo </th>
                        <th class='columnasnombres'>Marca </th>
                        <th class='columnasnombres'>Modelo </th>
                        <th class='columnasnombres'>Año </th>
                        <th class='columnasnombres'>Serie </th>
                        <th class='columnasnombres'>Condición </th>
                        <th class='columnasnombres'>Valor </th>
                        <th class='columnasnombres'>Fecha inclusión </th>
                        <th class='columnasnombres'>Fecha exclusión </th>-->
                        <th class='columnasnombres'>Estado </th>
                    </tr>        
        <?php
                    foreach ($interes_asegurado AS $row) {
                        echo "
                            <tr>
                                <td>".$row["numero"]."</td>
                                <td>".$row['nombre']."</td>
                                <td>".$row['detalle_certificado']."</td>
                                <td>".$row['detalle_suma_asegurada']."</td>
                                <td>".$row['detalle_prima']."</td>
                                <td>".$row['detalle_deducible']."</td>
                                <!--<td>".$row['clase_equipo']."</td>
                                <td>".$row['marca']."</td>
                                <td>".$row['modelo']."</td>
                                <td>".$row['anio']."</td>
                                <td>".$row['numero_serie']."</td>
                                <td>".$row['id_condicion']."</td>
                                <td>".$row['valor']."</td>
                                <td>".$row['fecha_inclusion']."</td>
                                <td></td>-->
                                <td>".$row['estado']."</td>
                            </tr>
                        ";
                    }
                }elseif($datos->id_tipo_int_asegurado == 2){ ?>
                    <tr class="text-center">
                        <th colspan="12">Datos de la carga</th>
                    </tr>
                    <tr>
                        <th class='columnasnombres'>No. Interés </th>
                        <th class='columnasnombres'>No. Liquidación</th> 
                        <th class='columnasnombres'>No. Certificado </th> 
                        <th class='columnasnombres'>Suma asegurada </th> 
                        <th class='columnasnombres'>Prima neta </th> 
                        <th class='columnasnombres'>Deducible </th> 
                        <th class='columnasnombres'>Estado </th>
                    </tr>   
        <?php
                    foreach ($interes_asegurado AS $row) {
                        echo "
                            <tr>
                                <td>".$row["numero"]."</td>
                                <td>".$row['no_liquidacion']."</td>
                                <td>".$row['detalle_certificado']."</td>
                                <td>".$row['detalle_suma_asegurada']."</td>
                                <td>".$row['detalle_prima']."</td>
                                <td>".$row['detalle_deducible']."</td>
                                <td>".$row['estado']."</td>
                            </tr>
                        ";
                    }
                }elseif($datos->id_tipo_int_asegurado == 3){ ?>
                    <tr class="text-center">
                        <th colspan="12">Datos del casco aéreo</th>
                    </tr>
                    <tr>
                        <th class='columnasnombres'>No. Interés </th>
                        <th class='columnasnombres'>Serie</th> 
                        <th class='columnasnombres'>No. Certificado </th> 
                        <th class='columnasnombres'>Suma asegurada </th> 
                        <th class='columnasnombres'>Prima neta </th> 
                        <th class='columnasnombres'>Deducible </th> 
                        <th class='columnasnombres'>Estado </th>
                    </tr>
        <?php
                    foreach ($interes_asegurado AS $row) {
                        echo "
                            <tr>
                                <td>".$row["numero"]."</td>
                                <td>".$row['serie']."</td>
                                <td>".$row['detalle_certificado']."</td>
                                <td>".$row['detalle_suma_asegurada']."</td>
                                <td>".$row['detalle_prima']."</td>
                                <td>".$row['detalle_deducible']."</td>
                                <td>".$row['estado']."</td>
                            </tr>
                        ";
                    }
                }elseif($datos->id_tipo_int_asegurado == 4){?>
                    <tr class="text-center">
                        <th colspan="12">Datos del casco marítimo</th>
                    </tr>
                    <tr>
                        <th class='columnasnombres'>No. Interés </th>
                        <th class='columnasnombres'>Serie</th> 
                        <th class='columnasnombres'>No. Certificado </th> 
                        <th class='columnasnombres'>Suma asegurada </th> 
                        <th class='columnasnombres'>Prima neta </th> 
                        <th class='columnasnombres'>Deducible </th> 
                        <th class='columnasnombres'>Estado </th>
                    </tr>
        <?php
                    foreach ($interes_asegurado AS $row) {
                        echo "
                            <tr>
                                <td>".$row["numero"]."</td>
                                <td>".$row['serie']."</td>
                                <td>".$row['detalle_certificado']."</td>
                                <td>".$row['detalle_suma_asegurada']."</td>
                                <td>".$row['detalle_prima']."</td>
                                <td>".$row['detalle_deducible']."</td>
                                <td>".$row['estado']."</td>
                            </tr>
                        ";
                    }    
                }elseif( $datos->id_tipo_int_asegurado == 5){ ?>
                    <tr class="text-center">
                        <th colspan="12">Datos de la persona</th>
                    </tr>
                <?php
                    $validar_persona = explode(" ",$datos->ramo);
                    if($validar_persona[1] == "vida" || $validar_persona[1] == "accidentes" || $validar_persona[1] == "accidente"){ ?>
                    <tr>
                        <th class='columnasnombres'>No. Interés </th>
                        <th class='columnasnombres'>Relación</th> 
                        <th class='columnasnombres'>No. Certificado </th> 
                        <th class='columnasnombres'>Suma asegurada </th> 
                        <th class='columnasnombres'>Participacion </th> 
                        <th class='columnasnombres'>Prima neta </th> 
                        <th class='columnasnombres'>Estado </th>
                    </tr>
        <?php
                    }else if($validar_persona[1] == "salud"){ ?>
                        <tr>
                            <th class='columnasnombres'>No. Interés </th>
                            <th class='columnasnombres'>Relación</th> 
                            <th class='columnasnombres'>No. Certificado </th> 
                            <th class='columnasnombres'>Beneficio vida </th> 
                            <th class='columnasnombres'>Monto </th> 
                            <th class='columnasnombres'>Prima neta </th> 
                            <th class='columnasnombres'>Estado </th>
                        </tr>
        <?php       }else{ ?>
                        <tr class="text-center">
                            <th colspan="12">Datos de la persona</th>
                        </tr>
                        <tr>
                            <th class='columnasnombres'>No. Interés </th>
                            <th class='columnasnombres'>Nombre completo</th> 
                            <th class='columnasnombres'>No. Certificado </th> 
                            <th class='columnasnombres'>Suma asegurada </th> 
                            <th class='columnasnombres'>Prima neta </th> 
                            <th class='columnasnombres'>Estado </th>
                        </tr>
        <?php
                    }
                    ?>            
        <?php
                    foreach ($interes_asegurado AS $row) {
                        if($validar_persona[1] == "vida" || $validar_persona[1] == "accidentes" || $validar_persona[1] == "accidente"){
                            echo "
                                <tr>
                                    <td>".$row["numero"]."</td>
                                    <td>".$row['detalle_relacion']."</td>
                                    <td>".$row['detalle_certificado']."</td>
                                    <td>".$row['detalle_suma_asegurada']."</td>
                                    <td>".$row['detalle_participacion']."</td>
                                    <td>".$row['detalle_prima']."</td>
                                    <td>".$row['estado']."</td>
                                </tr>
                            ";
                        }elseif($validar_persona[1] == "salud"){
                            echo "
                                <tr>
                                    <td>".$row["numero"]."</td>
                                    <td>".$row['detalle_relacion']."</td>
                                    <td>".$row['detalle_certificado']."</td>
                                    <td>".$row['detalle_beneficio']."</td>
                                    <td>".$row['detalle_moto']."</td>
                                    <td>".$row['detalle_prima']."</td>
                                    <td>".$row['estado']."</td>
                                </tr>
                            ";
                        }else{
                            echo "
                                <tr>
                                    <td>".$row["numero"]."</td>
                                    <td>".$row['nombrePersona']."</td>
                                    <td>".$row['detalle_certificado']."</td>
                                    <td>".$row['detalle_suma_asegurada']."</td>
                                    <td>".$row['detalle_prima']."</td>
                                    <td>".$row['estado']."</td>
                                </tr>
                            ";
                        }
                    }      
                }elseif($datos->id_tipo_int_asegurado == 6){ ?>
                    <tr class="text-center">
                        <th colspan="12">Datos del Proyecto / Actividad</th>
                    </tr>
                    <tr>
                        <th class='columnasnombres'>No. Interés </th>
                        <th class='columnasnombres'>Nombre</th> 
                        <th class='columnasnombres'>No. Certificado </th> 
                        <th class='columnasnombres'>Suma asegurada </th> 
                        <th class='columnasnombres'>Prima neta </th> 
                        <th class='columnasnombres'>Deducible </th> 
                        <th class='columnasnombres'>Estado </th>
                    </tr> 
        <?php
                    foreach ($interes_asegurado AS $row) {
                        echo "
                            <tr>
                                <td>".$row["numero"]."</td>
                                <td>".$row['nombre_proyecto']."</td>
                                <td>".$row['detalle_certificado']."</td>
                                <td>".$row['detalle_suma_asegurada']."</td>
                                <td>".$row['detalle_prima']."</td>
                                <td>".$row['detalle_deducible']."</td>
                                <td>".$row['estado']."</td>
                            </tr>
                        ";
                    }    
                }elseif($datos->id_tipo_int_asegurado == 7){?>
                    <tr class="text-center">
                        <th colspan="12">Datos de la ubicación</th>
                    </tr>
                    <tr>
                        <th class='columnasnombres'>No. Interés </th>
                        <th class='columnasnombres'>Nombre</th> 
                        <th class='columnasnombres'>No. Certificado </th> 
                        <th class='columnasnombres'>Suma asegurada </th> 
                        <th class='columnasnombres'>Prima neta </th> 
                        <th class='columnasnombres'>Deducible </th> 
                        <th class='columnasnombres'>Estado </th>
                    </tr>
        <?php
                    foreach ($interes_asegurado AS $row) {
                        echo "
                            <tr>
                                <td>".$row["numero"]."</td>
                                <td>".$row['nombre']."</td>
                                <td>".$row['detalle_certificado']."</td>
                                <td>".$row['detalle_suma_asegurada']."</td>
                                <td>".$row['detalle_prima']."</td>
                                <td>".$row['detalle_deducible']."</td>
                                <td>".$row['estado']."</td>
                            </tr>
                        ";
                    }    
                }elseif($datos->id_tipo_int_asegurado == 8){?>
                    <tr class="text-center">
                        <th colspan="12">Datos del vehículo</th>
                    </tr>
                    <tr>
                        <th class='columnasnombres'>No. Interés </th>
                        <th class='columnasnombres'>Chasis</th> 
                        <th class='columnasnombres'>No. Certificado </th> 
                        <th class='columnasnombres'>Suma asegurada </th> 
                        <th class='columnasnombres'>Prima neta </th> 
                        <th class='columnasnombres'>Deducible </th> 
                        <th class='columnasnombres'>Estado </th>
                    </tr>
       <?php
                    foreach ($interes_asegurado AS $row) {
                        echo "
                            <tr>
                                <td>".$row["numero"]."</td>
                                <td>".$row['chasis']."</td>
                                <td>".$row['detalle_certificado']."</td>
                                <td>".$row['detalle_suma_asegurada']."</td>
                                <td>".$row['detalle_prima']."</td>
                                <td>".$row['detalle_deducible']."</td>
                                <td>".$row['estado']."</td>
                            </tr>
                        ";
                    }            
                }
            }else{
               if( $datos->id_tipo_int_asegurado == 5){ ?>
                    <tr class="text-center">
                        <th colspan="12">Datos de la persona</th>
                    </tr>
        <?php
                    $validar_persona = explode(" ",$datos->ramo);
                    if($validar_persona[1] == "vida" || $validar_persona[1] == "accidentes" || $validar_persona[1] == "accidente"){ ?>
                    <tr>
                        <th class='columnasnombres'>No. Interés </th>
                        <th class='columnasnombres'>Relación</th> 
                        <th class='columnasnombres'>No. Certificado </th> 
                        <th class='columnasnombres'>Suma asegurada </th> 
                        <th class='columnasnombres'>Participacion </th> 
                        <th class='columnasnombres'>Prima neta </th> 
                        <th class='columnasnombres'>Estado </th>
                    </tr>
        <?php
                    }else if($validar_persona[1] == "salud"){ ?>
                        <tr>
                            <th class='columnasnombres'>No. Interés </th>
                            <th class='columnasnombres'>Relación</th> 
                            <th class='columnasnombres'>No. Certificado </th> 
                            <th class='columnasnombres'>Beneficio vida </th> 
                            <th class='columnasnombres'>Monto </th> 
                            <th class='columnasnombres'>Prima neta </th> 
                            <th class='columnasnombres'>Estado </th>
                        </tr>
        <?php       }else{ ?>
                        <tr class="text-center">
                            <th colspan="12">Datos de la persona</th>
                        </tr>
                        <tr>
                            <th class='columnasnombres'>No. Interés </th>
                            <th class='columnasnombres'>Nombre completo</th> 
                            <th class='columnasnombres'>No. Certificado </th> 
                            <th class='columnasnombres'>Suma asegurada </th> 
                            <th class='columnasnombres'>Prima neta </th> 
                            <th class='columnasnombres'>Estado </th>
                        </tr>
        <?php
                    }
                    ?>            
        <?php
                    foreach ($interes_asegurado AS $row) {
                        if($validar_persona[1] == "vida" || $validar_persona[1] == "accidentes" || $validar_persona[1] == "accidente"){
                            echo "
                                <tr>
                                    <td>".$row["numero"]."</td>
                                    <td>".$row['detalle_relacion']."</td>
                                    <td>".$row['detalle_certificado']."</td>
                                    <td>".$row['detalle_suma_asegurada']."</td>
                                    <td>".$row['detalle_participacion']."</td>
                                    <td>".$row['detalle_prima']."</td>
                                    <td>".$row['estado']."</td>
                                </tr>
                            ";
                        }elseif($validar_persona[1] == "salud"){
                            echo "
                                <tr>
                                    <td>".$row["numero"]."</td>
                                    <td>".$row['detalle_relacion']."</td>
                                    <td>".$row['detalle_certificado']."</td>
                                    <td>".$row['detalle_beneficio']."</td>
                                    <td>".$row['detalle_moto']."</td>
                                    <td>".$row['detalle_prima']."</td>
                                    <td>".$row['estado']."</td>
                                </tr>
                            ";
                        }else{
                            echo "
                                <tr>
                                    <td>".$row["numero"]."</td>
                                    <td>".$row['nombrePersona']."</td>
                                    <td>".$row['detalle_certificado']."</td>
                                    <td>".$row['detalle_suma_asegurada']."</td>
                                    <td>".$row['detalle_prima']."</td>
                                    <td>".$row['estado']."</td>
                                </tr>
                            ";
                        }
                    } 
                }
            }
        ?>
    </table>

</div>


