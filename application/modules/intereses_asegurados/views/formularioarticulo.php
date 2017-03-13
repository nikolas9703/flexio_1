<div class="row" style="margin-right:0px!important;">
    <div class="ibox-title border-bottom">
        <h5 class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Datos del artículo</h5>
        <hr style="margin-top:30px!important;">
        <div id="vistaPersona">
            <div class="tab-content">
                <div id="datosdelPersona-5" class="tab-pane active">
                    <!--<div id="etiqueta1FormArticulo">-->
                    <?php echo form_open_multipart(base_url('intereses_asegurados/guardar_articulo'), "id='articulo'");?>
                    <!--</div>-->
                    <input type="hidden" name="campo[uuid]" id="uuid_articulo" class="form-control" value="<?php if (isset($campos['uuid'])) echo $campos['uuid'] ?>" />
                    <input type="hidden" name="campo[tipo_id]" id="tipo_id_articulo" class="tipo_id" value="1" />
                    <div class="ibox">
                        <div class="ibox-content m-b-sm" style="display: block; border:0px">
                            <div class="row">
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Nombre del artículo <span required="" aria-required="true">*</span></label>
                                    <input type="text" name="campo[nombre]" id="nombre" class="form-control" data-rule-required="true" value="<?php if (isset($campos['datos']['nombre'])) echo $campos['datos']['nombre'] ?>"/>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Clase de equipo </label>
                                    <input type="text" name="campo[clase_equipo]" id="clase_equipo" class="form-control" value="<?php if (isset($campos['datos']['clase_equipo'])) echo $campos['datos']['clase_equipo'] ?>" />
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Marca</label>
                                    <input type="text" name="campo[marca]" id="marca" class="form-control" value="<?php if (isset($campos['datos']['marca'])) echo $campos['datos']['marca'] ?>" />
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Modelo</label>
                                    <input type="text" name="campo[modelo]" id="modelo" class="form-control" value="<?php if (isset($campos['datos']['modelo'])) echo $campos['datos']['modelo'] ?>" />
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Año</label>
                                    <input type="text" name="campo[anio]" id="anio_articulo" class="form-control" value="<?php if (isset($campos['datos']['anio'])) echo $campos['datos']['anio'] ?>" /> 
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>N° de serie</label>
                                    <input type="text" name="campo[numero_serie]" id="numero_serie" class="form-control" value="<?php if (isset($campos['datos']['numero_serie'])) echo $campos['datos']['numero_serie'] ?>" />
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Condición</label>
                                    <select class="form-control condicion" name="campo[id_condicion]" id="id_condicion">
                                        <option value="">Seleccione</option>
                                        <?php
                                        foreach ($condicion as $con) {
                                            if (isset($campos['datos']['id_condicion']) && $campos['datos']['id_condicion'] == $con->id) {
                                                echo'
												<option value="' . $con->id . '" selected>' . $con->valor . '</option>';
                                            } else {
                                                echo '
												<option value="' . $con->id . '">' . $con->valor . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <label>Valor</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input type="text" name="campo[valor]" id="valor" class="form-control valor_articulo" value="<?php if (isset($campos['datos']['valor'])) echo $campos['datos']['valor'] ?>" /> 
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="observa">
                                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-6 ">
                                    <label>Observaciones</label>
                                    <textarea name="campo[observaciones]" class="form-control" id="observaciones_articulo" ><?php if (isset($campos['datos']['observaciones'])) echo $campos['datos']['observaciones'] ?></textarea>  
                                    <label for="campo[observaciones]" generated="true" class="error"></label>
                                </div>

                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3 divestado" >
                                    <label>Estado </label>
                                    <select class="form-control estado_articulo" name="campo2[estado]" id="campo2[estado]">
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

                                <!--<div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" > </div>
                                <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 div1_persona"> </div>-->

                            </div> 

                            <?php if (!isset($campos['uuid'])) { ?>
                                <div class="row" id="doc_entregados">
                                    <div class=" col-xs-12 col-sm-12 col-md-12 col-lg-12 documentos_entregados" >
                                        <h5>Documentos entregados</h5><br><br>
                                        <label>Nombre del documento</label>
                                        <div class='file_upload_articulo' id='farticulo1'>
                                            <input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" /><input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/>
                                            <br><br>
                                        </div>
                                        <div id='file_tools_articulo' style="width: 90px!important; float: left;">
                                            <button type="button" class="btn btn-default btn-block" style="float: left; width: 40px; margin-right:5px;" id="add_file_articulo"><i class="fa fa-plus"></i>
                                            </button>
                                            <button type="button" style="float: left; width: 40px; margin-top:0px!important;" class="btn btn-default btn-block" id="del_file_articulo"><i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                            ?>
                            <div id="espac">
                               <br><br> 
                            </div>                            
                            <div class="row detalleinteres_articulo" style="display:none">
                                <input type="hidden" name="detalleunico" value="<?php echo strtotime('now'); ?>"> 
                                <div class="col-xs-12 col-sm-3 col-md-12 col-lg-12" >
                                    <h5>Detalle interés asegurado</h5>
                                    <br>
                                    <hr>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <label>No. Certificado</label>
                                        <div class="input-group">
                                            <input type="text" name="campodetalle[certificado]" id="certificadodetalle_articulo" class="form-control">
                                        </div>										
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <label>Suma asegurada<span required="" aria-required="true">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="text" name="campodetalle[suma_asegurada]" id="sumaaseguradadetalle_articulo" class="form-control">
                                        </div>
                                    </div>									
                                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <label>Prima neta<span required="" aria-required="true">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="text" name="campodetalle[prima_anual]" id="primadetalle_articulo" class="form-control">
                                        </div>										
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                        <label>Deducible</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="text" name="campodetalle[deducible]" id="deducibledetalle_articulo" class="form-control">
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
                                    <input type="submit" name="campo[guardar]" value="Guardar" class="guardar_articulo btn btn-primary btn-block guardarArticulo guardarInteresSolicitud" id="campo[guardar]" />
                                </div>
                            </div>

                            <div class="tabladetalle_articulo" style="display:none">
                                <!-- JQGRID -->
                                <?php echo modules::run('intereses_asegurados/ocultotablaarticulo', $campos); ?>
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
