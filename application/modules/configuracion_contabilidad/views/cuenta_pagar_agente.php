<div class="row">
  <div class="alert alert-dismissable alert-info">
      <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
      <p><strong>Seleccione</strong> una sola cuenta por pagar a Agentes.</p>
  </div>
  <table id="agente_pagar" class="table table-striped "><!-- tree-table cuentas_config -->
    <thead>
      <tr>
        <td width="50%">Cuenta: 2. Pasivo</td>
        <td width="50%">Cuenta seleccionada</td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          <div id="angentes_pagar" >cuentas</div><!-- class="cuentas" --> 
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
    <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6"><button id="btnGuardarAgentePagar" class="btn btn-block btn-primary ladda-button" data-style="zoom-in"><span class="ladda-label">Guardar</span></button></div>
  </div>
</div>
