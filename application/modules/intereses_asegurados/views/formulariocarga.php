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
                    <input type="hidden" name="campo[uuid]" id="campo[uuid]" class="uuid_carga" value="<?php if(isset($campos['uuid']))echo $campos['uuid']?>">
                    <input type="hidden" name="campo[tipo_id]" id="tipo_id_carga" class="tipo_id" value="6">
                    <div class="ibox">
                        <div class="ibox-content m-b-sm" style="display: block; border:0px">

                            <div class="row">                                   
                                <div class="form-group col-xs-12 col-sm-3 col-md-34 col-lg-3"><label>No. de Liquidación<span required="" aria-required="true">*</span>
                                    </label>
                                    <input type="text" name="campo[no_liquidacion]" id="no_liquidacion" value="<?php if(isset($campos['datos']['no_liquidacion']))echo $campos['datos']['no_liquidacion']?>" class="form-control" data-rule-required="true">
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Fecha de Despacho<span required="" aria-required="true" >*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>   
                                        <input type="text" name="campo[fecha_despacho]" id="fecha_despacho" value="<?php if(isset($campos['datos']['fecha_despacho']))echo $campos['datos']['fecha_despacho']?>" class="form-control fecha_despacho" data-rule-required="true"></div>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Fecha de Arribo<span required="" aria-required="true">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>   
                                        <input type="text" name="campo[fecha_arribo]" id="fecha_arribo" value="<?php if(isset($campos['datos']['fecha_arribo']))echo $campos['datos']['fecha_arribo']?>" class="form-control fecha_arribo" data-rule-required="true"></div>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Detalle Mercancia
                                    </label>
                                    <input type="text" name="campo[detalle]" id="detalle" value="<?php if(isset($campos['datos']['detalle']))echo $campos['datos']['detalle']?>" class="form-control">
                                </div>                                
                            </div>

                            <div class="row">
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Valor de la Mercancia<span required="" aria-required="true">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>   
                                        <input type="number" min="0" max="99999999" name="campo[valor]" id="valor" value="<?php if(isset($campos['datos']['valor']))echo $campos['datos']['valor']?>" class="form-control valor_mercancia" data-rule-required="true" >
                                    </div>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Tipo de Empaque<span required="" aria-required="true">*</span></label>
                                    <select class="form-control " name="campo[tipo_empaque]" data-rule-required="true">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($tipo_empaque as $empaque) { ?>
                                            <option value="<?php echo $empaque->id_cat ?>" <?php if(isset($campos['datos']['tipo_empaque']))if($campos['datos']['tipo_empaque']==$empaque->id_cat) echo "selected";?>  ><?php echo $empaque->etiqueta ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Condición de Envío<span required="" aria-required="true">*</span></label>
                                    <select class="form-control condicion_envio" name="campo[condicion_envio]" data-rule-required="true">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($condicion_envio as $cond_env) { ?>
                                            <option value="<?php echo $cond_env->id_cat ?>" <?php if(isset($campos['datos']['condicion_envio']))if($campos['datos']['condicion_envio']==$cond_env->id_cat) echo "selected";?> ><?php echo $cond_env->etiqueta ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Medio Transporte<span required="" aria-required="true">*</span></label>
                                    <select class="form-control medio_transporte" name="campo[medio_transporte]" data-rule-required="true">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($medio_transporte as $medt) { ?>
                                            <option value="<?php echo $medt->id_cat ?>" <?php if(isset($campos['datos']['medio_transporte']))if($campos['datos']['medio_transporte']==$medt->id_cat) echo "selected";?> ><?php echo $medt->etiqueta ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Origen<span required="" aria-required="true">*</span>
                                    </label>
                                    <input type="text" name="campo[origen]" id="detalle_mercancia" value="<?php if(isset($campos['datos']['origen']))echo $campos['datos']['origen']?>" class="form-control" data-rule-required="true">
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3"><label>Destino<span required="" aria-required="true" >*</span>
                                    </label>
                                    <input type="text" name="campo[destino]" id="detalle_mercancia" value="<?php if(isset($campos['datos']['destino']))echo $campos['datos']['destino']?>" class="form-control" data-rule-required="true">
                                </div>
                                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2"><label>Acreedor<span required="" aria-required="true">*</span></label>
                                    <select class="form-control acreedor_carga" name="campo[acreedor]" data-rule-required="true">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($acreedores as  $acre) { ?>
                                        <option value="<?php echo $acre->id ?>" <?php if(isset($campos['datos']['acreedor'])){ if($acre->id == $campos['datos']['acreedor'] ) {echo ' selected';}} ?> ><?php echo $acre->nombre ?></option>
                                        <?php }  ?>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>
                                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1"><label></label>
                                    <input type="text" class="form-control" value="" name="campo[acreedor_opcional]" id="acreedor_carga_opcional" disabled/>
                                </div>
                                <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2"><label>Tipo de obligaci&oacute;n<span required="" aria-required="true" >*</span></label>
                                    <select class="form-control tipo_obligacion" name="campo[tipo_obligacion]" data-rule-required="true">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($tipo_obligacion as  $obligacion) { ?>
                                        <option value="<?php echo $obligacion->id_cat ?>" <?php if(isset($campos['datos']['tipo_obligacion'])){ if($obligacion->id_cat == $campos['datos']['tipo_obligacion'] ) {echo ' selected';}} ?>  ><?php echo $obligacion->etiqueta ?></option>
                                        <?php }  ?>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>
                                <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1"><label></label>
                                    <input type="text" class="form-control" value="" name="campo[tipo_obligacion_opcional]" id="tipo_obligacion_opcional" disabled/>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-6 "><label>Observaciones</label>
                                    <textarea name="campo[observaciones]"
                                              class="form-control" id="observaciones_carga"><?php if(isset($campos['datos']['observaciones']))echo $campos['datos']['observaciones']?></textarea>
                                    <label for="campo[observaciones]" generated="true" class="error"></label>
                                </div>

                                 <?php if(isset($cambiarEstado)){print_r($cambiarEstado);} ?>

                                <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3" >
                                    <label>Estado </label>
                                    <select class="form-control estado_carga" name="campo[estado]">
                                            <!--<?php foreach ($estado as $status) { ?>
                                                <option value="<?php echo $status->etiqueta ?>" <?php if ($campos['datos']['condicion'] == $status->etiqueta) echo "selected" ?>><?php echo $status->etiqueta ?></option>
                                            <?php } ?>-->
                                        <option value="1" <?php if(isset($campos['datos']['estado'])){ if($campos['datos']['estado']==1){ echo "selected";} }  ?> >Activo</option>
                                        <option value="2" <?php if(isset($campos['datos']['estado'])){ if($campos['datos']['estado']==2){ echo "selected";} }  ?> >Inactivo</option>
                                    </select>
                                </div>                                   
                            </div>
                            <br><br>
                            <div class="row">                                
                                <div class="form-group col-xs-12 col-sm-3 col-md-12 col-lg-12" >
                                    <h5>Documentos entregados</h5><hr style="margin-top:30px!important;">
                                </div>
                                <div class="form-group col-xs-12 col-sm-3 col-md-12 col-lg-12" >
                                    <label>Nombre del documento</label>
                                    <div class="row">
                                        <div class='file_upload_carga' id='f1'>
                                            <input class="form-control" style="width: 300px!important; float: left;" name="nombre_documento[]" type="text" id="nombre_documento" />
                                            <input style="width: 300px!important; float: left;" class="form-control" name='file[]' type='file'/>
                                        </div>
                                        <div id='file_tools_carga' style="width: 90px!important; float: left;">
                                            <button type="button" class="btn btn-default btn-block" style="float: left; width: 40px; margin-right:5px;" id="add_file_carga"><i class="fa fa-plus"></i></button>
                                            <button type="button" style="float: left; width: 40px; margin-top:0px!important;" class="btn btn-default btn-block" id="del_file_carga"><i class="fa fa-trash"></i></button>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>

                            <div class="row">
                                <div class="row botones">
                                    <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                                    <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2" >
                                        <a href="<?php echo base_url('intereses_asegurados/listar'); ?>" class="btn btn-default btn-block" id="cancelar">Cancelar </a>
                                    </div>
                                    <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                                        <input type="submit" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block guardarCarga" id="campo[guardar]">
                                    </div>
                                </div>
                            </div>
                                
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div> 
</div>
<div class="alert alert-warning" ng-if="notificacion.campos.length || notificacion.limiteCapacidadAlcanzado.length">
    <div ng-if="notificacion.campos.length">
        <b>No es posible crear un nuevo descuento para este colaborador.</b><br> <b>Los siguientes datos deben ser completados en detalle del colaborador:</b>
        <ul>
            <li ng-repeat="notificacion in notificacion.campos track by $index" ng-bind-html="notificacion"></li>
        </ul>
    </div>
    <div ng-if="notificacion.limiteCapacidadAlcanzado.length" ng-bind-html="notificacion.limiteCapacidadAlcanzado">
    </div>
</div>
