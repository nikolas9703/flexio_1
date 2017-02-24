<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
    ?>
    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

        <div class="col-lg-12">
            <div id="formulario_creacion" class="wrapper-content">
                <div class="row">
                    <div id="mensaje_info"></div>
                    <div class="alert alert-dismissable <?php echo!empty($mensaje) ? 'show ' . $mensaje["clase"] : 'hide' ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <?php echo!empty($mensaje) ? $mensaje["mensaje"] : '' ?>
                    </div>
                </div>

                <?php
                $formAttr = array(
                    'method' => 'POST',
                    'id' => 'form_crear_cotizacion_alquiler',
                    'autocomplete' => 'off'
                );

                echo form_open(base_url('cotizaciones_alquiler/guardar'), $formAttr);
                ?>

                <div class="row rowhigth" v-show="config.acceso">
                        <!--componente empezar desde-->
                        <empezar-desde :titulo="empezable.titulo" :options="empezable.categoria" :config="empezable.configSelect2" :info="config"></empezar-desde>
                        <!--componente empezar desde-->
                </div>

                <div class="ibox border-bottom" v-show="config.acceso">
                    <div class="ibox-title">
                        <h5>Datos del cliente</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                        </div>
                    </div>

                    <div class="ibox-content" style="display:block;">
                        <div class="row">
                            <?php
                            $info = !empty($info) ? array("info" => $info) : array();
                            echo modules::run('cotizaciones_alquiler/ocultoformulario', $info);
                            ?>
                        </div>
                    </div>

                    <div class="ibox-title">
                        <h5>&Iacute;tems de alquiler</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                        </div>
                    </div>

                    <div class="ibox-content" style="display:block;">
                        <div class="row">
                             <tabla-articulos :config="config" :catalogos="catalogos" :detalle.sync="detalle"></tabla-articulos>
                        </div>

                        <div id="cargoadicional-accordion">
                      		<div class="ibox-title">
                      			<h5><input type="checkbox" name="campo[cargos_adicionales]" id="cargos_adicionales" class="toggle-cargoadicional" v-model="formulario.cargos_adicionales_checked" /> Cargos adicionales a</h5>
                      			<a href="#cargoadicional" id="togglecargoadicional" data-toggle="collapse">&nbsp;</a>
                      		</div>
                      		<div id="cargoadicional" class="ibox-content panel-collapse collapse {{formulario.cargos_adicionales_checked ? 'in' : ''}}">

                            <!-- Lista de Precio Item Adicional -->
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3">
                                <label>Lista de precio de venta<span required="" aria-required="true">*</span></label>
                                <select name="campo[item_precio_id]" class="" id="item_precio_id" :data-rule-required="detalle.cargos_adicionales_checked ? 'true' : 'false'" aria-required="true" v-model="detalle.item_precio_id" v-select2="detalle.item_precio_id" :config="config.select2" :disabled="config.disableDetalle">
                                    <option value="">Seleccione</option>
                                    <option :value="precio.id" v-for="precio in catalogos.precios">{{precio.nombre}}</option>
                                </select>
                            </div>

                      			<articulos :config="config" :detalle.sync="detalle" :catalogos="catalogos" :empezable.sync="empezable"></articulos>

                      		</div>
                      	</div>

                        <totales-alquiler :config="config" :detalle.sync="detalle" :catalogos="catalogos" :empezable.sync="empezable"></totales-alquiler>
                        <div class="row"></div>

                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <label for="comentario">Observaciones</label>
                            <textarea id="comentario" name="campo[comentario]" class="form-control" v-model="formulario.comentario" :disabled="disabledEditar"></textarea>
                          </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
                            <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                <a href="<?php echo base_url('cotizaciones_alquiler/listar'); ?>" class="btn btn-default btn-block" id="cancelarFormBtn">Cancelar </a>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2">
                                <input type="hidden" name="campo[id]" id="cotizacion_alquiler_id" value="{{formulario.id}}" />
                                <!--<input type="button" id="guardarBtn" data-style="expand-right" name="guardarBtn"  class="btn btn-primary btn-block" value="Guardar" @click="guardar()" :disabled="campoDisabled.botonDisabled"/>-->
<button class="btn btn-primary btn-block" name="guardarBtn" id="guardarBtn"  @click="guardar()" :disabled="campoDisabled.botonDisabled"><span>Guardar</span></button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
                <!-- Comentarios -->
                 <div class="row" v-show="config.acceso">
                  <vista_comments
                  v-if="config.vista ==='editar'"
                  :config="config"
                  :historial.sync="comentarios"
                  :modelo="formulario.model_class"
                  :registro_id="formulario.id"
                  ></vista_comments>
                </div>
             <!-- Comentarios -->
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

    <?php
        echo Modal::config(array(
            "id" => "opcionesModal",
            "size" => "sm"
        ))->html();
