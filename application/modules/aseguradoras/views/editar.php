<?php if (preg_match("/aseguradoras/i", self::$ci->router->fetch_class())): ?>
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
                        <div id="mensaje_info"></div>
                        <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show ' . $mensaje["clase"] : 'hide' ?>">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                            <?php echo !empty($mensaje) ? $mensaje["contenido"] : '' ?>
                        </div>
                        <div>
                        </div>
                    </div>
                    <div class="row" ng-controller="AseguradoraController">
                        <?php endif; ?>
                        <!-- formulario de Aseguradora -->
                        <div id="vistaFormularioAseguradora" <?php if($opcion != NULL && $opcion != 'ver_aseguradora'): ?> class="hide"<?php endif;?>>
                            <?php
                            echo modules::run('aseguradoras/ocultoformulario', $campos); ?>
                        </div>
                    <!-- formulario de contacto -->
                        <div ng-controller="aseguradoraFormularioController">
                            <div id="vistaFormularioContacto" <?php if($opcion != NULL && $opcion != 'nuevo_contacto'): ?> class="hide"<?php endif;?>>
                                <?php
                                $contacto = !empty($contacto) ? array("info" => $contacto) : array();
                                echo modules::run('contactos/crearsubpanel', $contacto); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php if (preg_match("/aseguradoras/i", self::$ci->router->fetch_class())) : Subpanel::visualizar_grupo_subpanel($campos["campos"]["uuid_aseguradora"]); ?>
                    </div>
                </div>
            </div><!-- cierra .col-lg-12 -->
        </div><!-- cierra #page-wrapper -->
    </div><!-- cierra #wrapper -->

    <?php

    echo Modal::config(array(
        "id" => "optionsModal",
        "size" => "sm"
    ))->html();

    ?>

<?php endif; ?>