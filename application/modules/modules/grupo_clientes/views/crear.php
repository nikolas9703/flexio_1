<?php
$formAttr = array(
    'method' => 'POST',
    'id' => 'crearGrupoClienteForm',
    'autocomplete' => 'off'
);
echo form_open("", $formAttr);
?>
<div class="ibox">
    <div style="display: block; border:0px" class="ibox-content m-b-sm">
        <div class="row">
            <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 GrupoClientes&lt;spanrequired&gt;*&lt;/span&gt; "><label>Nombre del Grupo de <span required="" aria-required="true">*</span></label><input type="text" id="campo[nombre]" data-rule-required="true" class="form-control" value="" name="campo[nombre]">
            </div>
            <!-- <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 "><label>Agrupador </label>
              <div class="input-group">
                    <span class="input-group-addon">
                        <input type="checkbox" id="padre_idCheck">
                    </span>
                    <select data-placeholder="Seleccione" class="form-control" id="select-id" name="campo[padre_id]" disabled=""><option value="">Seleccione</option><option value="2">Pensanomica</option><option value="3">Centro 3</option><option value="10">Smithsonian</option><option value="11">Boulevard Centennial Plaza</option><option value="15">Distribuidora de Autos RP</option><option value="25">Brookstone</option><option value="31">Olympic Mall</option><option value="34">Nike Store</option></select>
                </div>
            </div> -->
            <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 "><label>Descripcion </label><input type="text" id="campo[descripcion]" class="form-control" value="" name="campo[descripcion]">
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close(); ?>


