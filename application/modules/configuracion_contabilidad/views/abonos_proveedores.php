<div class="row">
  <div class="alert alert-dismissable alert-info">
      <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
      <p><strong>Seleccione</strong> una sola cuenta para anticipos a proveedores</p>
  </div>
  <table id="cuentas_activo_abono_proveedor" class="table table-striped tree-table">
    <thead>
      <tr>
        <td width="50%">Cuenta: 1. Activo</td>
        <td width="50%">Cuenta seleccionada</td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          <div id="cuentas_abono_proveedor">cuentas</div>
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
    <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6"><button id="btnGuardarAbonoProveedor" class="btn btn-block btn-primary ladda-button" data-style="zoom-in"><span class="ladda-label">Guardar</span></button></div>
  </div>
</div>
