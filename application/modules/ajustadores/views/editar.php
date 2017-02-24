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

                </div>

                <div class="row">
                    <?php
                    echo modules::run('ajustadores/editoformulario', $campos);
                    ?>
                </div>
                <div>
                    <?php
                    echo modules::run('ajustadores/tabladetalles', $campos);
                    ?>
                </div>



            </div>




        </div><!-- cierra .col-lg-12 -->
    </div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->
<?php
echo Modal::config(array(
"id" => "opcionesModal",  
"size" => "sm"
))->html();
$formAttr = array('method' => 'POST', 'id' => 'exportarContactos','autocomplete'  => 'off');

echo form_open(base_url('ajustadores/exportarContactos'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php
echo form_close();

$formAttr = array('method' => 'POST', 'id' => 'exportarPlanesLnk','autocomplete'  => 'off');
echo form_open(base_url('planes/exportar'), $formAttr);
?>
<input type="hidden" name="ids" id="ids" value="" />
<?php
echo form_close();
?>

?>
