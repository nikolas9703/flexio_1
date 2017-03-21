
<div class="row">
    <div class="col-sm-6">
        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <label>Nombre <span aria-required="true" required="">*</span></label>
            <input aria-required="true" name="campo[nombre]" value="<?php echo !empty($campos["nombre"]) ? $campos["nombre"]:""?>" class="form-control" data-rule-required="true" id="campo[nombre]" type="text">
        </div>
        
        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <label>Código <span aria-required="true" required="">*</span></label>
            <input aria-required="true" name="campo[codigo]" value="<?php echo !empty($campos["codigo"]) ? $campos["codigo"]:""?>" class="form-control" data-rule-required="true" id="campo[codigo]" type="text">
        </div>
        
        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <label>Contacto principal <span aria-required="true" required="">*</span></label>
            <input aria-required="true" name="campo[contacto_principal]" value="<?php echo !empty($campos["contacto_principal"]) ? $campos["contacto_principal"]:""?>" class="form-control" data-rule-required="true" id="campo[contacto_principal]" type="text">
        </div>
        
        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <label>Teléfono <span aria-required="true" required="">*</span></label>
            <input aria-required="true" name="campo[telefono]" value="<?php echo !empty($campos["telefono"]) ? $campos["telefono"]:""?>" class="form-control" data-rule-required="true" id="campo[telefono]" type="text">
        </div>
        
        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <label>Dirección <span aria-required="true" required="">*</span></label>
            <input aria-required="true" name="campo[direccion]" value="<?php echo !empty($campos["direccion"]) ? $campos["direccion"]:""?>" class="form-control" data-rule-required="true" id="campo[direccion]" type="text">
        </div>
        
        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <label>Recepción de items <span aria-required="true" required="">*</span></label>
            <select aria-required="true" name="campo[entrada]" class="chosen" id="entrada" data-rule-required="true">
                <option value="">Seleccione</option>
                <?php foreach ($tipos_bodegas as $tipo_bodega):?>
                <option value="<?php echo $tipo_bodega->id_cat?>" <?php echo (!empty($campos["entrada"]) and $campos["entrada"] == $tipo_bodega->id_cat) ? "selected":""?>><?php echo $tipo_bodega->etiqueta?></option>
                <?php endforeach;?>
            </select>
        </div>
        
        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <label>Estado de items en bodega <span aria-required="true" required="">*</span></label>
            <select aria-required="true" name="campo[estado_items_bodega]" class="chosen" id="estado_items_bodega" data-rule-required="true">
                <option value="">Seleccione</option>
                <?php foreach ($estados_items_bodegas as $estado_item_bodega):?>
                <option value="<?php echo $estado_item_bodega->id_cat?>" <?php echo (!empty($campos["estado_items_bodega"]) and $campos["estado_items_bodega"] == $estado_item_bodega->id_cat) ? "selected":""?>><?php echo $estado_item_bodega->etiqueta?></option>
                <?php endforeach;?>
            </select>
        </div>
        
        <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6" style="display: none;">
            <label>Padre <span aria-required="true" required="">*</span></label><br>
            <select aria-required="true" name="campo[padre]" id="padre" data-rule-required="true">
                <option value="0">Sin padre</option>
                <?php foreach ($bodegas_sin_entradas as $bodega_sin_entrada):?>
                <option value="<?php echo $bodega_sin_entrada->id?>" <?php echo (!empty($campos["padre"]) and $campos["padre"] == $bodega_sin_entrada->id) ? "selected":""?>><?php echo $bodega_sin_entrada->codigo?> <?php echo $bodega_sin_entrada->nombre?></option>
                <?php endforeach;?>
            </select>
        </div>
    </div>
    
    <div class="col-sm-6">
        <div id="jstree_bodegas"></div>
    </div>
</div>

<div class="row">
    <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
    
    <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
        <a href="<?php echo base_url("bodegas/listar")?>" class="btn btn-default btn-block" id="cancelarBodega">Cancelar </a> 
    </div>
    
    <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
        <input name="campo[guardarBodega]" value="Guardar " class="btn btn-primary btn-block" id="campo[guardarBodega]" type="submit">
    </div>
</div>
