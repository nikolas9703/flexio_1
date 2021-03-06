

<div class="ibox-content" style="display:block;">

    <detalle :config="config" :detalle.sync="detalle" :catalogos="catalogos" :empezable.sync="empezable"></detalle>

    <div class="row">
        <div class="col-lg-12">
            <h4 class="m-b-xs">Items de Alquiler</h4>
            <div class="hr-line-dashed m-t-xs"></div>
        </div>
    </div>

    <!--componente cargos de items de alquiler -->
    <cargos_alquiler :config="config" :detalle.sync="detalle" :empezable.sync="empezable"></cargos_alquiler>

    <div id="cargoadicional-accordion">
  		<div class="ibox-title">
  			<h5><input type="checkbox" name="campo[cargos_adicionales]" id="cargos_adicionales" class="toggle-cargoadicional" v-model="detalle.cargos_adicionales_checked" /> Cargos adicionales</h5>
  			<a href="#cargoadicional" id="togglecargoadicional" data-toggle="collapse">&nbsp;</a>
  		</div>
  		<div id="cargoadicional" class="ibox-content panel-collapse collapse {{detalle.cargos_adicionales_checked ? 'in' : ''}}">

        <!-- Lista de Precio Item Adicional -->
        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
            <label>Lista de precio de venta<span required="" aria-required="true">*</span></label>
            <select name="campo[item_precio_id]" class="" id="item_precio_id hide" :data-rule-required="detalle.cargos_adicionales_checked ? 'true' : 'false'" aria-required="true" v-model="detalle.item_precio_id" v-select2="detalle.item_precio_id" :config="config.select2" :disabled="config.disableDetalle">
                <option value="">Seleccione</option>
                <option :value="precio.id" v-for="precio in catalogos.precios" :selected="precio.nombre=='Regular'">{{precio.nombre}}</option>
            </select>
        </div>

  			<articulos :config="config" :detalle.sync="detalle" :catalogos="catalogos" :empezable.sync="empezable"></articulos>

  		</div>
  	</div>

    <totales :config="config" :detalle.sync="detalle" :catalogos="catalogos" :empezable.sync="empezable"></totales>

    <div class="row"></div>

    <div class="row">
        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
            <a href="<?php echo base_url('ordenes_ventas/listar'); ?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
        </div>
        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
            <input type="submit" value="Guardar " class="btn btn-primary form-control" :disabled="config.disableDetalle">
            <input type="hidden" name="campo[id]" value="{{detalle.id}}">
            <input type="hidden" name="campo[vista]" value="{{config.vista}}">
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
            <label>Observaciones </label>
            <textarea id="comentario" name="campo[comentario]"  class="form-control observaciones" v-model="detalle.observaciones" :disabled="config.disableDetalle"></textarea>
            <label id="comentario-error" class="error" for="comentario"></label>
        </div>
    </div>

</div>

<style type="text/css">
    .observaciones{
        width: 30%;
        height: 100px !important;
    }
</style>
