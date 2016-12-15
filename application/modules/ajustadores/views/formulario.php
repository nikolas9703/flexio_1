<?php
$info['form'] = array(
'method' => 'POST',
'id' => 'formAjustadores',
'autocomplete' => 'off'
);
$info = !empty($info) ? $info : array();
?>
<div id="vistaAjustadores">
    <div class="tab-content">
        <div id="datosdelAjustadores-5" class="tab-pane active">
            <?php echo form_open(base_url('ajustadores/guardar'), $info['form']); ?>
            <input type="hidden" name="campo[id]" id="campo[id]" class="id" value="" />
     
            <div class="ibox">
                <div class="ibox-title">
                <h5>Datos del ajustador</h5>
                    <hr style="margin-top:30px!important;">    
                </div>
                <div class="ibox-content m-b-sm" style="display: block; border:0px">                    
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2 nombreAjustadores"><label>Nombre del Ajustador
                                <span required="" aria-required="true">*</span></label>
                            <input type="text" name="campo[nombre]" id="campo[nombre]" value="<?php if (isset($info['ajustadores'])) {
                                echo $info['ajustadores']['nombre'];
                            } ?>" class="form-control nombre" id="campo[nombre]" data-rule-required="true">
                        </div>
                            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2" >
                                <label>Identificaci&oacute;n <span required="" aria-required="true">*</span></label>                               
                                <select data-rule-required="true" class="form-control identificacion" name="campo[identificacion]">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($tipo_identificacion as  $tipo_iden) {  ?>
                                    <option <?php if(isset($info['ajustadores']['letra'])){ if($tipo_iden->key == $info['ajustadores']['letra'] ) {echo ' selected';}} ?> value="<?php echo $tipo_iden->id_cat ?>"><?php echo $tipo_iden->etiqueta ?></option>
                                    <?php }  ?>
                                </select>
                            </div>
                            <div class="noPAS">
                            <div class="form-group col-xs-6 col-sm-3 col-md-2 col-lg-2" >
                                <label>Provincia <span required="" aria-required="true">*</span></label>
                                <select data-rule-required="true" class="form-control provincia" name="campo[provincia]">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($info['provincias'] as  $provincia) {  ?>
                                    <option <?php if(isset($info['ajustadores']['provincia'])){ if($provincia->key == $info['ajustadores']['provincia'] ) {echo ' selected';} } ?> value="<?php echo $provincia->id_cat ?>"><?php echo $provincia->etiqueta ?></option>
                                    <?php }  ?>
                                </select>
                            </div>                            
                            <div class="form-group col-xs-6 col-sm-3 col-md-2 col-lg-2" >
                                <label>Letras <span required="" aria-required="true">*</span></label>
                                <select data-rule-required="true" class="form-control letra" name="campo[letra]" id="campo[letra]">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($info['letras'] as  $letra) { ?>
                                        <option <?php if(isset($info['ajustadores']['letra'])){ if($letra->key == $info['ajustadores']['letra'] ) {echo ' selected';} } ?> value="<?php echo $letra->etiqueta ?>"><?php echo $letra->etiqueta ?></option>
                                    <?php }  ?>
                                </select>
                            </div>                            
                                <div class="form-group col-xs-6 col-sm-3 col-md-2 col-lg-2">
                                    <label>Tomo <span required="" aria-required="true">*</span></label>
                                    <input data-rule-required="true" value="<?php if(isset($info['ajustadores']['tomo'])){ echo $info['ajustadores']['tomo']; } ?>" type="text" id="campo[tomo]" name="campo[tomo]" class="form-control tomo_cedula">
                                </div>
                                <div class="form-group col-xs-6 col-sm-3 col-md-2 col-lg-2">
                                    <label>Asiento <span required="" aria-required="true">*</span></label>
                                    <input data-rule-required="true" value="<?php if(isset($info['ajustadores']['asiento'])){ echo $info['ajustadores']['asiento']; } ?>" type="text" id="campo[asiento]" name="campo[asiento]" class="form-control asiento_cedula">
                                </div>
                            </div>
                            <div class="RUC">
                            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                    <label>Tomo <span required="" aria-required="true">*</span></label>
                                    <input data-rule-required="true" value="<?php if(isset($info['ajustadores']['tomo_ruc'])){ echo $info['ajustadores']['tomo_ruc']; } ?>" type="text" id="campo[tomo_ruc]" name="campo[tomo_ruc]" class="form-control tomo_ruc">
                                </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                    <label>Folio/Imagen/Documento <span required="" aria-required="true">*</span></label>
                                    <input data-rule-required="true" value="<?php if(isset($info['ajustadores']['folio'])){ echo $info['ajustadores']['folio']; } ?>" type="text" id="campo[folio]" name="campo[folio]" class="form-control folio_ruc">
                                </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                    <label>Asiento/Ficha <span required="" aria-required="true">*</span></label>
                                    <input data-rule-required="true" value="<?php if(isset($info['ajustadores']['asiento_ruc'])){ echo $info['ajustadores']['asiento_ruc']; } ?>" type="text" id="campo[asiento_ruc]" name="campo[asiento_ruc]" class="form-control asiento_ruc">
                                </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                    <label>Digito Verificador <span required="" aria-required="true">*</span></label>
                                    <input data-rule-required="true" value="<?php if(isset($info['ajustadores']['digito'])){ echo $info['ajustadores']['digito']; } ?>" type="text" id="campo[digito]" name="campo[digito]" class="form-control digito_ruc">
                                </div>    
                            </div>
                            <div class="PAS">
                                <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                    <label>No. Pasaporte <span required="" aria-required="true">*</span></label>
                                    <input data-rule-required="true" value="<?php if(isset($info['ajustadores']['pasaporte'])){ echo $info['ajustadores']['pasaporte']; } ?>" type="text"  id="campo[pasaporte]" name="campo[pasaporte]" class="form-control pasaporte">
                                </div>
                            </div>
                        
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-4 ">
                            <label>Teléfono <span required="" aria-required="true">*</span></label>
                                <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-phone"></i></span><input type="input-left-addon" name="campo[telefono]"
                                       value="<?php if (isset($info['ajustadores'])) {
                                           echo $info['ajustadores']['telefono'];
                                       } ?>" class="form-control telefono" data-inputmask="'mask': '999-9999', 'greedy':true"
                                       id="campo[telefono]" data-rule-required="true">
                                </div></div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-4 ">
                            <label>E-mail</label>
                                <div class="input-group">
                                <span class="input-group-addon">@</span><input type="input-left-addon" name="campo[email]"
                                       value="<?php if (isset($info['ajustadores'])) {
                                           echo $info['ajustadores']['correo'];
                                       } ?>" data-rule-email="true"
                                       class="form-control correo" id="campo[email]">
                                </div>
                            <label for="campo[email]" generated="true" class="error"></label>
                                </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-4 "><label>Direcci&oacute;n</label>
                           
                                <input type="input-left-addon" name="campo[direccion]"
                                       value="<?php if (isset($info['ajustadores'])) {
                                           echo $info['ajustadores']['porcentaje_participacion'];
                                       } ?>" class="form-control direccion" id="campo[direccion]">
                            <label for="campo[porcentaje_participacion]" generated="true" class="error"></label>
                        </div>
                    </div>

                    <div class="row botones">
                        <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2" ><a
                                href="<?php echo base_url('ajustadores/listar'); ?>" class="btn btn-default btn-block"
                                id="cancelar">Cancelar </a></div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                            <input type="submit" name="campo[guardar]" value="Guardar "
                                   class="btn btn-primary btn-block enviar" id="campo[guardar]">
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
        <div class="row" ng-controller="ContactosFormularioController">
        <div id="form_contacto" class="tab-pane active" style="display:none;">
            <?php echo form_open(base_url('ajustadores/guardar_contacto'), "id='formulario_contacto'"); ?>
            <input type="hidden" name="campo[id_contacto]" id="campo[id]" class="id" value="" />
            <input type="hidden" name="campo[ajustador_id]" id="ajustador_id" class="ajustador_id" value="" />
     
            <div class="ibox">
                <div class="ibox-title">
                <h5>Datos del contacto</h5>
                    <hr style="margin-top:30px!important;">    
                </div>
                <div class="ibox-content m-b-sm" style="display: block; border:0px">
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3 nombreContacto"><label>Nombre</label>
                            <input type="text" name="campo2[nombre]" class="form-control nombreContacto" id="nombreContacto">
                        </div>
                         <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3 apellidoContacto"><label>Apellido</label>
                            <input type="text" name="campo2[apellido]" class="form-control apellidoContacto" id="apellidoContacto">
                        </div> 
                        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3 cargoContacto"><label>Cargo</label>
                            <input type="text" name="campo2[cargo]" class="form-control cargoContacto" id="cargoContacto">
                        </div>   
                     <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                            <label>Teléfono</label>
                                <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-phone"></i></span><input type="input-left-addon" name="campo2[telefono]" class="form-control telefonoContacto" data-inputmask="'mask': '999-9999', 'greedy':true"
                                       id="telefonoContacto">
                                </div></div>
                    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                            <label>Celular</label>
                                <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-mobile"></i></span><input type="input-left-addon" name="campo2[celular]" class="form-control celularContacto" data-inputmask="'mask': '9999-9999', 'greedy':true"
                                       id="celularContacto">
                                </div></div>
                   <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                            <label>E-mail</label>
                                <div class="input-group">
                                <span class="input-group-addon">@</span><input type="input-left-addon" name="campo2[email]" data-rule-email="true"
                                       class="form-control correoContacto" id="emailContacto">
                                </div>                            
                                </div>     
                    </div>
                    <div class="row botones">
                        <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2" ><a
                                href="#" class="btn btn-default btn-block volver"
                                id="cancelar">Cancelar </a></div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                            <a type="button" name="campo2[guardar]" class="btn btn-primary btn-block enviarContacto" id="campo2[guardar]">Guardar</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
    </div>
</div>