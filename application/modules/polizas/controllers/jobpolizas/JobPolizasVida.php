<?php
	
	$conex = mysql_connect('localhost','flexdevuser','F13xu5r2016.') or die('problemas en la conexion');
	mysql_select_db('flexiodesa2',$conex) or die('problemas con la base de datos');

	$datos_poliza = mysql_query("select pol_polizas.id, estado, inicio_vigencia, fin_vigencia, frecuencia_facturacion, poliza_declarativa, cantidad_pagos from pol_polizas inner join pol_poliza_prima prima on prima.id_poliza = pol_polizas.id where ramo like 'vida%'");
	
	mysql_query("update pol_polizas set centro_contable = 23 where id = 1");

	if(mysql_num_rows($datos_poliza) > 0){
		while($dato_poliza = mysql_fetch_array($datos_poliza)){
			
			$count = 0;
			if($dato_poliza['estado'] == 'Facturada'){

				$id_poliza = $dato_poliza['id'];
				$numero_facturas = $dato_poliza['cantidad_pagos'];

				$fecha_inicial = new DateTime($dato_poliza['inicio_vigencia']);
				$fecha_final = new DateTime($dato_poliza['fin_vigencia']);
				$fecha_final = $fecha_final->add(new DateInterval('P1D'));
				$intervalo = $fecha_inicial->diff($fecha_final);
				$anios_vigencia = $intervalo->format('%Y');

				$fecha_actual = date('Y-m-d');// '2018-02-01';
				$fecha_inicial = $dato_poliza['inicio_vigencia'];

				for($i = 1; $i <= $anios_vigencia; $i++){

					$fecha_final = date('Y-m-d', strtotime('+1 year', strtotime($fecha_inicial)));
					$fecha_final = date('Y-m-d', strtotime('-1 day', strtotime($fecha_final)));

					if( $fecha_actual >= $fecha_inicial && $fecha_actual <= $fecha_final){

						$vigencia = $i;
						$facturas = mysql_query('select * from fac_facturas where id_poliza ='.$id_poliza);
						$cantidad_facturas = mysql_num_rows($facturas);

						if(mysql_num_rows($facturas) > 0){

							$faturas_esperadas = $vigencia * $numero_facturas;
							if($cantidad_facturas < $faturas_esperadas ){

								while($dato_factura = mysql_fetch_array($facturas)){

									$id_poliza = $dato_factura['id_poliza'];

									$codigo_factura = mysql_query('select * from fac_facturas where empresa_id = '.$dato_factura['empresa_id']);

									$cont = mysql_num_rows($codigo_factura);
									$codigo = "INV".$dato_factura['empresa_id'].sprintf('%06d', $cont + 1);

									$centro_contable_id = $dato_factura['centro_contable_id'];
									$cliente_id = $dato_factura['cliente_id'];
									$created_by = $dato_factura['created_by'];
									$created_at = date('Y-m-d H:i:s');
									$updated_at = date('Y-m-d H:i:s');
									$empresa_id = $dato_factura['empresa_id'];

									if($count == 0){
										$estado = "por_cobrar";
									}else{
										$estado = "por_aprobar";
									}
									
									$fecha_desde = date('Y-m-d', strtotime('+1 year', strtotime($dato_factura['fecha_desde'])));
									$fecha_hasta = date('Y-m-d', strtotime('+1 year', strtotime($dato_factura['fecha_hasta'])));
									$termino_pago = $dato_factura['termino_pago'];
									$subtotal = $dato_factura['subtotal'];
									$otros = $dato_factura['otros'];
									$impuestos = $dato_factura['impuestos'];
									$total = $dato_factura['total'];
									$descuento = $dato_factura['descuento'];
									$formulario = $dato_factura['formulario'];
									$centro_facturacion_id = $dato_factura['centro_facturacion_id'];
									$saldo = $dato_factura['total'];


									$uuid = mysql_query('select ORDER_UUID(uuid())');
									while($uuid_factura = mysql_fetch_array($uuid)){
									
										mysql_query("insert fac_facturas (uuid_factura,id_poliza,codigo,centro_contable_id,cliente_id,created_by,created_at,updated_at,empresa_id,estado,fecha_desde,fecha_hasta,termino_pago,subtotal,otros,impuestos,total,descuento,formulario,centro_facturacion_id,saldo) values ('".$uuid_factura[0]."' , ".$id_poliza." , '".$codigo."' , ".$centro_contable_id." , ".$cliente_id." , ".$created_by." , '".$created_at."' , '".$updated_at."' , ".$empresa_id." , '".$estado."' , '".$fecha_desde."' , '".$fecha_hasta."' , '".$termino_pago."' , ".$subtotal." , ".$otros." , ".$impuestos ." , ".$total." , ".$descuento." , '".$formulario."' , ".$centro_facturacion_id." , ".$saldo." )");

									}
									$count++;
								}
							}
						}
					}
					$fecha_inicial = date('Y-m-d', strtotime('+1 day', strtotime($fecha_final)));
				}
			}
		}
	}

?>