
<detalle :config.sync="config" :detalle.sync="detalle" :catalogos.sync="catalogos" :empezable="empezable"></detalle>
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <!-- componente de vue-->
  <nota-debito-items :catalogos="catalogos" :config.sync="config" :empezable="empezable" :rows.sync="detalle.filas" :detalle.sync="detalle" :error.sync="tablaError" :boton.sync="botonDisabled"></nota-debito-items>
  <!-- componente de vue-->
  <div id="tablaError" class="error" v-text="tablaError"></div>
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6 ">
    <label>Observaciones </label>
    <textarea id="comentario" name="campo[comentario]"  class="form-control" :disabled="config.disableDetalle"></textarea>
  </div>


</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
      <a href="<?php echo base_url('notas_debitos/listar');?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
        <input type="hidden"  id="id"  name="campo[id]"  value="{{detalle.id}}">
        <button id="guardarBtn"  class="btn btn-primary btn-block" :disabled="botonDisabled || estado_actual=='anulado'">Guardar</button>
    </div>
</div>
<!-- Termina campos de Busqueda ...-->
