<?php
$info = !empty($info) ? $info : array();
?>
<!-- Inicia campos de Busqueda -->
<validator name="validation1">
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <label for="cliente_id">Centro Contable <span required="" aria-required="true">*</span></label>
        <select name="campo[centro_contable_id]" class="form-control select2" id="centro_contable_id" v-model="datos.centro_contable_id" :disabled="disableActivo" v-validate:centro="['required','exist']" initial="off" data-rule-required="true">
          <option value="">Seleccione</option>
          <?php foreach($centros_contables as $centro) {?>
          <option value="<?php echo $centro->id?>"><?php echo $centro->nombre?></option>
          <?php }?>
        </select>
        <label v-if="$validation1.centro.required"  class="error" >centro contable es requerido</label>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <label for="cliente_id">Categoria(s) de item <span required="" aria-required="true">*</span></label>
        <select name="campo[categoria_id]" class="form-control select2" id="categoria_id" data-rule-required="true" v-model="datos.categoria_id" :disabled="disableActivo" v-validate:categoria_id="['required','exist']" initial="off">
          <option value="">Seleccione</option>
          <?php foreach($categorias as $categoria) {?>
          <option value="<?php echo $categoria->id?>"><?php echo $categoria->nombre?></option>
          <?php }?>
        </select>
        <label v-if="$validation1.categoria_id.required" id="categoria_id-error" class="error" for="categoria_id"> categoria es requerida</label>
    </div>
      <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-4">
        <label for="porcentaje">Porcentaje <span required="" aria-required="true">*</span></label>
          <div class="input-group">
            <input type="input-left-addon" v-model="datos.porcentaje" name="campo[porcentaje]"  class="form-control money" id="porcentaje" v-validate:porcentaje="['required','exist']" initial="off" data-rule-required="true" data-inputmask="'alias':'percentage'">
            <span class="input-group-addon">%</span>
          </div>
          <label v-if="$validation1.porcentaje.required" id="porcentaje-error" class="error" for="porcentaje"></label>
      </div>
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-8 col-md-8 col-lg-8">
        <label for="referencia">Referencia <span required="" aria-required="true">*</span></label>
            <input type="text" name="campo[referencia]" class="form-control"  id="referencia" data-rule-required="true" v-model="datos.referencia"  v-validate:referencia="['required','exist']" initial="off">
      <label id="referencia-error"  v-if="$validation1.referencia.required" class="error" for="referencia">referencia es requerida</label>
    </div>
    <div class="form-group col-xs-12 col-sm-4 col-md-4 col-lg-4"></div>
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="col-xs-0 col-sm-0 col-md-10 col-lg-10">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-12 col-md-2 col-lg-2">
        <input type="hidden"  id="id" name="campo[id]" value="{{datos.id}}">
     <button id="actualizarBtn"  class="btn btn-primary btn-block"   v-on:click="actualizar($event)" :disabled="!$validation1.valid">Actualizar</button>
    </div>
</div>

<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
 <devolucion-productos :productos="articulos" :boton.sync="botonDisabled" :vista="vista" v-show="articulos.length > 0"></devolucion-productos>
</div>
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <span class="error" v-text="tablaError"></span>
</div>
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12" v-show="articulos.length > 0">
    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
      <a href="<?php echo base_url('depreciacion_activos_fijos/listar');?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
     <button id="guardarBtn"  class="btn btn-primary btn-block" :disabled="!$validation1.valid"  v-on:click="guardar()">Guardar</button>
    </div>
</div>
</validator>
<!-- Termina campos de Busqueda -->
