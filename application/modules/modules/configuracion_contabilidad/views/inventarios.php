<!--Box Inventario facturado-->
<div class="ibox border-bottom">
    <div class="ibox-title">
        <h5>Cuenta de inventario facturado sin recibir - Activo</h5>
        <div class="ibox-tools">
            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
        </div>
    </div>

    <div class="ibox-content" style="display:none;">
      <?php echo modules::run('configuracion_contabilidad/inventario_facturado'); ?>
   </div>
</div>
<!--Box Inventario recibido activo-->
<div class="ibox border-bottom">
    <div class="ibox-title">
        <h5>Cuenta de inventario recibido sin factura - Activo</h5>
        <div class="ibox-tools">
            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
        </div>
    </div>

    <div class="ibox-content" style="display:none;">
        <?php echo modules::run('configuracion_contabilidad/inventario_recibido_activo'); ?>
    </div>
</div>

<!--Box Inventario recibido pasivo-->
<div class="ibox border-bottom">
    <div class="ibox-title">
        <h5>Cuenta de inventario recibido sin factura - Pasivo</h5>
        <div class="ibox-tools">
            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
        </div>
    </div>

    <div class="ibox-content" style="display:none;">
        <?php echo modules::run('configuracion_contabilidad/inventario_recibido_pasivo'); ?>
    </div>
</div>
