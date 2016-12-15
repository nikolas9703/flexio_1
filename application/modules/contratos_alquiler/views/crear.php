<div id="wrapper">
    <?php
    Template::cargar_vista('sidebar');
    ?>
    <div id="page-wrapper" class="gray-bg row">

        <?php Template::cargar_vista('navbar'); ?>
        <div class="row border-bottom"></div>
        <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

        <div class="col-lg-12">
            <div class="wrapper-content" id="appContratoAlquiler">
                
                <div class="row">
                    <div id="mensaje_info"></div>
                    <div class="alert alert-dismissable <?php echo!empty($mensaje) ? 'show ' . $mensaje["clase"] : 'hide' ?>">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <?php echo!empty($mensaje) ? $mensaje["mensaje"] : '' ?>
                    </div>
                </div>
                <?php
                $formAttr = [
                    'id' => 'form_crear_contrato_alquiler',
                    'autocomplete' => 'off',
                    'method' => 'POST'];

                echo form_open(base_url('contratos_alquiler/guardar'), $formAttr);
                ?>
 
        
          <empezar_desde :empezable.sync="empezable" :detalle.sync="contrato_alquiler" :config="config"></empezar_desde>
          
 
                      
            <!--componente empezar desde-->
            
                <!--<div class="row" style="margin-right: 0px;">

                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="background-color: #D9D9D9;padding: 7px 0 7px 0px;">

                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3" style="padding-top: 7px;">
                            
                            <span><strong>Empezar contratos desde </strong></span>
                            
                        </div>
                        
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            
                            <select class="form-control" name="campo[empezar_desde_type]" required="" data-rule-required="true" v-model="contrato_alquiler.empezar_desde_type" @change="cambiarTipo(contrato_alquiler.empezar_desde_type)" :disabled="disabledEditar">
                                <option value="">Seleccione</option>
                                <option value="cliente">Cliente</option>
                            </select>
                            
                        </div>
                        
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            
                            <select  class="form-control" name="campo[empezar_desde_id]" v-model="contrato_alquiler.empezar_desde_id" @change="cambiarTipoId(contrato_alquiler.empezar_desde_id)" :disabled="contrato_alquiler.empezar_desde_type == '' || disabledHeader || disabledEditar">
                                <option value="">Seleccione</option>
                                <option value="{{cliente.id}}" v-for="cliente in clientes | orderBy 'nombre'">{{cliente.nombre}}</option>
                            </select>
                            
                        </div>

                    </div>

                </div>-->

                <div class="ibox border-bottom">
                    <div class="ibox-title">
                        <h5>Datos del contrato</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                        </div>
                    </div>

                    <div class="ibox-content" style="display:block;">
                        <div class="row">
                            <?php
                            $info = !empty($info) ? array("info" => $info) : array();
                            echo modules::run('contratos_alquiler/ocultoformulario', $info);
                            ?>
                        </div>
                    </div>
                 
                </div>
                   
                    <div class="ibox-title">
                        <h5>Items contratados</h5>
                     <!--    <div class="ibox-tools">
                            <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>  
                        </div> -->
                    </div>

                    <div class="ibox-content" style="display:block;">
                        <div class="row">
                            <?php
                            $info = !empty($info) ? array("info" => $info) : array();
                            echo modules::run('contratos_alquiler/ocultoformulario_items_contratados', $info);
                            ?>
                        </div>
                    </div>
                <?php echo form_close(); ?>

            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

    <?php
        echo Modal::config(array(
            "id" => "opcionesModal",
            "size" => "sm"
        ))->html();
