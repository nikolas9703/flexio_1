<?php
$formAttr = array(
    'method' => 'POST',
    'id' => 'formCarga',
    'autocomplete' => 'off'
);
?>

<div class="row" style="margin-right:0px!important;">
    <div class="ibox-title border-bottom">
        <h5>Datos de la carga</h5>
        <hr style="margin-top:30px!important;">
        <div id="vistaCarga">
            <div class="tab-content">
                <div id="datosdelCarga-5" class="tab-pane active">
                    <?php echo form_open_multipart(base_url('intereses_asegurados/guardar_carga'), $formAttr); ?>
                    <input type="hidden" name="campo[uuid]" id="campo[uuid]" class="uuid_carga" value="<?php if (isset($campos['uuid'])) echo $campos['uuid'] ?>">
                    <input type="hidden" name="campo[tipo_id]" id="tipo_id_carga" class="tipo_id" value="2">
                    <div class="ibox">
                        <div class="ibox-content m-b-sm" style="display: block; border:0px">

                            <div class="row form-group">                                   
                                <div class="col-xs-12 col-sm-3 col-md-34 col-lg-3"><label>No. de liquidación<span required="" aria-required="true">*</span>
                                    </label>
                                    <input type="text" name="campo[no_liquidacion]" id="no_liquidacion" value="<?php if (isset($campos['datos']['no_liquidacion'])) echo $campos['datos']['no_liquidacion'] ?>" class="form-control" data-rule-required="true">
                                </div>
                                <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Fecha de despacho</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>   
                                        <input type="text" name="campo[fecha_despacho]" id="fecha_despacho" value="<?php if (isset($campos['datos']['fecha_despacho'])) echo $campos['datos']['fecha_despacho'] ?>" class="form-control fecha_despacho"></div>
                                </div>
                                <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Fecha de arribo</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>   
                                        <input type="text" name="campo[fecha_arribo]" id="fecha_arribo" value="<?php if (isset($campos['datos']['fecha_arribo'])) echo $campos['datos']['fecha_arribo'] ?>" class="form-control fecha_arribo"></div>
                                </div>
                                <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Detalle mercancia</label>
                                    <input type="text" name="campo[detalle]" id="detalle" value="<?php if (isset($campos['datos']['detalle'])) echo $campos['datos']['detalle'] ?>" class="form-control">
                                </div>                                
                            </div>

                            <div class="row">
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Valor de la mercancia</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>   
                                        <input type="text" name="campo[valor]" id="valor" value="<?php if (isset($campos['datos']['valor'])) echo $campos['datos']['valor'] ?>" class="form-control valor_mercancia">
                                    </div>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Tipo de empaque</label>
                                    <select class="form-control tipo_empaque" name="campo[tipo_empaque]">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($tipo_empaque as $empaque) { ?>
                                            <option value="<?php echo $empaque->id_cat ?>" <?php if (isset($campos['datos']['tipo_empaque'])) if ($campos['datos']['tipo_empaque'] == $empaque->id_cat) echo "selected"; ?>  ><?php echo $empaque->etiqueta ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Condición de envío</label>
                                    <select class="form-control condicion_envio" name="campo[condicion_envio]">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($condicion_envio as $cond_env) { ?>
                                            <option value="<?php echo $cond_env->id_cat ?>" <?php if (isset($campos['datos']['condicion_envio'])) if ($campos['datos']['condicion_envio'] == $cond_env->id_cat) echo "selected"; ?> ><?php echo $cond_env->etiqueta ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Medio de transporte</label>
                                    <select class="form-control medio_transporte" name="campo[medio_transporte]" >
                                        <option value="">Seleccione</option>
                                        <?php foreach ($medio_transporte as $medt) { ?>
                                            <option value="<?php echo $medt->id_cat ?>" <?php if (isset($campos['datos']['medio_transporte'])) if ($campos['datos']['medio_transporte'] == $medt->id_cat) echo "selected"; ?> ><?php echo $medt->etiqueta ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Origen</label>
                                    <input type="text" name="campo[origen]" id="origen" value="<?php if (isset($campos['datos']['origen'])) echo $campos['datos']['origen'] ?>" class="form-control" >
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Destino</label>
                                    <input type="text" name="campo[destino]" id="destino" value="<?php if (isset($campos['datos']['destino'])) echo $campos['datos']['destino'] ?>" class="form-control" >
                                </div>
                                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                                    <label>Acreedor</label>
                                    <select class="form-control acreedor_carga" name="campo[acreedor]" >
                                        <option value="">Seleccione</option>
                                        <?php foreach ($acreedores as $acre) { ?>
                                            <option value="<?php echo $acre->id ?>" <?php
                                            if (isset($campos['datos']['acreedor'])) {
                                                if ($acre->id == $campos['datos']['acreedor']) {
                                                    echo ' selected';
                                                }
                                            }
                                            ?> ><?php echo $acre->nombre ?></option>
                                                <?php } ?>
                                        <option value="otro" <?php
                                        if (isset($campos['datos']['acreedor'])) {
                                            if ($campos['datos']['acreedor'] == "otro") {
                                                echo ' selected';
                                            }
                                        }
                                        ?>>Otro</option>
                                    </select>
                                </div>
                                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1"><label></label>
                                    <input type="text" class="form-control" value="<?php
                                    if (isset($campos['datos']['acreedor'])) {
                                        if ($campos['datos']['acreedor'] == "otro") {
                                            echo $campos['datos']['acreedor_opcional'];
                                        }
                                    }
                                    ?>" name="campo[acreedor_opcional]" id="acreedor_carga_opcional" <?php
                                           if (isset($campos['datos']['acreedor'])) {
                                               if ($campos['datos']['acreedor'] != "otro") {
                                                   echo ' disabled';
                                               }
                                           }
                                           ?>/>
                                </div>
                                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                                    <label>Tipo de obligaci&oacute;n</label>
                                    <select class="form-control tipo_obligacion" name="campo[tipo_obligacion]" >
                                        <option value="">Seleccione</option>
                                        <?php foreach ($tipo_obligacion as $obligacion) { ?>
                                            <option value="<?php echo $obligacion->id_cat ?>" <?php
                                            if (isset($campos['datos']['tipo_obligacion'])) {
                                                if ($obligacion->id_cat == $campos['datos']['tipo_obligacion']) {
                                                    echo ' selected';
                                                }
                                            }
                                            ?>  ><?php echo $obligacion->etiqueta ?></option>
                                                <?php } ?>
                                        <option value="otro" <?php
                                        if (isset($campos['datos']['tipo_obligacion'])) {
                                            if ($campos['datos']['tipo_obligacion'] == "otro") {
                                                echo ' selected';
                                            }
                                        }
                                        ?> >Otro</option>
                                    </select>
                                </div>
                                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1"><label></label>
                                    <input type="text" class="form-control" value="<?php
                                    if (isset($campos['datos']['tipo_obligacion'])) {
                                        if ($campos['datos']['tipo_obligacion'] == "otro") {
                                            echo $campos['datos']['tipo_obligacion_opcional'];
                                        }
                                    }
                                    ?>" name="campo[tipo_obligacion_opcional]" id="tipo_obligacion_opcional"  <?php
                                           if (isset($campos['datos']['tipo_obligacion'])) {
                                               if ($campos['datos']['tipo_obligacion'] != "otro") {
                                                   echo ' disabled';
                                               }
                                           }
                                           ?> />
                                </div>
                            </div>

                            <div class="row" id="observa">
                                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-6 ">
                                    <label>Observaciones</label>
                                    <textarea name="campo[observaciones]"
                                              class="form-control" id="observaciones_carga"><?php if (isset($campos['datos']['observaciones'])) echo $campos['datos']['observaciones'] ?></textarea>
                                    <label for="campo[observaciones]" generated="true" class="error"></label>
                                </div>


                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3 divestado" >

                                    <label>Estado </label>
                                    <select class="form-control estado_carga" id="estadoPersona" name="campo[estado]"  data-rule-required="true">

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
                            </div>
                            <div id="espac">
                                <br><br>
                            </div>                            
                            <div class="row docentregado" id="doc_entregados">                                
                                <div class=" col-xs-12 col-sm-12 col-md-12 col-lg-12 documentos_entregados" >
                                    <h5>Documentos entregados</h5><br><br>
                                    <label>Nombre del documento</label>
                                    <div class='file_upload_carga' id='fcarga1'>
                                        <input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" /><input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/>
                                        <br><br>
                                    </div>
                                    <div id='file_tools_carga' style="width: 90px!important; float: left;">
                                        <button type="button" class="btn btn-default btn-block" style="float: left; width: 40px; margin-right:5px;" id="add_file_carga"><i class="fa fa-plus"></i>
                                        </button>
                                        <button type="button" style="float: left; width: 40px; margin-top:0px!important;" class="btn btn-default btn-block" id="del_file_carga"><i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <br>
                            <div class="row detalleinteres_carga" style="display:none">
                                <input type="hidden" name="detalleunico" value="<?php echo strtotime('now'); ?>"> 
                                <div class="col-xs-12 col-sm-3 col-md-12 col-lg-12" >
                                    <h5>Detalle interés asegurado</h5>
                                    <br>
                                    <hr>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <label>No. Certificado</label>
                                        <div class="input-group">
                                            <input type="text" name="campodetalle[certificado]" id="certificadodetalle_carga" class="form-control">
                                        </div>                                      
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <label>Suma asegurada<span required="" aria-required="true">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="text" name="campodetalle[suma_asegurada]" id="sumaaseguradadetalle_carga" class="form-control">
                                        </div>
                                    </div>                                  
                                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <label>Prima neta<span required="" aria-required="true">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="text" name="campodetalle[prima_anual]" id="primadetalle_carga" class="form-control">
                                        </div>                                      
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <label>Deducible</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="text" name="campodetalle[deducible]" id="deducibledetalle_carga" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="campodesde[desde]" class="campodesde" value="">
                            <input type="hidden" name="campodesde[indcolec]" class="indcolec" value="">

                            <div class="row botones">
                                <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2" >
                                    <a href="<?php echo base_url('intereses_asegurados/listar'); ?>" class="btn btn-default btn-block" id="cancelar">Cancelar </a>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                                    <input type="submit" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block guardarCarga" id="campo[guardar]">
                                </div> 
                            </div>

                            <div class="tabladetalle_carga" style="display:none">
                                <!-- JQGRID -->
                                <?php echo modules::run('intereses_asegurados/ocultotablacarga', $campos); ?>
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
