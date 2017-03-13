<?php
$formAttr = array(
    'method' => 'POST',
    'id' => 'formCasco_maritimo',
    'autocomplete' => 'off'
);
?>
<div class="row" style="margin-right:0px!important;">
    <div class="ibox-title border-bottom">
        <h5 class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Datos del casco marítimo</h5>
        <hr style="margin-top:30px!important;">
        <div id="vistaPersona">
            <div class="tab-content">
                <div id="datosdelPersona-5" class="tab-pane active">
                    <?php echo form_open_multipart(base_url('intereses_asegurados/guardar_maritimo'), $formAttr); ?>
                    <input type="hidden" name="campo[uuid]" id="campo[uuid]" class="uuid_casco_maritimo" value="<?php if (isset($campos['uuid'])) echo $campos['uuid'] ?>">
                    <input type="hidden" name="campo[id]" id="id" class="tipo_id" value="<?php if (isset($campos['id'])) echo $campos['id'] ?>" />
                    <input type="hidden" name="campo[tipo_id]" id="tipo_id_maritimo" class="tipo_id" value="4">
                    <input type="hidden" name="campo2[serier]" id="campo2[serier]" class="serier" value="<?php
                    if (isset($campos['datos']['serie'])) {
                        echo $campos['datos']['serie'];
                    }
                    ?>">
                    <div class="ibox">
                        <div class="ibox-content m-b-sm" style="display: block; border:0px">
                            <div class="row">
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>N°. de serie del casco
                                        <span required="" aria-required="true">*</span></label>
                                    <input type="text" name="campo[serie]" id="serie_maritimo" value="<?php
                                    if (isset($campos['datos']['serie'])) {
                                        echo $campos['datos']['serie'];
                                    }
                                    ?>" class="form-control" data-rule-required="true">
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Nombre de la embarcación
                                    </label>
                                    <input type="text" name="campo[nombre_embarcacion]" id="nombre_embarcacion" value="<?php
                                    if (isset($campos['datos']['nombre_embarcacion'])) {
                                        echo $campos['datos']['nombre_embarcacion'];
                                    }
                                    ?>" class="form-control">
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Tipo
                                    </label>
                                    <select class="form-control tipo_maritimo" id="tipo_maritimo" name="campo[tipo]">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($tipos as $row) { ?>
                                            <option <?php
                                            if (isset($campos['datos']['tipo'])) {
                                                if ($row->id_cat == $campos['datos']['tipo']) {
                                                    echo ' selected';
                                                }
                                            }
                                            ?> value="<?php echo $row->id_cat ?>"><?php echo $row->etiqueta ?></option>
                                            <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Marca
                                    </label>
                                    <input type="text" name="campo[marca]" id="marca_maritimo" value="<?php
                                    if (isset($campos['datos']['marca'])) {
                                        echo $campos['datos']['marca'];
                                    }
                                    ?>" class="form-control">
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Valor
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>   
                                        <input type="text" name="campo[valor]" id="valor_maritimo" value="<?php
                                        if (isset($campos['datos']['valor'])) {
                                            echo $campos['datos']['valor'];
                                        }
                                        ?>" class="form-control"></div>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Pasajeros</label>
                                    <input type="text" name="campo[pasajeros]" id="pasajeros_maritimo" value="<?php
                                    if (isset($campos['datos']['pasajeros'])) {
                                        echo $campos['datos']['pasajeros'];
                                    }
                                    ?>" class="form-control">
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Acreedor</label>
                                    <select class="form-control acreedor_maritimo" id="acreedor_maritimo" name="campo[acreedor]">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($acreedores as $acre) { ?>
                                            <option <?php
                                            if (isset($campos['datos']['acreedor'])) {
                                                if ($acre->id == $campos['datos']['acreedor']) {
                                                    echo ' selected';
                                                }
                                            }
                                            ?> value="<?php echo $acre->id ?>"><?php echo $acre->nombre ?></option>
                                            <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>% Asignado al acreedor
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon">%</span>
                                        <input type="text" name="campo[porcentaje_acreedor]" id="porcentaje_acreedor" value="<?php
                                        if (isset($campos['datos']['porcentaje_acreedor'])) {
                                            echo $campos['datos']['porcentaje_acreedor'];
                                        }
                                        ?>" class="form-control porcentaje_acreedor_maritimo"></div>
                                </div>      
                            </div>
                            <div class="row" id="observa">
                                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-6 "><label>Observaciones</label>
                                    <textarea name="campo[observaciones]" class="form-control" id="observaciones_maritimo"><?php
                                        if (isset($campos['datos']['observaciones'])) {
                                            echo $campos['datos']['observaciones'];
                                        }
                                        ?></textarea>
                                    <label for="campo[observaciones]" generated="true" class="error"></label>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3 divestado_casco" >
                                    <label>Estado </label>
                                    <select class="form-control estado_casco" name="campo2[estado]">
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
                            <!--<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" > </div>
                            <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 div1_persona">	
                            </div> -->
                            <?php
                            if (!isset($campos['datos']['uuid_casco_maritimo'])) {
                                ?>
                                <div class="row">
                                    <div class=" col-xs-12 col-sm-12 col-md-12 col-lg-12 documentos_entregados" id="doc_entregados">
                                        <h5>Documentos entregados</h5><br><br>
                                        <label>Nombre del documento</label>
                                        <div class='file_upload_maritimo' id='fmaritimo1'>
                                            <input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" /><input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/>
                                            <br><br>
                                        </div>
                                        <div id='file_tools_maritimo' style="width: 90px!important; float: left;">
                                            <button type="button" class="btn btn-default btn-block" style="float: left; width: 40px; margin-right:5px;" id="add_file_maritimo"><i class="fa fa-plus"></i>
                                            </button>
                                            <button type="button" style="float: left; width: 40px; margin-top:0px!important;" class="btn btn-default btn-block" id="del_file_maritimo"><i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>                            
                            <br><br>
                            <div class="row detalleinteres_maritimo" style="display:none">
                                <input type="hidden" name="detalleunico" value="<?php echo strtotime('now'); ?>"> 
                                <div class="col-xs-12 col-sm-3 col-md-12 col-lg-12" >
                                    <h5>Detalle interés asegurado</h5>
                                    <br>
                                    <hr>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <label>No. Certificado</label>
                                        <div class="input-group">
                                            <input type="text" name="campodetalle[certificado]" id="certificadodetalle_maritimo" class="form-control">
                                        </div>                                      
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <label>Suma asegurada<span required="" aria-required="true">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="text" name="campodetalle[suma_asegurada]" id="sumaaseguradadetalle_maritimo" class="form-control">
                                        </div>
                                    </div>                                  
                                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <label>Prima neta<span required="" aria-required="true">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="text" name="campodetalle[prima_anual]" id="primadetalle_maritimo" class="form-control">
                                        </div>                                      
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <label>Deducible</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="text" name="campodetalle[deducible]" id="deducibledetalle_maritimo" class="form-control">
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
                                           class="btn btn-primary btn-block guardarMaritimo guardarInteresSolicitud" id="campo[guardar]">
                                </div>
                            </div>

                            <div class="tabladetalle_maritimo" style="display:none">
                                <!-- JQGRID -->
                                <?php echo modules::run('intereses_asegurados/ocultotablamaritimo', $campos); ?>
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
