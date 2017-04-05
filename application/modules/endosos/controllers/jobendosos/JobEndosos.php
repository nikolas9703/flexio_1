<?php
	
	$conex = mysql_connect('localhost','flexdevuser','F13xu5r2016.') or die('problemas en la conexion');
	mysql_select_db('flexiodesa2',$conex) or die('problemas con la base de datos');

	$fecha_actual = date('Y-m-d');

	$datos_endosos = mysql_query("select * from end_endosos");
	if(mysql_num_rows($datos_endosos) > 0){
		while($endoso = mysql_fetch_array($datos_endosos)){
			if($endoso['estado'] == "Aprobado" && $endoso['tipo'] == utf8_decode("Cancelación") ){
				if($endoso['fecha_efectividad'] == $fecha_actual){
					mysql_query('update pol_polizas set estado = "Cancelada" where id ='.$endoso['id_poliza']);
					$datos_facturas = mysql_query('select * from fac_facturas where id_poliza = '.$endoso['id_poliza']);
					if(mysql_num_rows($datos_facturas) > 0){
						while($factura = mysql_fetch_array($datos_facturas)){
							$fecha_desde_factura = date('Y-m-d', strtotime($factura['fecha_desde']));
							if( ($factura['estado'] == 'por_cobrar' || $factura['estado'] == 'por_aprobar') && ( $fecha_desde_factura >= $endoso['fecha_efectividad'] ) ){
								mysql_query('update fac_facturas set estado = "anulada" where id ='.$factura['id']);
								echo $fecha_desde_factura."<br>";
							}
						}
					}	
				}
			}


			if($endoso['estado'] == "Aprobado" && $endoso['fecha_efectividad'] == $fecha_actual){
                $fecha_creado = date('Y-m-d H:i:s');
                $comentario = utf8_decode("N° endoso: ".$endoso['endoso']."<br>Estado: ".$endoso['estado']."<br>Fecha efectividad: ".date('d-m-y',strtotime($endoso['fecha_efectividad']))."<br>Motivo: Se cumplio la fecha de efectividad");
                mysql_query("insert into pol_polizas_bitacora (comentario,comentable_id,comentable_type,usuario_id,created_at,updated_at,empresa_id) values ('".$comentario."',".$endoso['id_poliza'].",'Endosos_seguros',".$endoso['usuario'].",NOW(),NOW(),".$endoso['empresa_id'].")");
            }
		}
	}
?>