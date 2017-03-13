<div class="ibox border-bottom">
    <div class="ibox-title">
        <h5>Cuenta por pagar&nbsp;</h5><h6 style="margin-top:4px;">Aseguradoras</h6>
        <div class="ibox-tools">
            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
        </div>
    </div>

    <div class="ibox-content" style="display:none;">
      <?php echo modules::run('configuracion_contabilidad/cuenta_pagar_aseguradora'); ?>
   </div>
</div>

<div class="ibox border-bottom">
    <div class="ibox-title">
        <h5>Cuenta por cobrar&nbsp;</h5><h6 style="margin-top:4px;">Aseguradoras</h6>
        <div class="ibox-tools">
            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
        </div>
    </div>

    <div class="ibox-content" style="display:none;">
      <?php echo modules::run('configuracion_contabilidad/cuenta_cobrar_aseguradora'); ?>
   </div>
</div>

<div class="ibox border-bottom">
    <div class="ibox-title">
        <h5>Cuenta por pagar&nbsp;</h5><h6 style="margin-top:4px;">Agentes</h6>
        <div class="ibox-tools">
            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
        </div>
    </div>
    <div class="ibox-content" style="display:none;">
      <?php echo modules::run('configuracion_contabilidad/cuenta_pagar_agente'); ?>
   </div>
</div>

<div class="ibox border-bottom">
    <div class="ibox-title">
        <h5>Remesas&nbsp;</h5><h6 style="margin-top:4px;">Entrante</h6>
        <div class="ibox-tools">
            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
        </div>
    </div>
    <div class="ibox-content" style="display:none;">
      <?php echo modules::run('configuracion_contabilidad/remesa_entrante'); ?>
   </div>
</div>

<div class="ibox border-bottom">
    <div class="ibox-title">
        <h5>Remesas&nbsp;</h5><h6 style="margin-top:4px;">Saliente</h6>
        <div class="ibox-tools">
            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
        </div>
    </div>
    <div class="ibox-content" style="display:none;">
      <?php echo modules::run('configuracion_contabilidad/remesa_saliente'); ?>
   </div>
</div>
