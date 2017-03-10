<?php
$info = !empty($info) ? $info : array();
//dd($info);
?>
<div id="vue-form-adenda">

        <div class="form-group col-xs-12 col-sm-12 col-md-4 col-lg-4 "><label></label>
            <label for="fecha">Fecha</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar-minus-o"></i></span>
                <input type="text" name="campo[fecha]" class="form-control"  id="fecha" v-model="campo.fecha" disabled>
            </div>
            <label id="fecha-error" class="error" for="fecha"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-3 col-md-4 col-lg-4">
            <label for="numero_subcontrato">Numero de Adenda</label>
            <input type="text" disabled name="campo[codigo]"  class="form-control"  id="campo[codigo]" value="<?php echo empty($info) ? $codigo : $info['codigo'] ?>">
            <label id="numero_subcontrato-error" class="error" for="numero_subcontrato"></label>
        </div>

        <div class="form-group col-xs-12 col-sm-12 col-md-4 col-lg-4 ">
            <label for="referencia">Referencia <span required="" aria-required="true">*</span></label>
            <input type="text" name="campo[referencia]" class="form-control"  id="referencia" data-rule-required="true" value="<?php echo empty($info) ? '' : $info['referencia'] ?>">
            <label id="referencia-error" class="error" for="referencia"></label>
        </div>


    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <cuentas-montos :lista.sync="tablaDatos"></cuentas-montos>
        <div id="tablaError"></div>
    </div>
    <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="form-group col-md-4 col-lg-4  col-md-offset-4 col-lg-offset-4" style="padding: 8px;text-align: center;">
            <label for="centro_contable_id">Monto de adenda</label>
        </div>
        <div class="form-group col-md-3 col-lg-3">
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="text" disabled id="monto_adenda" v-model="monto_adendas" name="campo[monto_adenda]"  class="form-control">
            </div>
        </div>
        <div class="col-lg-3 col-md-3"></div>
    </div>
    <div class="row" v-show="campo.adenda_id == ''">
        <div class="form-group col-xs-12 col-sm-12 col-md-8 col-lg-8"></div>
        <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
            <a href="<?php echo base_url('subcontratos/listar'); ?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
        </div>
        <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
            <input type="hidden" name="campo[subcontrato_id]" id="subcontrato_id" v-model="campo.subcontrato_id">
            <input type="hidden" name="campo[adenda_id]" id="adenda_id" v-model="campo.adenda_id">
            <input type="submit" id="guardar_adendaBtn"  class="btn btn-primary btn-block" value="Guardar" v-on:click="guardar()"/>
        </div>
    </div>

    <div class="row">
        <component :is="vista_comments" :historial.sync="comentarios"></componente>
    </div>

</div>
