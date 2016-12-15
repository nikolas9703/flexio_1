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
                        <div class="panel-heading white-bg">
                            <span class="panel-title"></span>
                            <ul class="nav nav-tabs nav-tabs-xs formTabs">
                                <li class="dropdown pull-right tabdrop hide">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                        <i class="fa fa-align-justify"></i>
                                        <b class="caret"></b>
                                    </a>
                                    <ul class="dropdown-menu"></ul>
                                </li>
                                <li class="active">
                                    <a data-toggle="tab" href="#categoriasItems">Categor&iacute;as items</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#preciosItems">Listado de precios de ventas</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#preciosItemsAlquiler">Listado de precios de alquiler</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#unidadesItems">Unidades de medida</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#razonAjustes">Raz&oacute;n de ajustes</a>
                                </li>
                            </ul>
                        </div>
                        <div role="tabpanel" class="tab-pane active" id="categoriasItems">

                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Categor&iacute;as de items</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content">
                                    <!-- Inicia campos de Busqueda -->
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="categoria">Categor&iacute;a<span style="color:red;"> *</span></label>

                                            <input type="text" id="categoria" class="form-control" v-model="campo.nombre" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-9 col-lg-9">
                                            <label for="descripcion">Descripci&oacute;n<span style="color:red;"> *</span></label>
                                            <input type="text" name="description" id="descripcion" class="form-control" v-model="campo.descripcion" placeholder="">
                                        </div>

                                    </div>

                                    <!-- categoria depreciacion -->

                                      <div class="row cat--depreciacion">
                                        <h5>Depreciar la Categor&iacute;a de items</h5>
                                        <div class="onoffswitch">
                                            <input type="checkbox" name="campo[item_alquiler]" v-model="campo.depreciar" class="onoffswitch-checkbox" id="myonoffswitch" v-model="detalle.item_alquiler">
                                            <label class="onoffswitch-label" for="myonoffswitch" style="height:20px;">
                                                <span class="onoffswitch-inner"></span>
                                                <span class="onoffswitch-switch" style="height:20px;"></span>
                                            </label>
                                        </div>
                                      </div>

                                      <div class="row ver--despreciar animate" v-show="campo.depreciar" transition="listado">
                                        <div class="form-group col-lg-4 col-md-4 col-xs-6 col-sm-6">
                                            <label for="categoria">Depreciaci&oacute;n en meses</label>
                                            <input type="text" id="mese_depreciacion" v-model="campo.depreciacion_meses"
                                            class="form-control entero" v-on:keyup="calculo_pocentaje()">
                                        </div>
                                        <div class="form-group col-lg-4 col-md-4 col-xs-6 col-sm-6">
                                          <label for="categoria">Porcentaje a depreciar</label>
                                          <div class="input-group">
                                              <span class="input-group-addon">%</span>
                                          <input type="text" id="porcentaje_depreciar" v-model="campo.porcentaje_depreciacion" class="form-control" placeholder="" :disabled="!disablePorcentaje">
                                      </div>
                                        </div>
                                        <div class="form-group col-lg-4 col-md-4 col-xs-6 col-sm-6">
                                            <label for="categoria">Cuenta</label>
                                            <select id="fcuenta" class="form-control" class="form-control chosen-select" v-model="campo.cuenta_id">
                                            <option value="">Seleccione</option>
                                            <?php

                                            foreach($categoria_cuentas as $cuenta){ ?>
                                            <option value="<?php echo $cuenta['id'];?>"><?php echo $cuenta['nombre'];?></option>
                                            <?php }?>
                                            </select>
                                        </div>
                                      </div>


                                    <!-- categoria depreciacion -->
                                    <div class="row" style="margin-top:15px">
                                        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="cancelarCategoriaBtn" class="btn btn-default btn-block" value="Cancelar" />
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="guardarCategoriaBtn" v-on:click="guardar" class="btn btn-success btn-block" value="Guardar" />
                                        </div>
                                        <input type="hidden" id="modoCategoria" value="crear" data-uuid="">
                                        <input type="hidden" id="modo1Categoria" name="modo1Categoria" :value="campo.id">
                                    </div>
                                    <!-- categoria del item-->
                                    <!-- Termina campos de Busqueda -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- JQGRID -->
                                            <?php echo modules::run('catalogos_inventario/ocultotablaCategorias'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div role="tabpanel" class="tab-pane" id="preciosItems">

                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Listado de precios</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content">
                                    <!-- Inicia campos de Busqueda -->
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="precio_nombre">Nombre<span style="color:red;"> *</span></label>

                                            <input type="text" id="precio_nombre" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                            <label for="precio_descripcion">Descripci&oacute;n<span style="color:red;"> *</span></label>
                                            <input type="text" id="precio_descripcion" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="precio_estado">Estado<span style="color:red;"> *</span></label><br>
                                            <select id="precio_estado" class="form-control" data-placeholder=" ">
                                                <option value=""> </option>
                                                <?php foreach($estados as $estado):?>
                                                <option value="<?php echo $estado->id_cat?>"><?php echo $estado->etiqueta?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="cancelarPrecioBtn" class="btn btn-default btn-block" value="Cancelar" />
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="guardarPrecioBtn" class="btn btn-success btn-block" value="Guardar" />
                                        </div>
                                        <input type="hidden" id="modoPrecio" value="crear" data-uuid="">
                                    </div>
                                    <!-- Termina campos de Busqueda -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- JQGRID -->
                                            <?php echo modules::run('catalogos_inventario/ocultotablaPrecios'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div role="tabpanel" class="tab-pane" id="preciosItemsAlquiler">

                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Listado de precios</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content">
                                    <!-- Inicia campos de Busqueda -->
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="precio_nombre_alquiler">Nombre<span style="color:red;"> *</span></label>

                                            <input type="text" id="precio_nombre_alquiler" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                            <label for="precio_descripcion_alquiler">Descripci&oacute;n<span style="color:red;"> *</span></label>
                                            <input type="text" id="precio_descripcion_alquiler" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="precio_estado_alquiler">Estado<span style="color:red;"> *</span></label><br>
                                            <select id="precio_estado_alquiler" class="form-control" data-placeholder=" ">
                                                <option value=""> </option>
                                                <?php foreach($estados as $estado):?>
                                                <option value="<?php echo $estado->id_cat?>"><?php echo $estado->etiqueta?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="cancelarPrecioBtn" class="btn btn-default btn-block" value="Cancelar" />
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="guardarPrecioAlquilerBtn" class="btn btn-success btn-block" value="Guardar" />
                                        </div>
                                        <input type="hidden" id="modoPrecio_alquiler" value="crear" data-uuid="">
                                    </div>
                                    <!-- Termina campos de Busqueda -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- JQGRID -->
                                            <?php echo modules::run('catalogos_inventario/ocultotablaPreciosAlquiler'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div role="tabpanel" class="tab-pane" id="unidadesItems">

                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Listado de unidades</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content">
                                    <!-- Inicia campos de Busqueda -->
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="unidad_nombre">Nombre<span style="color:red;"> *</span></label>

                                            <input type="text" id="unidad_nombre" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                            <label for="unidad_descripcion">Descripci&oacute;n<span style="color:red;"> *</span></label>
                                            <input type="text" id="unidad_descripcion" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="unidad_estado">Estado<span style="color:red;"> *</span></label><br>
                                            <select id="unidad_estado" class="form-control" data-placeholder=" ">
                                                <option value=""> </option>
                                                <?php foreach($estados as $estado):?>
                                                <option value="<?php echo $estado->id_cat?>"><?php echo $estado->etiqueta?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="cancelarUnidadBtn" class="btn btn-default btn-block" value="Cancelar" />
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="guardarUnidadBtn" class="btn btn-success btn-block" value="Guardar" />
                                        </div>
                                        <input type="hidden" id="modoUnidad" value="crear" data-uuid="">
                                    </div>
                                    <!-- Termina campos de Busqueda -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- JQGRID -->
                                            <?php echo modules::run('catalogos_inventario/ocultotablaUnidades'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div role="tabpanel" class="tab-pane" id="razonAjustes">

                            <div class="ibox border-bottom">
                                <div class="ibox-title">
                                    <h5>Crear raz&oacute;n de ajustes</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                                    </div>
                                </div>
                                <div class="ibox-content">
                                    <!-- Inicia campos de Busqueda -->
                                    <div class="row">
                                        <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
                                            <label for="razon_nombre">Nombre<span style="color:red;"> *</span></label>

                                            <input type="text" id="razon_nombre" class="form-control" value="" placeholder="">
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-9 col-lg-9">
                                            <label for="razon_descripcion">Descripci&oacute;n<span style="color:red;"> *</span></label>
                                            <input type="text" id="razon_descripcion" class="form-control" value="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="cancelarRazonBtn" class="btn btn-default btn-block" value="Cancelar" />
                                        </div>
                                        <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                            <input type="button" id="guardarRazonBtn" class="btn btn-success btn-block" value="Guardar" />
                                        </div>
                                        <input type="hidden" id="modoRazon" value="crear" data-uuid="">
                                    </div>
                                    <!-- Termina campos de Busqueda -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- JQGRID -->
                                            <?php echo modules::run('catalogos_inventario/ocultotablaRazonesAjustes'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

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

?>
