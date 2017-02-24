<style type="text/css">
    body {
        padding-right: 0px !important;
    }
</style>
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
                    <div class="filtro-formularios" style="background-color: #D9D9D9; padding:6px 0 39px 10px">

                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                            <label>Empezar solicitud desde</label>
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                            <select id="formulario_tipo" name="campo[tipo]" class="white-bg form-control" role="tablist" @change="seleccionarCliente();">
                                <option value="seleccione">Seleccione</option>
                                <option v-for="tipo in catalogoClientes" v-bind:value="tipo.valor">
                                    {{{ tipo.etiqueta}}}
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-5 col-md-5 col-lg-5">
                            <select id="cliente_seleccionado" class="white-bg form-control" role="tablist" name="campo['cliente]" id="cliente" :disabled="disabledOpcionClientes" @change="clienteInfoSelect()">
                                <option value="">Seleccione</option>
                                <option v-for="cliente in clientes | orderBy 'nombre'" v-bind:value="cliente.id">
                                    {{ cliente.nombre + ' - ' + cliente.identificacion}}
                                </option>
                            </select>                            
                        </div>
                        <div class="form-group col-xs-12 col-sm-3 col-md-6 col-lg-6"></div>

                        <!-- Hide Nav-Tabs -->

                    </div> 
                    <?php
                    echo modules::run("solicitudes/ocultoformulario", $id_ramo);
                    ?>                    
                </div>
            </div>

        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<!-- 
    Modal de coberuras y deducciones 
-->
<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 div_coberturas" style="display:none;">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <label>Coberturas</label>    
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <label>Valor</label>    
    </div>

    <div v-for="find in coberturasInfo.coberturas" track-by="$index">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-5">
            <input type="text" name="coberturasNombre[]"  v-model="find.nombre" class="form-control coberturas">
        </div>

        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-5">
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="text" name="coberturasValor[]"  v-model="find.cobertura_monetario" class="form-control coberturas moneda">
            </div>
        </div>

        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-1">
            <button class="btn btn-default btn-block" @click="removeCampos(find)"><i class="fa fa-trash"></i></button>
        </div>

    </div>
    <div class="col-xs-12 col-sm-3 col-md-6 col-lg-1">
        <button class="btn btn-default btn-block addCobertura" @click="addCampos()"><i class="fa fa-plus"></i></button>
    </div>
    <br>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <label>Deducible</label>    
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <label>Valor</label>    
    </div>
    <br>
    <div v-for="find in coberturasInfo.deducion" track-by="$index">
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-5">
            <input type="text" name="deduciblesNombre[]" v-model="find.nombre" class="form-control coberturas">
        </div>
        <br>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-5">
            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="text" name="deduciblesValor[]" v-model="find.deducible_monetario" class="form-control coberturas moneda">
            </div>
        </div>

        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-1">
            <button class="btn btn-default btn-block" @click="removeCamposDeduc(find)"><i class="fa fa-trash"></i></button>
        </div>
    </div>
    <div class="col-xs-12 col-sm-3 col-md-6 col-lg-1">
        <button class="btn btn-default btn-block addCobertura" @click="addCamposDeduc()"><i class="fa fa-plus"></i></button>
    </div>
    <div class="row botones_coberturas" style="display:none;">
        <div class="form-group col-xs-12 col-sm-6 col-md-6">
            <button id="closeModal" class="btn btn-w-m btn-default btn-block" @click="clearFields()" type="button" >Cancelar</button>
        </div>
        <div class="form-group col-xs-12 col-sm-6 col-md-6" @click="setPlanValues()">
            <button id="guardarCoberturas" class="btn btn-w-m btn-primary btn-block" type="button">Guardar</button>

        </div>
    </div>
</div>
<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12 individual"  style="display:none;" id="indCoverageCtrl">
<div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label>Coberturas</label>    
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label>Valor</label>    
        </div>
        <div id="indCoveragefields">


        </div>
        <div class="col-xs-12 col-sm-3 col-md-6 col-lg-1" id="btnAddCoverage">
            <button class="btn btn-default btn-block addCobertura"><i class="fa fa-plus"></i></button>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label>Deducible</label>    
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <label>Valor</label>    
        </div>
        <br>
        <div  id="indDeductiblefields">

        </div>
        <div class="col-xs-12 col-sm-3 col-md-6 col-lg-1" id="btnAddDeductible">
            <button class="btn btn-default btn-block addCobertura"><i class="fa fa-plus"></i></button>
        </div>

    </div>
    <div class="row btnIndidualCoverage" style="display:none;">
        <div class="form-group col-xs-12 col-sm-6 col-md-6">
            <button id="closeModal" class="btn btn-w-m btn-default btn-block" data-dismiss="modal"  type="button" >Cancelar</button>
        </div>
        <div class="form-group col-xs-12 col-sm-6 col-md-6">
            <button id="saveIndividualCoveragebtn" class="btn btn-w-m btn-primary btn-block" type="button">Guardar</button>

        </div>

    </div>
</div>
<!-- 
    Termina Modal de coberuras y deducciones 
-->
<?php
echo Modal::config(array(
    "id" => "verCoberturas",
    "size" => "lg"
    ))->html();
echo Modal::config(array(
    "id" => "IndCoberturas",
    "size" => "lg"
    ))->html();
echo Modal::config(array(
    "id" => "optionsModal",
    "size" => "sm"
    ))->html();
