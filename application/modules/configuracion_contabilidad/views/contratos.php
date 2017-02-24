<div class="ibox border-bottom">
    <div class="ibox-title">
        <h5>Cuenta para anticipos a contratos</h5>
        <div class="ibox-tools">
            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
        </div>
    </div>

    <div class="ibox-content" style="display:none;">
      <?php echo modules::run('configuracion_contabilidad/contratos_cuenta_anticipos'); ?>
   </div>
</div>

<div class="ibox border-bottom">
    <div class="ibox-title">
        <h5>Cuenta para retención de contratos</h5>
        <div class="ibox-tools">
            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
        </div>
    </div>

    <div class="ibox-content" style="display:none;">
      <?php echo modules::run('configuracion_contabilidad/contratos_cuenta_retencion'); ?>
   </div>
</div>
