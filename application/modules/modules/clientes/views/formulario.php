<?php

$formAttr = array(
    'method' => 'POST',
    'id' => 'formClienteCrear',
    'autocomplete' => 'off'
);
?>
<div>

    <?php echo form_open(base_url('clientes/guardar'), $formAttr); ?>
    <div class="tab-content">

        <datos-cliente :config.sync="config" :detalle.sync="detalle" :catalogos="catalogos" v-show="!config.siguiente"></datos-cliente>

        <informacion-pago :config.sync="config" :detalle.sync="detalle" :catalogos="catalogos" v-show="!config.siguiente"></informacion-pago>

        <asignados :config.sync="config" :detalle.sync="detalle" :catalogos="catalogos" v-show="!config.siguiente"></asignados>

        <asignados-usuarios :config.sync="config" :detalle.sync="detalle" :catalogos="catalogos" v-show="!config.siguiente"></asignados-usuarios>

        <centros-facturacion :config.sync="config" :detalle.sync="detalle" :catalogos="catalogos" v-show="config.siguiente"></centros-facturacion>

        <div class="row">
			<input type='hidden' name='regreso' value='<?php if(isset($_GET['mod'])) echo $_GET['mod']; else echo '';?>' />
            <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
				<?php 
                //var_dump($clientes);
				if(isset($_GET['mod']))
				{
					 if ($_GET['mod']=="fact") {
                        $regreso='facturas_seguros/listar';
                    }else if ($_GET['mod']=="recl") {
                        $regreso='reclamos/listar';
                    }elseif ($_GET['mod']=="endo") {
                        $regreso = 'endosos/listar';
                    }	
				}				
				else 
					$regreso='clientes/listar';
				?>
                <a href="<?php echo base_url($regreso); ?>" class="btn btn-default btn-block" v-show="!config.siguiente">Cancelar </a>
                <a href="#" class="btn btn-default btn-block" v-show="config.siguiente" @click="toggleCentroFacturacion()">Regresar </a>
            </div>
            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                <a href="#" class="btn btn-primary btn-block" v-show="!config.siguiente && config.vista == 'crear'" @click="toggleCentroFacturacion()">Siguiente </a>
                <input type="submit" name="campo[guardar]" value="Guardar " class="btn btn-primary btn-block" id="campo[guardar]" v-show="config.siguiente || config.vista == 'ver'">
                <input type="hidden" name="campo[id]" v-model="detalle.id">
                <input type="hidden" name="id_cp" v-model="detalle.id_cp">
            </div>
        </div>

    </div>
    <?php echo form_close(); ?>

</div>
