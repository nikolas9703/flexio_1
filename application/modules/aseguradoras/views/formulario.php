<?php
$formAttr = array(
    'method' => 'POST',
    'id' => 'formClienteCrear',
    'autocomplete' => 'off'
);
?>
    <div id="vistaCliente" class="">

        <div class="tab-content">
            <?php echo form_open(base_url('seguros_aseguradoras/guardar'), $formAttr); ?>
            <div id="datosdelcliente-5" class="tab-pane active col-lg-12 col-md-12">
                    <input type="hidden" name="campo[uuid]" id="campo[uuid]" value="">
       
                <div class="ibox">  
                    <div class="ibox-content" style="display: block;" id="datosGenerales">
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6  "><label>Nombre Aseguradora <span required="" aria-required="true">*</span></label>
                                <input type="text" name="campo[nombre]" value="{{clienteInfo.nombre}}" class="form-control" id="campo[nombre]" data-rule-required="true" >
                            </div>
							<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3  "><label>RUC  <span required="" aria-required="true">*</span></label>
                                <input type="text" name="campo[ruc]" value="{{clienteInfo.ruc}}" class="form-control" id="campo[nombre]" data-rule-required="true" >
                            </div>
							<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                                <label>Teléfono <span required="" aria-required="true">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                    <input type="input-left-addon" name="campo[telefono]" v-model="clienteInfo.telefono" class="form-control" data-inputmask="'mask': '999-99999', 'greedy':true" id="campo[telefono]" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 ">
                                <label>Correo Electrónico <span required="" aria-required="true">*</span></label><div class="input-group">
                                    <span class="input-group-addon">@</span>
                                    <input type="input-left-addon" name="campo[correo]" v-model="clienteInfo.correo" data-rule-required="true" data-rule-email="true" class="form-control debito"  id="campo[correo]" >
                                </div>
                                <label  for="campo[correo]" generated="true" class="error"></label>
                            </div>
							<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3" >
                                <label>Tomo/Rollo</label>
                                <input type="text" name="campo[tomo]" v-model="clienteInfo.tomo" class="form-control" id="campo[tomo]" >
                            </div>
							<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3" >
                                <label>Folio/Imágen/Documento</label>
                                <input type="text" name="campo[folio]" v-model="clienteInfo.folio" class="form-control" id="campo[folio]" >
                            </div>
							<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3" >
                                <label>Asiento/Ficha</label>
                                <input type="text" name="campo[asiento]" v-model="clienteInfo.ficha" class="form-control" id="campo[asiento]" >
                            </div>
                        </div>
						<div class="row">
							<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 Dirección" >
                                <label>Digito Verificador</label>
                                <input type="text" name="campo[digveri]" v-model="clienteInfo.digveri" class="form-control" id="campo[digveri]" >
                            </div>
							<div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3 Dirección" >
                                <label>Dirección</label>
                                <input type="text" name="campo[direccion]" v-model="clienteInfo.direccion" class="form-control" id="campo[direccion]" >
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3" >
                                <label>Estado</label>
								<select name="campo[estado]" id="campo[estado]" class="form-control">
								<option value=''></option>
								<option value='por_aprobar'>Por Aprobar</option>
								<option value='Activo'>Activo</option>
								<option value='Inactivo'>Inactivo</option>
								</select>
                            </div>
                        </div>
                    </div>
				</div>
            </div>           
            <div class="row"> <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2"><a href="<?php echo base_url('seguros_aseguradoras/listar'); ?>" class="btn btn-default btn-block" id="cancelar">Cancelar </a> </div>
                <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                    <input type="submit" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block" id="campo[guardar]" :disabled="disabledSubmit">
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
