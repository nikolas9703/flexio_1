<div class="ibox-content m-b-sm" style="display: block; border:0px">
	<div class="row">
            
		<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <label>Fecha <span aria-required="true" required="">*</span></label>
                    <input aria-required="true" name="campo[fecha]" value="<?php echo (isset($campos["fecha"])) ? $campos["fecha"] : ""?>" class="form-control" disabled="" data-rule-required="true" id="campo[fecha]" type="text">
                </div>
            
		<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
			<label>Destino <span aria-required="true" required="">*</span></label>
			<select aria-required="true" style="display: none;" name="campo[destino]" class="chosen destino" id="destino" data-rule-required="true" disabled="">
				<option value="">Seleccione</option>
			</select>
		</div>
            
		<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
			<label>Bodega de salida <span aria-required="true" required="">*</span></label>
			<select aria-required="true" style="display: none;" disabled="" name="campo[bodega_salida]" class="chosen" id="bodega_salida" data-rule-required="true">
				<option value="">Seleccione</option>
                                <?php foreach($bodegas as $bodega):?>
                                <option value="<?php echo $bodega->uuid_bodega?>" <?php echo (isset($campos["bodega_salida"]) and $campos["bodega_salida"] == $bodega->uuid_bodega)?'selected':''?>><?php echo $bodega->nombre_codigo?></option>
                                <?php endforeach;?>
			</select>
		</div>
            
		<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
			<label>Estado  <span aria-required="true" required="">*</span></label>
			<select aria-required="true" style="display: none;" name="campo[estado]" class="chosen estado" id="estado" data-rule-required="true">
				<option value="">Seleccione</option>
				<?php foreach($estados as $estado):?>
                                <option value="<?php echo $estado->id_cat?>" <?php echo (isset($campos["estado"]) and $campos["estado"] == $estado->id_cat)?'selected':''?>><?php echo $estado->etiqueta?></option>
                                <?php endforeach;?>
			</select>
		</div>
            
		<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                    <label>Número de documento </label>
                    <input name="campo[numero_documento]" value="<?php echo (isset($campos["numero_documento"])) ? $campos["numero_documento"] : ""?>" class="form-control" disabled="" id="campo[numero_documento]" type="text">
                </div>
            
		<div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <label>Comentarios </label>
                    <input name="campo[comentarios]" value="<?php echo (isset($campos["comentarios"])) ? $campos["comentarios"] : ""?>" class="form-control comentarios" id="campo[comentarios]" type="text">
                </div>
	</div>
    
	<div class="row">
		<div class="col-lg-12">
			<div class="table-responsive">
				<table style="display: block;" id="itemsTable" class="table table-noline tabla-dinamica">
					<thead>
						<tr>
                                                        <th class="item " width="15.833333333333%" colspan="2">Item <span aria-required="true" class="required">*</span></th>
							<th class="descripcion " width="15.833333333333%">Descripción <span aria-required="true" class="required">*</span></th>
							<th class="observacion " width="15.833333333333%">Observación <span aria-required="true" class="required">*</span></th>
							<th class="cuenta " width="15.833333333333%">Cuenta de gasto <span aria-required="true" class="required">*</span></th>
							<th class="cantidad_enviada " width="15.833333333333%">Cantidad enviada <span aria-required="true" class="required">*</span></th>
							<th class="unidad " width="15.833333333333%">Unidad <span aria-required="true" class="required">*</span></th>
						</tr>
					</thead>
					<tbody>
                                                <?php foreach ($campos["items"] as $i => $fila):?>
                                                <?php $serializable = $fila["tipo_id"] == "5" || $fila["tipo_id"] == "8";?>
						<tr id="items<?php echo $i;?>">
							<?php if($serializable):?>
                                                                <td style="width: 1%;">
                                                                    <i class="fa fa-caret-right" style="font-size: 28px;"></i>
                                                                    <i class="fa fa-caret-down hide" style="font-size: 28px;"></i>
                                                                </td>
                                                                <?php endif;?>
                                                                <td class="item<?php echo $i;?>" <?php echo (!$serializable)?' colspan="2" ':''?>>
								<select style="display: none;" disabled="" name="items[<?php echo $i;?>][item]" class="chosen item" id="item<?php echo $i;?>">
									<option value="">Seleccione</option>
									<?php foreach($items as $item):?>
                                                                        <option value="<?php echo $item->uuid_item?>" <?php echo ($fila["item"] == $item->uuid_item)?'selected':''?>><?php echo $item->nombre_completo?></option>
                                                                        <?php endforeach;?>
								</select>
							</td>
							<td class="descripcion<?php echo $i;?> ">
                                                            <input aria-required="true" name="items[<?php echo $i;?>][descripcion]" value="<?php echo $fila["descripcion"]?>" class="form-control descripcion" disabled="" data-rule-required="true" id="descripcion<?php echo $i;?>" type="text">
                                                        </td>
							<td class="observacion<?php echo $i;?> ">
                                                            <input aria-required="true" name="items[<?php echo $i;?>][observacion]" value="<?php echo $fila["observacion"]?>" class="form-control observacion" disabled="" data-rule-required="true" id="observacion<?php echo $i;?>" type="text">
                                                        </td>
							<td class="cuenta<?php echo $i;?> ">
								<select style="display: none;" disabled="" name="items[<?php echo $i;?>][cuenta]" class="chosen cuenta" id="cuenta<?php echo $i;?>">
									<option value="">Seleccione</option>
									<?php foreach($cuentas as $cuenta):?>
                                                                        <option value="<?php echo $cuenta->uuid_cuenta?>" <?php echo ($fila["cuenta"] == $cuenta->uuid_cuenta)?'selected':''?>><?php echo $cuenta->nombre?></option>
                                                                        <?php endforeach;?>
								</select>
							</td>
							<td class="cantidad_enviada<?php echo $i;?> ">
                                                            <input aria-required="true" name="items[<?php echo $i;?>][cantidad_enviada]" value="<?php echo $fila["cantidad_enviada"]?>" class="form-control cantidad_enviada" disabled="" data-rule-required="true" id="cantidad_enviada<?php echo $i;?>" type="text">
                                                        </td>
							<td class="unidad<?php echo $i;?> ">
								<select style="display: none;" disabled="" name="items[<?php echo $i;?>][unidad]" class="chosen unidad" id="unidad<?php echo $i;?>">
									<option value="">Seleccione</option>
									<?php foreach($fila["unidades"] as $unidad):?>
                                                                        <option value="<?php echo $unidad->uuid_unidad?>" <?php echo ($fila["unidad"] == $unidad->uuid_unidad)?'selected':''?>><?php echo $unidad->nombre?></option>
                                                                        <?php endforeach;?>
								</select>
							</td>
                                                        <td class="hide">
                                                            <input name="items[<?php echo $i;?>][id_entrada_item]" value="<?php echo $fila["id_entrada_item"];?>" type="hidden">
                                                        </td>
						</tr>
                                                <?php if($serializable):?>
                                                    <tr id="items<?php echo $i;?>Seriales" class="hide">
                                                        <td colspan="7">
                                                            <table style="width: 100%;background-color: #A2C0DA">
                                                                <?php 
                                                                    $seriales   = $fila["cantidad_enviada"];
                                                                    $filas      = $seriales/5;
                                                                    $contador2  = 0;
                                                                ?>
                                                                <?php for($filas; $filas > 0; $filas--):?>
                                                                <tr>
                                                                    <?php $contador = 0;?>
                                                                    <?php for($seriales; $seriales > 0; $seriales--):?>
                                                                    <td style="padding: 15px !important">
                                                                        <input type="text" class="form-control" name="items[<?php echo $i;?>][seriales][]" value="<?php echo isset($fila["seriales"][$contador2]) ? $fila["seriales"][$contador2]["nombre"] : '';?>">
                                                                    </td>
                                                                    <?php
                                                                        $contador++;
                                                                        $contador2++;
                                                                        if($contador == 5){
                                                                            $seriales--;
                                                                            break;
                                                                        }
                                                                    ?>
                                                                    <?php endfor;?>
                                                                </tr>
                                                                <?php endfor;?>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <?php endif;?>
                                                <?php endforeach;?>
					</tbody>
				</table>
				
			</div>
		</div>
	</div>
	<div class="row"></div>
	<div class="row">
		<div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2"><a href="<?php echo base_url("salidas/listar")?>" class="btn btn-default btn-block" id="cancelarEntrada">Cancelar </a> </div>
		<div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                    <input name="campo[guardarEntrada]" value="Guardar " class="btn btn-primary btn-block btnGuardar" id="campo[guardarEntrada]" type="submit">
                    <input type="hidden" name="campo[salida_id]" value="<?php echo $campos["salida_id"]?>">
                </div>
	</div>
</div>
