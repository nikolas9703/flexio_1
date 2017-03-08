<div class="row"> 
  <div class="alert alert-dismissable alert-info">
      <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
      <p><strong>Seleccione</strong> una sola cuenta por cobrar a Aseguradoras.</p>
  </div>
  <table id="aseguradora_cobrar" class="table table-striped">
    <thead>
      <tr>
        <td width="50%">Cuenta:1. Activos</td>
        <td width="50%">Cuenta seleccionada</td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          <div id="aseguradoras_cobrar">cuentas</div>
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
    <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6"><button id="btnGuardarAseguradoraCobrar" class="btn btn-block btn-primary ladda-button" data-style="zoom-in">Guardar</button></div>
  </div>
</div>
