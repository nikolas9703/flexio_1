<!-- Inicia campos de Busqueda -->
<?php //dd($info)?>
<validator name="formPresupuesto">
<div id="form_presupuesto">
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="form-group col-xs-12 col-sm-4 col-md-4 col-lg-4">
        <label for="nombre">Centro Contable</label>
        <select name="presupuesto[centro_contable_id]" v-model="presupuesto.centro_contable_id" class="form-control" id="centro_contable_id" data-rule-required="true" v-validate:centro_contable_id="['required']">
          <option value="">Seleccione</option>
          <?php foreach($centros_contables as $centro) {?>
          <option <?php if(isset($info['info'])){if ($info['info']['centro_contable_id'] == $centro->id) echo "selected='selected'";}?> value="<?php echo $centro->id?>"><?php echo $centro->nombre?></option>
          <?php }?>
        </select>
        <input type="hidden" name="presupuesto[id]" id="presupuesto_id" value="{{presupuesto.id}}" />
    </div>

    <div class="form-group col-xs-12 col-sm-4 col-md-4 col-lg-4">
        <label for="nombre">Referencia</label>
        <input type="text" name="presupuesto[nombre]" id="nombre" v-model="presupuesto.nombre" class="form-control" data-rule-required="true" placeholder="" value="<?php if(isset($info['info'])) echo $info['info']['nombre'] ?>" v-validate:nombre="['required']">
    </div>

    <div class="form-group col-xs-12 col-sm-4 col-md-4 col-lg-4">
        <label for="estado">Tipo Presupuesto</label><br>

        <select name="presupuesto[tipo]" v-model="presupuesto.tipo" class="form-control" id="tipo" data-rule-required="true" @change="seleccionarTipo(presupuesto.tipo)" id="tipo" v-validate:tipo="['required']" :disabled="desabilitadoCampo">
          <option value="">Seleccione</option>
          <option value="avance">Por avance</option>
          <option value="periodo">Por periodo</option>
        </select>
    </div>
</div>
<!--  div periodo -->
<div id="presupuesto_periodo" class="col-xs-12 col-sm-12 col-md-12 col-lg-12" v-if="mostrarPeriodo">
    <div class="form-group col-xs-12 col-sm-4 col-md-4 col-lg-4">
        <label for="estado">Inicio</label><br>

        <select name="presupuesto[inicio]" v-model="presupuesto.inicio" class="form-control" id="inicio" data-rule-required="true" :disabled="!mostrarPeriodo" v-validate:inicio="['required']">
          <option value="">Seleccione</option>
          <?php foreach($inicio as $fecha) {?>
          <option <?php if(isset($info['info'])){if ($info['info']['inicio'] == $fecha['id']) echo "selected='selected'";}?> value="<?php echo $fecha['id']?>"><?php echo $fecha['valor']?></option>
          <?php }?>
        </select>
    </div>

    <div class="form-group col-xs-12 col-sm-4 col-md-4 col-lg-4">
        <label for="estado">Periodo</label><br>
        <select name="presupuesto[cantidad_meses]" v-model="presupuesto.cantidad_meses" class="form-control" id="cantidad_meses" data-rule-required="true" :disabled="!mostrarPeriodo" v-validate:cantidad_meses="['required']">
          <option value="">Seleccione</option>
          <?php foreach($periodos as $periodo) {?>
          <option <?php if(isset($info['info'])){if ($info['info']['cantidad_meses'] == $periodo['key_valor']) echo "selected='selected'";}?> value="<?php echo $periodo['key_valor']?>"><?php echo $periodo['nombre']?></option>
          <?php }?>
        </select>
    </div>

</div>

<div id="presupuesto_avance" class ="col-xs-12 col-sm-12 col-md-12 col-lg-12" v-if="mostrarAvance">
    <div class="form-group col-xs-12 col-sm-4 col-md-4 col-lg-4">
        <label for="fecha_inicio">Fecha de Inicio</label>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <input type="text" name="presupuesto[fecha_inicio]" class="form-control"  id="fecha_inicio" data-rule-required="true" v-model="presupuesto.fecha_inicio" v-datepicker2="presupuesto.fecha_inicio" :config="{dateFormat: 'dd/mm/yy'}" v-validate:fecha_inicio="['required']">
      </div>
      <label id="fecha_inicio-error" class="error" for="fecha_inicio"></label>
    </div>
</div>

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="col-xs-0 col-sm-0 col-md-10 col-lg-10">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
      <?php if(isset($info['info'])){?>
        <input type="hidden" value="<?php echo $info['info']['id'] ?>" id="presupuesto_id"  name="presupuesto_id">
     <?php }?>
     <input type="button" id="actualizarBtn" @click="actualizar(presupuesto)" v-if="$formPresupuesto.valid && showActualizar" class="btn btn-primary btn-block" value="Actualizar" />
    </div>
</div>
</div>
</validator>
<!-- Termina campos de Busqueda -->
