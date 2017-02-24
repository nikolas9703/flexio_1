  <div role="tabpanel">
    <!-- Tab panes -->
    <div class="row tab-content">
      <div role="tabpanel" class="tab-pane active" id="tabla">
        <div class="row">
          <div class="col-lg-12">
            <div class="">
              <ul class="nav nav-tabs">
                <li class="active" id="tab_planes"><a data-toggle="tab" href="#tab-1" aria-expanded="true" data-targe="generales">Planes</a></li>                        
                <li class="" id="tab_contactos"><a data-toggle="tab" href="#tab-2" aria-expanded="false"  data-targe="beneficios">Solicitudes</a></li>
				<li class="" id="tab_solicitudes"><a data-toggle="tab" href="#tab-3" aria-expanded="false"  data-targe="beneficios">Contactos</a></li>
                <div class="tab-content">
                  <div id="tab-1" class="tab-pane active">
                    <div class="panel-body" style="padding: 0px 15px 0px 0px!important">
                      <div class="tab-content row" >
                        <!-- Tab panes -->

                        <!-- BUSCADOR -->

                        <!-- Inicia campos de Busqueda -->
                        <div class="ibox-content tab-pane fade in active" id="planes">
						                <div><?php echo modules::run('planes/detalles_planes', $campos); ?></div>
                        </div>
                      </div>
                    </div>
                  </div>
				   <div id="tab-2" class="tab-pane">
                    <div class="panel-body" style="padding: 0px 15px 0px 0px!important">
                      <div class="tab-content row" >
                        <!-- Tab panes -->

                        <!-- BUSCADOR -->

                        <!-- Inicia campos de Busqueda -->
                        <div class="ibox-content tab-pane fade in active" id="solicitudes">
            						<?php  
            							echo modules::run('solicitudes/tablatabsolicitudes',$campos); ?>
            						 <?php //echo Jqgrid::cargar("contactosGrid")  ?>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div id="tab-3" class="tab-pane">
                    <div class="panel-body" style="padding: 0px 15px 0px 0px!important">
                      <div class="tab-content row" >
                        <!-- Tab panes -->

                        <!-- BUSCADOR -->

                        <!-- Inicia campos de Busqueda -->
                        <div class="ibox-content tab-pane fade in active" id="contactos">
            						<?php  
            							echo modules::run('aseguradoras/tabladetallescontactos',$campos); ?>
            						 <?php //echo Jqgrid::cargar("contactosGrid")  ?>
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