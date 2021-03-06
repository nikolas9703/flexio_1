<div id="vistaCliente" class="">
	<div class="tab-content">
		<div id="datosdelaaseguradora-5" class="col-lg-12">
			<input type="hidden" name="campo[uuid]" id="campo[uuid]" value="<?php echo $campos['uuid_comision']?>" />
			<div class="ibox">  
				<div class="ibox-title">
                        <h5>Detalle de la comisión</h5>
                </div>
				<div class="ibox-content " style="display: block;" id="datosGenerales" >
					<div class="row">
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2  ">
							<label>N. Póliza</label>
							<input type="text" name="campo[no_poliza]" class="form-control nombre" id="campo[no_poliza]" data-rule-required="true" value="<?php echo $campos['no_poliza']?>" disabled />
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3" >
							<label>Cliente</label>
							<input type="text" name="campo[cliente]" class="form-control" id="campo[cliente]" value="<?php echo $campos['cliente']?>" disabled />
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2" >
							<label>Ramo</label>
							<input type="text" name="campo[ramo]" class="form-control" id="campo[ramo]" value="<?php echo $campos['ramo']?>" disabled />
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3" >
							<label>Aseguradora</label>
							<input type="text" name="campo[aseguradora]" class="form-control" id="campo[aseguradora]" value="<?php echo $campos['aseguradora']?>" disabled />
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2" >
							<label>N. Factura</label>
							<input type="text" name="campo[no_factura]" class="form-control" id="campo[tomo]" value="<?php echo $campos['no_factura']?>" disabled />
						</div>
					</div>
					<div class="row">
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2  ">
							<label>N. Recibo</label>
							<input type="text" name="campo[no_recibo]" class="form-control nombre" id="campo[no_recibo]" data-rule-required="true" value="<?php echo $campos['no_recibo']?>" disabled />
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
							<label>Fecha </label>
							<input type="text" name="campo[fecha]" class="form-control" id="campo[fecha]" value="<?php echo $campos['fecha']?>" disabled />
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2 ">
							<label>Monto del recibo </label>
							<div class="input-group">
								<span class="input-group-addon" style='background-color: #eeeeee;'>$</span>
								<input type="input-left-addon" name="campo[monto_recibo]" class="form-control" id="campo[monto_recibo]" value="<?php echo $campos['monto_recibo']?>" disabled />
							</div>
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
							<label>Impuesto </label>
							<div class="input-group">
								<span class="input-group-addon" style='background-color: #eeeeee;'>$</span>
								<input type="input-left-addon" name="campo[impuesto_pago]" class="form-control" id="campo[impuesto_pago]" value="<?php echo $campos['impuesto_pago']?>" disabled />
							</div>
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2 ">
							<label>Pago a prima </label>
							<div class="input-group">
								<span class="input-group-addon" style='background-color: #eeeeee;'>$</span>
								<input type="input-left-addon" name="campo[pago_sobre_prima]" class="form-control" id="campo[pago_sobre_prima]" value="<?php echo $campos['pago_sobre_prima']?>" disabled />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2" >
							<label>Comisión %</label>
							<div class="input-group">
								<input type="input-left-addon" name="campo[comision]" class="form-control" id="campo[comision]" value="<?php echo $campos['comision']?>" disabled />
								<span class="input-group-addon" style='background-color: #eeeeee;'>%</span>
							</div>
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3  ">
							<label>Monto de comisión</label>
							<div class="input-group">
								<span class="input-group-addon" style='background-color: #eeeeee;'>$</span>
								<input type="input-left-addon" name="campo[monto_comision]" class="form-control" id="campo[monto_comision]" value="<?php echo $campos['monto_comision']?>" disabled />
							</div>
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2" >
							<label>Sobre comisión %</label>
							<div class="input-group">
								<input type="input-left-addon" name="campo[sobre_comision]" class="form-control" id="campo[sobre_comision]" value="<?php echo $campos['sobre_comision']?>" disabled />
								<span class="input-group-addon" style='background-color: #eeeeee;'>%</span>
							</div>
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3  ">
							<label>Monto de sobre comisión</label>
							<div class="input-group">
								<span class="input-group-addon" style='background-color: #eeeeee;'>$</span>
								<input type="input-left-addon" name="campo[monto_scomision]" class="form-control" id="campo[monto_scomision]" value="<?php echo $campos['monto_scomision']?>" disabled />
							</div>
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2" >
							<label>Comisión a recibir</label>
							<div class="input-group">
								<span class="input-group-addon" style='background-color: #eeeeee;'>$</span>
								<input type="input-left-addon" name="campo[comision_recibir]" class="form-control" id="campo[comision_recibir]" value="<?php echo $campos['comision_recibir']?>" disabled />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2" >
							<label>Comisión descontada</label>
							<div class="input-group">
								<span class="input-group-addon" style='background-color: #eeeeee;'>$</span>
								<input type="input-left-addon" name="campo[comision_descontada]" class="form-control" id="campo[comision_descontada]" value="<?php echo $campos['comision_descontada'] + $campos['monto_scomision'] ?>" disabled />
							</div>
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3" >
							<label>N. Remesa entrante</label>
							<input type="text" name="campo[no_remesa]" class="form-control" id="campo[no_remesa]" value="<?php echo $campos['no_remesa']?>" disabled />
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2" >
							<label>Fecha liquidación remesa</label>
							<input type="text" name="campo[fecha_liquidacion]" class="form-control" id="campo[fecha_liquidacion]" value="<?php if($campos['fecha_liquidacion']!="") echo date_format(date_create($campos['fecha_liquidacion']),'Y-m-d') ?> " disabled />
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3" >
							<label>Comisión pagada</label>
							<div class="input-group">
								<span class="input-group-addon" style='background-color: #eeeeee;'>$</span>
								<input type="input-left-addon" name="campo[comision_pagada]" class="form-control" id="campo[comision_pagada]" value="<?php echo number_format($campos['comision_pagada'],2)?>" disabled />
							</div>
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2" >
							<label>Comisión pendiente</label>
							<div class="input-group">
								<span class="input-group-addon" style='background-color: #eeeeee;'>$</span>
								<input type="input-left-addon" name="campo[comision_pendiente]" class="form-control" id="campo[comision_pendiente]" value="<?php echo $campos['comision_pendiente']?>" disabled />
							</div>
						</div>
					</div>
					
				</div>
				<?php
				if(count($campos['participacioncomision']) >0)
				{
				?>
				
				<?php
				}
				?>
				
				<div class="ibox-title">
                        <h5>Detalle de la participación</h5>
                </div>
				
				<div class="ibox-content " style="display: block;" id="datosGenerales2" >
					<div class="row">
					<?php
					foreach($campos['participacioncomision'] as $participacion)
					{
					?>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-4" >
							<label>Agente</label>
							<input type="text" name="campo[agente]" class="form-control" id="campo[agente]" value="<?php echo $participacion->datosAgente->nombre?>" disabled />
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2" >
							<label>% Participación</label>
							<div class="input-group">
								<input type="input-left-addon" name="campo[porcentaje]" class="form-control" id="campo[porcentaje]" value="<?php echo $participacion->porcentaje?>" disabled />
								<span class="input-group-addon" style='background-color: #eeeeee;'>%</span>
							</div>
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2" >
							<label>Monto</label>
							<div class="input-group">
								<span class="input-group-addon" style='background-color: #eeeeee;'>$</span>
								<input type="input-left-addon" name="campo[monto]" class="form-control" id="campo[monto]" value="<?php echo number_format($participacion->monto,2)?>" disabled />
							</div>
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2" >
							<label>N. Recibo de pago</label>
							<input type="text" name="campo[recibo]" class="form-control" id="campo[recibo]" value="<?php if($participacion->no_recibo != ''){echo $participacion->datosPago->codigo;}else{echo '';} ?>" disabled />
						</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2" >
							<label>Fecha de pago</label>
							<input type="text" name="campo[created_at]" class="form-control" id="campo[created_at]" value="<?php if($participacion->no_recibo != ''){echo $participacion->datosPago->fecha_pago;}else{echo '';} /*if($participacion->fecha_pago!="") date('d-m-Y', $participacion->datosPago->fecha_pago); date_format(date_create($participacion->datosPago->fecha_pago),'Y-m-d')*/ ?>" disabled />
						</div>
					<?php
					}
					?>
					</div>
				</div>
				<div class="ibox-content " style="display: block; border-style: none none none !important;" id="datosGenerales3" >
					<div class="row">
						<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-2" >
							<label>Estado</label>
							<input type="input-left-addon" name="campo[estado]" class="form-control" id="campo[estado]" value="<?php echo $campos['estado']?>" disabled />
						</div>
					</div>
					<div class="row">
						<div class="col-xs-0 col-sm-0 col-md-8 col-lg-10">&nbsp;</div>
						<div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
							<a href="<?php echo base_url('comisiones_seguros/listar'); ?>" class="btn btn-default btn-block" id="cerrar">Cerrar </a>
						</div>
					</div>
				</div>
			</div>
		</div>           
	</div>
</div>

