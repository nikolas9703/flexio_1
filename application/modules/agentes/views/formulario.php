<?php
$info = !empty($info) ? $info : array();
if(!empty($info['agente'])){
    if($info['agente']['letra'] == 'E' || $info['agente']['letra'] == 'N' || $info['agente']['letra'] == 'PE' || $info['agente']['letra'] == 'PI' || $info['agente']['letra'] == 0 ){
     $tipo_letra = "CE";
    }else{
     $tipo_letra=$info['agente']['letra'];
    }
}
?>
<div id="vistaAgente">
    <div class="tab-content">
        <div id="datosdelagente-5" class="tab-pane active">
            <?php echo form_open(base_url('agentes/guardar'), $info['form']); ?>
            <?php if (isset($info['agente'])) { ?>
                <input type="hidden" name="campo[uuid]" id="campo[uuid]"
                       value="<?php echo $info['agente']['uuid_agente'] ?>">
            <?php } ?>
            <div class="ibox">
                <div class="ibox-content m-b-sm" style="display: block; border:0px">
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2 nombreAgente"><label>Nombre
                                <span required="" aria-required="true">*</span></label>
                            <input type="text" name="campo[nombre]" id="campo[nombre]" value="<?php if (isset($info['agente'])) {
                                echo $info['agente']['nombre'];
                            } ?>" class="form-control" id="campo[nombre]" data-rule-required="true">
                        </div>
                            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2" >
                                <label>Identificaci&oacute;n <span required="" aria-required="true">*</span></label>
                                <select data-rule-required="true" class="form-control tipo_identificacion" name="campo[tipo_identificacion]">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($info['tipo_identificacion'] as  $tipo_identificacion) {  ?>
                                    <option <?php if(isset($tipo_letra)){ if($tipo_identificacion->key == $tipo_letra ) {echo ' selected';}} ?> value="<?php echo $tipo_identificacion->key ?>"><?php echo $tipo_identificacion->etiqueta ?></option>
                                    <?php }  ?>
                                </select>
                            </div>
                            <div class="noPAS">
                            <div class="form-group col-xs-6 col-sm-3 col-md-2 col-lg-2" >
                                <label>Provincia <span required="" aria-required="true">*</span></label>
                                <select data-rule-required="true" class="form-control provincia" name="campo[provincia]">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($info['provincias'] as  $provincia) {  ?>
                                    <option <?php if(isset($info['agente']['provincia'])){ if($provincia->key == $info['agente']['provincia'] ) {echo ' selected';} } ?> value="<?php echo $provincia->key ?>"><?php echo $provincia->etiqueta ?></option>
                                    <?php }  ?>
                                </select>
                            </div>                            
                            <div class="form-group col-xs-6 col-sm-6 col-md-2 col-lg-2" >
                                <label>Letras <span required="" aria-required="true">*</span></label>
                                <select data-rule-required="true" class="form-control letra" name="campo[letra]" id="campo[letra]">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($info['letras'] as  $letra) { ?>
                                        <option <?php if(isset($info['agente']['letra'])){ if($letra->key == $info['agente']['letra'] ) {echo ' selected';} } ?> value="<?php echo $letra->key ?>"><?php echo $letra->etiqueta ?></option>
                                    <?php }  ?>
                                </select>
                            </div>                            
                                <div class="form-group col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                    <label>Tomo <span required="" aria-required="true">*</span></label>
                                    <input data-rule-required="true" value="<?php if(isset($info['agente']['tomo'])){ echo $info['agente']['tomo']; } ?>" type="text" id="campo[tomo]" name="campo[tomo]" class="form-control">
                                </div>
                                <div class="form-group col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                    <label>Asiento <span required="" aria-required="true">*</span></label>
                                    <input data-rule-required="true" value="<?php if(isset($info['agente']['asiento'])){ echo $info['agente']['asiento']; } ?>" type="text" id="campo[asiento]" name="campo[asiento]" class="form-control">
                                </div>
                            </div>
                            <div class="RUC">
                            <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                    <label>Tomo <span required="" aria-required="true">*</span></label>
                                    <input data-rule-required="true" value="<?php if(isset($info['agente']['tomo_ruc'])){ echo $info['agente']['tomo_ruc']; } ?>" type="text" id="campo[tomo_ruc]" name="campo[tomo_ruc]" class="form-control">
                                </div>
                            <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                    <label>Folio/Imagen/Documento <span required="" aria-required="true">*</span></label>
                                    <input data-rule-required="true" value="<?php if(isset($info['agente']['folio'])){ echo $info['agente']['folio']; } ?>" type="text" id="campo[folio]" name="campo[folio]" class="form-control">
                                </div>
                            <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                    <label>Asiento/Ficha <span required="" aria-required="true">*</span></label>
                                    <input data-rule-required="true" value="<?php if(isset($info['agente']['asiento_ruc'])){ echo $info['agente']['asiento_ruc']; } ?>" type="text" id="campo[asiento_ruc]" name="campo[asiento_ruc]" class="form-control">
                                </div>
                            <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                    <label>Digito Verificador <span required="" aria-required="true">*</span></label>
                                    <input data-rule-required="true" value="<?php if(isset($info['agente']['digito'])){ echo $info['agente']['digito']; } ?>" type="text" id="campo[digito]" name="campo[digito]" class="form-control">
                                </div>    
                            </div>
                            <div class="PAS">
                                <div class="form-group col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                    <label>No. Pasaporte <span required="" aria-required="true">*</span></label>
                                    <input data-rule-required="true" value="<?php if(isset($info['agente']['pasaporte'])){ echo $info['agente']['pasaporte']; } ?>" type="text"  id="campo[pasaporte]" name="campo[pasaporte]" class="form-control">
                                </div>
                            </div>
                        
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2 ">
                            <label>Teléfono </label>
                            <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                <input type="input-left-addon" name="campo[telefono]"
                                       value="<?php if (isset($info['agente'])) {
                                           echo $info['agente']['telefono'];
                                       } ?>" class="form-control" data-inputmask="'mask': '999-9999', 'greedy':true"
                                       id="campo[telefono]"> </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2 ">
                            <label>Correo Electrónico</label>
                                <div class="input-group">
                                <span class="input-group-addon">@</span><input type="input-left-addon" name="campo[correo]"
                                       value="<?php if (isset($info['agente'])) {
                                           echo $info['agente']['correo'];
                                       } ?>" data-rule-email="true"
                                       class="form-control debito" id="campo[correo]"></div>
                            <label for="campo[correo]" generated="true" class="error"></label>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2 "><label>Participación</label>
                            <div class="input-group">
                                <input type="input-left-addon" name="campo[porcentaje_participacion]"
                                       value="<?php if (isset($info['agente'])) {
                                           echo $info['agente']['porcentaje_participacion'];
                                       } ?>" class="form-control" data-inputmask="'mask': '9{1,15}.99', 'greedy':true" id="campo[porcentaje_participacion]">
                                <span class="input-group-addon">%</span>
                            </div>
                            <label for="campo[porcentaje_participacion]" generated="true" class="error"></label>
                        </div>
                    </div>

                    <div class="row botones">
                        <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2" ><a
                                href="<?php echo base_url('agentes/listar'); ?>" class="btn btn-default btn-block"
                                id="cancelar">Cancelar </a></div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                            <input type="submit" name="campo[guardar]" value="Guardar "
                                   class="btn btn-primary btn-block" id="campo[guardar]">
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>