    <div class="filtro-formularios" style="background-color: #D9D9D9; padding:6px 0 0px 10px">
        <div class="row">

            <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
                <label>Empezar interés asegurado desde</label>
            </div>
            <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
                <select id="formulario" class="white-bg form-control " role="tablist" >
                    <option value="">Seleccione</option>
                    <?php
                    if(!empty($campos['campos']['tipos_intereses_asegurados'])){
                        foreach($campos['campos']['tipos_intereses_asegurados'] AS $tipo){
                            echo '<option value="'. $tipo->valor .'Tab">'. $tipo->etiqueta .'</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-xs-12 col-sm-5 col-md-5 col-lg-5 ">
                <select id="selInteres" class="white-bg form-control select2" role="tablist" :disabled="disabledfechaInicio" v-model="interesId" onchange="formularioCrear.getInteres()">
                    <option value="">Nuevo</option>
                    <option v-for="inter in sIntereses" v-bind:value="inter.id">{{inter.numero}}</option>
                </select>
            </div>

            <div class="col-xs-12 col-sm-1 col-md-3 col-lg-3"></div>

        </div>
    </div>
    <?php //print_r($campos); ?>
    <!-- Tabs Content -->
    <div class="tab-content filtro-formularios-content m-t-sm">
        <?php
        //foreach($campos['campos']['tipo_interes'] AS $tipo){
            ?>
            <div class="tab-pane" id="<?=$campos['campos']['tipo_interes']?>Tab">
                <?php
                echo modules::run("intereses_asegurados/" . $campos['campos']['tipo_interes']. "formularioparcial",$campos);
                ?>
            </div>
            <?php
        //}
        ?>
    </div>
    <div id="divvigencia"></div>

