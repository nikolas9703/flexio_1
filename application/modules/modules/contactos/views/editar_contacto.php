<?php 
/**
 * Verificar desde donde se esta accesando a esta vista.
 */
if(preg_match("/contactos/i", self::$ci->router->fetch_class())): 
?>
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
	                <div class="alert alert-dismissable <?php echo !empty($mensaje) ? 'show '. $mensaje["clase"] : 'hide'  ?>">
	                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
	                    <?php echo !empty($mensaje) ? $mensaje["contenido"] : ''  ?>
	                </div>
	            </div>
	            
	            <div class="row contactoTotal">
	            <?php endif; ?>
                        <?php
//                        echo "<pre>";
//                        print_r($contacto);
//                        echo "<pre>";
//                        die();
                        ?>
                	<?php Template::cargar_formulario((!empty($contacto) ? $contacto : "")); ?>
                	
                <?php if(preg_match("/contactos/i", self::$ci->router->fetch_class())): ?>
                </div>
        	</div>
        	
    	</div><!-- cierra .col-lg-12 -->
	</div><!-- cierra #page-wrapper -->
</div><!-- cierra #wrapper -->

<!-- inicia #optionsModal -->
<div class="modal fade bs-example-modal-sm" id="optionsModal" tabindex="-1" role="dialog" aria-labelledby="optionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                 <!--<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>-->
                <h4 class="modal-title" id="myModalLabel">Opciones</h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<!-- termina #optionsModal -->
<?php endif; ?>

<div class="modal fade" id="busquedaClienteModal" tabindex="-1" role="dialog" aria-labelledby="optionsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Buscar Cliente</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="form-group col-sm-4">
                        <label for="">Nombre</label>
                        <input type="text" id="nombre_cliente" class="form-control" value="" placeholder="">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="">R.U.C. O C&eacute;dula</label>
                        <input type="text" id="cedula_ruc" class="form-control" value="" placeholder="">
                    </div>
                    <div class="form-group col-sm-4">
                        <label for="">Nombre Comercial</label>
                        <input type="text" id="nombre_comercial" class="form-control" value="" placeholder="">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-sm-3 pull-right">
                        <input type="button" id="clearBtn" class="btn btn-w-m btn-default btn-block" value="Limpiar">
                    </div>
                    <div class="form-group col-sm-3 pull-right">
                        <input type="button" id="searchBtn" class="btn btn-w-m btn-default btn-block" value="Filtrar">
                    </div>
                </div>

                <?php
                echo modules::run("clientes/filtarclientes");
                ?>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
