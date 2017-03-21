<template id="presupuesto_avance">
  <?php echo Jqgrid::cargar("presupuestoDinamicoGrid")  ?>
  <div class="row" ng-hide="hideGuardar">

    <div class="col-xs-0 col-sm-0 col-md-11 col-lg-11">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-6 col-md-1 col-lg-1">
        <input type="button" id="actualizarBtn" ng-click="guardarPresupuesto()" class="btn btn-primary btn-block" value="Guardar" />
    </div>
  </div>
</template>
