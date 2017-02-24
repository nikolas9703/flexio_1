  <div role="tabpanel">
    <!-- Tab panes -->
    <div class="row tab-content">
      <div role="tabpanel" class="tab-pane active" id="tabla">
        <div class="row">
          <div class="col-lg-12">
            <div class="">
              <ul class="nav nav-tabs">
                <li class="active" id="tab_polizas"><a data-toggle="tab" href="#tab-1" aria-expanded="true" data-targe="generales">Polizas</a></li>                        
                <!--<li class="" id="tab_contactos"><a data-toggle="tab" href="#tab-2" aria-expanded="false"  data-targe="beneficios">Solicitudes</a></li>
                <li class="" id="tab_solicitudes"><a data-toggle="tab" href="#tab-3" aria-expanded="false"  data-targe="beneficios">Contactos</a></li>-->
                <div class="tab-content">
                  <div id="tab-1" class="tab-pane active">
                    <div class="panel-body" style="padding: 0px 15px 0px 0px!important">
                      <div class="tab-content row" >
                        <!-- Tab panes -->

                        <!-- BUSCADOR -->

                        <!-- Inicia campos de Busqueda -->
                        <div class="ibox-content tab-pane fade in active" id="polizas">
                          <div><?php echo modules::run('polizas/tablapolizas_agt'); ?></div>
                        </div>
                      </div>
                    </div>
                  </div>                 
                </div>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>