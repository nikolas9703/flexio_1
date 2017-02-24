  <div role="tabpanel">
    <!-- Tab panes -->
    <div class="row tab-content">
      <div role="tabpanel" class="tab-pane active" id="tabla">
        <div class="row">
          <div class="col-lg-12">
            <div class="">
				<ul class="nav nav-tabs">
					<li class="active" id="tab_intereses"><a data-toggle="tab" href="#tab-1" aria-expanded="true" data-targe="generales">Intereses Asegurados</a></li>
					<li id="tab_documentos"><a data-toggle="tab" href="#tab-2" aria-expanded="true" data-targe="generales">Documentos</a></li>
					<div class="tab-content">
						<div id="tab-1" class="tab-pane active">
							<div class="panel-body" style="padding: 0px 15px 0px 0px!important">
								<div class="tab-content row" >
									<!-- Tab panes -->

									<!-- BUSCADOR -->

									<!-- Inicia campos de Busqueda -->
									<div class="ibox-content tab-pane fade in active" id="accionPersonalTabla2">
										<div><?php echo modules::run('polizas/ocultotablaintereses',$campos["uuid_polizas"]);  ?></div>
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
									<div class="ibox-content tab-pane fade in active" id="accionPersonalTabla">
										<div><?php echo modules::run('documentos/ocultotablaseguros');  ?></div>
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