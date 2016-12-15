<template id="template_presupuesto_periodo">
<?php echo Jqgrid::cargar("presupuestoDinamicoGrid")  ?>

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
  <div class="col-xs-0 col-sm-0 col-md-11 col-lg-11">&nbsp;</div>
  <div class="form-group col-xs-12 col-sm-6 col-md-1 col-lg-1">
      <input type="button" id="actualizarBtn" @click="guardarPresupuesto()" v-if="showGuardar" class="btn btn-primary btn-block guardarPresupuesto" value="Guardar" />
  </div>
</div>
</template>
