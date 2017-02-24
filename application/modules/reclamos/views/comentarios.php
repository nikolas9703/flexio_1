  <div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12" id="div_comments">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
      <label for="comentario">Comentario</label>
        
		<input type="hidden" value="<?= $n_reclamo ?>" name="n_reclamo" id="n_reclamo" />
		<textarea rows="5" cols="10" name="campo[comentario]" id="comentario_reclamo" data-rule-required="true"></textarea>

        <label id="comentario-error" class="error" for="comentario"></label>
        <div class="row">
          <div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6"></div>
          <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <button class="btn btn-default btn-block" id="cancelarFormBtn"onclick="limpiarEditor()">Limpiar</button>
          </div>
          <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
            <button id="guardar_comentario"  class="btn btn-primary btn-block" onclick="guardar_comentario()">Comentar</button>
          </div>
        </div>
      </div>
	
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" >
        <label for="comentario">Historial</label>
        <div  class="vertical-container dark-timeline center-orientation" id="historial_comentario"  style="height: 320px; overflow: scroll;">
          <div class="ibox-content" >
            <div class="vertical-container dark-timeline center-orientation">
				<?php 
				foreach($historial as $item){
					$item = (object)$item;
					
					
				?>
				<div class="vertical-timeline-block">
					<div class="vertical-timeline-icon blue-bg">
						<i class="fa fa-comments-o"></i>
					</div>
					<div class="vertical-timeline-content" >
						<h2>Coment&oacute;</h2>
						<div>
						<?= $item->comentario ?>
						</div>

						<span class="vertical-date">
							<?= $Fecha->getCuantoTiempo($item->created_at) ?> <br>
							<small><?= $item->created_at ?></small>
							<div><small> <?= $item->nombre." ".$item->apellido ?>  <?= $Fecha->getHora($item->created_at) ?></small></div>
						</span>
					</div>
				</div>
				<?php 
				}
				?>
            </div>
          </div>
        </div>
    </div>
  </div>