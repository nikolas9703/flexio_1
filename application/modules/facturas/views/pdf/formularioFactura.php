<style type="text/css">
	*{
		margin:0;
		padding:0;
		font-size:12px;
	}
    .titulo1{
        font-weight:bolder;
        font-size: 15px;
        text-align: center;
    }
    .titulo2{
        font-weight:bolder;
        font-size: 15px;
        text-align: left;
    }
    .titulo1 td{
        font-size: 15px;
        text-align: center;
    }
    .columnasnombres{
           width: 30%;
           text-align: left !important;
    }
	.texto_rojo{
		color:red;
	}
	#tabla_prin{
		width: 100%;
		margin-left:50px !important;
		margin-right:50px !important;
		margin-top:2em;
		font-family:'calibri, sans-serif';
	}
	.u_line{
		text-decoration:underline;
        font-size: 12px;
	}
</style>
<div id="container">
	<table id="tabla_prin" >
		<!--seccion de cabecera-->
		<tr>
			<td width="50%"> <img id="logo" src="<?php $logo = !empty($datos->datosEmpresa->logo)?$datos->datosEmpresa->logo:'default.jpg'; echo $this->config->item('logo_path').$logo;?>" height="56.69px" alt="Logo" border="0" /></td>
			<td class="titulo1">
				<table width="100%">
					<tr>
						<td>FACTURA</td>
					</tr>
					<tr>
						<td>*** DOCUMENTO NO FISCAL ***</td>
					</tr>
					<tr>
						<td>No. de factura: <span class="texto_rojo"><?php echo $datos->codigo?></span></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan=2 class="titulo1"><br></td>
		</tr>
		<tr>
			<td>
			<table width="100%">
				<tr>
					<td><?php echo $datos->cliente->nombre; ?></td>
				</tr>
				<tr>
					<td>RUC: <?php echo $datos->cliente->identificacion; ?></td>
				</tr>
				<tr>
					<td>Direcci&oacute;n: <?php echo $datos->cliente->direccion; ?></td>
				</tr>
				<tr>
					<td>Tel&eacute;fono: <?php echo $datos->cliente->telefono; ?></td>
				</tr>
			</table>
			</td>
			<td>
			<table width="100%">
				<tr>
					<td>Fecha de emisi&oacute;n: <?php echo $datos->created_at; ?></td>
				</tr>
				<tr>
					<td>Pagar antes de: <?php echo $datos->fecha_hasta; ?></td>
				</tr>
				<tr>
					<td>Preparado por: <?php echo $datos->vendedor->nombre." ".$datos->vendedor->apellido; ?></td>
				</tr>
				<tr>
					<td><b>Centro: <?php echo $datos->centro->nombre; ?></td>
				</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td colspan=2 class="titulo1"><br><hr><br></td>
		</tr>
		<tr>
			<td>
				<table width="100%">
					<tr>
						<td class="titulo2 u_line">CLIENTE:</td>
					</tr>
					<tr>
						<td><?php echo strtoupper($datos->cliente->nombre); ?></td>
					</tr>
					<tr>
						<td>Nombre: <?php echo strtoupper($datos->centros_fac->nombre); ?></td>
					</tr>
					<tr>
						<td>Identificacion: <?php echo strtoupper($datos->cliente->identificacion); ?></td>
					</tr>
				</table>
			</td>
		
			<td>
				<table width="100%">
					<tr>
						<td class="titulo2 u_line">ENTREGAR EN:</td>
					</tr>
				</table>
			</td>
		
		</tr>

	</table>

</div>


