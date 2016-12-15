<!--Box Cuenta por pagar a proveedores-->
<div class="ibox border-bottom">
    <div class="ibox-title">
        <h5>Cuenta por pagar a proveedores</h5>
        <div class="ibox-tools">
            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
        </div>
    </div>

    <div class="ibox-content" style="display:none;">
      <div class="row">
        <div class="alert alert-dismissable alert-info">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
            <p><strong>Seleccione</strong> una sola cuenta para pagar a proveedores</p>
        </div>
        <table id="cuentas_por_pagar_proveedor" class="table table-striped tree-table">
          <thead>
            <tr>
              <td width="50%">Cuenta: 2. Pasivo</td>
              <td width="50%">Cuenta seleccionada</td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <div id="cuentas_pasivo_proveedor">cuentas</div>
              </td>
              <td  valign="top" style="vertical-align:top">
                <div id="cuenta_seleccionada"></div>
                <input type="hidden" id="id_seleccion" value=""/>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="row">
          <div class="col-lg-8 col-md-6 col-sm-0 col-xs-0"></div>
          <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6"></div>
          <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6"><button id="btnGuardarProveedor" class="btn btn-block btn-primary ladda-button" data-style="zoom-in"><span class="ladda-label">Guardar</span></button></div>
        </div>
      </div>
</div>
</div>
<!--Box Cuenta por pagar a acreedores-->
<div class="ibox border-bottom">
    <div class="ibox-title">
        <h5>Cuenta por pagar a acreedores</h5>
        <div class="ibox-tools">
            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
        </div>
    </div>

    <div class="ibox-content" style="display:none;">
      <div class="row">
        <div class="alert alert-dismissable alert-info">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
            <p><strong>Seleccione</strong> una sola cuenta para pagar a acreedores</p>
        </div>
        <?php echo modules::run('configuracion_contabilidad/porpagaracreedor'); ?>
      </div>
</div>
</div>
