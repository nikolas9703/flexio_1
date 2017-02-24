<style type="text/css">

    .titulo1{
        font-weight:bold;
        font-size: 18px;
        text-align: center;
    }

    .titulo1_1{
        font-weight:bold;
        font-size: 16px;
        text-align: center;
    }

    .titulo2{
        font-weight:bold;
        text-decoration: underline;
        font-size: 11px;
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

    .recuadros{
        border: 1px solid black;
        height: 100px;
        vertical-align: top;
        padding: 4px;
    }

	.titulo4{
        font-weight:bold;
        font-size: 16px;
    }

	.titulo5{
        font-size: 11px;
        padding-top: 10px;
    }

</style>

    <?php

        use Flexio\Modulo\Inventarios\Repository\CategoriasRepository;
        use Flexio\Modulo\Inventarios\Repository\UnidadesRepository;
        use Flexio\Modulo\Contabilidad\Repository\ImpuestosRepository;
		use Flexio\Modulo\Contabilidad\Repository\CuentasRepository;

        $CategoriasRepository = new CategoriasRepository();
        $UnidadesRepository   = new UnidadesRepository();
        $ImpuestosRepository  = new ImpuestosRepository();
		$CuentasRepository  = new CuentasRepository();

		//$CI =& get_instance();

        //$CI->load->library(array('hashgenerator'));
		//$hashgenerator = new Hashgenerator();
		$sum_art = 0;
 		//$nombre_qr = $hashgenerator->generar_qr();​

		//$nombre_qr = '166.78.244.188/prueba/flexio/verified_by_flexio/generar_qr';
		//echo $nombre_qr;

		//dd($orden_compra->empresa);
		//dd($orden_compra->proveedor);
		//dd($orden_compra);
		//dd($orden_compra->bodega);

		//$logo = !empty($orden_compra->empresa->logo)?$orden_compra->empresa->logo:'default.jpg'; echo $this->config->item('logo_path').$logo;
		//dd($coleccion["articulos"]);
    ?>
<div id="container">
  <table style="width: 100%;">
        <!--seccion de cabecera-->
        <tr>
            <td rowspan="3">&nbsp;</td>
            <td class="titulo1">ORDEN DE COMPRA</td>
        </tr>
        <tr>
            <td class="titulo1_1"><span class="titulo1">No. de Orden: <?php echo $orden_compra->numero_documento;?></span></td>
        </tr>
        <tr>
            <td class="titulo1"><br><br></td>
        </tr>

        <!--datos de la empresa-->
        <tr>
            <td><?php echo strtoupper($orden_compra->empresa->nombre);?></td>
            <td>Fecha de emisi&oacute;n: <?php echo $orden_compra->fecha_creacion;?></td>
        </tr>
        <tr>
            <td><?php echo strtoupper($orden_compra->empresa->ruc);?></td>
            <td>Fecha de esta impresi&oacute;n: <?php echo date('d-m-Y', time())?></td>
        </tr>
        <tr>
            <td><?php echo strtoupper($orden_compra->empresa->descripcion);?></td>
            <td>Preparado por: <?php echo $orden_compra->comprador->nombre.' '.$orden_compra->comprador->apellido?></td>
        </tr>
        <tr>
            <td><?php echo $orden_compra->empresa->telefono?></td>
            <td>Referencia: <?php echo $orden_compra->referencia?></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td class="titulo4">Centro: <?=$centro_contable->nombre;?></td>
        </tr>
        <!--division-->
        <tr>
            <td colspan="2" style="border-bottom: 1px solid black;"></td>
        </tr>

        <!--datos del proveedor-->
        <tr>
            <td class="titulo2">ORDEN DE COMPRA PARA:</td>
            <td class="titulo2">ENTREGAR EN:</td>
        </tr>
        <tr>
            <td><?php echo $orden_compra->proveedor->nombre;?></td>
            <td><?php echo $orden_compra->bodega->nombre;?></td>
        </tr>
        <tr>
            <td><?php echo $orden_compra->proveedor->direccion;?></td>
            <td><?php echo $orden_compra->bodega->direccion?></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><?php echo $orden_compra->bodega->telefono?></td>
        </tr>

        <!--tabla de items-->
        <tr>
            <td colspan="2">

                <table style="width: 100%;" class="tabla_items">
                    <thead>
                        <tr class="titulo2">
                          	<th>Req. No</th>
                            <th>Categor&iacute;a</th>
                            <th>Descripci&oacute;n</th>
                            <th>Atributos</th>
                            <th>Cta. Costo/Gasto</th>
                            <th>Cant.</th>
                            <th>Unidad</th>
                            <th>Precio unitario</th>
                            <th>Descuento</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $aux_subtotal_gnral = 0;?>
                        <?php $aux_descuento_gnral = 0;?>
                        <?php $aux_impuesto_gnral = 0;?>
                        <?php
							$i = 0;
							//dd($coleccion);
							//$coleccion = $this->ordenesCompraRep->getColletionCampos($orden_compra);
							// $data   = ['orden_compra'=>$orden_compra, 'centro_contable'=>$centro_contable, 'coleccion'=>$coleccion];
							foreach($orden_compra->items as $item):?>
                        <?php

						?>
                        <tr class="titulo5">
                          <td><?php $num_pedido = !isset($orden_compra->pedido->numero)?'':'PD'.$orden_compra->pedido->numero; echo $num_pedido?></td>
                            <?php
                                $aux_subtotal       = $item->pivot->cantidad * $item->pivot->precio_unidad;
                                $descuento_row = (($aux_subtotal * $item->pivot->descuento)/100);
                                $aux_subtotal_dos   = ($aux_subtotal) - $descuento_row;
                                $aux_subtotal_gnral+= $aux_subtotal;
                                $aux_descuento_gnral+= $descuento_row;
                                $aux_impuesto_gnral+= ($aux_subtotal_dos * $ImpuestosRepository->find($item->pivot->impuesto_id)->impuesto)/100;
                            ?>
                            <td><?php echo $CategoriasRepository->find($item->pivot->categoria_id)->nombre;?></td>
                            <td><?php echo $item->codigo.' - '.$item->nombre;?></td>
                            <td>
                							<?php
                							 	$atributo = !empty($coleccion["articulos"][$i]) && !empty($coleccion["articulos"][$i]["atributo_text"]) ? $coleccion["articulos"][$i]["atributo_text"] : (!empty($coleccion["articulos"][$i]) && !empty($coleccion["articulos"][$i]["nombre"]) ? $coleccion["articulos"][$i]["atributos"][0]["nombre"] : "");
                                echo $atributo;
                							?>
                            </td>
                            <td><?php echo $CuentasRepository->find($item->pivot->cuenta_id)->nombre;?></td>
                            <td class="numero">
								<?php
									echo $item->pivot->cantidad;
									$art_by_line = $item->pivot->cantidad;
									$sum_art += $art_by_line;
								?>
                            </td>
                            <td style="text-align: center;">
                              <?php
                              /*$unidad_info = $UnidadesRepository->find($item->pivot->unidad_id);
                              echo !empty($unidad_info->toArray()) ? $unidad_info->nombre : "";*/
                              $unidad_info = $UnidadesRepository->find($item->pivot->unidad_id);
                               
                              if(count($unidad_info)>0)
                                echo !empty($unidad_info->toArray()) ? $unidad_info->nombre : "";
                              else
                                 echo "";
                              ?>
                            </td>
                            <td class="numero"><?php echo $item->pivot->precio_unidad;?></td>
                            <td class="numero"><?php echo $item->pivot->descuento;?>%</td>
                            <td class="numero"><?php echo number_format($aux_subtotal,2);?></td>
                        </tr>

                        <?php
							$i++;
							endforeach;
						?>
                    </tbody>
                </table>
            </td>
        </tr>

        <!--pie de tabla de items-->


        <!--division-->

  </table>
    <!-- ************************************************************************************************************************************** -->
    <table style="width: 100%;">
    <tr>
        <td width="37%">&nbsp;</td>
        <?php
			$cambio = str_replace ("system/","",BASEPATH);
			$nombre_qr = modules::run('verified_by_flexio/generar_qr',$orden_compra->numero_documento,$orden_compra->fecha_creacion,$sum_art,number_format(($aux_subtotal_gnral - $aux_descuento_gnral +  $aux_impuesto_gnral),2));
			$nombre4 = $cambio.'public/uploads/tmp_qr_codes/'.$nombre_qr;
		?>
        <td width="13%" rowspan="4">&nbsp;</td>
        <td width="31%">Subtotal:</td>
        <td width="19%" class="numero">$<?php echo number_format($aux_subtotal_gnral,2);?></td>
    </tr>
        <tr>
        <td>&nbsp;</td>
        <td>Descuento:</td>
        <td class="numero">$<?php echo number_format($aux_descuento_gnral,2);?></td>
    </tr>
        <tr>
        <td>&nbsp;</td>
        <td>Impuesto:</td>
        <td class="numero">$<?php echo number_format($aux_impuesto_gnral,2)?></td>
    </tr>
        <tr>
        <td>&nbsp;</td>
        <td nowrap>Valor Total de la orden:</td>
        <td class="numero">$<?php echo number_format(($aux_subtotal_gnral - $aux_descuento_gnral +  $aux_impuesto_gnral),2);?></td>
    </tr>
    <tr>
        <td><!--<a href="www.flexio.com/verified_by_flexio">www.flexio.com/verified_by_flexio</a>--></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
    	<td colspan="4" style="border-bottom: 1px solid black;"></td>
    </tr>
    </table>
     <!-- ************************************************************************************************************************************** -->
     <table style="width: 100%;">
     <tr>
     	<td class="titulo4">Autorizado por:</td>
        <td class="titulo4">Observaciones:</td>
     </tr>
     <tr>
       <td><?php echo (!empty($orden_compra->aprobadopor) ? $orden_compra->aprobadopor->nombre : "") .' '. (!empty($orden_compra->aprobadopor) ? $orden_compra->aprobadopor->apellido : "") ?></td>
         <td><?php echo $orden_compra->observaciones;?></td>
     </tr>
     <tr>
     	<td><?php /*echo date('d-m-Y    H:i a', time())*/?></td>
     	<td>&nbsp;</td>
     </tr>
     <!--division-->
    <tr>
        <td colspan="2" style="border-bottom: 1px solid black;"></td>
    </tr>
    <tr>
        <td colspan="2" align="center" class="titulo4"><br>ESTA ORDEN DE COMPRA TIENE UNA VALIDEZ DE 30 DIAS A PARTIR DE SU EMISION</td>
    </tr>
     </table>
</div>
