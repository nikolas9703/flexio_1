<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
    ?>
    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

        <div class="col-lg-12">
            <div class="wrapper-content" id="form_crear_oportunidad_div">
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
                    'id' => 'form_crear_oportunidad',
                    'autocomplete' => 'off'
                );

                echo form_open(base_url('oportunidades/guardar'), $formAttr);
                ?>

                <div class="row" style="margin-right: 0px;">

                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: #D9D9D9;padding: 7px 0 7px 0px;">

                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" style="padding-top: 7px;">

                            <span><strong>Empezar oportunidad desde </strong></span>

                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <select class="form-control" name="campo[empezar_desde_type]" required="" data-rule-required="true" v-model="oportunidad.empezar_desde_type" @change="cambiarTipo(oportunidad.empezar_desde_type)" :disabled="disabledEditar || config.vista == 'editar'">
                                <option value="">Seleccione</option>
                                <option value="cliente">Cliente</option>
                                <option value="cliente_potencial">Cliente potencial</option>
                            </select>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <select  class="form-control" name="campo[empezar_desde_id]" v-model="oportunidad.empezar_desde_id" @change="cambiarTipoId(oportunidad.empezar_desde_id)" :disabled="config.vista == 'editar' || oportunidad.empezar_desde_type == '' || disabledHeader || disabledEditar">
                                <option value="">Seleccione</option>
                                <option value="{{cliente.id}}" v-for="cliente in clientes | orderBy 'nombre'" v-if="oportunidad.empezar_desde_type=='cliente'">{{cliente.nombre}}</option>
                                <option value="{{cliente_potencial.id_cliente_potencial}}" v-for="cliente_potencial in clientes_potenciales | orderBy 'nombre'" v-if="oportunidad.empezar_desde_type=='cliente_potencial'">{{cliente_potencial.nombre}}</option>
                            </select>
                        </div>

                    </div>

                </div>

                <div class="ibox border-bottom">
                    <div class="ibox-title">
                        <h5>Datos de la oportunidad</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                        </div>
                    </div>

                    <div class="ibox-content" style="display:block;">
                        <div class="row">
                            <?php
                            $info = !empty($info) ? array("info" => $info) : array();
                            echo modules::run('oportunidades/ocultoformulario', $info);
                            ?>
                        </div>
                    </div>

                </div>
                <?php echo form_close(); ?>

                <div class="row">
                    <div class="row" id="subpanel">
                        <?php SubpanelTabs::visualizar('oportunidad_id='.$oportunidad->id); ?>
                    </div>
                </div>

                <!-- Comentarios -->
                 <div class="row">
                     <vista_comments
                      v-if="config.vista === 'editar'"
                      :config="config"
                      :historial.sync="comentario.comentarios"
                      :modelo="comentario.comentable_type"
                      :registro_id="comentario.comentable_id"
                      ></vista_comments>
                    </div>
                 <!-- Comentarios -->

            </div>



        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

    <?php
        echo Modal::config(array(
            "id" => "optionsModal",
            "size" => "sm"
        ))->html();
