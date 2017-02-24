<div id="wrapper">
    <?php
	Template::cargar_vista('sidebar');
    ?>

    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

    	<div class="col-lg-12">
            <div class="wrapper-content">
                <div class="row">
                    <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
                    </div>
                </div>

                <div role="tabpanel">
                    <!-- Tab panes -->
                    <div class="row tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tabla">

                            <!-- BUSCADOR -->
                            <?php
                   $formAttr = array(
                      'method'        => 'POST',
                       'id'            => 'formularioBuscador',
                       'autocomplete'  => 'off'
                     );
                    echo form_open(base_url(uri_string()), $formAttr);
                   ?>
                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Buscar Pedido</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>



                                                                                 <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                                                                     <span id="color_ojo"><i class="fa fa-eye"></i></span>
                                                                                 </a>

                                                                                 <ul class="dropdown-menu dropdown-user">

                                                                                   <?php
                                                                                     if(count($menu_busqueda)){
                                                                                       foreach ($menu_busqueda as $key => $value) {
                                                                                           echo '<li><div style="text-align:center;">
      <div style=" display:inline-block; padding:14px;"><a  style="color:#4e3636 !important;" data-id="'.$value->id.'" "href="#"   class="boton_buscador "  >'.$value->busqueda.'</a></div>
      <div style=" display:inline-block;"><a data-id="'.$value->id.'" "href="#"  class="fa fa-times pull-right borrar_buscador"></a></div>
  </div></li> ';
                                                                                           //echo '<li><a  data-id="'.$value->id.'" "href="#"  style="float: left;z-index: -1;position: relative;"  class="boton_buscador ">'.$value->busqueda.'<span   style="z-index: 100 !important;position: absolute;margin-top: 7px;left: 100px;" class="fa fa-times pull-right borrar_buscador"></span></a> </li>';
                                                                                           //echo '<li><a  data-id="'.$value->id.'" "href="#"   class="boton_buscador ">'.$value->busqueda.'</a><a data-id="'.$value->id.'" "href="#"  class="fa fa-times pull-right borrar_buscador"></a> </li>';
                                                                                          //echo '<a    data-id="'.$value->id.'" "href="#"  class="fa fa-times pull-right borrar_buscador"></a></li>';
                                                                                        }
                                                                                     }
                                                                                   ?>

                                                                                  <li class="divider"></li>
                                                                                 <li><a data-toggle="modal" id="guardarbusqueda" href="#">Guardar búsqueda <span class="fa fa-save pull-right"></span></a></li>




                                                                             </ul>

                                    </div>
                                </div>
                                <div class="ibox-content" style="display:none;">
                                    <!-- Inicia campos de Busqueda -->
                                    <div class="row">
                                          <input type="hidden" class="form-control" name="busqueda[modulo]" value="<?php echo $modulo?>">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="fecha1">Rango de fechas</label><br>
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    <input type="text" name="campo[desde]"  id="fecha1" class="form-control">
                                                    <span class="input-group-addon">a</span>
                                                    <input type="text" class="form-control" name="campo[hasta]" id="fecha2">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="centro">Centro Contable</label><br>
                                            <select id="centro" name="campo[centro]" class="form-control" data-placeholder=" ">
                                                <option value=""> </option>
                                                <?php foreach($centros as $centro):?>
                                                <option value="<?php echo $centro['id']?>"><?php echo $centro['nombre']?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="estado">Estado</label><br>
                                            <select id="estado" name="campo[estado][]" class="form-control" multiple="true" data-placeholder=" ">
                                                <option value=""> </option>
                                                <?php foreach($estados as $estado):?>
                                                <option value="<?php echo $estado->id_cat?>"><?php echo $estado->etiqueta?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="referencia">Referencia</label>
                                            <input type="text" name="campo[referencia]" id="referencia" class="form-control" value="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="numero">N&uacute;mero</label>
                                            <input type="text" name="campo[numero]" id="numero" class="form-control" value="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="searchBtn" class="btn btn-default btn-block" value="Filtrar" />
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="clearBtn" class="btn btn-default btn-block" value="Limpiar" />
                                        </div>
                                    </div>
                                    <!-- Termina campos de Busqueda -->
                                </div>
                            </div>
                            <?php echo form_close(); ?>
                            <!-- /BUSCADOR -->

                            <!-- JQGRID -->
                            <?php echo modules::run('pedidos/ocultotabla'); ?>

                            <!-- /JQGRID -->
                        </div>

                        <div role="tabpanel" class="tab-pane" id="grid">
                            <?php
                                //QUITO ESTOS ELEMENTOS DEL ARRAY
                                //PARA EVITAR CONFLICTOS DE INDICES INDEFINIDOS
                                unset($vars["estados"]);
                                unset($vars["centros"]);
                            ?>
                            <?php //Grid::visualizar_grid($vars); ?>
                        </div>

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
	"size" => "lg",
	"titulo" => "Subir Documentos",
	"contenido" => modules::run("documentos/formulario", array())
))->html();
?>
