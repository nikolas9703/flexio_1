<!--Box Cuenta por pagar a proveedores-->
<div class="ibox border-bottom">
    <div class="ibox-title">
        <h5>Cuenta por anticipos a proveedores</h5>
        <div class="ibox-tools">
            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
        </div>
    </div>

    <div class="ibox-content" style="display:none;">
      <?php echo modules::run('configuracion_contabilidad/abonarproveedor'); ?>
   </div>
</div>
<!--Box Cuenta por pagar a acreedores-->
<div class="ibox border-bottom">
    <div class="ibox-title">
        <h5>Cuenta por anticipos a clientes</h5>
        <div class="ibox-tools">
            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
        </div>
    </div>

    <div class="ibox-content" style="display:none;">
        <?php echo modules::run('configuracion_contabilidad/abonarcliente'); ?>
    </div>
</div>
