<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
    ?>
    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

        <div class="col-lg-12">
            <div class="wrapper-content" id="controller_cheque"  ng-controller="crearChequeController">
                <div class="row">
                    <div id="mensaje_info"></div>
                    <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <?php echo !empty($mensaje) ? $mensaje["mensaje"] : ''  ?>
                    </div>
                </div>
                <?php
                $formAttr = array(
                    'method'       => 'POST',
                    'id'           => 'form_crear_cheque',
                    'autocomplete' => 'off'
                );

                echo form_open(base_url('cheques/guardar'), $formAttr);?>

                <div class="row rowhigth" ng-show="acceso">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <span>Empezar cheque desde </span>
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <select required="" class="form-control" id="tipo" ng-model="chequesHeader.tipo" ng-change="empezarDesde(chequesHeader.tipo)" ng-disabled="disableTipo">
                                <option value="">Seleccione</option>
                                <option value="pago">Pago</option>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <select required="" class="form-control" name="crear_desde" id="crear_desde" ng-model="chequesHeader.uuid" ng-change="llenarFormulario(chequesHeader.uuid)" ng-options="valores as valores.codigo for valores in chequesHeader.collection track by valores.uuid" ng-disabled="chequesHeader.tipo === ''">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="ibox border-bottom" ng-show="acceso">
                    <div class="ibox-title">
                        <h5>Datos del cheque</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                        </div>
                    </div>

                    <div class="ibox-content" style="display:block;">
                        <div class="row">
                      	<?php
                            $info = !empty($info) ? array("info" => $info) : array();
                            echo modules::run('cheques/ocultoformulario', $info);
                      	?>
                    </div>
                    </div>
                </div>
                <?php  echo  form_close();?>

            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
echo Modal::config(array(
    "id" => "opcionesModal",
    "size" => "sm"
))->html();
