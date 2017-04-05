<?php
	$conex = mysql_connect('localhost','flexdevuser','F13xu5r2016.') or die('problemas en la conexion');
	mysql_select_db('flexiodesa2',$conex) or die('problemas con la base de datos');

	$datos_facturas = mysql_query("Select fac_facturas.*, pol_polizas.numero as numero_polizas from fac_facturas inner join pol_polizas on fac_facturas.id_poliza = pol_polizas.id where fac_facturas.estado = 'por_aprobar' AND DATE(fac_facturas.fecha_desde) = CURDATE()");

	if(mysql_num_rows($datos_facturas) > 0){
		while($dato_factura = mysql_fetch_array($datos_facturas)){
			
			mysql_query("update fac_facturas set estado = 'por_cobrar' where id = ".$dato_factura['id']);
			mysql_query("insert into sys_transacciones (uuid_transaccion,codigo,nombre,created_at,updated_at,empresa_id,linkable_id,linkable_type) value (ORDER_UUID(uuid()),'Sys','TransaccionFactura-".$dato_factura['codigo']."-".$dato_factura['empresa_id']."',".date('-m-d-Y').",".date('m-d-Y').",".$dato_factura['empresa_id'].",".$dato_factura['id'].",'Flexio\\\Modulo\\\FacturasSeguros\\\Models\\\FacturaSeguro') ");
			
			$transaccionable = mysql_insert_id();
			mysql_query("insert into contab_transacciones (codigo,uuid_transaccion,nombre,debito,credito,empresa_id,created_at,updated_at,cuenta_id,transaccionable_id,transaccionable_type) value ('".$dato_factura['codigo']."',ORDER_UUID(uuid()),'".$dato_factura['codigo']."-".$dato_factura['numero_polizas']."',".$dato_factura['total'].",0.00,".$dato_factura['empresa_id'].",".date('d-m-Y').",".date('d-m-Y').",".$dato_factura['cuenta'].",".$transaccionable.",'Flexio\\\Modulo\\\Transaccion\\\Models\\\SysTransaccion')");
			mysql_query('update fac_facturas set bodega_id = 23 where id = 893');
		}
	}
?>
