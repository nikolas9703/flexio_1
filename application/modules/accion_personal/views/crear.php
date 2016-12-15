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

                    <div class="hide filtro-formularios" style="background-color: #D9D9D9; padding:6px 0 39px 10px">

                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                            <label>Empezar acci&oacute;n personal desde</label>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                            <select id="formulario" class="white-bg chosen-filtro" role="tablist">
                                <option value="">Seleccione</option>
                                <option value="evaluacionesTab" data-toggle="tab">Evaluaciones</option>
                                <option value="ausenciasTab" data-toggle="tab">Ausencias</option>
                                <option value="vacacionesTab" data-toggle="tab">Vacaciones</option>
                                <option value="licenciasTab" data-toggle="tab">Licencias</option>
                                <option value="incapacidadesTab" data-toggle="tab">Incapacidades</option>
                                <option value="liquidacionesTab" data-toggle="tab">Liquidaciones</option>
                                <option value="permisosTab" data-toggle="tab">Permisos</option>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                            <select id="colaborador_id" class="white-bg chosen-filtro">
                                <option value="">Seleccione</option>
                                <?php
                                if (!empty($colaboradores)) {
                                    foreach ($colaboradores AS $colaborador) {
                                        echo '<option value="' . $colaborador["id"] . '">' . $colaborador["nombre"] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-6 col-lg-6"></div>

                        <!-- Hide Nav-Tabs -->
                        <ul class="nav nav-tabs hide">
                            <li><a href="#evaluacionesTab" data-toggle="tab">Evaluaciones</a></li>
                            <li><a href="#ausenciasTab" data-toggle="tab">Ausencias</a></li>
                            <li><a href="#vacacionesTab" data-toggle="tab">Vacaciones</a></li>
                            <li><a href="#licenciasTab" data-toggle="tab">Licencias</a></li>
                            <li><a href="#incapacidadesTab" data-toggle="tab">Incapacidades</a></li>
                            <li><a href="#liquidacionesTab" data-toggle="tab">Liquidaciones</a></li>
                            <li><a href="#permisosTab" data-toggle="tab">Permisos</a></li>
                        </ul>
                    </div>

                    <!-- Tabs Content -->
                    <div class="tab-content filtro-formularios-content m-t-sm">
                        <div class="tab-pane" id="evaluacionesTab">
                            <?php
                            $info = !empty($info) ? array("info" => $info) : array();
                            echo modules::run('evaluaciones/formularioparcial', $info);
                            ?>
                        </div>
                        <div class="tab-pane" id="ausenciasTab">
                            <?php
                            $info = !empty($info) ? array("info" => $info) : array();
                            echo modules::run('ausencias/formularioparcial', $info);
                            ?>
                        </div>
                        <div class="tab-pane" id="vacacionesTab">
                            <?php
                            $info = !empty($info) ? array("info" => $info) : array();
                            echo modules::run('vacaciones/formularioparcial', $info);
                            ?>
                        </div>
                        <div class="tab-pane" id="licenciasTab">
                            <?php
                            $info = !empty($info) ? array("info" => $info) : array();
                            echo modules::run('licencias/formularioparcial', $info);
                            ?>
                        </div>
                        <div class="tab-pane" id="incapacidadesTab">
                            <?php
                            $info = !empty($info) ? array("info" => $info) : array();
                            echo modules::run('incapacidades/formularioparcial', $info);
                            ?>
                        </div>
                        <div class="tab-pane" id="liquidacionesTab">
                            <?php
                            $info = !empty($info) ? array("info" => $info) : array();
                            echo modules::run('liquidaciones/formularioparcial', $info);
                            ?>
                        </div>
                        <div class="tab-pane" id="permisosTab">
                            <?php
                            $info = !empty($info) ? array("info" => $info) : array();
                            echo modules::run('permisos/formularioparcial', $info);
                            ?>
                        </div>
                    </div>

                </div>
                <?php //dd($vista);
                if ($vista == "ver" ) { ?>
                    <!--comentarios-->
                    <div id="rootApp" class="row">
                        <vista_comments
                            v-if="config.vista ==='editar'"
                            :config="config"
                            :historial.sync="comentarios"
                            :modelo="modelo"
                            :registro_id="id"
                        ></vista_comments>
                    </div>
                    <!--comentarios-->
                <?php } ?>
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<input type="hidden" name="ids" id="ids" value=""/>
<?php echo form_close(); ?>
