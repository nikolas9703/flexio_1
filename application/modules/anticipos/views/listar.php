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
<?php //$mensaje = self::$ci->session->flashdata('mensaje'); ?>
<div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
<?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
</div>
</div>

<div role="tabpanel">
<!-- Tab panes -->
<div class="row tab-content">
<div role="tabpanel" class="tab-pane active" id="tabla">

<!-- BUSCADOR -->
<?php
$formAttr = array(
'method'       => 'POST',
'id'           => 'buscarAnticipoForm',
'autocomplete' => 'off'
);

echo form_open_multipart("", $formAttr);
?>
<div class="ibox border-bottom">
<div class="ibox-title">
<h5>Buscar Anticipos</h5>
<div class="ibox-tools">
    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
</div>
</div>
<div class="ibox-content" style="display:none;">
<!-- Inicia campos de Busqueda -->
<div class="row col-xs-12 col-sm-12 col-md-12 col-lg-12">

    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <label for="">No. Anticipo</label>
        <input type="text" name="campo[codigo]" id="codigo" class="form-control">
    </div>


    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <label for="proveedor"><?php echo ucfirst($anticipable_type)?></label>
        <select name="campo[<?php echo $anticipable_type?>]" class="form-control chosen-select" id="<?php echo $anticipable_type?>">
            <option value="">Seleccione</option>
            <?php foreach($anticipables as $anticipable) {?>
            <option value="<?php echo $anticipable['id']?>"><?php echo $anticipable['nombre']?></option>
            <?php }?>
        </select>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <label for="">Rango de fechas</label>
        <div class="form-inline">
            <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                  <input type="text" name="campo[fecha_min]" id="fecha_min" class="form-control fecha-menor">
                  <span class="input-group-addon">a</span>
                  <input type="text" name="campo[fecha_max]" id="fecha_max" class="form-control fecha-mayor">
                </div>
            </div>
        </div>
    </div>



    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <label for="">Montos</label>
        <div class="form-inline">
            <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon">$</span>
                  <input type="text" name="campo[monto_min]" id="monto_min" class="form-control moneda">
                  <span class="input-group-addon">a</span>
                  <input type="text" name="campo[monto_max]" id="monto_max" class="form-control moneda">
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <label for="">No. Documento</label>
        <input type="text" name="campo[documento]" id="documento1" class="form-control">
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <label for="">Metodo de Anticipo</label>
        <select name="campo[metodo_anticipo]" class="form-control chosen-select" id="metodo_anticipo">
            <option value="">Seleccione</option>
            <?php foreach($metodos as $metodo) {?>
            <option value="<?php echo $metodo->etiqueta?>"><?php echo $metodo->valor?></option>
            <?php }?>
        </select>
    </div>


    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <label for="">Estado</label>
        <select name="campo[estado]" class="form-control chosen-select" id="estado">
            <option value="">Seleccione</option>
            <?php foreach($etapas as $etapa) {?>
            <option value="<?php echo $etapa->etiqueta?>"><?php echo $etapa->valor?></option>
            <?php }?>
        </select>
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-3 col-lg-3">
        <label for="">Relacion de anticipo</label>
        <select name="campo[anticipable_type]" class="form-control chosen-select" id="anticipable_type" disabled>
            <option value="">Seleccione</option>
            <?php foreach($relacion_anticipo as $relacion) {?>
                <option  <?php echo $anticipable_type == $relacion['etiqueta']?'selected':'' ?> value="<?php echo $relacion['etiqueta']?>"><?php echo $relacion['valor']?></option>
            <?php }?>
        </select>
    </div>
</div>
<div class="row">
    <div class="col-xs-0 col-sm-0 col-md-8 col-lg-8">&nbsp;</div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
        <input type="button" id="searchBtn" class="btn btn-default btn-block" value="Filtrar" />
    </div>
    <div class="form-group col-xs-12 col-sm-6 col-md-2 col-lg-2">
        <input type="button" id="clearBtn" class="btn btn-default btn-block" value="Limpiar" />
    </div>
</div>
    <!-- Termina campos de Busqueda -->
</div>

</div>
<?php echo form_close(); ?>
<!-- /BUSCADOR -->

<!-- JQGRID -->
<?php echo modules::run('anticipos/ocultotabla'); ?>

<!-- /JQGRID -->
</div>
</div>
</div>
</div>

</div><!-- cierra .col-lg-12 -->
</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
//formulario exportar
$formAttr = array('method' => 'POST', 'id' => 'exportar','autocomplete'  => 'off');
echo form_open(base_url('anticipos/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php echo form_close();?>


<?php echo Modal::config(array( "id" => "optionsModal", "size"  => "sm"))->html();?> <!-- modal opciones -->
<?php //echo Modal::modalSubirDocumentos();?>  <!-- modal subir documentos -->


<?php
$formAttr = array('method' => 'POST', 'id' => 'cambiarEstadoEnGrupo','autocomplete'  => 'off');
echo form_open(base_url('anticipos/cambiar_estado_grupal'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<input type="hidden" name="estado" id="estadoGrupal" value="" />
<?php echo form_close(); ?>
<?php
echo Modal::config(array(
	"id" => "opcionesModal",
	"size" => "sm"
))->html();

echo Modal::config(array(
	"id" => "cambiarEstadoAnticipo",
	"size" => "sm"
))->html();
?>

<?php echo Modal::config(array(
	"id" => "documentosModal",
	"size" => "lg",
	"titulo" => "Subir Documentos",
	"contenido" => modules::run("documentos/formulario", array())
))->html(); ?>  <!-- modal subir documentos -->
