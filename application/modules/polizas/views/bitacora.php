<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
	$data["disabled"] = "disabled=true";
    ?>
    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

        <div class="col-lg-12">
            <div class="wrapper-content">
                <div class="row">
                    <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
                    </div>
                </div>

                <div class="row">
                	<label for="comentario">Historial de p&oacute;liza N°. <?= $data["campos"]["numero"] ?></label>
					<div id="historial_comentario">
					  <div class="ibox-content" >
						<div class="vertical-container dark-timeline center-orientation">
							<?php 
							foreach($historial as $item){
								$item = (object)$item;
								$item->usuario = (object)$item->usuario;
								
								switch($item->comentable_type){
									case 'Creacion Poliza':
										$titBit = "Creación de Póliza";
										$logoBit = "fa-file-text-o";
									break;
									case 'Comentario':
										$titBit = "Coment&oacute";
										$logoBit = "fa-comments-o";
									break;
									case 'Cambio de Estado':
										$titBit = "Cambio de Estado";
										$logoBit = "fa-pencil-square-o";
									break;
									case 'Cobro_seguros':
										$titBit = "Cobros";
										$logoBit = "fa-trophy";
									break;
									case 'facturas_seguro':
										$titBit = "Factura";
										$logoBit = "fa-money";
									break;
									default:
										$titBit = "Coment&oacute";
										$logoBit = "fa-comments-o";
									break;
								}
							?>
							<div class="vertical-timeline-block">
								<div class="vertical-timeline-icon blue-bg">
									<i class="fa <?= $logoBit; ?>"></i>
								</div>
								<div class="vertical-timeline-content" >
									<h2><?= $titBit ?></h2>
									<div>
									<?= $item->comentario ?>
									</div>

									<span class="vertical-date">
										<?= $Bitacora->getCuantoTiempo($item->created_at) ?> <br>
										<small><?= $item->created_at ?></small>
										<div><small> <?= $item->usuario->nombre." ".$item->usuario->apellido ?>  <?= $Bitacora->getHora($item->created_at) ?></small></div>
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
        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
