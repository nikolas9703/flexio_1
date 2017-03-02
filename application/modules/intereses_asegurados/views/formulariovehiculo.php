<div class="row" style="margin-right:0px!important;">
    <div class="ibox-title border-bottom">
        <h5>Datos del vehículo</h5>
        <hr style="margin-top:30px!important;">
        <div id="vistaPersona">
            <div class="tab-content">
                <div id="datosdelPersona-5" class="tab-pane active">
                    <?php echo form_open_multipart(base_url('intereses_asegurados/guardar_vehiculo'), "id='vehiculo'"); 
					?>
                    <input type="hidden" name="campo[uuid]" id="uuid_vehiculo" class="form-control" value="<?php if (isset($campos['uuid'])) echo $campos['uuid'] ?>" />
                    <input type="hidden" name="campo[tipo_id]" id="tipo_id_vehiculo" class="tipo_id" value="8" />
                    <input type="hidden" name="campo[id]" id="id" class="tipo_id" value="<?php if (isset($campos['id'])) echo $campos['id'] ?>" />
                    <div class="ibox">
                        <div class="ibox-content m-b-sm" style="display: block; border:0px">
                            <div class="row">
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Motor<span required="" aria-required="true">*</span></label>
                                    <input type="text" data-rule-required="true" name="campo[motor]" id="motor" class="form-control" value="<?php if (isset($campos['datos']['motor'])) echo $campos['datos']['motor'] ?>">
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>N°. Unidad </label>
                                    <input type="text" name="campo[unidad]" id="unidad" class="form-control" value="<?php if (isset($campos['datos']['unidad'])) echo $campos['datos']['unidad'] ?>"/>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Marca</label>
                                    <input type="text" name="campo[marca]" id="marca" class="form-control marca_vehiculo" value="<?php if (isset($campos['datos']['marca'])) echo $campos['datos']['marca'] ?>">
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Modelo</label>
                                    <input type="text" name="campo[modelo]" id="modelo" class="form-control modelo_vehiculo" value="<?php if (isset($campos['datos']['modelo'])) echo $campos['datos']['modelo'] ?>">
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Placa</label>
                                    <input type="text" name="campo[placa]" id="placa" class="form-control" value="<?php if (isset($campos['datos']['placa'])) echo $campos['datos']['placa'] ?>">
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Año</label>
                                    <input type="text" name="campo[ano]" id="ano" class="form-control" value="<?php if (isset($campos['datos']['ano'])) echo $campos['datos']['ano'] ?>">
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>N°. Chasis o serie </label>
                                    <input type="text" name="campo[chasis]" id="chasis" class="form-control"  value="<?php if (isset($campos['datos']['chasis'])) echo $campos['datos']['chasis'] ?>" />                                    
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Color</label>
                                    <input type="text" name="campo[color]" id="color" class="form-control" value="<?php if (isset($campos['datos']['color'])) echo $campos['datos']['color'] ?>">
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Capacidad de personas</label>
                                    <input type="text" name="campo[capacidad]" id="capacidad" class="form-control" value="<?php if (isset($campos['datos']['capacidad'])) echo $campos['datos']['capacidad'] ?>">
                                </div>    
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Uso
                                    </label>
                                    <select class="form-control uso_vehiculo" name="campo[uso]" id="uso">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($uso as $u) { ?>
                                            <option value="<?php echo $u->id ?>" <?php if (isset($campos['datos']['uso'])) if ($campos['datos']['uso'] == $u->id) echo "selected" ?>><?php echo $u->etiqueta ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Condición</label>
                                    <select class="form-control condicion_vehiculo" name="campo[condicion]" id="condicion">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($condicion as $con) { ?>
                                            <option value="<?php echo $con->id ?>" <?php if (isset($campos['datos']['condicion'])) if ($campos['datos']['condicion'] == $con->id) echo "selected" ?>><?php echo $con->etiqueta ?></option>
                                        <?php } ?>
                                    </select>
                                </div>   
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Operador</label>
                                    <input type="text" name="campo[operador]" id="operador" value="<?php if (isset($campos['datos']['operador'])) echo $campos['datos']['operador'] ?>" class="form-control">
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Extras</label>
                                    <input type="text" name="campo[extras]" id="extras" class="form-control" value="<?php if (isset($campos['datos']['extras'])) echo $campos['datos']['extras'] ?>">
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Valor de los extras</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>   
                                        <input type="text" name="campo[valor_extras]" id="valor_extras" class="form-control" value="<?php if (isset($campos['datos']['valor_extras'])) echo $campos['datos']['valor_extras'] ?>"
                                               ></div>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Acreedor</label>
                                    <select class="form-control acreedor" name="campo[acreedor]" id="acreedor">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($acreedores as $acre) { ?>
                                            <option value="<?php echo $acre->id ?>" <?php if (isset($campos['datos']['acreedor'])) if ($campos['datos']['acreedor'] == $acre->id) echo "selected" ?>><?php echo $acre->nombre ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>% Asignado al acreedor</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">%</span>
                                        <input type="text" name="campo[porcentaje_acreedor]" id="porcentaje_acreedor" class="form-control porcentaje_vehiculo" value="<?php if (isset($campos['datos']['porcentaje_acreedor'])) echo $campos['datos']['porcentaje_acreedor'] ?>"></div>
                                </div>    
                            </div>
                            <div class="row" id="observa">
                                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-6 ">
                                    <label>Observaciones</label>
                                    <textarea name="campo[observaciones]" class="form-control" id="observaciones_vehiculo"><?php if (isset($campos['datos']['observaciones'])) echo $campos['datos']['observaciones'] ?></textarea>
                                    <label for="campo[observaciones]" generated="true" class="error"></label>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3 divestado" >
                                    <label>Estado </label>
                                    <select class="form-control estado" name="campo2[estado]">
                                        <?php
                                        $activo = "";
                                        $inactivo = "";
                                        if (isset($campos['estado']) && $campos['estado'] == "Activo")
                                            $activo = "selected";
                                        if (isset($campos['estado']) && $campos['estado'] == "Inactivo")
                                            $inactivo = "selected";
										
                                        if (isset($campos['estado'])) {
											
											if($campos['superadmin']==0)
											{
												if (count($campos['politicas_generales'] > 0)) {
													if ($campos['estado'] == "Activo") {
														if (((in_array(19, $campos['politicas']) === true) && (in_array(19, $campos['politicas_generales']) === true)) || ((in_array(19, $campos['politicas_generales']) === false)) ) {
															?>
															<option value='Activo' <?php echo $activo ?> >Activo</option>
															<option value='Inactivo' <?php echo $inactivo ?> >Inactivo</option>
															<?php
														} else {
															?>
															<option value='Activo' <?php echo $activo ?> >Activo</option>
															<?php
														}
													} else if ($campos['estado'] == "Inactivo") {
														
														if (((in_array(20, $campos['politicas']) === true) && (in_array(20, $campos['politicas_generales']) === true)) || ((in_array(20, $campos['politicas_generales']) === false)) ) {
															?>
															<option value='Activo' <?php echo $activo ?> >Activo</option>
															<option value='Inactivo' <?php echo $inactivo ?> >Inactivo</option>
															<?php
															}
															else
															{
															?>
														  <option value='Inactivo' <?php  echo $inactivo ?> >Inactivo</option>
															<?php
														}
														
													}
												} else {
													?>
													<option value='Activo' >Activo</option>
													<option value='Inactivo' >Inactivo</option>
													<?php
												}
											}
											else {
													?>
													<option value='Activo' >Activo</option>
													<option value='Inactivo' >Inactivo</option>
													<?php
												}
                                        } else {
                                            ?>
                                            <option value='Activo' <?php echo $activo ?> >Activo</option>
                                            <?php
                                        }
                                        ?>
                                    </select>                    
                                </div>
                                <?php
                                if (!isset($campos['datos']['uuid_vehiculo'])) {
                                    ?>
                                    <div class="row" id="doc_entregados">
                                        <div class=" col-xs-12 col-sm-12 col-md-12 col-lg-12 documentos_entregados" >
                                            <h5>Documentos entregados</h5><br><br>
                                            <label>Nombre del documento</label>
                                            <div class='file_upload_vehiculo' id='fvehiculo1'>
                                                <input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" /><input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/>
                                                <br><br>
                                            </div>
                                            <div id='file_tools_vehiculo' style="width: 90px!important; float: left;">
                                                <button type="button" class="btn btn-default btn-block" style="float: left; width: 40px; margin-right:5px;" id="add_file_vehiculo"><i class="fa fa-plus"></i>
                                                </button>
                                                <button type="button" style="float: left; width: 40px; margin-top:0px!important;" class="btn btn-default btn-block" id="del_file_vehiculo"><i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>

                                <div id="espac">
                                    <br><br>
                                </div>
                                <div class="row detalleinteres_vehiculo" style="display:none">
                                    <input type="hidden" name="detalleunico" value="<?php echo strtotime('now'); ?>"> 
                                    <div class="col-xs-12 col-sm-3 col-md-12 col-lg-12" >
                                        <h5>Detalle interés asegurado</h5>
                                        <br>
                                        <hr>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <label>No. Certificado</label>
                                            <div class="input-group">
                                                <input type="text" name="campodetalle[certificado]" id="certificadodetalle_vehiculo" class="form-control">
                                            </div>                                      
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <label>Suma asegurada<span required="" aria-required="true">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-addon">$</span>
                                                <input type="text" name="campodetalle[suma_asegurada]" id="sumaaseguradadetalle_vehiculo" class="form-control">
                                            </div>
                                        </div>                                  
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <label>Prima neta<span required="" aria-required="true">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-addon">$</span>
                                                <input type="text" name="campodetalle[prima_anual]" id="primadetalle_vehiculo" class="form-control">
                                            </div>                                      
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <label>Deducible</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">$</span>
                                                <input type="text" name="campodetalle[deducible]" id="deducibledetalle_vehiculo" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="campodesde[desde]" class="campodesde" value="">
                                <input type="hidden" name="campodesde[indcolec]" class="indcolec" value="">

                                <div class="row botones">
                                    <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                                    <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2" >
                                        <a onclick="window.history.back();" class="btn btn-default btn-block" id="cancelar">Cancelar </a>
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                                        <input type="submit" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block guardarVehiculo" id="campo[guardar]" />
                                    </div>
                                </div>

                                <div class="tabladetalle_vehiculo" style="display:none">
                                    <!-- JQGRID -->
                                    <?php echo modules::run('intereses_asegurados/ocultotablavehiculo', $campos); ?>
                                    <!-- /JQGRID -->
                                </div>

                            </div>
                        </div>
                        <?php echo form_close(); ?>
                    </div>



                </div>
            </div>
        </div> 
    </div>
</div>