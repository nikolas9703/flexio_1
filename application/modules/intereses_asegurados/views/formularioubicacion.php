<?php
$formAttr = array(
    'method' => 'POST',
    'id' => 'formUbicacion',
    'autocomplete' => 'off'
);
?>
<div class="row" style="margin-right:0px!important;">
    <div class="ibox-title border-bottom">
        <h5>Datos de la ubicación</h5>
        <hr style="margin-top:30px!important;">
        <div id="vistaPersona">
            <div class="tab-content">
                <div id="datosdelPersona-5" class="tab-pane active">
                    <?php echo form_open_multipart(base_url('intereses_asegurados/guardar_ubicacion'), $formAttr); ?>
                    <input type="hidden" name="campo[uuid]" id="campo[uuid]" class="uuid_ubicacion" value="<?php if (isset($campos['uuid'])) echo $campos['uuid'] ?>">
                    <input type="hidden" name="campo[id]" id="id" class="tipo_id" value="<?php if (isset($campos['id'])) echo $campos['id'] ?>" />
                    <input type="hidden" name="campo[tipo_id]" id="tipo_id_ubicacion" class="tipo_id" value="7">
                    <input type="hidden" name="campo2[direccionr]" id="campo2[direccionr]" class="serier" value="<?php
                    if (isset($campos['datos']['direccion'])) {
                        echo $campos['datos']['direccion'];
                    }
                    ?>">
                    <div class="ibox">
                        <div class="ibox-content m-b-sm" style="display: block; border:0px">
                            <div class="row">
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Nombre de la ubicación<span required="" aria-required="true">*</span></label>
                                    <input type="text" name="campo[nombre]" id="nombre_ubicacion" value="<?php
                                    if (isset($campos['datos']['nombre'])) {
                                        echo $campos['datos']['nombre'];
                                    }
                                    ?>" class="form-control" data-rule-required="true">
                                </div>
                                <div class="form-group col-xs-12 col-sm-9 col-md-9 col-lg-9">
                                    <label>Dirección detallada<span required="" aria-required="true">*</span></label>
                                    <input type="text" name="campo[direccion]" id="direccion_ubicacion" value="<?php
                                    if (isset($campos['datos']['direccion'])) {
                                        echo $campos['datos']['direccion'];
                                    }
                                    ?>" class="form-control" data-rule-required="true">
                                </div>



                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Edificio y Mejoras</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>   
                                        <input type="text" name="campo[edif_mejoras]" id="edif_mejoras" value="<?php
                                        if (isset($campos['datos']['edif_mejoras'])) {
                                            echo $campos['datos']['edif_mejoras'];
                                        }
                                        ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Contenido, mercancía, etc</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>   
                                        <input type="text" name="campo[contenido]" id="contenido" value="<?php
                                        if (isset($campos['datos']['contenido'])) {
                                            echo $campos['datos']['contenido'];
                                        }
                                        ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Maquinaria y equipos</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>   
                                        <input type="text" name="campo[maquinaria]" id="maquinaria" value="<?php
                                        if (isset($campos['datos']['maquinaria'])) {
                                            echo $campos['datos']['maquinaria'];
                                        }
                                        ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Inventario</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>   
                                        <input type="text" name="campo[inventario]" id="inventario" value="<?php
                                        if (isset($campos['datos']['inventario'])) {
                                            echo $campos['datos']['inventario'];
                                        }
                                        ?>" class="form-control">
                                    </div>
                                </div>
                            </div>


                            <div class="row">

                                <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-3"><label>Acreedor</label>
                                    <select class="form-control acreedor_ubicacion" id="acreedor_ubicacion" name="campo[acreedor]">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($acreedores as $acre) { ?>
                                            <option <?php
                                            if (isset($campos['datos']['acreedor'])) {
                                                if ($acre->id == $campos['datos']['acreedor']) {
                                                    echo ' selected';
                                                } else if ($campos['datos']['acreedor'] == "otro") {
                                                    echo ' selected';
                                                }
                                            }
                                            ?> value="<?php echo $acre->id ?>"><?php echo $acre->nombre ?></option>
                                                <?php
                                            }
                                            if ((isset($campos['datos']['acreedor'])) && ($campos['datos']['acreedor'] == "otro")) {
                                                ?>  <option selected="" value="otro">Otro</option><?php } else {
                                                ?><option value="otro">Otro</option><?php } ?>

                                    </select>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-3" style="margin-left: 3px;">
                                    <label style="color: transparent">a</label>
                                    <input type="text" class="form-control" value="<?php
                                    if (isset($campos['datos']['acreedor_opcional'])) {
                                        echo $campos['datos']['acreedor_opcional'];
                                    }
                                    ?>" name="campo[acreedor_opcional]" id="acreedor_ubicacion_opcional" disabled/>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" style="margin-left: 6px;">
                                    <label>% Asignado al acreedor</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">%</span>
                                        <input type="text" name="campo[porcentaje_acreedor]" id="porcentaje_acreedor_ubicacion" value="<?php
                                        if (isset($campos['datos']['porcentaje_acreedor'])) {
                                            echo $campos['datos']['porcentaje_acreedor'];
                                        }
                                        ?>" class="form-control porcentaje_acreedor_ubicacion">

                                    </div>
                                </div>
                            </div>


                            <div class="row" id="observa">
                                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-6 ">
                                    <label>Observaciones</label>
                                    <textarea name="campo[observaciones]" class="form-control" id="observaciones_ubicacion"><?php if (isset($campos['datos']['observaciones'])) echo $campos['datos']['observaciones'] ?></textarea>
                                    <label for="campo[observaciones]" generated="true" class="error"></label>

                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3 divestado_ubicacion" >
                                    <label>Estado </label>
                                    <select class="form-control estado_ubicacion" name="campo2[estado]">
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

                            <?php
                            if (empty($campos['datos']['uuid_ubicacion'])) {
                                ?>
                                <div class="row" id="doc_entregados">
                                    <div class=" col-xs-12 col-sm-12 col-md-12 col-lg-12 documentos_entregados" >
                                        <h5>Documentos entregados</h5><br><br>
                                        <label>Nombre del documento</label>
                                        <div class='file_upload_ubicacion' id='fubicacion1'>
                                            <input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" /><input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/>
                                            <br><br>
                                        </div>
                                        <div id='file_tools_ubicacion' style="width: 90px!important; float: left;">
                                            <button type="button" class="btn btn-default btn-block" style="float: left; width: 40px; margin-right:5px;" id="add_file_ubicacion"><i class="fa fa-plus"></i>
                                            </button>
                                            <button type="button" style="float: left; width: 40px; margin-top:0px!important;" class="btn btn-default btn-block" id="del_file_ubicacion"><i class="fa fa-trash"></i>
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
                            

                            <div class="row detalleinteres_ubicacion" style="display:none">
                                <input type="hidden" name="detalleunico" value="<?php echo strtotime('now'); ?>"> 
                                <div class="col-xs-12 col-sm-3 col-md-12 col-lg-12" >
                                    <h5>Detalle interés asegurado</h5>
                                    <br>
                                    <hr>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <label>No. Certificado</label>
                                        <div class="input-group">
                                            <input type="text" name="campodetalle[certificado]" id="certificadodetalle_ubicacion" class="form-control">
                                        </div>                                      
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <label>Suma asegurada<span required="" aria-required="true">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="text" name="campodetalle[suma_asegurada]" id="sumaaseguradadetalle_ubicacion" class="form-control">
                                        </div>
                                    </div>                                  
                                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <label>Prima neta<span required="" aria-required="true">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="text" name="campodetalle[prima_anual]" id="primadetalle_ubicacion" class="form-control">
                                        </div>                                      
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <label>Deducible</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="text" name="campodetalle[deducible]" id="deducibledetalle_ubicacion" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="campodesde[desde]" class="campodesde" value="">
                            <input type="hidden" name="campodesde[indcolec]" class="indcolec" value="">

                            <div class="row botones">
                                <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2" >
                                    <a onclick="window.history.back();" class="btn btn-default btn-block" id="cancelar">Cancelar </a></div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                                    <input type="submit" name="campo[guardar]" value="Guardar "
                                           class="btn btn-primary btn-block guardarUbicacion" id="campo[guardar]">
                                </div>
                            </div>
                            <div class="tabladetalle_ubicacion" style="display: none">
                                <!-- JQGRID -->
                                <?php echo modules::run('intereses_asegurados/ocultotablaubicacion/2', $campos); ?>
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