<?php
$iconos = array('fa-archive', 'fa-automobile', 'fa-bank', 'fa-bicycle','fa-birthday-cake',' fa-flash','fa-bomb','fa-bullhorn','fa-clock-o','fa-coffee','fa-credit-card','fa-cutlery ','fa-desktop','fa-facebook-square','fa-gamepad',
'fa-gavel','fa-keyboard-o','fa-linkedin-square','fa-paint-brush','fa-pie-chart','fa-plane','fa-skype','fa-phone','fa-envelope-o', 'fa-mobile','fa-comments-o','fa-group','fa-home', 'fa-road');
?>

<form class="form-inline" id="formularioTipoActividad" method="post" action="#" novalidate="novalidate">
  <div class="col-md-12">
    <?php
      foreach ($iconos as $value) {
    ?>
       <div class="icon-continer"><a class="actividades-icono" data-icono="<?php echo $value ?>"><i class="fa <?php echo $value ?> fa-2x"></i></a></div>
    <?php  } ?>


  </div>
  <div class="col-md-12 Icono<span required>*</span> ">
     <label> <span required="true" aria-required="true"></span></label>
     <input type="hidden" id="icono" name="icono" data-rule-required="true" aria-required="true">
  </div>
  <div class="col-md-12">
  <div class="col-md-6 Nombre<span required>*</span> ">
  <label>Nombre <span required="true" aria-required="true">*</span></label>
  <input name="nombre" id="nombre" type="text" data-rule-required="true" class="form-control" aria-required="true"/>
</div>
<div class="col-md-6 Puntaje<span required>*</span> ">
<label> Puntaje <span required="true" aria-required="true">*</span></label>
  <input name="puntaje"  id="puntaje" type="text" data-rule-required="true"  class="form-control" aria-required="true"/>
  <input name ="id" id="id" type="hidden" value="0"/>
</div>
</div>
</form>
