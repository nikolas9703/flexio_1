    <div class="filtro-formularios" style="background-color: #D9D9D9; padding:6px 0 39px 10px">

        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <label>Empezar interés asegurado desde</label>
        </div>
        <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <select id="formulario" class="white-bg form-control" role="tablist" disabled="">
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
        <div class="form-group col-xs-12 col-sm-5 col-md-5 col-lg-5">
            <select id="selInteres" class="white-bg form-control" role="tablist" onchange="verIntereses()">
                <option value="">Nuevo</option>
                <option v-for="inter in sIntereses" v-bind:value="inter.id" :disabled="inter.disabled">{{inter.numero}}</option>
            </select>
            <input type="hidden" name="selInteres2" id="selInteres2" value=""> 
        </div>
        <div class="form-group col-xs-12 col-sm-3 col-md-6 col-lg-6"></div>

        <!-- Hide Nav-Tabs -->
        <ul class="nav nav-tabs hide">
            <?php
            if(!empty($campos['campos']['tipos_intereses_asegurados'])){
                foreach($campos['campos']['tipos_intereses_asegurados'] AS $tipo){
                    echo '<li><a href="#'.$tipo->valor.'Tab" data-toggle="tab">'.$tipo->etiqueta.'</a></li>';
                }
            }
            ?>
        </ul>
    </div>
    <?php //print_r($campos); ?>
    <!-- Tabs Content -->
    <div class="tab-content filtro-formularios-content m-t-sm">
        <?php
        foreach($campos['campos']['tipos_intereses_asegurados'] AS $tipo){
            ?>
            <div class="tab-pane" id="<?=$tipo->valor?>Tab">
                <?php
                    //var_dump($tipo->valor);
                echo modules::run("intereses_asegurados/" . $tipo->valor. "formularioparcial",$campos);
                ?>
            </div>
            <?php
        }
        ?>
    </div>
    <div id="divvigencia"></div>
    <br><br>

