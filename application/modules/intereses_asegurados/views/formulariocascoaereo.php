<div class="row" style="margin-right:0px!important;">
    <div class="ibox-title border-bottom">
        <h5>Datos del casco aéreo</h5>
        <hr style="margin-top:30px!important;">
        <div id="vistaPersona">
            <div class="tab-content">
                <div id="datosdelPersona-5" class="tab-pane active">
                    <?php echo form_open_multipart(base_url('intereses_asegurados/guardar_aereo'), "id='formcasco_aereo'"); ?>
                    <input type="hidden" name="campo[uuid]" id="campo[uuid]" class="uuid_aereo" value="<?php if (isset($campos['uuid'])) echo $campos['uuid'] ?>">
                    <input type="hidden" name="campo[tipo_id]" id="tipo_id_aereo" class="tipo_id" value="3">
                    <div class="ibox">
                        <div class="ibox-content m-b-sm" style="display: block; border:0px">
                            <div class="row">
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>N°. de serie del casco
                                        <span required="" aria-required="true">*</span></label>
                                    <input type="text" name="campo[serie]" id="serie_aereo" value="<?php if (isset($campos['datos']['serie'])) echo $campos['datos']['serie'] ?>" class="form-control" data-rule-required="true">
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Marca
                                    </label>
                                    <input type="text" name="campo[marca_aereo]" id="marca_aereo" value="<?php if (isset($campos['datos']['marca'])) echo $campos['datos']['marca'] ?>" class="form-control" >
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Modelo
                                    </label>
                                    <input type="text" name="campo[modelo_aereo]" id="modelo_aereo" value="<?php if (isset($campos['datos']['modelo'])) echo $campos['datos']['modelo'] ?>" class="form-control">
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Matrícula
                                    </label>
                                    <input type="text" name="campo[matricula_aereo]" id="matricula_aereo" value="<?php if (isset($campos['datos']['matricula'])) echo $campos['datos']['matricula'] ?>" class="form-control">
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Valor
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>   
                                        <input type="text" name="campo[valor_aereo]" id="valor_aereo" value="<?php if (isset($campos['datos']['valor'])) echo $campos['datos']['valor'] ?>" class="form-control"></div>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Pasajeros</label>
                                    <input type="number" name="campo[pasajeros_aereo]" id="pasajeros_aereo" value="<?php if (isset($campos['datos']['pasajeros'])) echo $campos['datos']['pasajeros'] ?>" class="form-control">
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Tripulación</label>
                                    <input type="number" name="campo[tripulacion_aereo]" id="tripulacion_a" value="<?php if (isset($campos['datos']['tripulacion'])) echo $campos['datos']['tripulacion'] ?>" class="form-control">
                                </div>
                            </div>
                            <div class="row" id="observa">
                                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-6 "><label>Observaciones</label>
                                    <textarea name="campo[observaciones]"
                                              value="" class="form-control" id="observaciones_aereo"><?php if (isset($campos['datos']['observaciones'])) echo $campos['datos']['observaciones'] ?></textarea>
                                    <label for="campo[observaciones]" generated="true" class="error"></label>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3 divestado" >
                                    <label>Estado </label>
                                    <select class="form-control estado_aereo" name="campo[estado]" >
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
                                <div class="row docentregados_aereo" id="doc_entregados">                             
                                    <div class=" col-xs-12 col-sm-12 col-md-12 col-lg-12 documentos_entregados" >
                                        <h5>Documentos entregados</h5><br><br>
                                        <label>Nombre del documento</label>
                                        <div class='file_upload_aereo' id='faereo1'>
                                            <input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" /><input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/>
                                            <br><br>
                                        </div>
                                        <div id='file_tools_aereo' style="width: 90px!important; float: left;">
                                            <button type="button" class="btn btn-default btn-block" style="float: left; width: 40px; margin-right:5px;" id="add_file_aereo"><i class="fa fa-plus"></i>
                                            </button>
                                            <button type="button" style="float: left; width: 40px; margin-top:0px!important;" class="btn btn-default btn-block" id="del_file_aereo"><i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div id="espac">
                                    <br><br>
                                </div>                               

                                <div class="row detalleinteres_aereo" style="display:none">
                                    <input type="hidden" name="detalleunico" value="<?php echo strtotime('now'); ?>"> 
                                    <div class="col-xs-12 col-sm-3 col-md-12 col-lg-12" >
                                        <h5>Detalle interés asegurado</h5>
                                        <br>
                                        <hr>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <label>No. Certificado</label>
                                            <div class="input-group">
                                                <input type="text" name="campodetalle[certificado]" id="certificadodetalle_aereo" class="form-control">
                                            </div>										
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <label>Suma asegurada<span required="" aria-required="true">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-addon">$</span>
                                                <input type="text" name="campodetalle[suma_asegurada]" id="sumaaseguradadetalle_aereo" class="form-control">
                                            </div>
                                        </div>									
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <label>Prima neta<span required="" aria-required="true">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-addon">$</span>
                                                <input type="text" name="campodetalle[prima_anual]" id="primadetalle_aereo" class="form-control">
                                            </div>										
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <label>Deducible</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">$</span>
                                                <input type="text" name="campodetalle[deducible]" id="deducibledetalle_aereo" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <input type="hidden" name="campodesde[desde]" class="campodesde" value="">
                            <input type="hidden" name="campodesde[indcolec]" class="indcolec" value="">

                            <div class="row botones">
                                <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2" ><a
                                        href="<?php echo base_url('intereses_asegurados/listar'); ?>" class="btn btn-default btn-block"
                                        id="cancelar">Cancelar </a></div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                                    <input type="submit" name="campo[guardar]" value="Guardar "
                                           class="btn btn-primary btn-block guardarAereo guardarInteresSolicitud" id="campo[guardar]">
                                </div>
                            </div>
                        </div>
                        <div class="tabladetalle_aereo" style="display:none">
                            <!-- JQGRID -->
                            <?php echo modules::run('intereses_asegurados/ocultotablaaereo', $campos); ?>
                            <!-- /JQGRID -->
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div> 
</div>
