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
										else
											$selected="";
										
								?>
                                        <option value="<?php echo $tipo->etiqueta.'Tab'?>" <?php echo $selected?> ><?php echo $tipo->etiqueta?></option>';
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
			<div class="row">
				<?php SubpanelTabs::visualizar($subpanels); ?>
				<?php //echo modules::run('intereses_asegurados/tabladetalles',$campos); ?>
			</div>
        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<div id="menu_solicitud" style="display:none;">
<?php 
    if(count($campos['campos']['ramos']) > 0){
        $cont = 0;
        foreach($campos['campos']['ramos'] AS $row){ 
            $contHijo[$cont] = $row['id'];
            foreach ($campos['campos']['menu_crear'] AS $value) {
                if($row['padre_id'] == $value['id']){
                    if($value['padre_id'] == 0){
                        $contPadre[$cont] = $value['id'];
                    }else{
                        $contPadre[$cont] = $value['id'];
                        $cont++;
                        foreach ($campos['campos']['menu_crear'] AS $info) {
                            if($value['padre_id'] == $info['id']){
                                if($info['padre_id'] == 0){
                                    $contPadre[$cont] = $info['id'];
                                }
                            }
                        }
                    }
                    
                }
            }
            $cont++;
        }       

        foreach($campos['campos']['menu_crear'] AS $row){ 
            $id_solicitudes = $row['id'];  
            $cont = 0;
            foreach ($campos['campos']['menu_crear'] AS $value) {
                if($row['padre_id'] == 0 && $row['id'] == $value['padre_id']){
                    $cont++;
                }
            }

            if($row['padre_id'] == 0 && $row['estado'] == 1 && $row['level'] == 1 && $cont >= 1 && in_array($row['id'],$contPadre) ){ ?>
                <a href="#collapse0000<?php echo $row['id'] ?>" class="btn btn-block btn-outline btn-success" style="margin-bottom:5px;" data-toggle="collapse" data-id="<?php echo $row['id'] ?>"><?php echo $row['nombre'] ?></a>
                <div id="collapse0000<?php echo $row['id'] ?>" class="collapse">
                    <ul id="<?php echo $row['nombre'] ?>" class="list-group clear-list">      
            <?php 
                foreach($campos['campos']['menu_crear'] AS $info){  
                    $cont2 = 0;
                    foreach ($campos['campos']['menu_crear'] AS $valor) {
                        if($info['id'] == $valor['padre_id']){
                            $cont2++;
                        }
                    }
                    if($info['padre_id'] == $id_solicitudes && $info['estado'] == 1 & $cont2 > 0 && in_array($info['id'],$contPadre) ){ ?>
                        <li class="m-sm">
                            <a href="#collapse0000<?php echo $info['id'] ?>" data-toggle="collapse" data-id="<?php echo $info['id'] ?>"><?php echo $info['nombre'] ?><i class="fa fa-chevron-down" aria-hidden="true"></i></a>
                        </li>
                        <div id="collapse0000<?php echo $info['id'] ?>" class="collapse">
                            <ul id="<?php echo $info['nombre'] ?>" class="list-group clear-list">
                    <?php 
                        foreach($campos['campos']['menu_crear'] AS $result){  
                            if($result['padre_id'] == $info['id'] && $result['estado'] == 1 && $result['level'] == 3 && !empty($result['codigo_ramo']) && in_array($result['id'],$contHijo) ){ ?>    
                                <li class="m-md">     
                                    <a href="<?php echo base_url().'solicitudes/crear/'.$result['id'].'/'.$campos['campos']['id'] ?>" ><?php echo $result['nombre'] ?></a>
                                </li>
                        <?php                      
                            } 
                        } ?>
                            </ul>
                        </div>
                <?php       
                    }elseif($info['padre_id'] == $id_solicitudes && $info['estado'] == 1 && $cont2 == 0 && in_array($info['id'],$contHijo) ){ ?>
                        <li class="m-md">  
                            <a href="<?php echo base_url().'solicitudes/crear/'.$info['id'].'/'.$campos['campos']['id'] ?>"><?php echo $info['nombre'] ?></a>
                        </li>
            <?php          
                    }   
                } ?>
                    </ul>
                </div>
    <?php  
            } 
        } ?>               
<?php
    }else{ ?>
        <button class='btn btn-block btn-outline btn-warning'><p>No existen ramos para </p><p>el interes asegurado</p></button>
    <?php
    }
?>
</div>

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

echo Modal::config(array(
	"id" => "documentosModalEditar",
	"size" => "md",
	"titulo" => "Cambiar nombre del documento",
	"contenido" => modules::run("intereses_asegurados/formularioModalEditar", $campos)
))->html();

//formulario para exportar los documentos
$formAttr = array('method' => 'POST', 'id' => 'exportarDocumentos','autocomplete'  => 'off');
echo form_open(base_url('intereses_asegurados/exportarDocumentos'), $formAttr);
?>
<input type="hidden" name="ids" id="ids_documentos" value="" />
<?php
echo form_close();

//formulario para exportar las solicitudes
$formAttr = array('method' => 'POST', 'id' => 'exportarSolicitudes','autocomplete'  => 'off');
echo form_open(base_url('solicitudes/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids_solicitudes" value="" />
<?php
echo form_close();

//formulario para exportar las polizas
$formAttr = array('method' => 'POST', 'id' => 'exportarPolizas','autocomplete'  => 'off');
echo form_open(base_url('polizas/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids_polizas" value="" />
<?php
echo form_close();

echo Modal::config(array(
    "id" => "opcionesModal",
    "size" => "sm"
))->html();
echo Modal::config(array(
    "id" => "documentosModalSolicitudes",
    //"size" => "md",
    "titulo" => "Subir Documentos",
    "contenido" => modules::run("solicitudes/formularioModal")
))->html();
echo Modal::config(array(
    "id" => "moduloOpciones",
    "size" => "sm"
))->html();
echo Modal::config(array(
    "id" => "opcionesModalAnular",
    "size" => "md"
))->html();
echo Modal::config(array(
    "id" => "opcionesModalAprobar",
    "size" => "md"
))->html();
echo Modal::config(array(
    "id" => "opcionesModalRechazar",
    "size" => "md"
))->html();
?>