<div id="wrapper">
    <?php
	Template::cargar_vista('sidebar');
 	?>
    <div id="page-wrapper" class="gray-bg row">

    <?php Template::cargar_vista('navbar'); ?>
	<div class="row border-bottom"></div>
    <?php Template::cargar_vista('breadcrumb'); //Breadcrumb ?>

        <div class="row border-bottom"></div>
        <div class="row">
            <div id="app_landing_page" class="col-lg-12">
              <h2>¡Hola, <?php echo $nombre_usuario?>!</h2>

              <landing-page v-if="showComponente"></landing-page>
            </div>
            <?php echo self::$ci->load->view('landing_page/componente_landing_page', '', true);?>
            <?php echo self::$ci->load->view('landing_page/componente_comentario_modulo', '', true);?>
            <?php echo self::$ci->load->view('landing_page/paginador', '', true);?>
            <?php echo self::$ci->load->view('landing_page/comentario_texto', '', true);?>
        </div>
    </div>
</div>
