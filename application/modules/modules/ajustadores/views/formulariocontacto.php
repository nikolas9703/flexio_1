<?php
$formAttr = array(
    'method' => 'POST',
    'id' => 'formAjustadoresCrearContacto',
    'autocomplete' => 'off'
);
?>
<div id="vistaCliente" class="">
    <div class="tab-content">
        <?php
        echo form_open(base_url('ajustadores/agregarcontacto'), $formAttr);
        if (isset($campos['opt']))
            $opt = $campos['opt'];
        else
            $opt = "";
        ?>
        <div id="datosdelaaseguradora-5" class="col-lg-12">
            <input type="hidden" name="campo[uuid_ajustadores]" id="campo[uuid_ajustadores]" value="<?php echo $campos['uuid_ajustadores'] ?>" />
            <input type="hidden" name="campo[uuid]" id="campo[uuid]" />
            <input type="hidden" name="campo[opt]" id="campo[opt]" value="<?php echo $opt ?>" />
            <div class="ibox">  
                <div class="ibox-title">
                    <h5>Datos del Contacto <?php echo $campos['nombre'] ?></h5>
                    <div style="width: 8%;float: right;padding: 0px;" id="impresioncontacto">
                        <input style="height: 31px;" type="button" name="campo[imprimirContacto]" value="Imprimir " class="btn btn-primary btn-block" id="campo[imprimirContacto]" :disabled="disabledSubmit">
                    </div>
                </div>
                <div class="ibox-content " style="display: block;" id="datosGenerales" >
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3">
                            <label>Nombre<span required="" aria-required="true">*</span></label>
                            <input type="text" name="campo[nombre]" class="form-control nombre" id="campo[nombre]" data-rule-required="true"  />

                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-3 col-lg-3  ">
                            <label>Cargo</label>
                            <input type="text" name="campo[cargo]" class="form-control cargo" id="campo[cargo]" />

                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                            <label>Teléfono <span required="" aria-required="true">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                <input type="input-left-addon" name="campo[telefono]" class="form-control telefono" data-inputmask="'mask': '999-99999', 'greedy':true" id="campo[telefono]" data-rule-required="true"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                            <label>Celular </label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-phone-square"></i></span>
                                <input type="input-left-addon" name="campo[celular]" class="form-control celular" data-inputmask="'mask': '999-99999', 'greedy':true" id="campo[celular]" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                            <label>Correo electrónico<span required="" aria-required="true">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">@</span>
                                <input type="input-left-addon" name="campo[email]" data-addon-text="@" class="form-control email"  id="campo[email]" data-rule-required="true"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>     
        <?php
        $url = "";
        if ($opt != 1) {
            $url = base_url('ajustadores/editar/' . $campos['uuid_ajustadores']);
        } else {
            $url = base_url('ajustadores/listar');
        }
        ?>
        <div class="row"> <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2"><a href="<?php echo $url; ?>" class="btn btn-default btn-block" id="cancelar">Cancelar </a> </div>
            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                <input type="submit" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block" id="campo[guardar]" :disabled="disabledSubmit">
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
