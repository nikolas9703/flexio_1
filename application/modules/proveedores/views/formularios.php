<?php
$info = !empty($info) ? $info : array();
$campos = !empty($campos) ? $campos : array();
//Template::cargar_formulario($info);
$formAttr = array(
    'method' => 'POST',
    'id' => 'crearProveedoresForm',
    'autocomplete' => 'off'
);
?>
    <div id="vistaProveedor" class="">
        <div class="tab-content">
            <?php echo form_open(base_url('proveedores/guardar'), $formAttr); ?>
            <div id="datosproveedor" class="tab-pane active col-lg-12 col-md-12">
                <?php if (isset($info['proveedor'])) { ?>
                    <input type="hidden" name="campo[uuid]" id="campo[uuid]" value="<?php echo $info['proveedor']['uuid_proveedor'] ?>">
                <?php } ?>
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Datos del proveedor</h5>
                    </div>
                    <div class="ibox-content" style="display: block;">
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 NombredelProveedor "><label>Nombre <span required="" aria-required="true">*</span></label>
                                <input type="text" name="campo[nombre]" value="" v-model="proveedor.nombre" class="form-control" id="campo[nombre]" data-rule-required="true">
                            </div>

                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                                <label>Teléfono </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                    <input type="input-left-addon" name="campo[telefono]" value="" v-model="proveedor.telefono" class="form-control" data-inputmask="'mask': '999-99999', 'greedy':true" id="campo[telefono]">
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                                <label>Correo Electrónico <span required="" aria-required="true">*</span></label><div class="input-group">
                                    <span class="input-group-addon">@</span>
                                    <input type="input-left-addon" name="campo[email]" value="" data-rule-required="true" data-rule-email="true" v-model="proveedor.email" class="form-control debito"  id="campo[email]">
                                </div>
                                <label  for="campo[email]" generated="true" class="error"></label>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                                <label>Tipo <span required="" aria-required="true">*</span></label>
                                <select data-rule-required="true" name="campo[tipo_id]" v-model="proveedor.tipo_id" class="form-control" id="tipo_id">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($info['tipos'] as $tipo) { ?>
                                        <option value="<?php echo $tipo["id"] ?>"><?php echo $tipo["nombre"]; ?></option>
                                   <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
                                <?php //echo dd($info['catSelect'], $info['categorias']->toArray());?>
                                <label>Categoría(s) <span required="" aria-required="true">*</span></label>
                                <select name="campo[categorias][]" class="chosen categorias" id="categorias" data-rule-required="true" data-placeholder="Seleccione" multiple="multiple" aria-required="true" style="display: none;">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($info['categorias'] as $categoria) { ?>
                                        <option value="<?php echo $categoria->id?>" <?php echo ((isset($info['catSelect'])) && in_array($categoria->id, $info['catSelect'])) ? ' selected ' : ''?>><?php echo $categoria->nombre;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                                <label>Estado <span required="" aria-required="true">*</span></label>
                                <select name="campo[estado]" class="chosen" id="estado" data-rule-required="true" aria-required="true" :disabled="acceso == '' || cambio_estado == 0">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($info['estados'] as $estados) { ?>
                                        <option <?php if($estados->valor =='por_aprobar'){echo "selected='selected'";}?>value="<?php echo $estados->valor?>"><?php echo $estados->etiqueta;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6 Dirección ">
                                <label>Dirección </label>
                                <input type="text" name="campo[direccion]" value="" class="form-control" v-model="proveedor.direccion" id="campo[direccion]">
                            </div>
                        </div>
                        <!--seccion de identificacion del cliente-->
                        <div class="row">
                            <!-- ID Component Start -->
                            <identificacion :config.sync="config" :detalle.sync="proveedor"></identificacion>
        					<!-- ID Component End -->
                            <br>
                        </div>
                        <!--/seccion de identificacion del cliente-->
                        <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <h4 class="m-b-xs">Información de pago <span required="" aria-required="true">*</span></h4>
                                <div class="hr-line-dashed m-t-xs"></div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
                                <label>Método preferido de pago </label>
                                <select name="campo[forma_pago][]" class="chosen" id="forma_pago" data-placeholder="Seleccione" style="display: none;">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($info['formaPago'] as $formaPago) { ?>
                                        <option value="<?php echo $formaPago->id?>" <?php echo ((isset($info['pagosSelect'])) && in_array($formaPago->id, $info['pagosSelect'])) ? ' selected ' : ''?>><?php echo $formaPago->valor;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 Términosdepago ">
                                <label>Términos de pago </label>
                                <select name="campo[termino_pago_id]"  class="chosen" id="termino_pago_id" style="display: none;">
                                    <option value="" selected="selected">Seleccione</option>
                                    <?php foreach ($info['terminoPago'] as $terminoPago) { ?>
                                        <option <?php if(isset($info['terminoPagoSelect'])){if ($info['terminoPagoSelect'] == $terminoPago->id_cat) echo "selected='selected'";}?> value="<?php echo $terminoPago->id_cat ?>"><?php echo $terminoPago->etiqueta ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row hide campos_metodo_ach">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
                                <label>Banco </label>
                                <select name="campo[banco]" class="chosen" id="banco" style="display: none;">
                                    <option value="" selected="selected">Seleccione</option>
                                    <?php foreach ($info['bancos'] as $bancos) { ?>
                                        <option <?php if(isset($info['bancoSelect'])){if ($info['bancoSelect'] == $bancos->id) echo "selected='selected'";}?> value="<?php echo $bancos->id ?>"><?php echo $bancos->nombre ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 Tipodecuenta ">
                                <label>Tipo de cuenta </label>
                                <select name="campo[tipo_cuenta]" class="chosen" id="tipo_cuenta" style="display: none;">
                                    <option value="" selected="selected">Seleccione</option>
                                    <?php foreach ($info['tipoCuenta'] as $tipoCuenta) { ?>
                                        <option <?php if(isset($info['tipoCuentaSelect'])){if ($info['tipoCuentaSelect'] == $tipoCuenta->id_cat ) echo "selected='selected'";}?> value="<?php echo $tipoCuenta->id_cat  ?>"><?php echo $tipoCuenta->etiqueta ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 Númerodecuenta ">
                                <label>Número de cuenta </label>
                                <input type="text" name="campo[numero_cuenta]" v-model="proveedor.numero_cuenta" class="form-control" data-inputmask="'mask':'9{0,20}','greedy':false" id="campo[numero_cuenta]">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
                                <label>Límite de crédito de compras </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                    <input type="text" name="campo[limite_credito]" v-model="proveedor.limite_credito" class="form-control" data-inputmask="'alias':'currency','prefix':'','autoUnmask':true,'removeMaskOnSubmit':true" id="campo[limite_credito]">
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 RetieneImpuestos ">
                                <label>Retiene Impuestos </label>
                                <select name="campo[retiene_impuesto]" v-model="proveedor.retiene_impuesto" class="chosen" id="retiene_impuesto">
                                </select>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 Acreedor ">
                                <label>Acreedor </label>
                                <select name="campo[acrededor]" class="chosen" id="acrededor" style="display: none;">
                                    <option value="" selected="selected">Seleccione</option>
                                    <option <?php if(isset($info['acreedor'])){if ($info['acreedor'] == "SI" ) echo "selected='selected'";}?>value="SI">Si</option>
                                    <option <?php if(isset($info['acreedor'])){if ($info['acreedor'] == "NO" ) echo "selected='selected'";}?> value="NO">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="row"> <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2"><a href="<?php echo base_url('proveedores/listar');?>" class="btn btn-default btn-block" id="cancelarProveedor">Cancelar </a>
                            </div>
                            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2"><input type="submit" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block" id="guardarProveedor">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
