<div id="wrapper">
    <?php

    Template::cargar_vista('sidebar');
  
    ?>

    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

        <div class="col-lg-12">
            <div class="wrapper-content" id="formulario">
                <div class="row">
                    <div class="filtro-formularios" style="background-color: #D9D9D9; padding:6px 0 39px 10px">
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                            <label>Empezar interés asegurado desde</label>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">

                            <select id="formulario" class="white-bg chosen-filtro" role="tablist" disabled>
                                <option value="">Seleccione</option>
                                <?php
                                if(!empty($campos['campos']['tipos_intereses_asegurados'])){
									$selected="";
                                    foreach($campos['campos']['tipos_intereses_asegurados'] AS $tipo){
										
										if($campos['campos']['tipoformulario']==$tipo->valor)
											$selected="selected";
								?>
                                        <option value="<?php echo $tipo->etiqueta.'Tab'?>" <?php echo $selected?>><?php echo $tipo->etiqueta?></option>';
								<?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-6 col-lg-6"></div>
                    </div>

                    <!-- Tabs Content -->
                    <div class="tab-content filtro-formularios-content m-t-sm">
                    <?php 
					echo modules::run("intereses_asegurados/" . $campos['campos']['tipoformulario']. "formularioparcial",$campos); ?>
                    </div>

                </div>
            </div>
			<div class="row" id="sub-panel" >
					<div style="height:50px !important" class="panel-heading white-bg">	
			    		<ul class="nav nav-tabs nav-tabs-xs">
							<li class="active"><a role="tab" data-toggle="tab" href="#accionPersonalTabla">Documentos</a></li>
						</ul>
					</div>
					<div class="tab-content white-bg p-xs">
						<div id="accionPersonalTabla" class="tab-pane active" role="tabpanel">
						
				       <?php echo modules::run('documentos/ocultotabla'); ?>
						</div>
						
					</div>
			</div>
        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<?php

echo    Modal::config(array(
    "id"    => "optionsModal",
    "size"  => "sm"
))->html();

echo Modal::config(array(
	"id" => "documentosModal",
	//"size" => "md",
	"titulo" => "Subir Documentos",
	"contenido" => modules::run("intereses_asegurados/formularioModal", $campos)
))->html();
?>