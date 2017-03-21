  <div role="tabpanel">
    <!-- Tab panes -->
    <div class="row tab-content">
      <div role="tabpanel" class="tab-pane active" id="tabla">
        <div class="row">
          <div class="col-lg-12">
            <div class="">
              <ul class="nav nav-tabs">
                <li class="active" id="tab_documentos"><a data-toggle="tab" href="#tab-1" aria-expanded="true" data-targe="generales">Documentos</a></li>                        
               <div class="tab-content">
                  <div id="tab-1" class="tab-pane active">
                    <div class="panel-body" style="padding: 0px 15px 0px 0px!important">
                      <div class="tab-content row" >
                        <!-- Tab panes -->

                        <!-- BUSCADOR -->

                        <!-- Inicia campos de Busqueda -->
                        <div class="ibox-content tab-pane fade in active" id="accionPersonalTabla">
						     <div><?php echo modules::run('documentos/ocultotabla');  ?></div>
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