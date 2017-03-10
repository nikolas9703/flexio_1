<?php
$formAttr = array(
    'method' => 'POST',
    'id' => 'formProyecto_actividad',
    'autocomplete' => 'off'
);
?>
<div class="row" style="margin-right:0px!important;">
    <div class="ibox-title border-bottom">
        <h5 class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Datos del Proyecto / Actividad</h5>
        <hr style="margin-top:30px!important;">
        <div id="vistaPersona">
            <div class="tab-content">
                <div id="datosdelPersona-5" class="tab-pane active">
                    <?php echo form_open_multipart(base_url('intereses_asegurados/guardar_proyecto'), $formAttr); ?>
                    <input type="hidden" name="campo[uuid]" id="campo[uuid]" class="uuid_proyecto" value="<?php if (isset($campos['uuid'])) echo $campos['uuid'] ?>">
                    <input type="hidden" name="campo[id]" id="id" class="tipo_id" value="<?php if (isset($campos['id'])) echo $campos['id'] ?>" />
                    <input type="hidden" name="campo[tipo_id]" id="tipo_id_proyecto" class="tipo_id" value="6">
                    <input type="hidden" name="campo2[no_ordenr]" id="campo2[no_ordenr]" class="no_ordenr" value="<?php
                    if (isset($campos['datos']['no_orden'])) {
                        echo $campos['datos']['no_orden'];
                    }
                    ?>">
                    <div class="ibox">
                        <div class="ibox-content m-b-sm" style="display: block; border:0px">
                            <div class="row"> 
                                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6"><label>Nombre del proyecto o actividad
                                        <span required="" aria-required="true">*</span></label>
                                    <input type="text" name="campo[nombre_proyecto]" id="nombre_proyecto" class="form-control" data-rule-required="true"  <?php if (isset($campos['datos']['nombre_proyecto'])) { ?> value="<?php echo $campos['datos']['nombre_proyecto'] ?>"  <?php } ?>>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Contratista
                                    </label>
                                    <input type="text" name="campo[contratista]" id="contratista_proyecto" class="form-control" <?php if (isset($campos['datos']['contratista'])) { ?> value="<?php echo $campos['datos']['contratista'] ?>"  <?php } ?>>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Representante legal
                                    </label>
                                    <input type="text" name="campo[representante_legal]" id="representante_legal_proyecto" class="form-control" <?php if (isset($campos['datos']['representante_legal'])) { ?> value="<?php echo $campos['datos']['representante_legal'] ?>"  <?php } ?>>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Fecha de concurso
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>   
                                        <input type="text" name="campo[fecha_concurso]" id="fecha_concurso" class="form-control fecha_concurso" <?php if (isset($campos['datos']['fecha_concurso'])) { ?> value="<?php echo $campos['datos']['fecha_concurso'] ?>"  <?php } ?>></div>
                                </div>    
                                <div class="form-group col-xs-12 col-sm-3 col-md-34 col-lg-3"><label>No. de orden o contrato<span required="" aria-required="true">*</span>
                                    </label>
                                    <input type="text" name="campo[no_orden]" id="no_orden_proyecto" class="form-control" data-rule-required="true" <?php if (isset($campos['datos']['no_orden'])) { ?> value="<?php echo $campos['datos']['no_orden'] ?>"  <?php } ?>>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Duración del contrato
                                    </label>
                                    <input type="text" name="campo[duracion]" id="duracion_proyecto" class="form-control" <?php if (isset($campos['datos']['duracion'])) { ?> value="<?php echo $campos['datos']['duracion'] ?>"  <?php } ?>>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Fecha de inicio
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>   
                                        <input type="text" name="campo[fecha]" id="fecha" class="form-control fecha_proyecto" <?php if (isset($campos['datos']['fecha'])) { ?> value="<?php echo $campos['datos']['fecha'] ?>"  <?php } ?>></div>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Tipo de fianza</label>
                                    <select class="form-control tipo_fianza" name="campo[tipo_fianza]" id="tipo_fianza">
                                        <?php foreach ($tipo_fianza as $propuesta) { ?>
                                            <option <?php
                                            if (isset($campos['datos']['tipo_fianza'])) {
                                                if ($propuesta->valor == $campos['datos']['tipo_fianza']) {
                                                    echo ' selected';
                                                }
                                            }
                                            ?> value="<?php echo $propuesta->valor ?>"><?php echo $propuesta->etiqueta ?></option>
                                            <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Monto del contrato
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>   
                                        <input type="text" min="0" max="99999999" name="campo[monto]" id="monto" class="form-control monto_proyecto" <?php if (isset($campos['datos']['monto'])) { ?> value="<?php echo $campos['datos']['monto'] ?>"  <?php } ?>></div>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Monto afianzado %</label>
                                    <div class="input-group">  
                                        <input type="text" name="campo[monto_afianzado]" id="monto_afianzado" class="form-control" <?php if (isset($campos['datos']['monto_afianzado'])) { ?> value="<?php echo $campos['datos']['monto_afianzado'] ?>"  <?php } ?>><span class="input-group-addon">%</span>
                                    </div>  
                                </div>
                                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2"><label>Tipo de propuesta</label>
                                    <select class="form-control tipo_propuesta" name="campo[tipo_propuesta]" disabled="">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($tipo_propuesta as $propuesta) { ?>
                                            <option <?php
                                            if (isset($campos['datos']['tipo_propuesta'])) {
                                                if ($propuesta->id_cat == $campos['datos']['tipo_propuesta']) {
                                                    echo ' selected';
                                                } else if ($campos['datos']['tipo_propuesta'] == "otro") {
                                                    echo ' selected';
                                                }
                                            }
                                            ?> value="<?php echo $propuesta->id_cat ?>"><?php echo $propuesta->etiqueta ?></option>
                                            <?php } if ((isset($campos['datos']['tipo_propuesta'])) && ($campos['datos']['tipo_propuesta'] == "otro")) {
                                                ?>  <option selected="" value="otro">Otro</option><?php } else {
                                                ?><option value="otro">Otro</option><?php } ?>
                                    </select>
                                </div>
                                <!--                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Asignado al acreedor %
                                                                    </label>
                                                                    <div class="input-group">  
                                                                        <input type="text" name="campo[asignado_acreedor]" id="asignado_acreedor" class="form-control" ><span class="input-group-addon">%</span></div>
                                                                </div>-->
                                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1"><label style="color: transparent">a</label>
                                    <input type="text" class="form-control tipo_propuesta_opcional" name="campo[tipo_propuesta_opcional]" id="tipo_propuesta_opcional" <?php if (isset($campos['datos']['tipo_propuesta_opcional'])) { ?> value="<?php echo $campos['datos']['tipo_propuesta_opcional'] ?>"  <?php } ?> disabled/>
                                </div>
                                <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6"><label>Ubicación
                                    </label>
                                    <input type="text" name="campo[ubicacion]" id="ubicacion_proyecto" class="form-control" <?php if (isset($campos['datos']['ubicacion'])) { ?> value="<?php echo $campos['datos']['ubicacion'] ?>"  <?php } ?>>
                                </div>
                                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2"><label>Acreedor</label>
                                    <select class="form-control acreedor_proyecto" id="acreedor_pr" name="campo[acreedor]">
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
                                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1"><label style="color: transparent"></label>
                                    <input type="text" class="form-control acreedor_opcional_proyecto" name="campo[acreedor_opcional]" id="acreedor_opcional" <?php if (isset($campos['datos']['acreedor_opcional'])) { ?> value="<?php echo $campos['datos']['acreedor_opcional'] ?>"  <?php } ?> disabled/>
                                </div>
                                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2"><label>Validez de la fianza</label>
                                    <select class="form-control validez_fianza_pr" id="validez_fianza_pr" name="campo[validez_fianza_pr]"> 
                                        <option value="">Seleccione</option>
                                        <?php foreach ($validez_fianza as $fianza) { ?>

                                            <option <?php
                                            if (isset($campos['datos']['validez_fianza_pr'])) {
                                                if ($campos['datos']['validez_fianza_pr'] == $fianza->id_cat) {
                                                    echo ' selected';
                                                }
                                            }
                                            ?> value="<?php echo $fianza->id_cat ?>"><?php echo $fianza->etiqueta ?></option>
                                                <?php
                                            }
                                            if ((isset($campos['datos']['validez_fianza_pr'])) && ($campos['datos']['validez_fianza_pr'] == "otro")) {
                                                ?>  <option selected="" value="otro">Otro</option><?php } else {
                                                ?><option value="otro">Otro</option><?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1"><label style="color: transparent">a</label>
                                    <input type="text" class="form-control validez_fianza_opcional" name="campo[validez_fianza_opcional]" id="validez_fianza_opcional" disabled="" <?php if (isset($campos['datos']['validez_fianza_opcional'])) { ?> value="<?php echo $campos['datos']['validez_fianza_opcional'] ?>"  <?php } ?> />
                                </div>    
                            </div>
                            <div class="row" id="observa">
                                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-6 "><label>Observaciones</label>
                                    <textarea name="campo[observaciones]"
                                              class="form-control" id="observaciones_proyecto" ><?php
                                                  if (isset($campos['datos']['observaciones'])) {
                                                      echo $campos['datos']['observaciones'];
                                                  }
                                                  ?></textarea>
                                    <label for="campo[observaciones]" generated="true" class="error"></label>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3 divestado_proyecto" >
                                    <label>Estado </label>
                                    <select class="form-control estado_proyecto" name="campo2[estado]">
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
                            <!--                            <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12" > </div>
                                                        <div class="form-group col-xs-12 col-sm-3 col-md-12 col-lg-12" ><h5>Documentos entregados</h5><hr style="margin-top:30px!important;"></div>
                                                        <div class="form-group col-xs-12 col-sm-3 col-md-6 col-lg-6" >
                                                            <label>Nombre del documento</label>
                                                            <div class='file_upload_proyectos' id='f1'><input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" /><input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/></div>
                                                            <div id='file_tools_proyectos' style="width: 90px!important; float: left;">
                                                                <button type="button" class="btn btn-default btn-block" style="float: left; width: 40px; margin-right:5px;" id="add_file_proyectos"><i class="fa fa-plus"></i></button><button type="button" style="float: left; width: 40px; margin-top:0px!important;" class="btn btn-default btn-block" id="del_file_proyectos"><i class="fa fa-trash"></i></button>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-6" >
                                                        </div>-->

                        </div>
                        <?php
                        if (!isset($campos['datos']['uuid_proyecto'])) {
                            ?>
                            <div class="row" id="doc_entregados">
                                <div class=" col-xs-12 col-sm-12 col-md-12 col-lg-12 documentos_entregados" >
                                    <h5>Documentos entregados</h5><br><br>
                                    <label>Nombre del documento</label>
                                    <div class='file_upload_proyecto' id='fproyecto1'>
                                        <input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" /><input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/>
                                        <br><br>
                                    </div>
                                    <div id='file_tools_proyecto' style="width: 90px!important; float: left;">
                                        <button type="button" class="btn btn-default btn-block" style="float: left; width: 40px; margin-right:5px;" id="add_file_proyecto"><i class="fa fa-plus"></i>
                                        </button>
                                        <button type="button" style="float: left; width: 40px; margin-top:0px!important;" class="btn btn-default btn-block" id="del_file_proyecto"><i class="fa fa-trash"></i>
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

                        <div class="row detalleinteres_proyecto" style="display:none">
                            <input type="hidden" name="detalleunico" value="<?php echo strtotime('now'); ?>"> 
                            <div class="col-xs-12 col-sm-3 col-md-12 col-lg-12" >
                                <h5>Detalle interés asegurado</h5>
                                <br>
                                <hr>
                                <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                    <label>No. Certificado</label>
                                    <div class="input-group">
                                        <input type="text" name="campodetalle[certificado]" id="certificadodetalle_proyecto" class="form-control">
                                    </div>                                      
                                </div>
                                <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                    <label>Suma asegurada<span required="" aria-required="true">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input type="text" name="campodetalle[suma_asegurada]" id="sumaaseguradadetalle_proyecto" class="form-control">
                                    </div>
                                </div>                                  
                                <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                    <label>Prima neta<span required="" aria-required="true">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input type="text" name="campodetalle[prima_anual]" id="primadetalle_proyecto" class="form-control">
                                    </div>                                      
                                </div>
                                <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                    <label>Deducible</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input type="text" name="campodetalle[deducible]" id="deducibledetalle_proyecto" class="form-control">
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
                                       class="btn btn-primary btn-block guardarProyecto guardarInteresSolicitud" id="campo[guardar]">
                            </div>
                        </div>
                        <div class="tabladetalle_proyecto" style="display:none">
                            <!-- JQGRID -->
                            <?php echo modules::run('intereses_asegurados/ocultotablaproyecto', $campos); ?>
                            <!-- /JQGRID -->
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>