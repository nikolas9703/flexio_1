


<div class="ibox-content m-b-sm" style="display: block; border:0px">
    <div class="row"><div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 Nombre<spanrequired>*</span> "><label>Nombre <span aria-required="true" required="">*</span></label><input aria-required="true" name="campo[nombre]" value="<?php echo isset($campos["nombre"]) ? $campos["nombre"] : ""?>" class="form-control" data-rule-required="true" id="campo[nombre]" type="text">
        </div><div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 Teléfono<spanrequired>*</span> "><label>Teléfono <span class="span_requerido" aria-required="true" required="">*</span></label><input aria-required="true" name="campo[telefono]" value="<?php echo isset($campos["telefono"]) ? $campos["telefono"] : ""?>" class="form-control telefono" data-rule-required="true" id="campo[telefono]" type="text">
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 E-mail ">
            <label>E-mail </label>
            <input name="campo[email]" value="<?php echo isset($campos["email"]) ? $campos["email"] : ""?>" class="form-control validEmailFormat" id="campo[email]" type="text">
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Categor&iacute;as <span class="span_requerido" aria-required="true" required="">*</span></label>
            <select aria-required="true" name="campo[categorias][]" class="chosen categorias" id="categorias" data-rule-required="true" data-placeholder="Seleccione" multiple="multiple">
                <?php foreach ($categorias as $categoria):?>
                <option value="<?php echo $categoria->id?>" <?php echo (isset($campos["categorias"]) && in_array($categoria->id, $campos["categorias"])) ? 'selected=""':''?>><?php echo $categoria->nombre;?></option>
                <?php endforeach;?>
            </select>
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Tipo <span aria-required="true" required="">*</span></label>
            <select aria-required="true" style="display: none;" name="campo[tipo]" class="chosen" id="tipo" data-rule-required="true">
                <option value="">Seleccione</option>
                <?php foreach ($tipos as $tipo):?>
                <option value="<?php echo $tipo->id_cat?>" <?php echo (isset($campos["tipo"]) && $campos["tipo"] == $tipo->id_cat) ? 'selected=""':''?>><?php echo $tipo->etiqueta;?></option>
                <?php endforeach;?>
            </select>
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 direccion">
            <label>Direcci&oacute;n </label>
            <input name="campo[direccion]" value="<?php echo isset($campos["direccion"]) ? $campos["direccion"] : ""?>" class="form-control" id="campo[direccion]" type="text">
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 R.U.C. " style="display: none;">
            <label>R.U.C. </label>
            <input name="campo[ruc]" value="<?php echo isset($campos["ruc"]) ? $campos["ruc"] : ""?>" class="form-control" id="campo[ruc]" type="text" ng-model="identificacion">
        </div>
    </div>
    
    <div class="row">
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-2">
            <label>Identificaci&oacute;n</label>
            <select required="" aria-required="true" name="campo[identificacion][identificacion]" class="form-control" id="identificacion" ng-model="ruc.tipo" ng-change="changedIdentificacion()">
                <option value="">Seleccione</option>
                <option value="natural">Natural</option>
                <option value="juridico">Jur&iacute;dico</option>
            </select>
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-2" ng-hide="ruc.hideNatural">
            <label>Provincia </label>
            <select name="campo[identificacion][provincia_id]" class="form-control" id="provincia_id" ng-model="ruc.provincia_id" ng-disabled="ruc.disabledNatural || !(ruc.letra_id == '53' || ruc.letra_id == '50'|| ruc.letra_id == '') " ng-change="chagedRUC()">
                <option value="">Seleccione</option>
                <option value="35">Bocas del Toro (1)</option>
                <option value="38">Coclé (2)</option>
                <option value="37">Colón (3)</option>
                <option value="36">Chiriquí (4)</option>
                <option value="39">Darién (5)</option>
                <option value="40">Herrera (6)</option>
                <option value="41">Los Santos (7)</option>
                <option value="42">Panamá (8)</option>
                <option value="43">Veraguas (9)</option>
                <option value="47">Guna Yala (10)</option>
                <option value="48">Embera Wounann (11)</option>
                <option value="49">Ngäbe-Buglé (12)</option>
                <option value="44">Panamá Oeste (13)</option>
            </select>
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-2 Letra " ng-hide="ruc.hideNatural">
            <label>Letra </label>
            <select name="campo[identificacion][letra_id]" class="form-control" id="letra_id" ng-model="ruc.letra_id" ng-disabled="ruc.disabledNatural" ng-change="changedLetra()">
                <option value="">Seleccione</option>
                <option value="50">0</option>
                <option value="51">N</option>
                <option value="52">PE</option>
                <option value="53">PI</option>
                <option value="54">PAS</option>
                <option value="55">E</option>
            </select>
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-2 Tomo " ng-hide="ruc.hideNatural || ruc.letra_id == '54'" >
            <label>Tomo </label>
            <input name="campo[identificacion][tomo]" value="" class="form-control" type="text" ng-model="ruc.tomo" ng-disabled="ruc.disabledNatural || ruc.letra_id == '54'" ng-change="chagedRUC()">
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-2 Asiento " ng-hide="ruc.hideNatural || ruc.letra_id == '54'">
            <label>Asiento </label>
            <input name="campo[identificacion][asiento]" value="" class="form-control" type="text" ng-model="ruc.asiento" ng-disabled="ruc.disabledNatural || ruc.letra_id == '54'" ng-change="chagedRUC()">
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-4 No.Pasaporte" ng-hide="ruc.hideNatural2 || ruc.tipo == 'juridico'">
            <label>No. Pasaporte </label>
            <input name="campo[identificacion][no_pasaporte]" value="" class="form-control" id="campo[identificacion][no_pasaporte]" type="text" ng-model="ruc.no_pasaporte" ng-disabled="ruc.disabledNatural2" ng-change="chagedRUC()">
        </div>
        
        <!--campos cuando es un acreedor juridico-->
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-2 Asiento " ng-hide="ruc2.hideJuridico">
            <label>Tomo/Rollo </label>
            <input name="campo[identificacion][tomo2]" value="" class="form-control" type="text" ng-model="ruc2.tomo" ng-disabled="ruc2.disabledJuridico" ng-change="chagedRUC()">
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 Asiento " ng-hide="ruc2.hideJuridico">
            <label>Folio/Im&aacute;gen/Documento </label>
            <input name="campo[identificacion][folio]" value="" class="form-control" type="text" ng-model="ruc2.folio" ng-disabled="ruc2.disabledJuridico" ng-change="chagedRUC()">
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-2 Asiento " ng-hide="ruc2.hideJuridico">
            <label>Asiento/Ficha </label>
            <input name="campo[identificacion][asiento2]" value="" class="form-control" type="text" ng-model="ruc2.asiento" ng-disabled="ruc2.disabledJuridico" ng-change="chagedRUC()">
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-2 Asiento " ng-hide="ruc2.hideJuridico">
            <label>D&iacute;gito verificador </label>
            <input name="campo[identificacion][digito_verificador]" value="" class="form-control" type="text" ng-model="ruc2.digito_verificador" ng-disabled="ruc2.disabledJuridico" ng-change="chagedRUC()">
        </div>
    
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <h4 class="m-b-xs">Información de pago </h4>
            <div class="hr-line-dashed m-t-xs"></div>
        </div>
    </div>
    
    <div class="row">
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 Formadepago ">
            <label>Forma de pago </label>
            <select name="campo[forma_pago]" class="chosen" id="forma_pago">
                <option value="">Seleccione</option>
                <?php foreach ($formas_pago as $forma_pago):?>
                <option value="<?php echo $forma_pago->id_cat?>" <?php echo (isset($campos["forma_pago"]) && $campos["forma_pago"] == $forma_pago->id_cat) ? 'selected=""':''?>><?php echo $forma_pago->etiqueta;?></option>
                <?php endforeach;?>
            </select>
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 Formadepago ">
            <label>T&eacute;rminos de pago </label>
            <select name="campo[termino_pago_id]" class="chosen" id="termino_pago_id" data-placeholder="Seleccione">
                <option value="">Seleccione</option>
                <?php foreach ($terminos_pago as $termino_pago):?>
                <option value="<?php echo $termino_pago->id_cat?>" <?php echo (isset($campos["termino_pago_id"]) && $campos["termino_pago_id"] == $termino_pago->id_cat) ? 'selected=""':''?>><?php echo $termino_pago->etiqueta?></option>
                <?php endforeach;?>
            </select>
        </div>
    </div>
    
    <div class="row">
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Banco </label>
            <select name="campo[banco]" class="chosen" id="banco">
                <option value="">Seleccione</option>
                <?php foreach ($bancos as $banco):?>
                <option value="<?php echo $banco->id?>" <?php echo (isset($campos["banco"]) && $campos["banco"] == $banco->id) ? 'selected=""':''?>><?php echo $banco->nombre;?></option>
                <?php endforeach;?>
            </select>
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 Tipodecuenta ">
            <label>Tipo de cuenta </label>
            <select name="campo[tipo_cuenta]" class="chosen" id="tipo_cuenta">
                <option value="">Seleccione</option>
                <?php foreach ($tipos_cuenta as $tipo_cuenta):?>
                <option value="<?php echo $tipo_cuenta->id_cat?>" <?php echo (isset($campos["tipo_cuenta"]) && $campos["tipo_cuenta"] == $tipo_cuenta->id_cat) ? 'selected=""':''?>><?php echo $tipo_cuenta->etiqueta;?></option>
                <?php endforeach;?>
            </select>
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
            <label>N&uacute;mero de cuenta </label>
            <input name="campo[numero_cuenta]" value="<?php echo isset($campos["numero_cuenta"]) ? $campos["numero_cuenta"] : ""?>" class="form-control" data-inputmask="'mask':'9{0,20}','greedy':false" id="campo[numero_cuenta]" type="text">
        </div>
        
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
            <label>Límite de crédito </label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                <input name="campo[limite_credito]" value="<?php echo isset($campos["limite_credito"]) ? $campos["limite_credito"] : ""?>" class="form-control" data-inputmask="'mask':'9{0,8}[.9{0,4}]','greedy':false" id="campo[limite_credito]" type="text">

            </div>
        </div>
    
    </div>
    
    <div class="row"> 
        <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">
            <input name="campo[acreedor]" value="SI" id="campo[acreedor]" type="hidden">
            <input name="campo[id]" value="<?php echo isset($campos["id"]) ? $campos["id"] : ""?>" id="campo[id]" type="hidden">
            &nbsp;
        </div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <a href="<?php echo base_url("acreedores/listar")?>" class="btn btn-default btn-block" id="cancelarAcreedor">Cancelar </a> 
        </div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <input name="campo[guardarAcreedor]" value="Guardar " class="btn btn-primary btn-block" id="campo[guardarAcreedor]" type="submit">
        </div>
    </div>
    
</div>

<?php
//echo "<pre>";
//print_r($campos);
//echo "<pre>";


